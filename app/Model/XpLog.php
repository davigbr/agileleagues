<?php

App::uses('AppModel', 'Model');

class XpLog extends AppModel {
	
	public $useTable = 'xp_log';

	public $belongsTo = array(
		'Player'
	);
	
	public $validate = array(
		'player_id' => 'notEmpty',
		'xp' => 'notEmpty'
	);

	public $uses = array('Notification', 'Log', 'EventTask', 'Event');

	public function _eventCompleted($playerId, $eventId) {
		$playerBefore = $this->Player->findById($playerId);

		$event = $this->Event->findById($eventId);
		if (!$event) {
			throw new ModelException('Event not found.');
		}
		$xp = $event['Event']['xp'];

		$this->_add(array(
			'event_id_completed' => $eventId,
			'player_id' => $playerId,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));
		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _eventJoined($playerId, $eventId) {
		$playerBefore = $this->Player->findById($playerId);		
		$xp = EVENT_JOIN_XP;
		$this->_add(array(
			'event_id_joined' => $eventId,
			'player_id' => $playerId,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));
		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _activityReviewed($action, $playerIdReviewer, $logId) {
		$log = $this->Log->findById($logId);
		if (!$log) {
			throw new Exception('Log not found');
		}
		
		if ($action === 'accept') {
			$xp = floor($log['Log']['xp'] * ACCEPTANCE_XP_MULTIPLIER);
		} else if ($action === 'reject') {
			$xp = REJECTION_XP_BONUS;
		}
		
		// Minimum of 1XP point
		if ($xp == 0) $xp = 1;

		$this->_add(array(
			'log_id_reviewed' => $logId,
			'player_id' => $playerIdReviewer,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));

		if ($action === 'accept') {
			$this->Notification->_success(
				$playerIdReviewer,
				__('Activity Reviewed - Accepted'),
				__('The activity you reviewed was accepted and you earned %s XP.', $xp)
			);
		} else {
			$this->Notification->_success(
				$playerIdReviewer,
				__('Activity Reviewed - Rejected'),
				__('The activity you reviewed was rejected and you earned %s XP.', $xp)
			);
		}
	}

	public function _activityReported($playerId, $logId) {
		$log = $this->Log->findById($logId);
		if (!$log) {
			throw new Exception('Log not found');
		}

		$playerBefore = $this->Player->findById($playerId);
		if (!$playerBefore) {
			throw new Exception('Player not found');
		}

		$xp = $log['Log']['xp'];

		$this->_add(array(
			'log_id' => $logId,
			'player_id' => $playerId,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));

		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _eventTaskReviewed($smId, $eventTaskId) {
		$players = $this->Player->playersCount();

		if ($players > 0) {
			$eventTask = $this->EventTask->findById($eventTaskId);
			if (!$eventTask) {
				throw new Exception('EventTask not found');
			}

			$smXp = floor($eventTask['EventTask']['xp'] / $players);
			// Ganha no mínimo 1 ponto de experiência
			if ($smXp == 0) $smXp = 1;

			$this->_add(array(
				'event_task_id_reviewed' => $eventTaskId,
				'player_id' => $smId,
				'created' => date('Y-m-d H:i:s'),
				'xp' => $smXp
			));

			$this->Notification->_success(
				$smId,
				__('Event Task Reviewed'),
				__('You reviewed an event task and earned %s XP.', $smXp)
			);
		}
	}

	public function _eventTaskReported($playerId, $eventTaskId) {
		$eventTask = $this->EventTask->findById($eventTaskId);
		if (!$eventTask) {
			throw new Exception('EventTask not found');
		}

		$playerBefore = $this->Player->findById($playerId);
		if (!$playerBefore) {
			throw new Exception('Player not found');
		}

		$xp = $eventTask['EventTask']['xp'];
		$this->_add(array(
			'event_task_id' => $eventTaskId,
			'player_id' => $playerId,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));

		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _levelUpNotification($playerBefore, $xpGained) {
		$levelBefore = (int)$playerBefore['Player']['level'];
		$playerName = h($playerBefore['Player']['name']);
		$playerId = $playerBefore['Player']['id'];

		$levelAfter = $this->Player->level($playerBefore['Player']['xp'] + $xpGained);

		if ($levelAfter > $levelBefore) {
			switch ($levelAfter) {
				case EVENT_LEVEL_REQUIRED_MISSION: {
					$this->Notification->_broadCast(
						$playerId,
						__('Level Up - Missions Unlocked'),
						__('%s reached level 10 and can now join Missions!', $playerName),
						'warning');
					break;
				}
				case EVENT_LEVEL_REQUIRED_CHALLENGE: {
					$this->Notification->_broadCast(
						$playerId,
						__('Level Up - Challenges Unlocked'),
						__('%s reached level 10 and can now join Challenges!', $playerName),
						'warning');
					break;
				}
				default: {
					$this->Notification->_broadCast(
						$playerId,
						__('Level Up'),
						__('%s reached level %s!', $playerName, $levelAfter),
						'warning');
					break;
				}
			}
		}
	}

	public function afterSave($created, $options = array()) {
		$playerId = $this->data['XpLog']['player_id'];
		$xp = $this->data['XpLog']['xp'];
		$this->Player->query('UPDATE player SET xp = xp + ? WHERE id = ?', array($xp, $playerId));
	}
}