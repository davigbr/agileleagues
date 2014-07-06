<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class BadgesControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateBadges();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$result = $this->testAction('/badges/index', array('return' => 'vars'));
		$badges = $result['badges'];
		$this->assertNotEmpty($badges);
		foreach ($badges as $badge) {
			$this->assertTrue(isset($badge['Badge']['name']));
		}
	}

	public function testClaimSuccess() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$badge = $this->utils->Badge->findById(1);
		$badgeId = $badge['Badge']['id'];
		$this->testAction('/badges/claim/' . $badgeId, array('return' => 'vars'));
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testClaimException() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$this->testAction('/badges/claim/0');
		$this->assertNotNull($this->controller->flashError);
	}

	public function testView() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$this->utils->generateActivityRequisites();
		$this->utils->generateBadgeRequisites();
		$badge = $this->utils->Badge->findById(1);
		$badgeId = $badge['Badge']['id'];
		$result = $this->testAction('/badges/view/' . $badgeId, array('return' => 'vars'));
		$this->assertEquals($badgeId, $result['badge']['Badge']['id']);
		$this->assertEquals(false, $result['claimed']);
		$this->assertEquals(false, $result['canClaim']);
	}

	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$domain = $this->utils->Domain->find('first');
		$domainId = $domain['Domain']['id'];
		$vars = $this->testAction('/badges/add/' . $domainId, array('method' => 'get', 'return' => 'vars'));
		$this->assertTrue(isset($vars['badges']));
		$this->assertTrue(isset($vars['activities']));
		$this->assertTrue(isset($vars['domain']));
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$domain = $this->utils->Domain->find('first');
		$badge = $this->utils->Badge->find('first');
		$id = $badge['Badge']['id'];
		$domainId = $domain['Domain']['id'];
		$vars = $this->testAction('/badges/edit/' . $domainId . '/' . $id, array('method' => 'get', 'return' => 'vars'));
		$this->assertTrue(isset($vars['badges']));
		$this->assertTrue(isset($vars['activities']));
		$this->assertTrue(isset($vars['domain']));
	}


	public function testEditNotFound() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$domain = $this->utils->Domain->find('first');
		$domainId = $domain['Domain']['id'];
		$this->setExpectedException('NotFoundException');
		$this->testAction('/badges/edit/' . $domainId . '/0', array('method' => 'get'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$data = array();
		$data['Badge']['name']= 'Glossarier';
		$data['Badge']['icon']= 'entypo entypo-users';
		$data['BadgeRequisite'][0]['badge_id_requisite']= '';
		$data['BadgeRequisite'][1]['badge_id_requisite']= '';
		$data['BadgeRequisite'][2]['badge_id_requisite']= '';
		$data['BadgeRequisite'][3]['badge_id_requisite']= '';
		$data['ActivityRequisite'][0]['activity_id']= '';
		$data['ActivityRequisite'][0]['count']= '';
		$data['ActivityRequisite'][1]['activity_id']= '';
		$data['ActivityRequisite'][1]['count']= '';
		$data['ActivityRequisite'][2]['activity_id']= '';
		$data['ActivityRequisite'][2]['count']= '';
		$data['ActivityRequisite'][3]['activity_id']= '';
		$data['ActivityRequisite'][3]['count']= '';
		$data['Badge']['new']= '1';
		$domain = $this->utils->Domain->find('first');
		$domainId = $domain['Domain']['id'];
		$badgesBefore = $this->utils->Badge->find('count');
		$this->testAction('/badges/add/' . $domainId, array('data' => $data));
		$badgesAfter = $this->utils->Badge->find('count');
		$this->assertNotNull($this->controller->flashSuccess);
		$this->assertEquals($badgesBefore + 1, $badgesAfter);
	}


	public function testAddPostError() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$data = array();
		$data['Badge']['name']= '';
		$data['Badge']['icon']= 'entypo entypo-users';
		$data['BadgeRequisite'][0]['badge_id_requisite']= '';
		$data['BadgeRequisite'][1]['badge_id_requisite']= '';
		$data['BadgeRequisite'][2]['badge_id_requisite']= '';
		$data['BadgeRequisite'][3]['badge_id_requisite']= '';
		$data['ActivityRequisite'][0]['activity_id']= '';
		$data['ActivityRequisite'][0]['count']= '';
		$data['ActivityRequisite'][1]['activity_id']= '';
		$data['ActivityRequisite'][1]['count']= '';
		$data['ActivityRequisite'][2]['activity_id']= '';
		$data['ActivityRequisite'][2]['count']= '';
		$data['ActivityRequisite'][3]['activity_id']= '';
		$data['ActivityRequisite'][3]['count']= '';
		$data['Badge']['new']= '1';
		$domain = $this->utils->Domain->find('first');
		$domainId = $domain['Domain']['id'];		
		$teamsBefore = $this->utils->Team->find('count');
		$this->testAction('/badges/add/' . $domainId, array('data' => $data));
		$teamsAfter = $this->utils->Team->find('count');
		$this->assertNotNull($this->controller->flashError);
		$this->assertEquals($teamsBefore, $teamsAfter);
	}
	

}