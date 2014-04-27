<?php

App::uses('AppModel', 'Model');

class EventCompleteLog extends AppModel {
	
	public $useTable = 'event_complete_log';

	public $belongsTo = array('Player', 'Event');

	public $validate = array(
		'event_id' => 'notEmpty',
		'player_id' => 'notEmpty'
	);

	public function _log($playerId, $eventId) {
		$saved = $this->save(array(
			'event_id' => $eventId,
			'player_id' => $playerId
		));
		if (!$saved) {
			throw new InternalErrorException('Could not save EventCompleteLog');
		}
	}
}