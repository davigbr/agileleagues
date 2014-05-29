<?php

App::uses('AppController', 'Controller');

class NotificationsController extends AppController {

	public function index() {
		$this->set('notifications', $this->Notification->find('all', array(
			'contain' => array(
				'Player', 
				'PlayerSender'
			),
			'limit' => 100,
		)));
	}

	public function send() {
		if ($this->request->is('post') || $this->request->is('put')) {
			$notification = $this->request->data;
			$playerId = $notification['Notification']['player_id'];
			$title = h($notification['Notification']['title']);
			$text = h($notification['Notification']['text']);
			$type = $notification['Notification']['type'];

			try {
				$this->Notification->send($this->Auth->user('id'), $title, $text, $type, $playerId);
				$this->request->data = array();
				$this->flashSuccess('Notification sent successfully!');
			} catch (ModelException $ex) {
				$this->flashError($ex->getMessage());
			}
		}
		$this->set('players', $this->Player->simpleVerifiedFromPlayerTeam($this->Auth->user('id')));
	}
}

