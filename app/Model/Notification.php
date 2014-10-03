<?php

App::uses('AppModel', 'Model');

class Notification extends AppModel {
	
	public $useTable = 'notification';
	public $displayField = 'text';
	public $belongsTo = array(
		'Player',
		'PlayerSender' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id_sender'
		)
	);
	
	public $order = array('Notification.created' => 'DESC');

	public $validate = array(
		'title' => 'notEmpty',
		'text' => 'notEmpty',
		'type' => 'notEmpty'
	);

	public function allFromSenderOrRecipient($playerId, $limit = 100) {
		return $this->find('all', array(
			'recursive' => 1,
			'limit' => $limit,
			'conditions' => array(
				'OR' => array(
					'Notification.player_id' => $playerId,
					'Notification.player_id_sender' => $playerId
				)
			)
		));
	}


	public function unread($playerId, $limit = 5) {
		$this->recursive = -1;
		return $this->find('all', array(
			'conditions' => array(
				'Notification.read' => 0,
				'Notification.player_id' => $playerId
			),
			'limit' => $limit
		));
	}

	/**
	 * Controller method only!
	 */
	public function send($playerIdSender, $title, $text, $type, $playerIdTarget = null) {
		$ds = $this->getDataSource();
		$ds->begin();
		try {
			if (!$playerIdTarget) {
				$this->_broadcast($playerIdSender, $title, $text, $type);
			} else {
				$this->_add(array(
					'player_id_sender' => $playerIdSender,
					'type' => $type,
		            'title' => $title,
		            'text' => $text,
		            'player_id' => $playerIdTarget
				));
			}
			$ds->commit();
		} catch (Exception $ex) {
			$ds->rollback();
			throw $ex;
		}
	}

	public function _add($data) {
		$this->create();
		$saved = $this->save($data);
		if (!$saved) {
			throw new Exception('Could not save notification');
		}
		return $saved;
	}

	public function _success($playerId, $title, $text, $playerIdSender = null) {
		return $this->_add(array(
			'type' => 'success',
            'title' => $title,
            'text' => $text,
            'player_id' => $playerId,
            'player_id_sender' => $playerIdSender
		));
	}

	public function _warning($playerId, $title, $text, $playerIdSender = null) {
		return $this->_add(array(
			'type' => 'warning',
            'title' => $title,
            'text' => $text,
            'player_id' => $playerId,
            'player_id_sender' => $playerIdSender
		));
		if (!$saved) throw new Exception('Could not save notification');
	}

	public function _broadcast($playerIdSender, $title, $text, $type = 'success') {
		// Broadcast messages only to related players
		// For example, if it is a GameMaster, broadcast the message to all players from all his teams
		// If it is a Player, broadcast the message to all players from the same team, including the SM
		$players = $this->Player->simpleVerifiedFromPlayerTeam($playerIdSender, true);
		
		$notifications = array();
		foreach ($players as $id => $name) {
			$notifications[] = array(
				'player_id_sender' => $playerIdSender,
				'type' => $type,
	            'title' => $title,
	            'text' => $text,
	            'player_id' => $id
			);
		}
		$saved = $this->saveMany($notifications);
		if ($saved === false) {
			throw new Exception('Could not broadcast notification');
		}
		return true;
	}

	public function markAsRead($notifications) {
		$idList = array();
		foreach ($notifications as $notification) {
			$idList[] = $notification['Notification']['id'];
		}

		return $this->updateAll(
			array('Notification.read' => 1), 
			array('Notification.id' => $idList)
		);
	}
	
}