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
		$this->utils->generateTags();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
	}

	public function testDelete() {
		$log = $this->utils->Log->find('first');
		$logId = $log['Log']['id'];
		// Adiciona tags ao log
		$this->utils->Log->saveAll(array(
			'Log' => array(
				'id' => $logId
			),
			'Tags' => array(
				'Tags' => array(
					0 => 1,
					1 => 2
				)
			)
		));
		$this->utils->Log->recursive = 2;
		$log = $this->utils->Log->findById($logId);

		$this->testAction('/logs/delete/' . $log['Log']['id']);
		$this->assertEquals(7, $this->utils->Log->find('count'));
	}

	public function testDeleteInvalid() {
		$this->testAction('/logs/delete/0');
	}
}