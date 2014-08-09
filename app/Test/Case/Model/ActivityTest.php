<?php

App::uses('TestUtils', 'Lib');

class ActivityTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateInactiveActivities();
		$this->utils->generateLogs();
	}

	public function testSimpleActive() {
		$this->assertEquals(12, count($this->utils->Activity->simpleActive(GAME_MASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Activity->simpleActive(GAME_MASTER_ID_2)));
	}

	public function testSimpleActiveFromPlayerType() {
		$this->assertEquals(10, count($this->utils->Activity->simpleActiveFromPlayerType(GAME_MASTER_ID_1, PLAYER_TYPE_PLAYER)));
		$this->assertEquals(0, count($this->utils->Activity->simpleActiveFromPlayerType(GAME_MASTER_ID_2, PLAYER_TYPE_PLAYER)));
	}

	public function testAllActive() {
		$this->assertEquals(12, count($this->utils->Activity->allActive(GAME_MASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Activity->allActive(GAME_MASTER_ID_2)));
	}

	public function testCount() {
		$this->assertEquals(12, $this->utils->Activity->count(GAME_MASTER_ID_1));
		$this->assertEquals(0, $this->utils->Activity->count(GAME_MASTER_ID_2));
	}

	public function testLeaderboardsLastWeek() {
		$leaderboards = $this->utils->Activity->leaderboardsLastWeek(GAME_MASTER_ID_1);
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(PLAYER_ID_1, PLAYER_ID_2))) {
				$this->assertEquals(1, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsLastMonth() {
		$leaderboards = $this->utils->Activity->leaderboardsLastMonth(GAME_MASTER_ID_1);
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(PLAYER_ID_1, PLAYER_ID_2))) {
				$this->assertTrue((int)$row['Leaderboards']['count'] >= 1);
			}
		}
	}
	
	public function testLeaderboardsEver() {
		$leaderboards = $this->utils->Activity->leaderboardsEver(GAME_MASTER_ID_1);
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(PLAYER_ID_1, PLAYER_ID_2))) {
				$this->assertEquals(4, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsThisWeek() {
		$leaderboards = $this->utils->Activity->leaderboardsThisWeek(GAME_MASTER_ID_1);
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(PLAYER_ID_1, PLAYER_ID_2))) {
				$this->assertEquals(2, (int)$row['Leaderboards']['count']);
			}
		}
	}

	public function testLeaderboardsThisMonth() {
		$leaderboards = $this->utils->Activity->leaderboardsThisMonth(GAME_MASTER_ID_1);
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(PLAYER_ID_1, PLAYER_ID_2))) {
				$this->assertTrue((int)$row['Leaderboards']['count'] >= 2);
			}
		}
	}

	public function testSimpleFromDomain() {
		$simple = $this->utils->Activity->simpleFromDomain(1);
		$this->assertEquals(8, count($simple));
		foreach ($simple as $key => $value) {
			$this->assertTrue(is_int($key));
			$this->assertTrue(is_string($value));
		}
	}

	public function testNeverReported() {
		$neverReported = $this->utils->Activity->neverReported(GAME_MASTER_ID_1, 1);
		$this->assertEquals(1, count($neverReported));
		$this->assertEquals(0, (int)$neverReported[0]['Activity']['reported']);
	}

	public function testLeastReported() {
		$leastReported = $this->utils->Activity->leastReported(GAME_MASTER_ID_1, 1);
		$this->assertEquals(1, count($leastReported));
	}

	public function testMostReported() {
		$mostReported = $this->utils->Activity->mostReported(GAME_MASTER_ID_1, 1);
		$this->assertEquals(1, count($mostReported));
	}


	public function testLeaderboardsLastWeekSM2() {
		$this->assertEmpty($this->utils->Activity->leaderboardsLastWeek(GAME_MASTER_ID_2));
	}
	
	public function testLeaderboardsLastMonthSM2() {
		$this->assertEmpty($this->utils->Activity->leaderboardsLastMonth(GAME_MASTER_ID_2));
	}
	
	public function testLeaderboardsEverSM2() {
		$this->assertEmpty($this->utils->Activity->leaderboardsEver(GAME_MASTER_ID_2));
	}
	
	public function testLeaderboardsThisWeekSM2() {
		$this->assertEmpty($this->utils->Activity->leaderboardsThisWeek(GAME_MASTER_ID_2));
	}

	public function testLeaderboardsThisMonthSM2() {
		$this->assertEmpty($this->utils->Activity->leaderboardsThisMonth(GAME_MASTER_ID_2));
	}

}