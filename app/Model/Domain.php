<?php

App::uses('AppModel', 'Model');

class Domain extends AppModel {
	
	public $useTable = 'domain';

	public $order = array('Domain.player_type_id' => 'ASC', 'Domain.name' => 'ASC');

	public $validate = array(
		'abbr' => 'notEmpty',
		'color' => array(
			'notEmpty',
			'color' => array(
				'rule' => array('custom', '/\\#[A-Fa-f0-9]{6}/'),
				'message' => 'Invalid color'
			)
		),
		'player_type_id' => 'notEmpty',
		'icon' => 'notEmpty',
		'name' => 'notEmpty',
		'description' => 'notEmpty'
	);

	public $hasMany = array(
		'Badge',
		'Activity' => array(
			'conditions' => array('Activity.inactive' => false),
			'order' => 'Activity.name'
		)
	);

	public function activitiesCount() {
		$data = $this->query('SELECT * FROM domain_activities_count AS Domain');
		$list = array();
		// Return a list where the key is the domain_id and the value is the activities count
		foreach ($data as $row) {
			$list[$row['Domain']['domain_id']] = $row['Domain']['count'];
		}
		return $list;
	}
}