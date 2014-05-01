<?php

App::uses('AppModel', 'Model');

class Notification extends AppModel {
	
	public $useTable = 'notification';
	public $displayField = 'text';
	public $belongsTo = array('Player');
	public $order = array('Notification.created' => 'DESC');

	public $validate = array(
		'title' => 'notEmpty',
		'text' => 'notEmpty',
		'type' => 'notEmpty'
	);

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
	public function send($title, $text, $type, $playerId = null) {
		$ds = $this->getDataSource();
		$ds->begin();
		try {
			if (!$playerId) {
				$this->_broadcast($title, $text, $type);
			} else {
				$this->_add(array(
					'type' => $type,
		            'title' => $title,
		            'text' => $text,
		            'player_id' => $playerId
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

	public function _success($playerId, $title, $text) {
		return $this->_add(array(
			'type' => 'success',
            'title' => $title,
            'text' => $text,
            'player_id' => $playerId
		));
	}

	public function _warning($playerId, $title, $text) {
		return $this->_add(array(
			'type' => 'warning',
            'title' => $title,
            'text' => $text,
            'player_id' => $playerId
		));
		if (!$saved) throw new Exception('Could not save notification');
	}

	public function _broadcast($title, $text, $type = 'success') {
		$players = $this->Player->find('list');
		$notifications = array();
		foreach ($players as $id => $name) {
			$notifications[] = array(
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