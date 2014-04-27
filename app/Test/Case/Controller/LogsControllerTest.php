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
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
	}

	public function testDelete() {
		$log = $this->utils->Log->findByReviewed(null);
		$this->testAction('/logs/delete/' . $log['Log']['id']);
		$this->assertEquals(7, $this->utils->Log->find('count'));
	}

	public function testDeleteInvalid() {
		$this->testAction('/logs/delete/0');
	}

	public function testReview() {
		$log = $this->utils->Log->find('first', array('conditions' => array('Log.reviewed IS NULL')));
		$logId = $log['Log']['id'];
		$this->testAction('/logs/review/' . $logId);
		$log = $this->utils->Log->findById($logId);
		$this->assertTrue($log['Log']['reviewed'] !== null);
	}

	public function testReviewError() {
		$this->testAction('/logs/review/0');
		$this->assertNotNull($this->controller->flashError);
	}
}