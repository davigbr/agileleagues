<?php

App::uses('TestUtils', 'Lib');

class NotificationTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateNotifications();
	}

	public function testBroadcastAsDeveloper() {
		$countBefore = $this->utils->Notification->find('count');
		$this->utils->Notification->_broadcast(DEVELOPER_1_ID, 'título', 'texto', 'success');
		$countAfter = $this->utils->Notification->find('count');
		$this->assertEquals(4, $countAfter - $countBefore);
	}

	public function testBroadcastAsScrumMaster() {
		$countBefore = $this->utils->Notification->find('count');
		$this->utils->Notification->_broadcast(SCRUMMASTER_ID, 'título', 'texto', 'success');
		$countAfter = $this->utils->Notification->find('count');
		$this->assertEquals(5, $countAfter - $countBefore);
	}

	public function testBroadcastAsProductOwner() {
		$countBefore = $this->utils->Notification->find('count');
		$this->utils->Notification->_broadcast(PRODUCT_OWNER_ID, 'título', 'texto', 'success');
		$countAfter = $this->utils->Notification->find('count');
		$this->assertEquals(4, $countAfter - $countBefore);
	}

	public function testSuccess() {
		$this->utils->Notification->_success(DEVELOPER_1_ID, 'success', 'texto', DEVELOPER_1_ID);
		$notification = $this->utils->Notification->findByTitleAndType('success', 'success');
		$this->assertNotEmpty($notification);
	}

	public function testWarning() {
		$this->utils->Notification->_warning(DEVELOPER_1_ID, 'warning', 'texto', DEVELOPER_1_ID);
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
			$this->utils->Notification->_broadcast(DEVELOPER_1_ID, '', 'texto', 'success');
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Could not broadcast notification', $ex->getMessage());
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

	public function testSendBroadcast() {
		$this->utils->Notification->query('DELETE FROM notification');
		$this->utils->Notification->send(DEVELOPER_1_ID, 'a', 'b', 'success');	
		$notificationsCount = $this->utils->Notification->find('count');
		$this->assertEquals(4, $notificationsCount);
	}

	public function testSend() {
		$this->utils->Notification->query('DELETE FROM notification');
		$playerId = DEVELOPER_1_ID;
		$this->utils->Notification->send($playerId, 'a', 'b', 'success', $playerId);	
		$notificationsCount = $this->utils->Notification->find('count');
		$this->assertEquals(1, $notificationsCount);
	}

	public function testSendException() {
		$this->utils->Notification->query('DELETE FROM notification');
		$playerId = DEVELOPER_1_ID;
		$this->setExpectedException('Exception');
		$this->utils->Notification->send(DEVELOPER_1_ID, '', '', 'success', $playerId);	
	}

}