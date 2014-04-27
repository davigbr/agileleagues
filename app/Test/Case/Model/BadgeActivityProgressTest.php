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
		$this->utils->generateBadgeLogs();
	}

	public function testAllFromPlayerByBadgeIdAndActivityId() {
		$this->utils->BadgeActivityProgress->allFromPlayerByBadgeIdAndActivityId(1);
	}

	public function testAllFromBadgeAndPlayer() {
		$this->utils->BadgeActivityProgress->allFromBadgeAndPlayer(1, 1);
	}
}