<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class LogsControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogsNotReviewed();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
	}

	public function testDelete() {
		$log = $this->utils->Log->findByReviewed(null);
		$this->testAction('/logs/delete/' . $log['Log']['id']);
		$this->assertEquals(7, $this->utils->Log->find('count'));
	}

	public function testDeleteInvalid() {
		$this->testAction('/logs/delete/0');
	}
}