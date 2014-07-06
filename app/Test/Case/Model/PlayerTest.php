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

	public function testVisibleTeamsScrumMaster() {
		$teams = $this->utils->Player->visibleTeams(SCRUMMASTER_ID_1);
		$this->assertEquals(array(TEAM_ID_1, TEAM_ID_2), $teams);
	}

	public function testVisibleTeamsDeveloper() {
		$teams = $this->utils->Player->visibleTeams(DEVELOPER_ID_1);
		$this->assertEquals(array(TEAM_ID_1), $teams);
	}

	public function testAllFromPlayerTeamAsScrumMaster() {
		$players = $this->utils->Player->allFromPlayerTeam(SCRUMMASTER_ID_1);
		$this->assertEquals(4, count($players));
	}

	public function testAllFromPlayerTeamAsDeveloper() {
		$players = $this->utils->Player->allFromPlayerTeam(DEVELOPER_ID_1);
		$this->assertEquals(3, count($players));
	}

	public function testAllFromPlayerScrumMasterTeamsAsScrumMaster() {
		$players = $this->utils->Player->allFromPlayerScrumMasterTeams(SCRUMMASTER_ID_1);
		$this->assertEquals(5, count($players));
	}

	public function testAllFromPlayerScrumMasterTeamsAsDeveloper() {
		$players = $this->utils->Player->allFromPlayerScrumMasterTeams(DEVELOPER_ID_1);
		$this->assertEquals(5, count($players));
	}

	public function testScrumMasterIdScrumMaster() {
		$scrumMasterId = $this->utils->Player->scrumMasterId(SCRUMMASTER_ID_1);
		$this->assertEquals(SCRUMMASTER_ID_1, $scrumMasterId);
	}

	public function testScrumMasterIdDeveloper() {
		$scrumMasterId = $this->utils->Player->scrumMasterId(DEVELOPER_ID_1);
		$this->assertEquals(SCRUMMASTER_ID_1, $scrumMasterId);
	}

	public function testFreeDeveloperList() {
		$this->utils->generatePlayer('Developer');
		$list = $this->utils->Player->freeDeveloperList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testScrumMasterList() {
		$list = $this->utils->Player->scrumMasterList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testDevelopersCount() {
		$count = $this->utils->Player->developersCount();
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
		$activitiesByDomain = $this->utils->Player->differentActivitiesCompletedCount(SCRUMMASTER_ID_1, 1);
		$this->assertEquals(1, count($activitiesByDomain));
		foreach ($activitiesByDomain as $domainId => $count) {
			$this->assertEquals(4, (int)$count);
		}
	}

	public function testDifferentActivitiesCompletedSM2(){
		$this->assertEmpty($this->utils->Player->differentActivitiesCompletedCount(SCRUMMASTER_ID_2, 1));
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

	public function testSimpleTeamMatesTeamAsDeveloper() {
		$this->assertEquals(3, count($this->utils->Player->simpleTeamMates(DEVELOPER_ID_1)));
	}

	public function testSimpleTeamMatesAsScrumMaster() {
		$this->assertEquals(4, count($this->utils->Player->simpleTeamMates(SCRUMMASTER_ID_1)));
	}

	public function testSimpleVerifiedFromPlayerTeamAsDeveloper() {
		$this->assertEquals(4, count($this->utils->Player->simpleVerifiedFromPlayerTeam(DEVELOPER_ID_1)));
	}

	public function testSimpleVerifiedFromPlayerTeamScrumMaster() {
		$this->assertEquals(5, count($this->utils->Player->simpleVerifiedFromPlayerTeam(SCRUMMASTER_ID_1)));
	}

}