<?php

App::uses('AppController', 'Controller');

class NotificationsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		if (!$this->isScrumMaster) {
			throw new ForbiddenException();
		}
	}

	public function index() {
		$this->set('notifications', $this->Notification->find('all', array(
			'contain' => array('Player'),
			'limit' => 100
		)));
	}

	public function send() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$notification = $this->request->data;
			$playerId = $notification['Notification']['player_id'];
			$title = $notification['Notification']['title'];
			$text = $notification['Notification']['text'];
			$type = $notification['Notification']['type'];

			try {
				$this->Notification->send($title, $text, $type, $playerId);
				$this->flashSuccess('Notification sent successfully!');
			} catch (ModelException $ex) {
				$this->flashError($ex->getMessage());
			}
		}
		$this->set('players', $this->Player->simple());
	}
}

