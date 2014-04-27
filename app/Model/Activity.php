<?php

App::uses('AppModel', 'Model');

class Activity extends AppModel {
	
	public $useTable = 'activity';
	public $order = array('Activity.domain_id' => 'ASC', 'Activity.name' => 'ASC');
	public $displayField = 'name';

	public $validate = array(
		'name' => 'notEmpty',
		'description' => 'notEmpty',
		'xp' => 'notEmpty'
	);

	public $belongsTo = array(
		'Domain'
	);

	public $hasOne = array('LastWeekLog');

	public function count() {
		return $this->find('count', array('conditions' => array('inactive' => 0)));
	}

	public function simpleActive() {
		return $this->simple(array('Activity.inactive' => 0));
	}

	public function simpleFromDomain($domainId) {
		return $this->simple(array('Activity.domain_id' => $domainId));
	}
	
	public function leaderboardsLastWeek() {
		return $this->query('
			SELECT * FROM activity_leaderboards_last_week AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
		');
	}
	
	public function leaderboardsLastMonth() {
		return $this->query('
			SELECT * FROM activity_leaderboards_last_month AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
		');
	}
	
	public function leaderboardsEver() {
		return $this->query('
			SELECT * FROM activity_leaderboards AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
		');
	}
	
	public function leaderboardsThisWeek() {
		return $this->query('
			SELECT * FROM activity_leaderboards_this_week AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
		');
	}

	public function leaderboardsThisMonth() {
		return $this->query('
			SELECT * FROM activity_leaderboards_this_month AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
		');
	}

	public function leastReported($limit = 20) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.reported <>' => 0
			),
			'limit' => $limit,
			'order' => 'Activity.reported ASC'
		));
	}

	public function mostReported($limit = 20) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.reported <>' => 0
			),
			'limit' => $limit,
			'order' => 'Activity.reported DESC'
		));
	}

	public function neverReported($limit = 20) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.reported' => 0
			),
			'limit' => $limit
		));
	}
}