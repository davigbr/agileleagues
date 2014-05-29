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

	public function testSimple() {
		$simple = $this->utils->Activity->simple();
		$this->assertEquals(22, count($simple));
	}

	public function testSimpleActive() {
		$simple = $this->utils->Activity->simpleActive();
		$this->assertEquals(14, count($simple));
	}

	public function testSimpleActiveFromPlayerType() {
		$simple = $this->utils->Activity->simpleActiveFromPlayerType(PLAYER_TYPE_DEVELOPER);
		$this->assertEquals(10, count($simple));
	}

	public function testAll() {
		$all = $this->utils->Activity->all();
		$this->assertEquals(22, count($all));
	}

	public function testCount() {
		$count = $this->utils->Activity->count();
		$this->assertEquals(14, $count);
	}

	public function testLeaderboardsLastWeek() {
		$leaderboards = $this->utils->Activity->leaderboardsLastWeek();
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(DEVELOPER_1_ID, DEVELOPER_2_ID))) {
				$this->assertEquals(1, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsLastMonth() {
		$leaderboards = $this->utils->Activity->leaderboardsLastMonth();
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(DEVELOPER_1_ID, DEVELOPER_2_ID))) {
				$this->assertTrue((int)$row['Leaderboards']['count'] >= 1);
			}
		}
	}
	
	public function testLeaderboardsEver() {
		$leaderboards = $this->utils->Activity->leaderboardsEver();
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(DEVELOPER_1_ID, DEVELOPER_2_ID))) {
				$this->assertEquals(4, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsThisWeek() {
		$leaderboards = $this->utils->Activity->leaderboardsThisWeek();
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(DEVELOPER_1_ID, DEVELOPER_2_ID))) {
				$this->assertEquals(2, (int)$row['Leaderboards']['count']);
			}
		}
	}

	public function testLeaderboardsThisMonth() {
		$leaderboards = $this->utils->Activity->leaderboardsThisMonth();
		foreach ($leaderboards as $row) {
			if (in_array($row['Player']['id'], array(DEVELOPER_1_ID, DEVELOPER_2_ID))) {
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
		$neverReported = $this->utils->Activity->neverReported(PLAYER_TYPE_DEVELOPER, 1);
		$this->assertEquals(1, count($neverReported));
		$this->assertEquals(0, (int)$neverReported[0]['Activity']['reported']);
	}

	public function testLeastReported() {
		$leastReported = $this->utils->Activity->leastReported(PLAYER_TYPE_DEVELOPER, 1);
		$this->assertEquals(1, count($leastReported));
	}

	public function testMostReported() {
		$mostReported = $this->utils->Activity->mostReported(PLAYER_TYPE_DEVELOPER, 1);
		$this->assertEquals(1, count($mostReported));
	}

}