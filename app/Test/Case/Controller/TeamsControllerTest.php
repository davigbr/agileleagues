<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class TeamsControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$this->setExpectedException('ForbiddenException');
		$this->testAction('/teams');
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$vars = $this->testAction('/teams/', array('return'=>'vars'));
		$teams = $vars['teams'];
		$this->assertNotEmpty($teams);
		foreach ($teams as $team) {
			$this->assertEquals(array(
				'id', 
				'name', 
				'player_id_scrummaster'
			), array_keys($team['Team']));
			$this->assertEquals(array(
				'id', 
				'name' 
			), array_keys($team['ScrumMaster']));
			foreach ($team['Developers'] as $dev) {
				$this->assertEquals(array(
					'id', 
					'name',
					'team_id'
				), array_keys($dev));
			}
			foreach ($team['ProductOwners'] as $dev) {
				$this->assertEquals(array(
					'id', 
					'name',
					'team_id'
				), array_keys($dev));
			}
		}
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$team = $this->utils->Team->find('first');
		$id = $team['Team']['id'];
		$vars = $this->testAction('/teams/edit/' . $id, array('method' => 'get', 'return' => 'vars'));
	}


	public function testEditNotFound() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$this->setExpectedException('NotFoundException');
		$this->testAction('/teams/edit/0', array('method' => 'get'));
	}
	
	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$vars = $this->testAction('/teams/add', array('method' => 'get', 'return' => 'vars'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$data = array(
			'Team' => array(
				'name' => 'A team'
			)
		);
		$teamsBefore = $this->utils->Team->find('count');
		$this->testAction('/teams/add', array('data' => $data));
		$teamsAfter = $this->utils->Team->find('count');
		$this->assertNotNull($this->controller->flashSuccess);
		$this->assertEquals($teamsBefore + 1, $teamsAfter);
	}

	public function testAddPostError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$data = array(
			'Team' => array(
				'name' => '',
				'player_id_scrummaster' => SCRUMMASTER_ID_1,
				'player_id_product_owner' => null
			)
		);
		$teamsBefore = $this->utils->Team->find('count');
		$this->testAction('/teams/add', array('data' => $data));
		$teamsAfter = $this->utils->Team->find('count');
		$this->assertNotNull($this->controller->flashError);
		$this->assertEquals($teamsBefore, $teamsAfter);
	}

	public function testDeleteNotFound() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$this->setExpectedException('NotFoundException');
		$this->testAction('/teams/delete/0');
	}

	public function testDeleteSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$team = $this->utils->Team->findById(TEAM_ID_EMPTY);
		$id = $team['Team']['id'];
		$this->testAction('/teams/delete/' . $id);
		$this->assertNotNull($this->controller->flashSuccess);
		$this->assertFalse($this->utils->Team->exists($id));
	}

	public function testDeleteError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$team = $this->utils->Team->findById(TEAM_ID_1);
		$id = $team['Team']['id'];
		$this->testAction('/teams/delete/' . $id);
		$this->assertNotNull($this->controller->flashError);
		$this->assertTrue($this->utils->Team->exists($id));
	}

}