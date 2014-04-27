<?php

App::uses('AppModel', 'Model');

class EventActivity extends AppModel {
	
	public $useTable = 'event_activity';

	public $belongsTo = array('Activity', 'Event');
}