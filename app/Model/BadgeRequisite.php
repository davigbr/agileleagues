<?php

App::uses('AppModel', 'Model');

class BadgeRequisite extends AppModel {
	
	public $useTable = 'badge_requisite';
	public $validates = array();
	public $belongsTo = array(
		'BadgeRequisite' => array(
			'className' => 'Badge',
			'foreignKey' => 'badge_id_requisite'
		),
		'Badge' => array(
			'className' => 'Badge',
			'foreignKey' => 'badge_id'
		)	
	);
}