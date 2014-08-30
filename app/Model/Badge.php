<?php

App::uses('AppModel', 'Model');

class Badge extends AppModel {
	
	public $useTable = 'badge';

	public $belongsTo = 'Domain';

	public $hasMany = array(
		'BadgeRequisite' => array(
			'dependent' => true
		),
		'ActivityRequisite' => array(
			'dependent' => true
		),
		'BadgeLog'
	);

	public $validate = array(
		'name' => 'notEmpty',
		'abbr' => 'notEmpty',
		'icon' => 'notEmpty'
	);

	public $uses = array(
		'BadgeActivityProgress', 
		'BadgeClaimed', 
		'BadgeLog', 
		'Player', 
		'Notification',
		'Log',
		'ActivityRequisiteSummary'
	);

	public function allFromOwner($playerIdOwner) {
		$badges = $this->find('all', array(
			'conditions' => array(
				'Badge.inactive' => 0,
				'Badge.player_id_owner' => $playerIdOwner
			),
			'contain' => array(
				'Domain',
				'ActivityRequisite' => array(
					'Activity',
					'Tags'
				),
				'BadgeRequisite'
			)
		));
		return $badges;
	}

	public function allFromOwnerById($playerIdOwner) {
		return $this->all(array(
			'Badge.player_id_owner' => $playerIdOwner
		), 'id');
	}

	public function allFromDomainById($domainId) {
		return $this->all(array(
			'Badge.inactive' => 0,
			'Badge.domain_id' => $domainId
		), 'id');
	}

	public function simpleFromDomain($domainId) {
		return $this->simple(array(
			'Badge.inactive' => 0,
			'Badge.domain_id' => $domainId
		));
	}

	public function saveBadge($id, $data, $playerIdOwner) {
		$this->begin();
		try {
			if ($id !== null) {
	            $this->ActivityRequisiteSummary->deleteAll(array(
	            	'ActivityRequisiteSummary.player_id_owner' => $playerIdOwner
            	), true);

	            $this->BadgeRequisite->deleteAll(array('BadgeRequisite.badge_id' => $id), true);
	            $this->ActivityRequisite->deleteAll(array('ActivityRequisite.badge_id' => $id), true);

	            // Rebuild activity requisite summary
	            $logs = $this->Log->find('all', array(
					'conditions' => array(
						'Log.accepted IS NOT NULL',
						'Log.player_id_owner' => $playerIdOwner
					),
					'contain' => array(
						'Tags'
					)
				));	

				foreach ($logs as $log) {
					$this->ActivityRequisite->_updateActivityRequisiteSummary($log);
				}
	        }
	        if ($this->saveAssociated($data)) {
        		$this->commit();
	        	return true;
	        } else {
	        	$this->rollback();
	        	return false;
	        }
        } catch (Exception $ex) {
        	$this->rollback();
			throw $ex;        	
        }
	}

	public function claim($playerId, $badgeId) {
		$this->begin();
		try {
			$this->recursive = 1;
			$badge = $this->findById($badgeId);
			if (!$badge) {
				throw new ModelException('Badge not found.');
			}
			$player = $this->Player->findById($playerId);
			if ($badge['Domain']['player_type_id'] != $player['Player']['player_type_id']) {
				throw new ModelException('Badge not compatible with player type.');
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

			$this->BadgeLog->_add(array(
				'badge_id' => $badgeId,
				'player_id' => $playerId
			));

			$badgeName = $badge['Badge']['name'];
            $playerName = $player['Player']['name'];

            $this->Notification->_broadcast(
                'New Badge', 
                __('It seems someone has gotten the %s badge! Congratulations, %s!', $badgeName, $playerName), 
                'warning'
            );

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			throw $ex;
		}
	}
}