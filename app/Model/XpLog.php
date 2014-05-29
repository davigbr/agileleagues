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

	public $uses = array('Notification', 'Activity', 'EventTask', 'Event');

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
			'xp' => $xp
		));
		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _activityReviewed($activityId) {
		$developers = $this->Player->developersCount();

		if ($developers > 0) {
			$activity = $this->Activity->findById($activityId);
			if (!$activity) {
				throw new Exception('Activity not found');
			}

			$sm = $this->Player->_scrumMaster();
			$smId = $sm['Player']['id'];

			$smXp = floor($activity['Activity']['xp'] / $developers);
			// Ganha no mínimo 1 ponto de experiência
			if ($smXp == 0) $smXp = 1;

			$this->_add(array(
				'activity_id_reviewed' => $activityId,
				'player_id' => $smId,
				'xp' => $smXp
			));

			$this->Notification->_success(
				$smId,
				__('Activity Reviewed'),
				__('You reviewed an activity and earned %s XP.', $smXp)
			);
		}
	}

	public function _activityReported($playerId, $activityId) {
		$activity = $this->Activity->findById($activityId);
		if (!$activity) {
			throw new Exception('Activity not found');
		}

		$playerBefore = $this->Player->findById($playerId);
		if (!$playerBefore) {
			throw new Exception('Player not found');
		}

		$xp = $activity['Activity']['xp'];
		$this->_add(array(
			'activity_id' => $activityId,
			'player_id' => $playerId,
			'xp' => $xp
		));

		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _eventTaskReviewed($eventTaskId) {
		$developers = $this->Player->developersCount();

		if ($developers > 0) {
			$eventTask = $this->EventTask->findById($eventTaskId);
			if (!$eventTask) {
				throw new Exception('EventTask not found');
			}
			
			$sm = $this->Player->_scrumMaster();
			$smId = $sm['Player']['id'];

			$smXp = floor($eventTask['EventTask']['xp'] / $developers);
			// Ganha no mínimo 1 ponto de experiência
			if ($smXp == 0) $smXp = 1;

			$this->_add(array(
				'event_task_id_reviewed' => $eventTaskId,
				'player_id' => $smId,
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