<?php

App::uses('AppModel', 'Model');

class Timeline extends AppModel {
	
	public $useTable = 'timeline';
	public $order = 'Timeline.when DESC';

	public $belongsTo = array(
		'Domain', 'Activity', 'Badge', 'Player'
	);

	public function last($howMany) {
		return $this->find('all', array(
			'limit' => $howMany,
			'order' => array('Timeline.when' => 'DESC')
		));
	}
}