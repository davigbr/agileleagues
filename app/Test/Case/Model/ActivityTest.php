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
		$this->assertEquals(18, count($simple));
	}

	public function testSimpleActive() {
		$simple = $this->utils->Activity->simpleActive();
		$this->assertEquals(10, count($simple));
	}

	public function testAll() {
		$all = $this->utils->Activity->all();
		$this->assertEquals(18, count($all));
	}

	public function testCount() {
		$count = $this->utils->Activity->count();
		$this->assertEquals(10, $count);
	}

	public function testLeaderboardsLastWeek() {
		$leaderboards = $this->utils->Activity->leaderboardsLastWeek();
		foreach ($leaderboards as $row) {
			if ($row['Player']['player_type_id'] != 2) {
				$this->assertEquals(1, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsLastMonth() {
		$leaderboards = $this->utils->Activity->leaderboardsLastMonth();
		foreach ($leaderboards as $row) {
			if ($row['Player']['player_type_id'] != 2) {
				$this->assertEquals(1, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsEver() {
		$leaderboards = $this->utils->Activity->leaderboardsEver();
		foreach ($leaderboards as $row) {
			if ($row['Player']['player_type_id'] != 2) {
				$this->assertEquals(4, (int)$row['Leaderboards']['count']);
			}
		}
	}
	
	public function testLeaderboardsThisWeek() {
		$leaderboards = $this->utils->Activity->leaderboardsThisWeek();
		foreach ($leaderboards as $row) {
			if ($row['Player']['player_type_id'] != 2) {
				$this->assertEquals(2, (int)$row['Leaderboards']['count']);
			}
		}
	}

	public function testLeaderboardsThisMonth() {
		$leaderboards = $this->utils->Activity->leaderboardsThisMonth();
		foreach ($leaderboards as $row) {
			if ($row['Player']['player_type_id'] != 2) {
				$this->assertEquals(3, (int)$row['Leaderboards']['count']);
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
		$neverReported = $this->utils->Activity->neverReported(1);
		$this->assertEquals(1, count($neverReported));
		$this->assertEquals(0, (int)$neverReported[0]['Activity']['reported']);
	}

	public function testLeastReported() {
		$leastReported = $this->utils->Activity->leastReported(1);
		$this->assertEquals(1, count($leastReported));
	}

	public function testMostReported() {
		$mostReported = $this->utils->Activity->mostReported(1);
		$this->assertEquals(1, count($mostReported));
	}

}