<?php

App::uses('AppModel', 'Model');

class EventTaskLog extends AppModel {
	
	public $useTable = 'event_task_log';

	public $belongsTo = array('EventTask', 'Player', 'Event');

	public $uses = array('XpLog', 'EventCompleteLog');

	public $validate = array(
		'player_id' => 'notEmpty',
		'event_id' => array(
			'notEmpty',
			'eventAlreadyCompletedRule' => array(
				'rule' => 'eventAlreadyCompletedRule',
				'message' => 'It seems you have already completed this event.'
			),
			'joinBeforeReportingRule' => array(
				'rule' => 'joinBeforeReportingRule',
				'message' => 'You need to join this event before reporting any task.'
			)
		),
		'event_task_id' => array(
			'notEmpty',
			'uniqueTaskPerPlayerRule' => array(
				'rule' => 'uniqueTaskPerPlayerRule',
				'message' => 'You have already reported this event task.'
			)
		)
	);

	public function eventAlreadyCompletedRule() {
		$found = $this->EventCompleteLog->findByPlayerIdAndEventId(
			$this->data['EventTaskLog']['player_id'], 
			$this->data['EventTaskLog']['event_id']
		);
		return !$found;
	}

	public function joinBeforeReportingRule() {
		$found = $this->EventTask->Event->EventJoinLog->findByPlayerIdAndEventId(
			$this->data['EventTaskLog']['player_id'], 
			$this->data['EventTaskLog']['event_id']
		);
		return !empty($found);
	}

	public function uniqueTaskPerPlayerRule() {
		$found = $this->findByEventTaskIdAndPlayerId(
			$this->data['EventTaskLog']['event_task_id'], 
			$this->data['EventTaskLog']['player_id']
		);
		return !$found;
	}

	public function countPendingFromPlayer($playerId) {
		return $this->find('count', array(
			'conditions' => array('EventTaskLog.player_id' => $playerId, 'EventTaskLog.reviewed IS NULL')
		));
	}

	
	public function allNotReviewed() {
		return $this->find('all',
			array(
				'conditions' => array('EventTaskLog.reviewed IS NULL'),
				'recursive' => 2
			)
		);
	}

	public function countNotReviewed() {
		return $this->find('count', array('conditions' => array('EventTaskLog.reviewed IS NULL'), 'recursive' => -1));
	}
	
	public function allPendingFromPlayer($playerId) {
		return $this->find('all', array(
			'conditions' => array('EventTaskLog.player_id' => $playerId, 'EventTaskLog.reviewed IS NULL')
		));
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
				throw new Exception('Log not found');
			}
			if ($log['EventTaskLog']['reviewed'] !== null) {
				throw new Exception('Log already reviewed');
			}
			$eventTaskId = $log['EventTaskLog']['event_task_id'];
			$playerId = $log['EventTaskLog']['player_id'];
			
			$smId = $this->Player->scrumMasterId($playerId);
			
			$this->query('UPDATE event_task_log SET reviewed = NOW() WHERE id = ?', array($this->id));

			// Gera experiência para o jogador
			$this->XpLog->_eventTaskReported($playerId, $eventTaskId);

			// Gera experiência para o ScrumMaster que revisou a atividade
			$this->XpLog->_eventTaskReviewed($smId, $eventTaskId);

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			throw $ex;
		}
	}


}