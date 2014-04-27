<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class BadgesControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateBadges();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/badges/index', array('return' => 'vars'));
		$badges = $result['badges'];
		$this->assertNotEmpty($badges);
		foreach ($badges as $badge) {
			$badgeFields = array('id', 'code', 'name', 'domain_id', 'abbr', 'new', 'icon');
			$this->assertEquals($badgeFields, array_keys($badge['Badge']));
		}
	}

	public function testClaimSuccess() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$badge = $this->utils->Badge->findById(1);
		$badgeId = $badge['Badge']['id'];
		$this->testAction('/badges/claim/' . $badgeId, array('return' => 'vars'));
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testClaimException() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$this->testAction('/badges/claim/0');
		$this->assertNotNull($this->controller->flashError);
	}

	public function testView() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$this->utils->generateActivityRequisites();
		$this->utils->generateBadgeRequisites();
		$badge = $this->utils->Badge->findById(1);
		$badgeId = $badge['Badge']['id'];
		$result = $this->testAction('/badges/view/' . $badgeId, array('return' => 'vars'));
		$this->assertEquals($badgeId, $result['badge']['Badge']['id']);
		$this->assertEquals(false, $result['claimed']);
		$this->assertEquals(false, $result['canClaim']);
	}

	

}