<?php

App::uses('AppModel', 'Model');

class EventJoinLog extends AppModel {
	
	public $useTable = 'event_join_log';
	public $belongsTo = array('Player', 'Event');
	public $uses = array('Notification', 'XpLog');

	public function join($playerId, $eventId) {
		$this->begin();
		try {
			$this->_add(array(
				'event_id' => $eventId,
				'player_id' => $playerId
			));

			$player = $this->Player->findById($playerId);
			$event = $this->Event->findById($eventId);

			$this->Notification->_broadcast(
				$playerId,
				'Event Joined',
				__('%s joined the %s %s.', $player['Player']['name'], $event['Event']['name'], $event['EventType']['name'])
			);

			$xpLogSaved = $this->XpLog->_eventJoined($playerId, $eventId);
			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			throw $ex;
		}
	}

}