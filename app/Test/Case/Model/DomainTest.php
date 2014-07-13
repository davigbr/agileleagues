<?php

App::uses('TestUtils', 'Lib');

class DomainTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateBadges();
		$this->utils->generateActivities();
	}

	public function testInactivate() {
		$domain = $this->utils->Domain->find('first');
		$domainId = $domain['Domain']['id'];
		$this->utils->Domain->inactivate($domain['Domain']['id']);
		$domainAfter = $this->utils->Domain->findById($domainId);
		$activities = $this->utils->Activity->findAllByDomainId($domainId);
		$badges = $this->utils->Badge->findAllByDomainId($domainId);
		$this->assertEquals(1, (int)$domainAfter['Domain']['inactive']);
		$this->assertNotEmpty($activities);
		$this->assertNotEmpty($badges);
		foreach ($activities as $activity) {
			$this->assertEquals(1, (int)$activity['Activity']['inactive']);
		}
		foreach ($badges as $badge) {
			$this->assertEquals(1, (int)$badge['Badge']['inactive']);
		}
	}

	public function testAllFromOwner() {
		$this->assertEqual(3, count($this->utils->Domain->allFromOwner(GAME_MASTER_ID_1)));
		$this->assertEqual(0, count($this->utils->Domain->allFromOwner(GAME_MASTER_ID_2)));
	}
	
	public function testSimpleFromOwner() {
		$list = $this->utils->Domain->simpleFromOwner(GAME_MASTER_ID_1);
		
		$this->assertEqual(3, count($list));
		$this->assertEqual(0, count($this->utils->Domain->simpleFromOwner(GAME_MASTER_ID_2)));

		foreach ($list as $key => $value) {
			$this->assertTrue(is_integer($key), is_string($value));
		}
	}

	public function testActivitiesCount(){
		$domains = $this->utils->Domain->activitiesCount(GAME_MASTER_ID_1);
		foreach ($domains as $id => $activities) {
			if ($id == 1) {
				$this->assertEquals(4, $activities);
			} else if ($id == 2) {
				$this->assertEquals(6, $activities);
			}
		}
		$this->assertEquals(3, count($domains));
	}

	public function testActivitiesCountGM2(){
		$this->assertEmpty($this->utils->Domain->activitiesCount(GAME_MASTER_ID_2));

	}
}