<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class EventsControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateEvents();
		$this->utils->generateEventTasks();
		$this->utils->generateEventJoinLogs();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testComplete() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$event = $this->utils->Event->find('first', array('order' => 'Event.id'));
		$eventId = $event['Event']['id'];
		// Remove as tarefas do evento
		$this->utils->EventTask->query('DELETE FROM event_task');
		$result = $this->testAction('/events/complete/' . $eventId, array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testCompleteError() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/events/complete/0', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testNotReviewed() {
		$this->utils->generateEventTaskLogs();
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$result = $this->testAction('/events/notReviewed/', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($result['eventTaskLogs']);
	}

	public function testDeleteTaskError() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_2_ID);
		$result = $this->testAction('/events/deleteTask/0', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testReviewTaskError() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_2_ID);
		$result = $this->testAction('/events/review/0', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testReviewTask() {
		$this->utils->generateEventTaskLogs();
		$this->controllerUtils->mockAuthUser(DEVELOPER_2_ID);
		$log = $this->utils->EventTaskLog->find('first', array('order'=>'EventTaskLog.id'));
		$result = $this->testAction('/events/review/' . $log['EventTaskLog']['id'], array('return' => 'vars', 'method' => 'GET'));
		$log = $this->utils->EventTaskLog->findById($log['EventTaskLog']['id']);
		$this->assertNotNull($log['EventTaskLog']['reviewed']);
	}

	public function testDeleteTask() {
		$this->utils->generateEventTaskLogs();
		$this->controllerUtils->mockAuthUser(DEVELOPER_2_ID);
		$log = $this->utils->EventTaskLog->find('first', array('order'=>'EventTaskLog.id'));
		$result = $this->testAction('/events/deleteTask/' . $log['EventTaskLog']['id'], array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testPending() {
		$this->utils->generateEventTaskLogs();
		$this->controllerUtils->mockAuthUser(DEVELOPER_2_ID);
		$result = $this->testAction('/events/pending/', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($result['eventTaskLogs']);
	}

	public function testReportGet() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/events/report/', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($result['events']);
		$this->assertNotEmpty($result['allEvents']);
	}

	public function testReportPostSuccess() {
		$playerId = DEVELOPER_2_ID;

		$this->controllerUtils->mockAuthUser($playerId);
		$event = $this->utils->Event->find('first', array('recursive' => 1, 'order' => 'Event.id'));
		$task = $event['EventTask'][0];

		$data = array(
			'EventTaskLog' => array(
				'event_id' => $event['Event']['id'],
				'event_task_id' => $task['id']
			)
		);
		$result = $this->testAction('/events/report/', array('return' => 'vars', 'data' => $data));

		$log = $this->utils->EventTaskLog->findByEventTaskIdAndPlayerId($task['id'], $playerId);
		$this->assertNotEmpty($log);
	}

	public function testReportPostNotJoined() {
		$playerId = DEVELOPER_1_ID;

		$this->controllerUtils->mockAuthUser($playerId);
		$event = $this->utils->Event->find('first', array('recursive' => 1, 'order' => 'Event.id'));
		$task = $event['EventTask'][0];

		$data = array(
			'EventTaskLog' => array(
				'event_id' => $event['Event']['id'],
				'event_task_id' => $task['id']
			)
		);
		$result = $this->testAction('/events/report/', array('return' => 'vars', 'data' => $data));

		$log = $this->utils->EventTaskLog->findByEventTaskIdAndPlayerId($task['id'], $playerId);
		$this->assertEmpty($log);
	}
	public function testCreateNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$this->testAction('/events/create/', array('method' => 'GET'));
	}

	public function testCreateGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$result = $this->testAction('/events/create', array('method' => 'GET', 'return' => 'vars'));
		$this->assertNotEmpty($result['activities']);
	}

	public function testCreatePost() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$data = array();
		$data['Event']['event_type_id']= '1';
		$data['Event']['name']= 'Mission';
		$data['Event']['description']= 'Description';
		$data['Event']['start']['month']= '04';
		$data['Event']['start']['day']= '05';
		$data['Event']['start']['year']= '2014';
		$data['Event']['end']['month']= '04';
		$data['Event']['end']['day']= '12';
		$data['Event']['end']['year']= '2014';
		$data['EventTask']['0']['name'] = 'Task Name 1';
		$data['EventTask']['0']['description'] = 'Task Description 1';
		$data['EventTask']['0']['xp'] = rand(1, 10);
		$data['EventTask']['1']['name'] = 'Task Name 2';
		$data['EventTask']['1']['description'] = 'Task Description 2';
		$data['EventTask']['1']['xp'] = rand(1, 10);
		$data['EventTask']['2']['name'] = '';
		$data['EventTask']['2']['description'] = '';
		$data['EventTask']['2']['xp'] = '';
		$data['EventTask']['3']['name'] = '';
		$data['EventTask']['3']['description'] = '';
		$data['EventTask']['3']['xp'] = '';
		$data['EventTask']['4']['name'] = '';
		$data['EventTask']['4']['description'] = '';
		$data['EventTask']['4']['xp'] = '';
		$data['EventTask']['5']['name'] = '';
		$data['EventTask']['5']['description'] = '';
		$data['EventTask']['5']['xp'] = '';
		$data['EventTask']['6']['name'] = '';
		$data['EventTask']['6']['description'] = '';
		$data['EventTask']['6']['xp'] = '';
		$data['EventTask']['7']['name'] = '';
		$data['EventTask']['7']['description'] = '';
		$data['EventTask']['7']['xp'] = '';
		$data['EventActivity']['0']['activity_id'] = '9';
		$data['EventActivity']['0']['count'] = '';
		$data['EventActivity']['1']['activity_id'] = '9';
		$data['EventActivity']['1']['count'] = '10';
		$data['EventActivity']['2']['activity_id'] = '';
		$data['EventActivity']['2']['count'] = '';
		$data['EventActivity']['3']['activity_id'] = '';
		$data['EventActivity']['3']['count'] = '';
		$data['EventActivity']['4']['activity_id'] = '';
		$data['EventActivity']['4']['count'] = '';
		$data['EventActivity']['5']['activity_id'] = '';
		$data['EventActivity']['5']['count'] = '';
		$data['EventActivity']['6']['activity_id'] = '';
		$data['EventActivity']['6']['count'] = '';
		$data['EventActivity']['7']['activity_id'] = '';
		$data['EventActivity']['7']['count'] = '';

		$result = $this->testAction('/events/create', array('data' => $data));
		$event = $this->utils->Event->recursive = 2;
		$event = $this->utils->Event->findByName('Mission');
		$this->assertNotEmpty($event);
		$this->assertEquals(2, count($event['EventTask']));
		$this->assertEquals(2, count($event['EventActivity']));
	}

	public function testCreatePostError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$data = array(
			'Event' => array(),
			'EventTask' => array(),
			'EventActivity' => array()
		);	

		$result = $this->testAction('/events/create', array('data' => $data));
		$event = $this->utils->Event->recursive = 2;
		$event = $this->utils->Event->findByName('Mission');
		$this->assertEmpty($event);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testEditNotScrumMaster() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$event = $this->utils->Event->find('first');
		$id = $event['Event']['id'];
		$this->testAction('/events/edit/' . $id, array('method' => 'GET'));
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$event = $this->utils->Event->find('first');
		$id = $event['Event']['id'];
		$result = $this->testAction('/events/edit/' . $id, array('method' => 'GET'));
		$this->assertNotEmpty($this->controller->request->data);
	}

	public function testEditGetNotFound() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$result = $this->testAction('/events/edit/0', array('method' => 'GET'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testEditPost() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID);
		$data = array();
		$id = 1;
		$data['Event']['id']= 1;
		$data['Event']['event_type_id']= '1';
		$data['Event']['name']= 'Mission';
		$data['Event']['description']= 'Description';
		$data['Event']['start']['month']= '04';
		$data['Event']['start']['day']= '05';
		$data['Event']['start']['year']= '2014';
		$data['Event']['end']['month']= '04';
		$data['Event']['end']['day']= '12';
		$data['Event']['end']['year']= '2014';
		$data['EventTask']['0']['name'] = 'Task Name 1';
		$data['EventTask']['0']['description'] = 'Task Description 1';
		$data['EventTask']['0']['xp'] = rand(1, 10);
		$data['EventTask']['1']['name'] = 'Task Name 2';
		$data['EventTask']['1']['description'] = 'Task Description 2';
		$data['EventTask']['1']['xp'] = rand(1, 10);
		$data['EventTask']['2']['name'] = '';
		$data['EventTask']['2']['description'] = '';
		$data['EventTask']['2']['xp'] = '';
		$data['EventTask']['3']['name'] = '';
		$data['EventTask']['3']['description'] = '';
		$data['EventTask']['3']['xp'] = '';
		$data['EventTask']['4']['name'] = '';
		$data['EventTask']['4']['description'] = '';
		$data['EventTask']['4']['xp'] = '';
		$data['EventTask']['5']['name'] = '';
		$data['EventTask']['5']['description'] = '';
		$data['EventTask']['5']['xp'] = '';
		$data['EventTask']['6']['name'] = '';
		$data['EventTask']['6']['description'] = '';
		$data['EventTask']['6']['xp'] = '';
		$data['EventTask']['7']['name'] = '';
		$data['EventTask']['7']['description'] = '';
		$data['EventTask']['7']['xp'] = '';
		$data['EventActivity']['0']['activity_id'] = '9';
		$data['EventActivity']['0']['count'] = '';
		$data['EventActivity']['1']['activity_id'] = '9';
		$data['EventActivity']['1']['count'] = '10';
		$data['EventActivity']['2']['activity_id'] = '';
		$data['EventActivity']['2']['count'] = '';
		$data['EventActivity']['3']['activity_id'] = '';
		$data['EventActivity']['3']['count'] = '';
		$data['EventActivity']['4']['activity_id'] = '';
		$data['EventActivity']['4']['count'] = '';
		$data['EventActivity']['5']['activity_id'] = '';
		$data['EventActivity']['5']['count'] = '';
		$data['EventActivity']['6']['activity_id'] = '';
		$data['EventActivity']['6']['count'] = '';
		$data['EventActivity']['7']['activity_id'] = '';
		$data['EventActivity']['7']['count'] = '';

		$result = $this->testAction('/events/edit/' . $id, array('data' => $data));
		$event = $this->utils->Event->recursive = 2;
		$event = $this->utils->Event->findByName('Mission');
		$this->assertNotEmpty($event);
		$this->assertEquals(2, count($event['EventTask']));
		$this->assertEquals(2, count($event['EventActivity']));
	}


	public function testIndex() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/events/', array('return' => 'vars'));
		$activeEvents = $result['activeEvents'];
		$pastEvents = $result['pastEvents'];
		$futureEvents = $result['futureEvents'];
		$this->assertNotEmpty($activeEvents);
		$this->assertNotEmpty($pastEvents);
		$this->assertNotEmpty($futureEvents);
	}

	public function testDetails() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$event = $this->utils->Event->find('first');
		$id = $event['Event']['id'];
		$result = $this->testAction('/events/details/' . $id, array('return' => 'vars'));
		$this->assertEquals($id, $result['event']['Event']['id']);
	}

	public function testDetailsNotFound() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$result = $this->testAction('/events/details/0', array('return' => 'vars'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testJoinInvalidEvent() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$this->testAction('/events/join/0');
		$this->assertNotNull($this->controller->flashError);
	}

	public function testJoinMissionFailuredLowLevel() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$event = $this->utils->Event->find('first', array('conditions' => array('Event.event_type_id' => EVENT_TYPE_MISSION)));
		$id = $event['Event']['id'];
		$this->testAction('/events/join/' . $id);
		$joinLog = $this->utils->EventJoinLog->findByPlayerIdAndEventId(DEVELOPER_1_ID, $id);
		$this->assertEmpty($joinLog);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testJoinChallengeFailuredLowLevel() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$event = $this->utils->Event->find('first', array('conditions' => array('Event.event_type_id' => EVENT_TYPE_CHALLENGE)));
		$id = $event['Event']['id'];
		$this->testAction('/events/join/' . $id);
		$joinLog = $this->utils->EventJoinLog->findByPlayerIdAndEventId(DEVELOPER_1_ID, $id);
		$this->assertEmpty($joinLog);
		$this->assertNotNull($this->controller->flashError);
	}

	public function testJoinMissionSuccess() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		//Avança o jogador para um nível alto
		$this->utils->XpLog->save(array('player_id' => DEVELOPER_1_ID, 'xp' => 10000));
		$event = $this->utils->Event->find('first', array('conditions' => array('Event.event_type_id' => EVENT_TYPE_MISSION)));
		$id = $event['Event']['id'];
		$this->testAction('/events/join/' . $id);
		$joinLog = $this->utils->EventJoinLog->findByPlayerIdAndEventId(DEVELOPER_1_ID, $id);
		$this->assertNotEmpty($joinLog);
	}

	public function testJoinChallengeSuccess() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		//Avança o jogador para um nível alto
		$this->utils->XpLog->save(array('player_id' => DEVELOPER_1_ID, 'xp' => 10000));
		$event = $this->utils->Event->find('first', array('conditions' => array('Event.event_type_id' => EVENT_TYPE_CHALLENGE)));
		$id = $event['Event']['id'];
		$this->testAction('/events/join/' . $id);
		$joinLog = $this->utils->EventJoinLog->findByPlayerIdAndEventId(DEVELOPER_1_ID, $id);
		$this->assertNotEmpty($joinLog);
	}

	public function testJoinTwice() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_1_ID);
		$event = $this->utils->Event->find('first');
		$id = $event['Event']['id'];
		$this->utils->EventJoinLog->save(array(
			'event_id' => $id,
			'player_id' => DEVELOPER_1_ID
		));
		$this->testAction('/events/join/' . $id);
		$this->assertNotNull($this->controller->flashError);
	}
}