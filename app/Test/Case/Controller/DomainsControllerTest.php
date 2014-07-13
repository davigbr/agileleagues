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

	public function testInactivateNotGameMaster() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$this->setExpectedException('ForbiddenException');
		$this->testAction('/domains/inactivate/0');
	}

	public function testInactivateInvalidBadge() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$this->setExpectedException('NotFoundException');
		$this->testAction('/domains/inactivate/0');
	}

	public function testInactivateNotConfirmed() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$domain = $this->utils->Domain->find('first');
		$domainId = $domain['Domain']['id'];
		$result = $this->testAction('/domains/inactivate/' . $domainId, array('return' => 'vars'));
		$this->assertEquals($domainId, $result['domain']['Domain']['id']);
		$domainAfter = $this->utils->Domain->findById($domainId);
		$this->assertEquals(0, (int)$domainAfter['Domain']['inactive']);
	}

	public function testNotGameMaster() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$this->setExpectedException('ForbiddenException');
		$this->testAction('/domains/add');
	}

	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$vars = $this->testAction('/domains/add', array('method' => 'get', 'return' => 'vars'));
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$domain = $this->utils->Domain->find('first');
		$id = $domain['Domain']['id'];
		$vars = $this->testAction('/domains/edit/' . $id, array('method' => 'get', 'return' => 'vars'));
	}

	public function testEditNotFound() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$this->setExpectedException('NotFoundException');
		$this->testAction('/domains/edit/0', array('method' => 'get'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$data = array(
			'Domain' => array(
				'name' => 'A domain',
				'abbr' => 'ABBR',
				'color' => '#112233',
				'description' => 'Blablablabla',
				'icon' => 'icon-icon'
			)
		);
		$this->testAction('/domains/add', array('data' => $data));
		
		$domain = $this->utils->Domain->findByName('A domain');

		$this->assertNotNull($this->controller->flashSuccess);
		$this->assertEquals(GAME_MASTER_ID_1, $domain['Domain']['player_id_owner']);
	}


	public function testAddPostError() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$data = array(
			'Domain' => array(
				'name' => '',
				'abbr' => 'ABBR',
				'color' => '#112233',
				'description' => 'Blablablabla',
				'icon' => 'icon-icon'
			)
		);
		$domainsBefore = $this->utils->Domain->find('count');
		$this->testAction('/domains/add', array('data' => $data));
		$domainsAfter = $this->utils->Domain->find('count');
		$this->assertEquals($domainsBefore, $domainsAfter);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$result = $this->testAction('/domains/index', array('return' => 'vars'));
		foreach ($result['domains'] as $domain) {
			$this->assertTrue(isset($domain['Domain']['name']));
		}
	}

	public function testIndexAsSMWithoutDomains() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_2);
		$result = $this->testAction('/domains/index', array('return' => 'vars'));
		$this->assertEmpty($result['domains']);
	}

}