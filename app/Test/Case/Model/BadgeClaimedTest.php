<?php

App::uses('TestUtils', 'Lib');

class BadgeClaimedTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateBadges();
		$this->utils->generateBadgeLogs();
	}

	public function testAllFromPlayerByBadgeId() {
		$all = $this->utils->BadgeClaimed->allFromPlayerByBadgeId(1);
		$this->assertEquals(4, count($all));
	}
}