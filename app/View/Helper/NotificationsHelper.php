<?php

App::uses('AppHelper', 'View/Helper');

class NotificationsHelper extends AppHelper {

	public $uses = array('Notification');

	public function markAsRead($notifications) {
		if (!empty($notifications)) {
			$this->Notification->markAsRead($notifications);
		}
	}
}
