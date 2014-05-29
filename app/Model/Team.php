<?php

App::uses('AppModel', 'Model');

class Team extends AppModel {
	
	public $useTable = 'team';

	public $order = array('Team.name' => 'ASC');

	public $hasMany = array(
		'Developers' => array(
			'className' => 'Player',
			'conditions' => array(
				'Developers.player_type_id' => PLAYER_TYPE_DEVELOPER
			)
		),
		'ProductOwners' => array(
			'className' => 'Player',
			'conditions' => array(
				'ProductOwners.player_type_id' => PLAYER_TYPE_PRODUCT_OWNER
			)
		)
	);

	public $validate = array(
		'name' => 'notEmpty'
	);

	public $belongsTo = array(
		'ScrumMaster' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_scrummaster'
		)
	);

	public function simpleFromScrumMaster($scrumMasterId) {
		return $this->simple(array('Team.player_id_scrummaster'=> $scrumMasterId));
	}

}