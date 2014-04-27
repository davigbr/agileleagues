<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class ActivitiesControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateLogsNotReviewed();
		$this->utils->generateEvents();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/activities/index', array('return' => 'vars', 'mehtod' => 'GET'));
		$activities = $result['activities'];

		$this->assertNotEmpty($activities);

		foreach ($activities as $activity) {
			$activityFields = array('id', 'name', 'domain_id', 'description', 'inactive', 'new', 'xp', 'reported');
			$domainFields = array('id', 'name', 'color', 'abbr', 'description', 'icon');
			$this->assertEquals($activityFields, array_keys($activity['Activity']));
			$this->assertEquals($domainFields, array_keys($activity['Domain']));
		}
	}

	public function testInactivateNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$this->testAction('/activities/inactivate/0', array('return' => 'vars'));
	}

	public function testInactivateNotFound() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$this->testAction('/activities/inactivate/0', array('return' => 'vars'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testInactivateSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$this->testAction('/activities/inactivate/1', array('return' => 'vars'));
		$activity = $this->utils->Activity->findById(1);
		$this->assertEquals(true, (bool)$activity['Activity']['inactive']);
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testReportGet()  {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/activities/report', array('return' => 'vars', 'method' => 'GET'));
		$activities = $result['activities'];
		$players = $result['players'];
		$events = $result['events'];

		$this->assertEquals(2, count($events));
		$this->assertEquals(10, count($activities));
		$this->assertEquals(3, count($players));
	}

	public function testReportPost() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$data = array(
			'Log' => array(
				'activity_id' => 2,
				'acquired' => array(
					'day' => date('d'),
					'month' => date('m'),
					'year' => date('Y'),
				),
				'description' => 'blablabla'
			),
			'Event' => array(
				'id' => '',
			)
		);
		$this->testAction('/activities/report', array('method' => 'POST', 'data' => $data));
		$log = $this->utils->Log->find('first', array('order'=> 'Log.id'));
		$this->assertEquals(1, (int)$log['Log']['player_id']);
		$this->assertEquals(1, (int)$log['Log']['activity_id']);
	}

	public function testMyPending() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/activities/mypending', array('return' => 'vars'));
	}

	public function testMyReviewed() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/activities/myreviewed', array('return' => 'vars'));
	}

	public function testDay(){
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$log = $this->utils->Log->findByPlayerId(DEVELOPER_1_ID);
		$acquired = $log['Log']['acquired'];
		$result = $this->testAction('/activities/day/' . $acquired, array('return' => 'vars'));
		$count = $this->utils->Log->find('count', array('conditions' => array('Log.acquired' => $acquired, 'Log.player_id' => DEVELOPER_1_ID)));
		$this->assertEquals($count, count($result['logs']));
	}

	public function testCalendar() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/activities/calendar/', array('return' => 'vars'));
		$this->assertEquals(8, count($result['calendarLogs']));
	}

	public function testNotReviewedNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/activities/notReviewed', array('return' => 'vars'));
		$this->assertTrue(!isset($result['logs']));
	}

	public function testNotReviewed() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$result = $this->testAction('/activities/notReviewed', array('return' => 'vars'));
		$logs = $result['logs'];
		$this->assertTrue(!empty($logs));
	}

	public function testEditNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);		
		$this->testAction('/activities/edit/1', array('return' => 'vars', 'method' => 'GET'));
		$this->assertEmpty($this->controller->request->data);
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);		
		$this->testAction('/activities/edit/1', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($this->controller->request->data['Activity']);
	}

	public function testEditPostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);		
		$id = 1;
		$name = 'name changed';
		$data = array(
			'Activity' => array(
				'id' => $id,
				'name' => $name
			)
		);
		$this->testAction("/activities/edit/$id", array('return' => 'vars', 'data' => $data));
		$activity = $this->utils->Activity->findById($id);
		$this->assertEquals($name, $activity['Activity']['name']);
	}

	public function testEditPostValidationError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);		
		$id = 1;
		$name = 'name changed';
		$data = array(
			'Activity' => array(
				'id' => $id,
				'name' => null
			)
		);
		$this->testAction("/activities/edit/$id", array('return' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testAddNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);		
		$this->testAction('/activities/add/', array('return' => 'vars', 'method' => 'GET'));
		$this->assertEmpty($this->controller->request->data);
	}

	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);		
		$this->testAction('/activities/add/', array('return' => 'vars', 'method' => 'GET'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);		
		$name = 'name new activity';
		$data = array(
			'Activity' => array(
				'name' => $name,
				'code' => 123,
				'domain_id' => 1,
				'description' => 'blablabla'
			)
		);
		$this->testAction('/activities/add/', array('return' => 'vars', 'data' => $data));
		$activity = $this->utils->Activity->find('first', array('order'=> 'Activity.id DESC'));
		$this->assertEquals($name, $activity['Activity']['name']);
	}

	public function testAddPostValidationError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);		
		$id = 1;
		$data = array(
			'Activity' => array(
				'name' => 'some name',
				'code' => 123,
				'domain_id' => 1,
				'description' => ''
			)
		);
		$this->testAction('/activities/add/', array('return' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}

}