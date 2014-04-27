<?php

App::uses('NotificationsHelper', 'View/Helper');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('ComponentCollection', 'Controller');

class NotificationsHelperTest extends CakeTestCase {

    public $Notifications = null;

    public function setUp() {
        parent::setUp();
        $this->Notifications = new NotificationsHelper(new View(new Controller()));
    }

    public function testMarkAsRead() {
    	$notifications = array(
    		array('Notification' => array(
    			'id' => 1
			))
		);
    	$this->Notifications->markAsRead($notifications);
    }

}