<?php

App::uses('AppModel', 'Model');

class EventTask extends AppModel {
	
	public $useTable = 'event_task';
	public $belongsTo = array('Event');

}