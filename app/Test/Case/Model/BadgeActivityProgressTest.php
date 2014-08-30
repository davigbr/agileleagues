<?php

App::uses('TestUtils', 'Lib');

class BadgeActivityProgressTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateBadges();
		$this->utils->generateBadgeRequisites();
		$this->utils->generateActivityRequisites();
		$this->utils->generateBadgeLogs();
	}

	public function testAllFromPlayerByBadgeId() {
		$all = $this->utils->BadgeActivityProgress->allFromPlayerByBadgeId(1);
		$this->assertNotEmpty($all);
	}

	public function testAllFromBadgeAndPlayer() {
		$all = $this->utils->BadgeActivityProgress->allFromBadgeAndPlayer(1, 1);
		$this->assertNotEmpty($all);
	}
}