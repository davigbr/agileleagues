<?php

App::uses('TestUtils', 'Lib');

class BadgeLogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateBadges();
		$this->utils->generateBadgeLogs();
	}

	public function testPlayerCount(){
		$logs = $this->utils->BadgeLog->playerCount(PLAYER_ID_1);
		$this->assertEquals(4, $logs);
		$logs = $this->utils->BadgeLog->playerCount(PLAYER_ID_2);
		$this->assertEquals(4, $logs);
	}

	public function testAllFromPlayerByBadgeId() {
		$logs = $this->utils->BadgeLog->allFromPlayerByBadgeId(1);
		$this->assertEquals(1, (int)$logs[1]['BadgeLog']['badge_id']);
		$this->assertEquals(2, (int)$logs[2]['BadgeLog']['badge_id']);
	}

}