<?php

App::uses('TestUtils', 'Lib');

class PlayerTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
	}

	public function testVisibleTeamsGameMaster() {
		$teams = $this->utils->Player->visibleTeams(GAME_MASTER_ID_1);
		$this->assertEquals(array(TEAM_ID_1, TEAM_ID_2), $teams);
	}

	public function testVisibleTeamsPlayer() {
		$teams = $this->utils->Player->visibleTeams(PLAYER_ID_1);
		$this->assertEquals(array(TEAM_ID_1), $teams);
	}

	public function testAllFromPlayerTeamAsGameMaster() {
		$players = $this->utils->Player->allFromPlayerTeam(GAME_MASTER_ID_1);
		$this->assertEquals(3, count($players));
	}

	public function testAllFromPlayerTeamAsPlayer() {
		$players = $this->utils->Player->allFromPlayerTeam(PLAYER_ID_1);
		$this->assertEquals(2, count($players));
	}

	public function testAllFromPlayerGameMasterTeamsAsGameMaster() {
		$players = $this->utils->Player->allFromPlayerGameMasterTeams(GAME_MASTER_ID_1);
		$this->assertEquals(4, count($players));
	}

	public function testAllFromPlayerGameMasterTeamsAsPlayer() {
		$players = $this->utils->Player->allFromPlayerGameMasterTeams(PLAYER_ID_1);
		$this->assertEquals(4, count($players));
	}

	public function testGameMasterIdGameMaster() {
		$gameMasterId = $this->utils->Player->gameMasterId(GAME_MASTER_ID_1);
		$this->assertEquals(GAME_MASTER_ID_1, $gameMasterId);
	}

	public function testGameMasterIdPlayer() {
		$gameMasterId = $this->utils->Player->gameMasterId(PLAYER_ID_1);
		$this->assertEquals(GAME_MASTER_ID_1, $gameMasterId);
	}

	public function testFreePlayerList() {
		$this->utils->generatePlayer('Player');
		$list = $this->utils->Player->freePlayerList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testGameMasterList() {
		$list = $this->utils->Player->gameMasterList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testPlayersCount() {
		$count = $this->utils->Player->playersCount();
		$this->assertEquals(3, $count);
	}

	public function testBeforeSave() {
		$player = array(
			'name' => 'New Player',
			'password' => '123456'
		);
		$this->utils->Player->save($player);
		$player = $this->utils->Player->read();
		$this->assertEquals('24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', $player['Player']['password']);
	}

	public function testVirtualFields() {
		$player = $this->utils->Player->find('first');
		$this->assertEquals(500, (int)$player['Player']['xp']);
		$this->assertEquals(519, (int)$player['Player']['next_level_total_xp']);
		$this->assertEquals(237, (int)$player['Player']['next_level_xp']);
		$this->assertEquals(218, (int)$player['Player']['next_level_xp_completed']);
		$this->assertEquals(91.9831, (float)$player['Player']['progress']);
	}
	
	public function testDifferentActivitiesCompleted(){
		$activitiesByDomain = $this->utils->Player->differentActivitiesCompletedCount(GAME_MASTER_ID_1, 1);
		$this->assertEquals(1, count($activitiesByDomain));
		foreach ($activitiesByDomain as $domainId => $count) {
			$this->assertEquals(4, (int)$count);
		}
	}

	public function testDifferentActivitiesCompletedSM2(){
		$this->assertEmpty($this->utils->Player->differentActivitiesCompletedCount(GAME_MASTER_ID_2, 1));
	}

	public function testLevelAndXP() {
		$this->assertEquals(11, (int)$this->utils->Player->level(3162.2776601684));
		$this->assertEquals(3162, (int)$this->utils->Player->xp(10));
	}


	public function testRepeatPasswordRule() {
		$this->utils->Player->data = array(
			'Player' => array(
				'password' => '123456',
				'repeat_password' => '986532'
			)
		);
		$this->assertEquals(false, $this->utils->Player->repeatPasswordRule());
	}

	public function testSimpleTeamMatesTeamAsPlayer() {
		$this->assertEquals(1, count($this->utils->Player->simpleTeamMates(PLAYER_ID_1)));
	}

	public function testSimpleTeamMatesAsGameMaster() {
		$this->assertEquals(3, count($this->utils->Player->simpleTeamMates(GAME_MASTER_ID_1)));
	}

	public function testSimpleVerifiedFromPlayerTeamAsPlayer() {
		$this->assertEquals(2, count($this->utils->Player->simpleVerifiedFromPlayerTeam(PLAYER_ID_1)));
	}

	public function testSimpleVerifiedFromPlayerTeamGameMaster() {
		$this->assertEquals(3, count($this->utils->Player->simpleVerifiedFromPlayerTeam(GAME_MASTER_ID_1)));
	}

}