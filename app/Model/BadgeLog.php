<?php

App::uses('AppModel', 'Model');

class BadgeLog extends AppModel {
	
	public $useTable = 'badge_log';
	public $belongsTo = array(
        'Player', 
        'Badge'
    );
    public $order = array('Badge.domain_id' => 'ASC', 'Badge.id' => 'ASC');

    public function beforeInsert($options = array()) {
        $badge = $this->Badge->findById($this->data['BadgeLog']['badge_id']);
        $this->data['BadgeLog']['domain_id'] = $badge['Badge']['domain_id'];
        $this->data['BadgeLog']['creation'] = date('Y-m-d H:i:s');
        return true;
    }

    public function playerCount($playerId) {
        return $this->find('count', array('conditions' => array('player_id' => $playerId)));
    }

    public function allFromPlayerByBadgeId($playerId) {
    	return $this->all(array(
    		'BadgeLog.player_id' => $playerId,
		), 'badge_id');
    }
}