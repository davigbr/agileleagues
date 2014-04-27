<?php

App::uses('AppModel', 'Model');

class EventActivityProgress extends AppModel {
	
	public $useTable = 'event_activity_progress';

	public $belongsTo = array('Activity', 'Event', 'Player');

}