<?php

App::uses('AppModel', 'Model');

class ActivityRequisite extends AppModel {
	
	public $useTable = 'activity_requisite';
	public $belongsTo = array('Activity', 'Badge');
}