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
				'message' => 'Activities performed more than one day ago cannot be reported. '
			),
			'acquiredFutureRule' => array(
				'rule' => 'acquiredFutureRule',
				'message' => 'Activities should be executed before reported. '
			)
		),
		'description' => array('notEmpty'),
	);

	public $belongsTo = array(
		'Activity', 'Player', 'Domain', 'Event'
	);

	public $uses = array('XpLog', 'Notification');

	public function beforeInsert($options = array()) {
		$activity = $this->Activity->findById($this->data['Log']['activity_id']);
		$this->data['Log']['domain_id'] = $activity['Activity']['domain_id'];
		$this->data['Log']['xp'] = $activity['Activity']['xp'];
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
			$now->modify('-1 day');
			if ($acquired < $now) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Update an activity as reviewed
	 */	
	public function review($id = null) {
		$this->begin();
		try {
			if ($id) {
				$this->id = $id;
			}
			$log = $this->findById($this->id);
			if (!$log) {
				throw new ModelException('Log not found');
			}
			
			$activityId = $log['Log']['activity_id'];
			$activityName = $this->Activity->field('name', array('Activity.id' => $activityId));
			$playerId = $log['Log']['player_id'];
			$playerName = $this->Player->field('name', array('Player.id' => $playerId));

			// Verifica se esta atividade já foi logada (e revisada)
			$logged = $this->find('count', array(
				'conditions'=> array(
					'Log.activity_id' => $activityId,
					'Log.reviewed IS NOT NULL'
				)
			));

			$this->query('UPDATE log SET reviewed = NOW() WHERE id = ?', array($this->id));
			$this->query('UPDATE activity SET reported = reported + 1 WHERE id = ?', array($activityId));

			// Gera experiência para o jogador
			$this->XpLog->_activityReported($playerId, $activityId);

			// Gera experiência para o ScrumMaster que revisou a atividade
			$this->XpLog->_activityReviewed($activityId);

			// Se foi a primeira vez que esta atividade foi logada, gera uma notificação
			if (!$logged) {
				$this->Notification->_broadcast(
					'First Time Completion', 
					__('The %s activity was completed for the first time in this game. Congratulations, %s!', $activityName, $playerName)
				);
			}

			$this->commit();
		} catch (ModelException $ex) {
			$this->rollback();
			throw $ex;
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
			'order' => array('Log.creation' => 'DESC'),
			'limit' => $limit
		));
	}

	public function playerCount($playerId) {
		return $this->find('count', array(
			'conditions' => array(
				'Log.reviewed IS NOT NULL', 
				'Log.player_id' => $playerId)));
	}

	public function average() {
		$result = $this->query('SELECT AVG(count) AS average FROM (SELECT COUNT(*) AS count FROM log WHERE reviewed IS NOT NULL GROUP BY player_id) A');
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
			'order' => array('Log.creation' => 'DESC')
		));
	}

	public function countPendingFromPlayer($playerId) {
		return $this->find('count', array(
			'conditions' => array('Log.player_id' => $playerId, 'Log.reviewed IS NULL'),
			'order' => array('Log.creation' => 'DESC')
		));
	}

	public function lastFromEachPlayer($limit = 10) {
		$players = $this->Player->find('list');
		$logs = array();
		foreach ($players as $id => $desc) {
			$logs[$id] = $this->find('all', array(
				'limit' => $limit,
				'contain' => array(
					'Activity', 'Domain'
				),
				'conditions' => array(
					'Log.reviewed IS NOT NULL',
					'Log.player_id' => $id
				),
				'order' => array('Log.acquired' => 'DESC')
			));
		}
		return $logs;
	}

}