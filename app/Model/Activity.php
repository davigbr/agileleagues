<?php

App::uses('AppModel', 'Model');

class Activity extends AppModel {
	
	public $useTable = 'activity';
	public $order = array('Activity.inactive' => 'ASC', 'Activity.domain_id' => 'ASC', 'Activity.name' => 'ASC');
	public $displayField = 'name_xp';
	public $virtualFields = array();
	public $hasOne = array('LastWeekLog');
	public $belongsTo = array('Domain');

	public $validate = array(
		'name' => 'notEmpty',
		'description' => 'notEmpty',
		'xp' => 'notEmpty',
		'domain_id' => 'notEmpty',
		'acceptance_votes' => array(
			'notEmpty',
			'comparison' => array(
				'rule' => array('comparison', 'is greater', 0),
				'message' => 'Should be greater than 0.'
			)
		),
		'rejection_votes' => array(
			'notEmpty',
			'comparison' => array(
				'rule' => array('comparison', 'is greater', 0),
				'message' => 'Should be greater than 0.'
			)
		)
	);


    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $alias = $this->alias;
        $this->virtualFields['name_xp'] = "IF({$alias}.inactive = 1, CONCAT('(inactive) ', {$alias}.name, ' (', {$alias}.xp, ' XP)'), CONCAT({$alias}.name, ' (', {$alias}.xp, ' XP)'))";
	}

	public function allFromOwnerById($playerIdOwner) {
		return $this->all(array(
			'Activity.player_id_owner' => $playerIdOwner
		), 'id');
	}

	public function allActive($playerIdOwner) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.player_id_owner' => $playerIdOwner
			)
		));
	}

	public function count($playerIdOwner) {
		return $this->find('count', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.player_id_owner' => $playerIdOwner
			))
		);
	}

	public function simpleActive($playerIdOwner) {
		return $this->simple(array(
			'Activity.inactive' => 0,
			'Activity.player_id_owner' => $playerIdOwner
		));
	}

	public function simpleActiveFromPlayerType($playerIdOwner, $playerTypeId) {
		return $this->find('list', array(
			'conditions' => array(
				'Activity.player_id_owner' => $playerIdOwner,
				'Activity.inactive' => 0, 
				'Domain.player_type_id' => $playerTypeId
			),
			'recursive' => 0
		));
	}

	public function simpleFromDomain($domainId) {
		return $this->simple(array('Activity.domain_id' => $domainId));
	}
	
	public function leaderboardsLastWeek($playerIdOwner) {
		return $this->query('
			SELECT * FROM activity_leaderboards_last_week AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
			WHERE player_id_owner = ? ORDER BY count DESC
		', array($playerIdOwner));
	}
	
	public function leaderboardsLastMonth($playerIdOwner) {
		return $this->query('
			SELECT * FROM activity_leaderboards_last_month AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
			WHERE player_id_owner = ? ORDER BY count DESC
		', array($playerIdOwner));
	}
	
	public function leaderboardsEver($playerIdOwner) {
		return $this->query('
			SELECT * FROM activity_leaderboards AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
			WHERE player_id_owner = ? ORDER BY count DESC
		', array($playerIdOwner));
	}
	
	public function leaderboardsThisWeek($playerIdOwner) {
		return $this->query('
			SELECT * FROM activity_leaderboards_this_week AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
			WHERE player_id_owner = ? ORDER BY count DESC
		', array($playerIdOwner));
	}

	public function leaderboardsThisMonth($playerIdOwner) {
		return $this->query('
			SELECT * FROM activity_leaderboards_this_month AS Leaderboards
			INNER JOIN player AS Player ON Player.id = Leaderboards.player_id
			WHERE player_id_owner = ? ORDER BY count DESC
		', array($playerIdOwner));
	}

	public function leastReported($gameMasterId, $limit = 20) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.reported <>' => 0,
				'Activity.player_id_owner' => $gameMasterId
			),
			'limit' => $limit,
			'order' => 'Activity.reported ASC'
		));
	}

	public function mostReported($gameMasterId, $limit = 20) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.reported <>' => 0,
				'Activity.player_id_owner' => $gameMasterId
			),
			'limit' => $limit,
			'order' => 'Activity.reported DESC'
		));
	}

	public function neverReported($gameMasterId, $limit = 20) {
		return $this->find('all', array(
			'conditions' => array(
				'Activity.inactive' => 0,
				'Activity.reported' => 0,
				'Activity.player_id_owner' => $gameMasterId
			),
			'limit' => $limit
		));
	}
}