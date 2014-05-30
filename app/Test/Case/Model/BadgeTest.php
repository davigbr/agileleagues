<?php

App::uses('TestUtils', 'Lib');

class BadgeTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateBadges();
		$this->utils->generateBadgeRequisites();
		$this->utils->generateActivityRequisites();
	}

	public function testAllFromOwner() {
		$this->assertEquals(4, count($this->utils->Badge->allFromOwner(SCRUMMASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Badge->allFromOwner(SCRUMMASTER_ID_2)));
	}

	public function testAllFromOwnerById() {
		$this->assertEquals(4, count($this->utils->Badge->allFromOwnerById(SCRUMMASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Badge->allFromOwnerById(SCRUMMASTER_ID_2)));
	}
	
	public function testAllFromDomainById(){
		$domain = $this->utils->Domain->find('first', array('order' => 'Domain.id'));
		$domainId = $domain['Domain']['id'];
		$badges = $this->utils->Badge->allFromDomainById($domainId);
		$this->assertNotEmpty($badges);

		foreach ($badges as $id => $badge) {
			$this->assertEquals($badge['Badge']['id'], $id);
			$this->assertEquals($badge['Badge']['domain_id'], $domainId);
		}
	}

	public function testSimpleFromDomain(){
		$domain = $this->utils->Domain->find('first', array('order' => 'Domain.id'));
		$domainId = $domain['Domain']['id'];
		$badges = $this->utils->Badge->simpleFromDomain($domainId);
		$this->assertNotEmpty($badges);

		foreach ($badges as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testClaimSuccess() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$this->utils->Badge->recursive = 1;
		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = DEVELOPER_ID_1;

		$this->utils->BadgeLog->query('DELETE FROM badge_log WHERE player_id = ? AND badge_id = ?', array($playerId, $badgeId));
		$this->utils->Badge->claim($playerId, $badgeId);
		$this->assertNotEmpty($this->utils->BadgeLog->findByPlayerIdAndBadgeId($playerId, $badgeId));

		// Verifica se consumiu as activity coins
		$logs = $this->utils->Log->find('all', array(
			'conditions' => array(
				'Log.spent' => 1
		)));
		$activitiesSpent = array();
		foreach ($logs as $log) {
			$activitiesSpent[] = (int)$log['Log']['activity_id'];
		}
		$activitiesRequired = array();
		foreach ($badge['ActivityRequisite'] as $activityRequisite) {
			$activitiesRequired[] = (int)$activityRequisite['activity_id'];
		}
		$this->assertEquals($activitiesRequired, $activitiesSpent);
	}

	public function testClaimAlreadyClaimed() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = DEVELOPER_ID_1;
		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Badge already claimed.', $ex->getMessage());
		}
	}

	public function testClaimBadgeNotFound() {
		$playerId = DEVELOPER_ID_1;
		try {
			$this->utils->Badge->claim($playerId, 0);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Badge not found.', $ex->getMessage());
		}
	}


	public function testClaimFailureNoActivities() {
		$this->utils->generateBadgeLogs();

		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = DEVELOPER_ID_2;

		$this->utils->BadgeLog->query('DELETE FROM badge_log WHERE player_id = ? AND badge_id = ?', array($playerId, $badgeId));

		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('You lack the necessary activities to claim this badge.', $ex->getMessage());
		}
		$this->assertEmpty($this->utils->BadgeLog->findByPlayerIdAndBadgeId($playerId, $badgeId));
	}


	public function testClaimFailureNoBadges() {
		$this->utils->generateBadgeLogs();
		$this->utils->generateLogs();

		$badge = $this->utils->Badge->findById(2);
		$badgeId = $badge['Badge']['id'];
		$playerId = DEVELOPER_ID_2;

		$this->utils->BadgeLog->query('DELETE FROM badge_log');

		try {
			$this->utils->Badge->claim($playerId, $badgeId);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('You lack the necessary badge requisites to claim this badge.', $ex->getMessage());
		}
		$this->assertEmpty($this->utils->BadgeLog->findByPlayerIdAndBadgeId($playerId, $badgeId));
	}

}