<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class PlayersControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateTeams();
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
			'verification_hash' => $hash,
			'player_type_id' => PLAYER_TYPE_DEVELOPER,
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
			'verification_hash' => $hash,
			'player_type_id' => PLAYER_TYPE_DEVELOPER,
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
			'verification_hash' => $hash,
			'player_type_id' => PLAYER_TYPE_DEVELOPER,
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
		$this->assertNull($playerAfter['Player']['verified_in']);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testJoinPostSuccess() {
		$id = 1000;
		$hash = Security::hash($id, 'sha256', true);
		$player = array('Player' => array(
			'id' => $id,
			'name' => 'Name',
			'email' => 'email@email.com',
			'verification_hash' => $hash,
			'player_type_id' => PLAYER_TYPE_DEVELOPER,
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
			'verification_hash' => $hash,
			'verified_in' => date('Y-m-d H:i:s'),
			'player_type_id' => PLAYER_TYPE_DEVELOPER
		));
		unset($this->utils->Player->validate);
		$this->utils->Player->create();
		$this->utils->Player->save($player);

		$this->testAction('/players/join/' . $hash, array('method' => 'get'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testInviteGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$vars = $this->testAction('/players/invite', array('method' => 'get', 'return' => 'vars'));
		$this->assertTrue(isset($vars['playerTypes']));
	}

	public function testInviteNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$this->setExpectedException('ForbiddenException');
		$vars = $this->testAction('/players/invite', array('method' => 'get', 'return' => 'vars'));
	}

	public function testInvitePostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$data = array(
			'Player' => array(
				'name' => 'A team',
				'email' => 'email@email.com',
				'player_type_id' => PLAYER_TYPE_PRODUCT_OWNER,
				'team_id' => '1'
			)
		);
		$this->testAction('/players/invite', array('data' => $data));
		$player = $this->utils->Player->findByEmail('email@email.com');
		$this->assertNotEmpty($player['Player']['verification_hash']);
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testInvitePostScrumMaster() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$data = array(
			'Player' => array(
				'name' => 'A team',
				'email' => 'email@email.com',
				'player_type_id' => PLAYER_TYPE_SCRUMMASTER,
				'team_id' => '1'
			)
		);
		$this->testAction('/players/invite', array('data' => $data));
		$player = $this->utils->Player->findByEmail('email@email.com');
		$this->assertEmpty($player);
		$this->assertNotNull($this->controller->flashError);
	}


	public function testInvitePostValidationError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$data = array(
			'Player' => array(
				'name' => '',
				'email' => 'email@email.com',
				'player_type_id' => PLAYER_TYPE_PRODUCT_OWNER,
				'team_id' => '1'
			)
		);
		$playersBefore = $this->utils->Player->find('count');
		$this->testAction('/players/invite', array('data' => $data));
		$playersAfter = $this->utils->Player->find('count');
		$this->assertEquals($playersBefore, $playersAfter);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testTeamGet() {
		$id = DEVELOPER_ID_1;
		$vars = $this->testAction('/players/team/' . $id, array('method' => 'get', 'return' => 'vars'));
		$this->assertTrue(isset($vars['teams']));
	}

	public function testTeamNotFound() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/players/team/0', array('method' => 'get'));
	}

	public function testTeamPost() {
		$id = DEVELOPER_ID_1;
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
		$this->assertNotEmpty($player['Player']['verification_hash']);
		$this->assertNotNull($this->controller->flashSuccess);
	}
}