<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class PlayersControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
		session_unset ();
	}

	public function testTeamGet() {
		$id = DEVELOPER_1_ID;
		$vars = $this->testAction('/players/team/' . $id, array('method' => 'get', 'return' => 'vars'));
		$this->assertTrue(isset($vars['teams']));
	}

	public function testTeamNotFound() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/players/team/0', array('method' => 'get'));
	}

	public function testTeamPost() {
		$id = DEVELOPER_1_ID;
		$teamId = TEAM_ID_EMPTY;
		$data = array('Player' => array(
			'id' => $id,
			'team_id' => $teamId
		));
		$this->testAction('/players/team/' . $id, array('return' => 'vars', 'data' => $data));
		$player = $this->utils->Player->findById($id);
		$this->assertEquals($teamId, (int)$player['Player']['team_id']);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser();

		$result = $this->testAction('/players/index', array('return' => 'vars'));
		$players = $result['players'];
		$this->assertNotEmpty($players);
		foreach ($players as $player) {
			$playerFields = array('id', 
				'name', 
				'player_type_id', 
				'email', 
				'password', 
				'xp', 
				'team_id',
				'level', 
				'next_level_total_xp', 
				'next_level_xp', 
				'next_level_xp_completed', 
				'progress', 
				'title'
			);
			$this->assertEquals($playerFields, array_keys($player['Player']));
		}
	}

	public function testLoginAlreadyLoggedIn() {
		$this->controllerUtils->mockAuthUser();
		$this->testAction('/players/login');
	}

	public function testLoginGet() {
		$this->testAction('/players/login', array('method' => 'GET'));
	}

	public function testLoginPostWrongUser() {
		$data = array();
		$result = $this->testAction('/players/login', array('method' => 'POST', 'data' => $data, 'return' => 'vars'));
		$this->assertEquals($result['login_status'], 'invalid');
	}

	public function testLoginRightUser() {
		$this->controllerUtils->mockAuthLogin();
		$data = array(
			'Player' => array(
				'email' => 'email1@email.com',
				'password' => '123456'
			)
		);
		$result = $this->testAction('/players/login', array('method' => 'POST', 'data' => $data, 'return' => 'vars'));
		$this->assertEquals($result['login_status'], 'success');
	}

	public function testMyAccountGet() {
		$this->controllerUtils->mockAuthUser();
		$result = $this->testAction('/players/myaccount', array('method' => 'GET', 'return' => 'vars'));
		$this->assertNotNull($this->controller->request->data);
	}

	public function testMyAccountPostSuccess() {
		$this->controllerUtils->mockAuthUser();
		$player = $this->utils->Player->findById(1);
		$data = array(
			'Player' => array(
				'id' => $player['Player']['id'],
				'password' => '654321',
				'repeat_password' => '654321'
			)
		);
		$result = $this->testAction('/players/myaccount', array('method' => 'POST', 'data' => $data, 'return' => 'vars'));
		$savedPlayer = $this->utils->Player->findById(1);
		$this->assertNotEquals($savedPlayer['Player']['password'], $player['Player']['password']);
	}

	public function testMyAccountPostFailure() {
		$this->controllerUtils->mockAuthUser();
		$player = $this->utils->Player->findById(1);
		$data = array(
			'Player' => array(
				'id' => $player['Player']['id'],
				'password' => '654321', 
				'repeat_password' => '123456'
			)
		);
		$result = $this->testAction('/players/myaccount', array('method' => 'POST', 'data' => $data, 'return' => 'vars'));
		$savedPlayer = $this->utils->Player->findById(1);
		$this->assertEquals($savedPlayer['Player']['password'], $player['Player']['password']);
	}

	public function testLogout() {
		$this->controllerUtils->mockAuthUser();
		$this->testAction('/players/logout');
	}
}