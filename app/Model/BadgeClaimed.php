<?php

App::uses('AppModel', 'Model');

class BadgeClaimed extends AppModel {
	
	public $useTable = 'badge_claimed';
	public $belongsTo = array('Badge', 'Player');

	public function allFromPlayerByBadgeId($playerId) {
		return $this->all(array('BadgeClaimed.player_id' => $playerId), 'badge_id');
	}


}