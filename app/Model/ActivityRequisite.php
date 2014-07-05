<?php

App::uses('AppModel', 'Model');

class ActivityRequisite extends AppModel {
	
	public $useTable = 'activity_requisite';
	public $belongsTo = array('Activity', 'Badge');
	public $hasAndBelongsToMany = array(
		'Tags' => array(
			'className' => 'Tag',
			'dependent' => true 
		)
	);

	public $uses = array('ActivityRequisiteSummary');

	public function _updateActivityRequisiteSummary($log) {
		$playerId = $log['Log']['player_id'];
		$activityRequisiteIds = $this->_getLogMatches($log);

		foreach ($activityRequisiteIds as $activityRequisiteId) {
			$exists = $this->ActivityRequisiteSummary->findByActivityRequisiteIdAndPlayerId($activityRequisiteId, $playerId);
			if (!$exists) {
				$activityRequisite = $this->findById($activityRequisiteId);
				$badgeId = $activityRequisite['ActivityRequisite']['badge_id'];

				$summary = array(
					'activity_requisite_id' => $activityRequisiteId,
					'badge_id' => $badgeId,
					'player_id' => $log['Log']['player_id'],
					'times' => 1
				);
				$this->ActivityRequisiteSummary->create();
				$this->ActivityRequisiteSummary->save($summary);
			} else {
				$this->query(
					'UPDATE activity_requisite_summary SET times = times + 1 ' . 
					'WHERE activity_requisite_id = ? AND player_id = ?', 
					array($activityRequisiteId, $playerId)
				);
			}
		}
	}

	public function _getLogMatches($log) {
		$activityId = $log['Log']['activity_id'];
		$logTags = $log['Tags'];
		$logTagIds = array();

		foreach ($logTags as $tag) {
			$logTagIds[] = (int)$tag['id'];
		}
		sort($logTagIds);

		$requisites = $this->find('all', array(
			'conditions' => array('ActivityRequisite.activity_id' => $activityId),
			'contain' => array(
				'Tags'
			)
		));
		$matches = array();
		foreach ($requisites as $requisite) {
			$requisiteTagIds = array();
			foreach ($requisite['Tags'] as $requisiteTag) {
				$requisiteTagIds[] = (int)$requisiteTag['id'];
			}
			sort($requisiteTagIds);

			// Verifica se $requisiteTagIds possui todos os itens de $logTagIds			
			if (count(array_intersect($requisiteTagIds, $logTagIds)) == count($requisiteTagIds)) {
				$matches[] = (int)$requisite['ActivityRequisite']['id'];
			}
		}
		return $matches;
	}
}