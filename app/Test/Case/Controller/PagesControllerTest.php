<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class PagesControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
	}

	public function testHome() {
		$this->testAction('/pages/home');
	}

	public function testPages() {
		$this->testAction('/pages/');
	}

	public function testMissingView() {
		$this->setExpectedException('MissingViewException');
		$this->testAction('/pages/doesnotexist/subpage');
	}

	public function testNotFound() {
		Configure::write('debug', 0);
		$this->setExpectedException('NotFoundException');
		$this->testAction('/pages/doesnotexist');
	}

}