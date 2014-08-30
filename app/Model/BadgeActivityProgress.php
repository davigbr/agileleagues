<?php

App::uses('AppModel', 'Model');

class BadgeActivityProgress extends AppModel {
	
	public $useTable = 'badge_activity_progress';
	
	public $belongsTo = array('Activity', 'ActivityRequisite');

	public $virtualFields = array(
		'progress' => 'IF(activities_completed >= activities_required, 100, FLOOR(activities_completed/activities_required*100))'
	);

	public function allFromPlayerByBadgeId($playerId) {
		$all = $this->all(array(
			'player_id' => $playerId
		));
		$new = array();
		foreach ($all as $row) {
			$badgeId = $row['BadgeActivityProgress']['badge_id'];
			$new[$badgeId][] = $row;
		}
		return $new;
	}

	public function allFromBadgeAndPlayer($badgeId, $playerId) {
		return $this->find('all', array(
			'contain' => array(
				'Activity',
				'ActivityRequisite' => array(
					'Tags'
				)
			),
			'conditions' => array(
				'BadgeActivityProgress.badge_id' => $badgeId,
				'BadgeActivityProgress.player_id' => $playerId
			)
		));

	}

	
}