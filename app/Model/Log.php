<?php

App::uses('AppModel', 'Model');

class Log extends AppModel {
	
	public $useTable = 'log';

	public $validate = array(
		'player_id' => array('notEmpty'),
		'activity_id' => array('notEmpty'),
		'acquired' => array(
			'notEmpty', 
			'acquiredPastRule' => array(
				'rule' => 'acquiredPastRule',
				'message' => 'Activities performed more than one week ago cannot be reported. '
			),
			'acquiredFutureRule' => array(
				'rule' => 'acquiredFutureRule',
				'message' => 'Activities should be executed before reported. '
			)
		),
		'description' => array(
			'notEmpty',
			'duplicated' => array(
				'rule' => 'duplicated',
				'message' => 'It seems this activity has already been reported today with exactly the same information (activity, description, player).'
			)
		),
	);

	public $belongsTo = array(
		'Activity', 'Player', 'Domain',
		'PairedPlayer' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_pair'
		)
	);

	public $hasMany = array(
		'LogVote' => array(
			'dependent' => true
		)
	);
	
	public $hasAndBelongsToMany = array(
		'Tags' => array(
			'className' => 'Tag',
			'dependent' => true
		)
	);
	
	public $uses = array('XpLog', 'Notification', 'ActivityRequisite');

	public function beforeInsert($options = array()) {
		$activity = $this->Activity->findById($this->data['Log']['activity_id']);
		$this->data['Log']['domain_id'] = $activity['Activity']['domain_id'];
		$xp = $activity['Activity']['xp'];
		$this->data['Log']['xp'] = (int)$xp;
		$this->data['Log']['hash'] = $this->hash($this->data);
		return true;
	}

	public function hash($log) {
		$data = isset($log['Log']['activity_id'])? $log['Log']['activity_id'] : '?';
		$data .= isset($log['Log']['description'])? $log['Log']['description'] : '?';
		$data .= isset($log['Log']['acquired'])? $log['Log']['acquired'] : '?';
		$data .= isset($log['Log']['player_id'])? $log['Log']['player_id'] : '?';
		return Security::hash($data, 'sha256', true);
	}

	public function acquiredFutureRule() {
		if (isset($this->data['Log']['acquired'])) {
			$acquired = new DateTime($this->data['Log']['acquired']);
			$now = new DateTime();
			if ($acquired > $now) {
				return false;
			}
		}
		return true;
	}

	public function duplicated() {
		$hash = $this->hash($this->data);
		if ($this->find('count', array('conditions' => array('Log.hash' => $hash)))) {
			return false;
		} else {
			return true;
		}
	}

	public function acquiredPastRule() {
		if (isset($this->data['Log']['acquired'])) {
			$acquired = new DateTime($this->data['Log']['acquired']);
			$now = new DateTime(date('Y-m-d') . ' 00:00:00');
			$now->modify('-1 week');
			if ($acquired < $now) {
				return false;
			}
		}
		return true;
	}

	public function _review($id, $playerIdReviewer, $action) {
		if (!in_array($action, array('accept', 'reject'))) {
			throw new ModelException('Invalid action');
		}
		$log = $this->find('first', array(
			'conditions' => array(
				'Log.id' => $id
			), 
			'contain' => array(
				'Activity',
				'LogVote',
				'Player',
				'Tags'
			)
		));
		if (!$log) {
			throw new ModelException('Log not found');
		}

		$logId = $log['Log']['id'];
		$activityId = $log['Activity']['id'];
		$activityName = $log['Activity']['name'];
		$playerId = $log['Player']['id'];
		$playerName = $log['Player']['name'];

		// Verifica se esta atividade já foi logada (e revisada)
		$logged = $this->find('count', array(
			'conditions'=> array(
				'Log.activity_id' => $activityId,
				'Log.reviewed IS NOT NULL'
			)
		));
		// Atualiza as tabelas de resumo de progresso
		$this->ActivityRequisite->_updateActivityRequisiteSummary($log);

		$logUpdate = array();
		$logUpdate['id'] = $log['Log']['id'];
		$logUpdate['reviewed'] = date('Y-m-d H:i:s');

		if ($action === 'accept') {	
			$logUpdate['accepted'] = date('Y-m-d H:i:s');
		} else {
			$logUpdate['rejected'] = date('Y-m-d H:i:s');
		}
		if (!$this->save($logUpdate)) {
			throw new ModelException('Could not update log');
		}

		$this->query('UPDATE activity SET reported = reported + 1 WHERE id = ?', array($activityId));

		// Gera experiência para o jogador
		$this->XpLog->_activityReported($playerId, $logId);

		// Generate XP for all players that accepted or rejected this activity
		// Search all players that accepted or reject
		foreach ($log['LogVote'] as $logVote) {
			if (($action === 'accept' && $logVote['vote'] == 1) || 
				($action === 'reject' && $logVote['vote'] == -1)) {
				$this->XpLog->_activityReviewed($action, $logVote['player_id'], $logId);
			}
		}

		// Se foi a primeira vez que esta atividade foi logada, gera uma notificação
		if (!$logged) {
			$this->Notification->_broadcast(
				$playerId,
				__('First Time Completion'), 
				__('The %s activity was completed for the first time in this game. Congratulations, %s!', $activityName, $playerName)
			);
		}
	}

	public function allNotReviewed() {
		return $this->find('all',
			array(
				'conditions' => array('Log.reviewed IS NULL'),
				'recursive' => 2
			)
		);
	}

	public function countNotReviewed() {
		return $this->find('count',
			array(
				'conditions' => array('Log.reviewed IS NULL'),
				'recursive' => 2
			)
		);
	}

	public function timeline($limit = 100) {
		return $this->find('all', array(
			'recursive' => 2,
			'order' => array('Log.created' => 'DESC'),
			'limit' => $limit
		));
	}

	public function playerCount($playerId) {
		return $this->find('count', array(
			'conditions' => array(
				'Log.reviewed IS NOT NULL', 
				'Log.player_id' => $playerId)));
	}

	public function average($playerIdOwner) {
		$result = $this->query('
			SELECT AVG(count) AS average 
			FROM (
				SELECT COUNT(*) AS count FROM log 
				WHERE reviewed IS NOT NULL 
				AND player_id_owner = ?
				GROUP BY player_id
			) A', array($playerIdOwner));
		$average = @$result[0][0]['average'];
		return $average? (float)$average : 0;
	}

	public function simpleReviewed() {
		return $this->find('list', array('conditions' => array('Log.reviewed IS NOT NULL')));
	}

	public function allReviewed() {
		return $this->all(array('Log.reviewed IS NOT NULL'));
	}

	public function allPendingFromPlayer($playerId) {
		return $this->find('all', array(
			'contain' => array(
				'Domain',
				'PairedPlayer',
				'Tags', 
				'Activity'
			),
			'conditions' => array('Log.player_id' => $playerId, 'Log.reviewed IS NULL'),
			'order' => array('Log.created' => 'DESC')
		));
	}

	public function countPendingFromPlayer($playerId) {
		return $this->find('count', array(
			'conditions' => array('Log.player_id' => $playerId, 'Log.reviewed IS NULL')
		));
	}

	public function countPendingFromTeam($teamId) {
		return $this->find('count', array(
			'conditions' => array('Player.team_id' => $teamId, 'Log.reviewed IS NULL')
		));
	}

	public function countPendingFromTeamNotFromPlayer($playerId) {
		$player = $this->Player->findById($playerId);
		if (!$player || !$player['Player']['team_id']) {
			return 0;
		}
		
		return $this->find('count', array(
			'conditions' => array(
				'Player.team_id' => $player['Player']['team_id'], 
				'Log.player_id <>' => $playerId,
				'Log.reviewed IS NULL'
			)
		));
	}

	public function allPendingFromTeamNotFromPlayer($playerId, $limit = 50) {
		$player = $this->Player->findById($playerId);
		if (!$player || !$player['Player']['team_id']) {
			return array();
		}
		return $this->find('all', array(
			'conditions' => array(
				'Player.team_id' => $player['Player']['team_id'], 
				'Log.player_id <>' => $playerId,
				'Log.reviewed IS NULL'
			),
			'contain' => array(
				'Domain',
				'Player',
				'PairedPlayer',
				'Activity',
				'Tags',
				'LogVote' => array(
					'Player'
				)
			),
			'limit' => $limit,
			'order' => array('Log.created' => 'ASC')
		));
	}


	public function count($playerIdOwner) {
		return $this->find('count', array(
			'conditions' => array(
				'Log.player_id_owner' => $playerIdOwner
			))
		);
	}

	public function report($logs) {
		$this->begin();
		try {
			// Save each log
			// Update corresponding activity
			foreach ($logs as $log) {
				$activity = $this->Activity->findById($log['Log']['activity_id']);
				if (!$activity) {
					throw new Exception('Activity not found.');
				}
				$now = date('Y-m-d H:i:s');
				$firstReport = $activity['Activity']['first_report'] ? $activity['Activity']['first_report'] : $now; 
				$lastReport = $now;
				$timesReported = (int)$activity['Activity']['times_reported'] + 1;
				$created = new DateTime(substr($activity['Activity']['created'], 0, 10) . ' 00:00:00');
				$today = new DateTime(date('Y-m-d') . ' 00:00:00');
				$days = 1 + (int)(($today->format('U') - $created->format('U')) / 86400);
				$reportsPerDay = $days > 0 ? $timesReported / $days : 0; 

				$activityUpdate = array(
					'id' => $activity['Activity']['id'],
					'first_report' => $firstReport,
					'last_report' => $lastReport,
					'reports_per_day' => $reportsPerDay,
					'times_reported' => $timesReported
				);
				$activitySaved = $this->Activity->save($activityUpdate);
				$logSaved = $this->saveAssociated($log);
				if (!$activitySaved) {
					throw new Exception('Activity could not be saved.');
				}
				if (!$logSaved) {
					throw new Exception('Log could not be saved.');
				}
			}
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			throw $ex;
		}
	}
}