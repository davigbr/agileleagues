<?php

App::uses('AppModel', 'Model');

class CalendarLog extends AppModel {
	
	public $useTable = 'calendar_log';

	public $belongsTo = array('Activity', 'Domain', 'Player');


}