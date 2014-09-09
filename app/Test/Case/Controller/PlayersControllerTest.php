<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class PlayersControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testJoinGetHashNotFound() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/players/join/invalidhash', array('method' => 'get'));
	}

	public function testJoinGetEmptyHash() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/players/join/', array('method' => 'get'));
	}

	public function testJoinGetSuccess() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);
		$this->testAction('/players/join/' . $hash, array('method' => 'get', 'return' => 'vars'));
		$playerAfter = $this->utils->Player->findById($id);
		$this->assertNull($playerAfter['Player']['verified_in']);
	}

	public function testJoinGetSuccessPasswordAlreadyDefined() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'password' => md5('123456'), 
			'hash' => $hash,
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);
		$this->testAction('/players/join/' . $hash, array('method' => 'get', 'return' => 'vars'));
		$playerAfter = $this->utils->Player->findById($id);
		$this->assertNotNull($playerAfter['Player']['verified_in']);
	}

	public function testJoinPostValidationError() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);

		$data = array(
			'Player' => array(
				'id' => $id,
				'password' => '654321',
				'repeat_password' => '123456'
			)
		);

		$this->testAction('/players/join/' . $hash, array('method' => 'post', 'data' => $data));
		$playerAfter = $this->utils->Player->findById($id);
		$this->assertNull($playerAfter['Player']['verified_in']);		$this->assertNotNull($this->controller->flashError);
	}

	public function testJoinPostSuccess() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);

		$data = array(
			'Player' => array(
				'id' => $id,
				'password' => '123456',
				'repeat_password' => '123456'
			)
		);

		$this->testAction('/players/join/' . $hash, array('method' => 'post', 'data' => $data));
		$playerAfter = $this->utils->Player->findById($id);
		$this->assertNotNull($playerAfter['Player']['verified_in']);
	}

	public function testJoinGetAlreadyVerified() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'verified_in' => date('Y-m-d H:i:s'),
			'player_type_id' => PLAYER_TYPE_PLAYER
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);

		$this->testAction('/players/join/' . $hash, array('method' => 'get'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testInviteGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$vars = $this->testAction('/players/invite', array('method' => 'get', 'return' => 'vars'));
		$this->assertTrue(isset($vars['teams']));
	}

	public function testInviteNotGameMaster() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$this->setExpectedException('ForbiddenException');
		$vars = $this->testAction('/players/invite', array('method' => 'get', 'return' => 'vars'));
	}

	public function testInvitePostValidationError() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$data = array(
			'Player' => array(
				'name' => '',
				'email' => 'email@email.com',
				'team_id' => '1'
			)
		);
		$playersBefore = $this->utils->Player->find('count');
		$this->testAction('/players/invite', array('data' => $data));
		$playersAfter = $this->utils->Player->find('count');
		$this->assertEquals($playersBefore, $playersAfter);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testInvitePostSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$data = array(
			'Player' => array(
				'name' => 'Player name',
				'email' => 'email@email.com',
				'team_id' => '1'
			)
		);
		$playersBefore = $this->utils->Player->find('count');
		$this->testAction('/players/invite', array('data' => $data));
		$player = $this->utils->Player->findByEmail('email@email.com');
		$this->assertNotNull($player['Player']['hash']);
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testTeamGet() {
		$this->controllerUtils->mockAuthUser();
		$id = PLAYER_ID_1;
		$vars = $this->testAction('/players/team/' . $id, array('method' => 'get', 'return' => 'vars'));
		$teams = $vars['teams'];
		$this->assertEquals(2, count($teams));
	}

	public function testTeamNotFound() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/players/team/0', array('method' => 'get'));
	}

	public function testTeamPost() {
		$id = PLAYER_ID_1;
		$teamId = TEAM_ID_EMPTY;
		$data = array('Player' => array(
			'id' => $id,
			'team_id' => $teamId
		));
		$this->testAction('/players/team/' . $id, array('return' => 'vars', 'data' => $data));
		$player = $this->utils->Player->findById($id);
		$this->assertEquals($teamId, (int)$player['Player']['team_id']);
	}

	public function testInvited() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);

		$result = $this->testAction('/players/invited', array('return' => 'vars'));
		$players = $result['players'];
		$this->assertNotEmpty($players);
		foreach ($players as $player) {
			$this->assertTrue(isset($player['Player']['name']));
		}
	}


	public function testIndex() {
		$this->controllerUtils->mockAuthUser();

		$result = $this->testAction('/players/index', array('return' => 'vars'));
		$players = $result['players'];
		$this->assertNotEmpty($players);
		foreach ($players as $player) {
			$this->assertTrue(isset($player['Player']['name']));
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
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$result = $this->testAction('/players/myaccount', array('method' => 'GET', 'return' => 'vars'));
		$this->assertNotNull($this->controller->request->data);
	}

	public function testMyAccountPostSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
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
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
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

	public function testSigninPostWrongUser() {
		$data = array();
		$result = $this->testAction('/players/signin', array('method' => 'POST', 'data' => $data, 'return' => 'vars'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testSigninRightUser() {
		$this->controllerUtils->mockAuthLogin();
		$data = array(
			'Player' => array(
				'email' => 'email1@email.com',
				'password' => '123456'
			)
		);
		$result = $this->testAction('/players/signin', array('method' => 'POST', 'data' => $data, 'return' => 'vars'));
		$this->assertNull($this->controller->flashError);
	}

	public function testSignupGet() {
		$result = $this->testAction('/players/signup', array('method' => 'GET', 'return' => 'vars'));
		$this->assertEqual(1, count($result['playerTypes']));
	}

	public function testSignupPostValidationErrors() {
		$data = array(
			'Player' => array(
				'name' => 'A team',
				'password' => '',
				'email' => 'email@email.com'
			)
		);
		$this->testAction('/players/signup', array('data' => $data));
		$player = $this->utils->Player->findByEmail('email@email.com');
		$this->assertNotNull($this->controller->flashError);
	}

	public function testSignupPostSuccess() {
		$data = array(
			'Player' => array(
				'name' => 'A team',
				'password' => '123456',
				'email' => 'email@email.com'
			)
		);
		$this->testAction('/players/signup', array('data' => $data));
		$player = $this->utils->Player->findByEmail('email@email.com');
		$this->assertNotEmpty($player['Player']['hash']);
		$this->assertEquals(PLAYER_TYPE_GAME_MASTER, $player['Player']['player_type_id']);
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testResetGet() {
		$this->testAction('/players/reset', array('method' => 'get'));
	}

	public function testResetPostWrongEmail() {
		$data = array('Player' => array('email' => 'invalid'));
		$result = $this->testAction('/players/reset', array('result' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testResetPostSuccess() {
		$data = array('Player' => array('email' => 'email1@email.com'));
		$result = $this->testAction('/players/reset', array('result' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testResetPassingHashPostPlayerNotFound() {
		$this->setExpectedException('NotFoundException');
		$result = $this->testAction('/players/reset/someinvalidhash', array('method' => 'get'));
	}

	public function testResetPassingHashGet() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'verified_in' => date('Y-m-d H:i:s'),
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);
		$this->testAction('/players/reset/' . $hash, array('method' => 'get'));
	}

	public function testResetPassingHashPostValidationError() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'verified_in' => date('Y-m-d H:i:s'),
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);

		$data = array(
			'Player' => array(
				'id' => $id,
				'password' => '654321',
				'repeat_password' => '123456'
			)
		);

		$this->testAction('/players/reset/' . $hash, array('method' => 'post', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}


	public function testResetPassingHashPostSuccess() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'hash' => $hash,
			'verified_in' => date('Y-m-d H:i:s'),
			'player_type_id' => PLAYER_TYPE_PLAYER,
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);

		$data = array(
			'Player' => array(
				'id' => $id,
				'password' => '123456',
				'repeat_password' => '123456'
			)
		);

		$this->testAction('/players/reset/' . $hash, array('method' => 'post', 'data' => $data));
		$this->assertNotNull($this->controller->flashSuccess);
	}
}