<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class DomainsControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$this->setExpectedException('ForbiddenException');
		$this->testAction('/domains/add');
	}

	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$vars = $this->testAction('/domains/add', array('method' => 'get', 'return' => 'vars'));
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$domain = $this->utils->Domain->find('first');
		$id = $domain['Domain']['id'];
		$vars = $this->testAction('/domains/edit/' . $id, array('method' => 'get', 'return' => 'vars'));
	}

	public function testEditNotFound() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$this->setExpectedException('NotFoundException');
		$this->testAction('/domains/edit/0', array('method' => 'get'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$data = array(
			'Domain' => array(
				'name' => 'A domain',
				'abbr' => 'ABBR',
				'color' => '#112233',
				'description' => 'Blablablabla',
				'icon' => 'icon-icon'
			)
		);
		$domainsBefore = $this->utils->Domain->find('count');
		$this->testAction('/domains/add', array('data' => $data));
		$domainsAfter = $this->utils->Domain->find('count');
		$this->assertNotNull($this->controller->flashSuccess);
		$this->assertEquals($domainsBefore + 1, $domainsAfter);
	}


	public function testAddPostError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$data = array(
			'Team' => array(
				'name' => '',
				'player_id_scrummaster' => SCRUMMASTER_ID,
				'player_id_product_owner' => null
			)
		);
		$teamsBefore = $this->utils->Team->find('count');
		$this->testAction('/domains/add', array('data' => $data));
		$teamsAfter = $this->utils->Team->find('count');
		$this->assertNotNull($this->controller->flashError);
		$this->assertEquals($teamsBefore, $teamsAfter);
	}

	public function testIndex() {
		$result = $this->testAction('/domains/index', array('return' => 'vars'));
		foreach ($result['domains'] as $domain) {
			$domainFields = array('id', 'name', 'color', 'abbr', 'description', 'icon');
			$this->assertEquals($domainFields, array_keys($domain['Domain']));
		}
	}
}