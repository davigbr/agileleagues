<?php

App::uses('AppModel', 'Model');

class Badge extends AppModel {
	
	public $useTable = 'badge';

	public $belongsTo = 'Domain';

	public $hasMany = array(
		'BadgeRequisite',
		'ActivityRequisite',
		'BadgeLog'
	);

	public $uses = array(
		'BadgeActivityProgress', 
		'BadgeClaimed', 
		'BadgeLog', 
		'Player', 
		'Notification'
	);

	public function allFromDomainById($domainId) {
		return $this->all(array('Badge.domain_id' => $domainId), 'id');
	}

	public function simpleFromDomain($domainId) {
		return $this->simple(array('Badge.domain_id' => $domainId));
	}

	public function claim($playerId, $badgeId) {
		$this->begin();
		try {
			$this->recursive = 1;
			$badge = $this->findById($badgeId);
			if (!$badge) {
				throw new ModelException('Badge not found.');
			}

			$badgesClaimed = $this->BadgeClaimed->allFromPlayerByBadgeId($playerId);
			if ($badgesClaimed[$badgeId]['BadgeClaimed']['claimed']) {
				throw new ModelException('Badge already claimed.');
			}

			foreach ($badge['BadgeRequisite'] as $requisite) {
				if (!($badgesClaimed[$requisite['badge_id_requisite']]['BadgeClaimed']['claimed'])) {
					throw new ModelException('You lack the necessary badge requisites to claim this badge.');
				}
			}
			$badgeActivitiesProgress = $this->BadgeActivityProgress->allFromBadgeAndPlayer($badgeId, $playerId);
			foreach ($badgeActivitiesProgress as $activity) {
				if ($activity['BadgeActivityProgress']['progress'] != 100) {
					throw new ModelException('You lack the necessary activities to claim this badge.');
				}
			}

			// Consome as activity coins
			foreach ($badge['ActivityRequisite'] as $activity) {
				$activityId = (int)$activity['activity_id'];
				$count = (int)$activity['count'];
				$this->query('UPDATE log SET spent = 1 WHERE activity_id = ? AND player_id = ? AND spent = 0
						ORDER BY acquired ASC LIMIT ' . $count, array($activityId, $playerId));
			}

			$this->BadgeLog->_add(array(
				'badge_id' => $badgeId,
				'player_id' => $playerId
			));

			$player = $this->Player->findById($playerId);

			$badgeName = $badge['Badge']['name'];
            $playerName = $player['Player']['name'];

            $this->Notification->_broadcast(
                'New Badge', 
                __('It seems someone has gotten the <strong>%s</strong> badge! Congratulations, <strong>%s</strong>!', $badgeName, $playerName), 
                'warning'
            );

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			throw $ex;
		}
	}
}