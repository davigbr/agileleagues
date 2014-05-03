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
		)
	);

	public $validate = array(
		'name' => 'notEmpty'
	);

	public $belongsTo = array(
		'ScrumMaster' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_scrummaster'
		),
		'ProductOwner' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_product_owner'
		)
	);

}