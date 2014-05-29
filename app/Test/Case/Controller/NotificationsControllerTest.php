<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class NotificationsControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateNotifications();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$result = $this->testAction('/notifications', array('return' => 'vars'));
		$notifications = $result['notifications'];
		$this->assertNotEmpty($notifications);
	
		foreach ($notifications as $notification) {
			$this->assertTrue(isset($notification['Notification']['text']));
		}
	}

	public function testSendGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$result = $this->testAction('/notifications/send', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($result['players']);
	}

	public function testSendPost() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$data = array(
			'Notification' => array(
				'title' => 'A',
				'text' => 'B',
				'type' => 'success',
				'player_id' => '',
			)
		);
		$notificationsCountBefore = $this->utils->Notification->find('count');
		$result = $this->testAction('/notifications/send', array('return' => 'vars', 'data' => $data));
		$notificationsCountAfter = $this->utils->Notification->find('count');
		$this->assertNotEmpty($result['players']);
		$this->assertNotNull($this->controller->flashSuccess);
		$this->assertTrue($notificationsCountAfter > $notificationsCountBefore);
	}
}