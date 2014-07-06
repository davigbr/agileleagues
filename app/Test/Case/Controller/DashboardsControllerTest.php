<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class DashboardsControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateBadges();
		$this->utils->generateActivities();
		$this->utils->generateInactiveActivities();
		$this->utils->generateLogs();
		$this->utils->generateBadgeLogs();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testActivities() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
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

	public function testActivitiesScrumMaster2() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_2);
		$result = $this->testAction('/dashboards/activities', array('return' => 'vars'));
		
		$activitiesNeverReported = $result['neverReportedActivities'];
		$activitiesLeastReported = $result['leastReportedActivities'];
		$activitiesMostReported = $result['mostReportedActivities'];

		$this->assertEmpty($activitiesNeverReported);
		$this->assertEmpty($activitiesLeastReported);
		$this->assertEmpty($activitiesMostReported);
		
		$this->assertEquals(0, $result['averageActivitiesLogged']);
		$this->assertEquals(0, $result['activitiesLogged']);
		$this->assertEquals(0, $result['totalActivities']);
		$this->assertEquals(0, $result['badgesCompletedCount']);
		$this->assertEquals(0, $result['activitiesCompletedCount']);
		$this->assertEquals(0, $result['totalActivitiesCount']);
		$this->assertEquals(0, $result['totalDifferentActivitiesCompleted']);
		$this->assertEquals(0, count($result['domains']));
		$this->assertEquals(0, count($result['differentActivitiesCompleted']));
		$this->assertEquals(0, count($result['activitiesCount']));
	}

	public function testActivitiesDomainDetails() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$domain = $this->utils->Domain->find('first');
		$result = $this->testAction('/dashboards/activities/' . $domain['Domain']['id'], array('return' => 'vars'));
		$activitiesSummary = $result['activitiesSummary'];
		$domain = $result['domain'];
		$this->assertNotEmpty($activitiesSummary);
		$this->assertNotEmpty($domain);
		$this->assertTrue(isset($domain['Domain']));
		foreach ($activitiesSummary as $summary) {
			$this->assertTrue(isset($summary['PlayerActivitySummary']));
		}
		foreach ($domain['Activity'] as $activity) {
			$this->assertFalse($activity['inactive']);
		}
	}


	public function testBadges() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$result = $this->testAction('/dashboards/badges', array('return' => 'vars'));
	}

	public function testLeaderboards() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$result = $this->testAction('/dashboards/leaderboards', array('return' => 'vars'));
        $this->assertNotEmpty($result['activityLeaderboardsEver']);
        $this->assertNotEmpty($result['activityLeaderboardsThisWeek']);
        $this->assertNotEmpty($result['activityLeaderboardsThisMonth']);
        $this->assertNotEmpty($result['activityLeaderboardsLastWeek']);
        $this->assertNotEmpty($result['activityLeaderboardsLastMonth']);
	}

	public function testLeaderboardsSM2() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_2);
		$result = $this->testAction('/dashboards/leaderboards', array('return' => 'vars'));
        $this->assertEmpty($result['activityLeaderboardsEver']);
        $this->assertEmpty($result['activityLeaderboardsThisWeek']);
        $this->assertEmpty($result['activityLeaderboardsThisMonth']);
        $this->assertEmpty($result['activityLeaderboardsLastWeek']);
        $this->assertEmpty($result['activityLeaderboardsLastMonth']);
	}

	public function testPlayers() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$result = $this->testAction('/dashboards/players', array('return' => 'vars'));
		$players = $result['players'];
		$this->assertEquals(3, count($players));
	}

}