<?php

App::uses('AppModel', 'Model');

class BadgeLog extends AppModel {
	
	public $useTable = 'badge_log';
	public $belongsTo = array('Player', 'Badge');

    public function playerCount($playerId) {
        return $this->find('count', array('conditions' => array('player_id' => $playerId)));
    }

    public function allFromPlayerByBadgeId($playerId) {
    	return $this->all(array(
    		'BadgeLog.player_id' => $playerId,
		), 'badge_id');
    }
}