<?php

App::uses('AppModel', 'Model');

class BadgeActivityProgress extends AppModel {
	
	public $useTable = 'badge_activity_progress';
	
	public $belongsTo = array('Activity');

	public $virtualFields = array(
		'progress' => 'IF(coins_obtained > coins_required, 100, FLOOR(coins_obtained/coins_required*100))'
	);

	public function allFromPlayerByBadgeIdAndActivityId($playerId) {
		$all = $this->all(array(
			'player_id' => $playerId
		));
		$new = array();
		foreach ($all as $row) {
			$badgeId = $row['BadgeActivityProgress']['badge_id'];
			$activityId = $row['BadgeActivityProgress']['activity_id'];
			$new[$badgeId][$activityId] = $row;
		}
		return $new;
	}

	public function allFromBadgeAndPlayer($badgeId, $playerId) {
		return $this->all(array(
			'BadgeActivityProgress.badge_id' => $badgeId,
			'BadgeActivityProgress.player_id' => $playerId
		));
	}

	
}