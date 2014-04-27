<?php

App::uses('AppModel', 'Model');

class Domain extends AppModel {
	
	public $useTable = 'domain';

	public $order = array('Domain.id' => 'ASC');

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