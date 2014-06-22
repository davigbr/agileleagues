<?php

App::uses('AppModel', 'Model');

class PlayerActivitySummary extends AppModel {
	
	public $useTable = 'player_activity_summary';
	public $belongsTo = array('Domain');

	public function allFromPlayer($playerId, $domainId = null){ 
		$conditions = array(
			'PlayerActivitySummary.player_id' => $playerId,
		);
		if ($domainId !== null) {
			$conditions['PlayerActivitySummary.domain_id'] = $domainId;
		}

		return $this->find('all', array(
			'conditions' => $conditions,
			'order' => array('PlayerActivitySummary.count' => 'DESC')
		));
	}
	public function countFromPlayer($playerId){ 
		return $this->find('count', array(
			'conditions' => array(
				'PlayerActivitySummary.player_id' => $playerId,
			),
			'order' => array('PlayerActivitySummary.count' => 'DESC')
		));
	}

}