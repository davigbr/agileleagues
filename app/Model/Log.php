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
		'description' => array('notEmpty'),
	);

	public $belongsTo = array(
		'Activity', 'Player', 'Domain', 'Event',
		'PairedPlayer' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_pair'
		)
	);

	public $hasMany = array('LogVote');
	public $hasAndBelongsToMany = array('Tag');
	
	public $uses = array('XpLog', 'Notification');

	public function beforeInsert($options = array()) {
		$activity = $this->Activity->findById($this->data['Log']['activity_id']);
		$this->data['Log']['domain_id'] = $activity['Activity']['domain_id'];
		$xp = $activity['Activity']['xp'];
		if (isset($this->data['Log']['player_id_pair'])) {
			$xp *= PAIR_XP_MULTIPLIER;
		}
		$this->data['Log']['xp'] = (int)$xp;
		return true;
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
				'Player'
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
				'LogVote' => array(
					'conditions' => array('LogVote.player_id' => $playerId)
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

}