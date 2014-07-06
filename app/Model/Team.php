<?php

App::uses('AppModel', 'Model');

class Team extends AppModel {
	
	public $useTable = 'team';

	public $order = array('Team.name' => 'ASC');

	public $hasMany = array(
		'Players' => array(
			'className' => 'Player',
			'conditions' => array(
				'Players.player_type_id' => PLAYER_TYPE_PLAYER
			)
		)
	);

	public $validate = array(
		'name' => 'notEmpty'
	);

	public $belongsTo = array(
		'GameMaster' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_owner'
		)
	);

	public function simpleFromGameMaster($gameMasterId) {
		return $this->simple(array('Team.player_id_owner'=> $gameMasterId));
	}

	public function allFromOwner($playerIdOwner) {
		return $this->find('all', array(
			'conditions' => array(
				'Team.player_id_owner' => $playerIdOwner
			),
			'contain' => array(
				'GameMaster' => array('id', 'name'), 
				'Players' => array('id', 'name')
			)
		));
	}

}