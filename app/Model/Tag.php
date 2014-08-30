<?php

App::uses('AppModel', 'Model');

class Tag extends AppModel {
	
	public $useTable = 'tag';
	public $order = array('Tag.name' => 'ASC');

	public $validate = array(
		'name' => 'notEmpty',
		'color' => array(
			'notEmpty',
			'color' => array(
				'rule' => array('custom', '/\\#[A-Fa-f0-9]{6}/'),
				'message' => 'Invalid color'
			)
		),
		'bonus_type' => 'notEmpty',
		'bonus_value' => 'notEmpty'
	);

	public function allActive($playerIdOwner) {
		return $this->find('all', array(
			'conditions' => array(
				'Tag.inactive' => 0,
				'Tag.player_id_owner' => $playerIdOwner
			)
		));
	}
	
	public function simpleActive($playerIdOwner) {
		return $this->find('list', array(
			'conditions' => array(
				'Tag.inactive' => 0,
				'Tag.player_id_owner' => $playerIdOwner
			)
		));
	}


}