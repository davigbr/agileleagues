<?php

App::uses('AppModel', 'Model');

class PlayerActivityCoins extends AppModel {
	
	public $useTable = 'player_activity_coins';
	public $belongsTo = array('Domain');

	public function allFromPlayer($playerId, $domainId = null){ 
		$conditions = array(
			'PlayerActivityCoins.player_id' => $playerId,
		);
		if ($domainId !== null) {
			$conditions['PlayerActivityCoins.domain_id'] = $domainId;
		}

		return $this->find('all', array(
			'conditions' => $conditions,
			'order' => array('PlayerActivityCoins.coins' => 'DESC')
		));
	}
	public function countFromPlayer($playerId){ 
		return $this->find('count', array(
			'conditions' => array(
				'PlayerActivityCoins.player_id' => $playerId,
			),
			'order' => array('PlayerActivityCoins.coins' => 'DESC')
		));
	}

}