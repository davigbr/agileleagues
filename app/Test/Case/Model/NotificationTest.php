<?php

App::uses('TestUtils', 'Lib');

class NotificationTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateNotifications();
	}

	public function testBroadcast() {
		$this->utils->Notification->_broadcast('título', 'texto', 'success');
		$all = $this->utils->Notification->all();
		$this->assertEquals(11, count($all));
	}

	public function testSuccess() {
		$this->utils->Notification->_success(DEVELOPER_1_ID, 'success', 'texto');
		$notification = $this->utils->Notification->findByTitleAndType('success', 'success');
		$this->assertNotEmpty($notification);
	}

	public function testWarning() {
		$this->utils->Notification->_warning(DEVELOPER_1_ID, 'warning', 'texto');
		$notification = $this->utils->Notification->findByTitleAndType('warning', 'warning');
		$this->assertNotEmpty($notification);
	}

	public function testAddError() {
		try {
			$this->utils->Notification->_add(array(
				'player_id' => DEVELOPER_1_ID, 
				'type' => 'warning', 
				'text' => '',
				'title' => ''
			));
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Could not save notification', $ex->getMessage());
		}
	}

	public function testBroadcastFailure() {
		try {
			$this->utils->Notification->_broadcast('', 'texto', 'success');
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Could not broad notification', $ex->getMessage());
		}
	}

	public function testUnread() {
		$notifications = $this->utils->Notification->unread(1, 2);
		$this->assertEquals(2, count($notifications));
		foreach ($notifications as $notification) {
			$this->assertEquals(0, (int)$notification['Notification']['read']);
		}
	}

	public function testMarkAsRead() {
		$notifications = $this->utils->Notification->all();
		$this->utils->Notification->markAsRead($notifications);
		$notifications = $this->utils->Notification->all();
		foreach ($notifications as $notification) {
			$this->assertEquals(1, (int)$notification['Notification']['read']);
		}
	}

}