<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class ActivitiesControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateLogsNotReviewed();
		$this->utils->generateEvents();
		$this->utils->generateTags();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$result = $this->testAction('/activities/index', array('return' => 'vars', 'mehtod' => 'GET'));
		$activities = $result['activities'];

		$this->assertNotEmpty($activities);

		foreach ($activities as $activity) {
			$this->assertTrue(isset($activity['Activity']['name']));
			$this->assertTrue(isset($activity['Domain']['name']));
		}
	}

	public function testIndexAsGameMasterWithoutActivities() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_2);
		$result = $this->testAction('/activities/index', array('return' => 'vars', 'mehtod' => 'GET'));
		$activities = $result['activities'];
		$this->assertEmpty($activities);
	}	

	public function testInactivateNotGameMaster() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$this->testAction('/activities/inactivate/0', array('return' => 'vars'));
	}

	public function testInactivateNotFound() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$this->testAction('/activities/inactivate/0', array('return' => 'vars'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testInactivateSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);
		$this->testAction('/activities/inactivate/1', array('return' => 'vars'));
		$activity = $this->utils->Activity->findById(1);
		$this->assertEquals(true, (bool)$activity['Activity']['inactive']);
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testReportGet()  {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$result = $this->testAction('/activities/report', array('return' => 'vars', 'method' => 'GET'));
		$activities = $result['activities'];
		$events = $result['events'];
		$players = $result['players'];
		$tags = $result['tags'];

		$this->assertEquals(2, count($events));
		$this->assertEquals(10, count($activities));
		$this->assertEquals(1, count($players));
		$this->assertEquals(4, count($tags));
	}

	public function testReportGetNoActivities()  {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_2);
		$result = $this->testAction('/activities/report', array('return' => 'vars', 'method' => 'GET'));
		$activities = $result['activities'];
		$events = $result['events'];
		$players = $result['players'];

		$this->assertEquals(0, count($events));
		$this->assertEquals(0, count($activities));
		$this->assertEquals(1, count($players));
	}

	public function testReportPostSingle() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$description = 'Some random and unique description';
		$data = array(
			'Log' => array(
				'activity_id' => 2,
				'acquired' => array(
					'day' => date('d'),
					'month' => date('m'),
					'year' => date('Y'),
				),
				'description' => array($description),
				'player_id_pair' => PLAYER_ID_2
			),
			'Tags' => array(
				'Tags' => array(
					0 => '1',
					1 => '2'
				)
			),
			'Event' => array(
				'id' => '',
			)
		);
		$this->testAction('/activities/report', array('method' => 'POST', 'data' => $data));
		$this->utils->Log->recursive = 2;
		$log = $this->utils->Log->findById($this->controller->Log->id);

		$this->assertEquals(1, (int)$log['Log']['player_id']);
		$this->assertEquals(2, (int)$log['Log']['activity_id']);
		// Check if the tags were saved
		$this->assertNotEmpty($log['Tags'][0]);
		$this->assertNotEmpty($log['Tags'][1]);
		$this->assertEquals(GAME_MASTER_ID_1, (int)$log['Log']['player_id_owner']);
	}

	public function testReportPostMultiple() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$description = 'Some random and unique description';
		$data = array(
			'Log' => array(
				'activity_id' => 2,
				'acquired' => array(
					'day' => date('d'),
					'month' => date('m'),
					'year' => date('Y'),
				),
				'description' => array(
					$description . ' 1',
					$description . ' 2',
					'' // empty description should be ignored
				),
				'player_id_pair' => PLAYER_ID_2
			),
			'Tags' => array(
				'Tags' => array(
					0 => '1',
					1 => '2'
				)
			),
			'Event' => array(
				'id' => '',
			)
		);
		$this->testAction('/activities/report', array('method' => 'POST', 'data' => $data));
		$this->utils->Log->recursive = 2;
		$logs = $this->utils->Log->find('all' , array('conditions' => array(
			'Log.description LIKE' => $description . '%'
		)));
		
		$this->assertEquals(2, count($logs));

		foreach ($logs as $log) {
			$log = $this->utils->Log->findById($this->controller->Log->id);

			$this->assertEquals(1, (int)$log['Log']['player_id']);
			$this->assertEquals(2, (int)$log['Log']['activity_id']);
			// Check if the tags were saved
			$this->assertNotEmpty($log['Tags'][0]);
			$this->assertNotEmpty($log['Tags'][1]);
			$this->assertEquals(GAME_MASTER_ID_1, (int)$log['Log']['player_id_owner']);
		}
	}

	public function testMyPending() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$result = $this->testAction('/activities/myPending', array('return' => 'vars'));
	}

	public function testDay(){
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$log = $this->utils->Log->findByPlayerId(PLAYER_ID_1);
		$acquired = $log['Log']['acquired'];
		$result = $this->testAction('/activities/day/' . $acquired, array('return' => 'vars'));
		$count = $this->utils->Log->find('count', array('conditions' => array('Log.acquired' => $acquired, 'Log.player_id' => PLAYER_ID_1)));
		$this->assertEquals($count, count($result['logs']));
	}

	public function testCalendar() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);
		$result = $this->testAction('/activities/calendar/', array('return' => 'vars'));
		$this->assertEquals(8, count($result['calendarLogs']));
	}

	public function testAddNotGameMaster() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);		
		$this->setExpectedException('ForbiddenException');
		$this->testAction('/activities/edit/1', array('return' => 'vars', 'method' => 'GET'));
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);		
		$this->testAction('/activities/edit/1', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($this->controller->request->data['Activity']);
	}

	public function testEditPostSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);		
		$id = 1;
		$name = 'name changed';
		$data = array(
			'Activity' => array(
				'id' => $id,
				'name' => $name,
				'acceptance_votes' => '1',
				'rejection_votes' => '1'
			)
		);
		$this->testAction("/activities/edit/$id", array('return' => 'vars', 'data' => $data));
		$activity = $this->utils->Activity->findById($id);
		$this->assertEquals($name, $activity['Activity']['name']);
	}

	public function testEditPostValidationError() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);		
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

	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);		
		$this->testAction('/activities/add/', array('return' => 'vars', 'method' => 'GET'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);		
		$name = 'name new activity';
		$data = array(
			'Activity' => array(
				'name' => $name,
				'domain_id' => '1',
				'description' => 'blablabla',
				'acceptance_votes' => '1',
				'rejection_votes' => '1'
			)
		);
		$this->testAction('/activities/add/', array('return' => 'vars', 'data' => $data));
		$activity = $this->utils->Activity->findByName($name);
		$this->assertEquals($activity['Activity']['player_id_owner'], GAME_MASTER_ID_1);
	}

	public function testAddPostValidationError() {
		$this->controllerUtils->mockAuthUser(GAME_MASTER_ID_1);		
		$id = 1;
		$data = array(
			'Activity' => array(
				'name' => 'some name',
				'domain_id' => '1',
				'description' => '',
				'acceptance_votes' => '0',
				'rejection_votes' => '0'
			)
		);
		$this->testAction('/activities/add/', array('return' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testTeamGet() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);		
		$result = $this->testAction('/activities/team/', array('return' => 'vars', 'method' => 'get'));
		$this->assertNotEmpty($result['logs']);
	}

	public function testTeamPost() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);		
		$logs = $this->utils->Log->find('all', array(
			'conditions' => array('Log.reviewed IS NULL'),
			'contain' => array(
				'Activity', 'Domain', 'Tags'
			)
		));
		$data = array('Log' => array());
		foreach ($logs as $log) {
			$acceptance = $log['Log']['id'] % 2;
			$comment = 'some very very very long comment';
			if ($acceptance) {
				$data['Log'][$log['Log']['id']]['acceptance_comment'] = $comment;
			} else {
				$data['Log'][$log['Log']['id']]['rejection_comment'] = $comment;
			}
		}
		$this->testAction('/activities/team/', array('return' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testMyActivities() {
		$this->controllerUtils->mockAuthUser(PLAYER_ID_1);	
		$result = $this->testAction('/activities/myActivities/', array('return' => 'vars', 'method' => 'get'));
		$logs = $result['logs'];
		$this->assertNotEmpty($logs);
		$this->assertEquals(8, count($logs));
	}

}