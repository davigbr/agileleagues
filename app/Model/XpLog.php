<?php

App::uses('AppModel', 'Model');

class XpLog extends AppModel {
	
	public $useTable = 'xp_log';

	public $belongsTo = array(
		'Player',
		'LogReviewed' => array(
			'className' => 'Log',
			'foreignKey' => 'log_id_reviewed'
		),
		'LogReported' => array(
			'className' => 'Log',
			'foreignKey' => 'log_id'
		)
	);
	
	public $validate = array(
		'player_id' => 'notEmpty',
		'xp' => 'notEmpty'
	);

	public $virtualFields = array(
		'description' => "IF(log_id IS NOT NULL, 'Activity reported', IF(log_id_reviewed IS NOT NULL, 'Activity reviewed', '?'))"
	);

	public $uses = array('Notification', 'Log');

	public function _activityReviewed($action, $playerIdReviewer, $logId) {
		$log = $this->Log->find('first', array(
			'conditions' => array(
				'Log.id' => $logId
			),
			'contain' => array(
				'Tags'
			)
		));
		if (!$log) {
			throw new Exception('Log not found');
		}

		$xp = $this->_applyTagModifiers($log);

		if ($action === 'accept') {
			$xp = (int)($xp * ACCEPTANCE_XP_MULTIPLIER);
		} else if ($action === 'reject') {
			$xp = REJECTION_XP_BONUS;
		}
		
		// Minimum of 1XP point
		if ($xp == 0) $xp = 1;

		$this->_add(array(
			'log_id_reviewed' => $logId,
			'player_id' => $playerIdReviewer,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));

		if ($action === 'accept') {
			$this->Notification->_success(
				$playerIdReviewer,
				__('Activity Reviewed - Accepted'),
				__('The activity you reviewed was accepted and you earned %s XP.', $xp)
			);
		} else {
			$this->Notification->_success(
				$playerIdReviewer,
				__('Activity Reviewed - Rejected'),
				__('The activity you reviewed was rejected and you earned %s XP.', $xp)
			);
		}
	}

	public function _applyTagModifiers($log) {
		$xp = (int)$log['Log']['xp'];
		$percentualModifiers = 100;
		$additionModifiers = 0;

		foreach ($log['Tags'] as $tag) {
			if ($tag['bonus_type'] === '%') {
				$percentualModifiers += (int)$tag['bonus_value'];
			} else if ($tag['bonus_type'] === '+') {
				$additionModifiers += (int)$tag['bonus_value'];
			}
		}

		$xp *= $percentualModifiers/100;
		$xp += $additionModifiers;
		return (int)$xp;
	}

	public function _activityReported($playerId, $logId) {
		$log = $this->Log->find('first', array(
			'conditions' => array(
				'Log.id' => $logId
			),
			'contain' => array(
				'Tags'
			)
		));
		if (!$log) {
			throw new Exception('Log not found');
		}

		$playerBefore = $this->Player->findById($playerId);
		if (!$playerBefore) {
			throw new Exception('Player not found');
		}

		$xp = $this->_applyTagModifiers($log);

		$this->_add(array(
			'log_id' => $logId,
			'player_id' => $playerId,
			'created' => date('Y-m-d H:i:s'),
			'xp' => $xp
		));

		$this->_levelUpNotification($playerBefore, $xp);
	}

	public function _levelUpNotification($playerBefore, $xpGained) {
		$levelBefore = (int)$playerBefore['Player']['level'];
		$playerName = h($playerBefore['Player']['name']);
		$playerId = $playerBefore['Player']['id'];

		$levelAfter = $this->Player->level($playerBefore['Player']['xp'] + $xpGained);

		if ($levelAfter > $levelBefore) {
			switch ($levelAfter) {
				default: {
					$this->Notification->_broadCast(
						$playerId,
						__('Level Up'),
						__('%s reached level %s!', $playerName, $levelAfter),
						'warning');
					break;
				}
			}
		}
	}

	public function afterSave($created, $options = array()) {
		$playerId = $this->data['XpLog']['player_id'];
		$xp = $this->data['XpLog']['xp'];
		$this->Player->query('UPDATE player SET xp = xp + ? WHERE id = ?', array($xp, $playerId));
	}
}