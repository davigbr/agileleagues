<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class DashboardsControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateDomains();
		$this->utils->generateBadges();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateActivities();
		$this->utils->generateInactiveActivities();
		$this->utils->generateLogs();
		$this->utils->generateBadgeLogs();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
		$this->controllerUtils->mockAuthUser();
	}

	public function testActivities() {
		$result = $this->testAction('/dashboards/activities', array('return' => 'vars'));
		
		$activitiesNeverReported = $result['neverReportedActivities'];
		$activitiesLeastReported = $result['leastReportedActivities'];
		$activitiesMostReported = $result['mostReportedActivities'];

		$this->assertNotEmpty($activitiesNeverReported);
		$this->assertNotEmpty($activitiesLeastReported);
		$this->assertNotEmpty($activitiesMostReported);
		
		$this->assertEquals(4, $result['averageActivitiesLogged']);
		$this->assertEquals(8, $result['activitiesLogged']);
		$this->assertEquals(14, $result['totalActivities']);
		$this->assertEquals(4, $result['badgesCompletedCount']);
		$this->assertEquals(4, $result['activitiesCompletedCount']);
		$this->assertEquals(14, $result['totalActivitiesCount']);
		$this->assertEquals(4, $result['totalDifferentActivitiesCompleted']);
		$this->assertEquals(2, count($result['domains']));
		$this->assertEquals(1, count($result['differentActivitiesCompleted']));
		$this->assertEquals(4, count($result['activitiesCount']));
	}

	public function testActivitiesDomainDetails() {
		$domain = $this->utils->Domain->find('first');
		$result = $this->testAction('/dashboards/activities/' . $domain['Domain']['id'], array('return' => 'vars'));
		$activityCoins = $result['activityCoins'];
		$domain = $result['domain'];
		$this->assertNotEmpty($activityCoins);
		$this->assertNotEmpty($domain);
		$this->assertTrue(isset($domain['Domain']));
		foreach ($activityCoins as $coins) {
			$this->assertTrue(isset($coins['PlayerActivityCoins']));
		}
		foreach ($domain['Activity'] as $activity) {
			$this->assertFalse($activity['inactive']);
		}
	}


	public function testBadges() {
		$result = $this->testAction('/dashboards/badges', array('return' => 'vars'));
	}

	public function testLeaderboards() {
		$result = $this->testAction('/dashboards/leaderboards', array('return' => 'vars'));
	}

	public function testPlayers() {
		$result = $this->testAction('/dashboards/players', array('return' => 'vars'));
		$players = $result['players'];
		$this->assertEquals(4, count($players));
	}

}