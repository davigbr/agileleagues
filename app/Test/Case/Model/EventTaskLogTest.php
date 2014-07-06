<?php

App::uses('TestUtils', 'Lib');

class EventTaskLogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateEvents();
		$this->utils->generateEventTasks();
		$this->utils->generateEventJoinLogs();
		$this->utils->generateEventTaskLogs();
	}

	public function testEventAlreadyCompletedRule() {
		$playerId = PLAYER_ID_1;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];
		$this->utils->EventTaskLog->data = array(
			'EventTaskLog' => array(
				'player_id' => $playerId,
				'event_id' => $eventId
			)
		);
		$this->assertTrue($this->utils->EventTaskLog->eventAlreadyCompletedRule());
	}

	public function testJoinBeforeReportingRule() {
		$playerId = PLAYER_ID_1;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];
		$this->utils->EventTaskLog->data = array(
			'EventTaskLog' => array(
				'player_id' => $playerId,
				'event_id' => $eventId
			)
		);
		$this->assertFalse($this->utils->EventTaskLog->joinBeforeReportingRule());
	}

	public function testUniqueTaskPerPlayerRule() {
		$playerId = PLAYER_ID_1;
		$eventTask = $this->utils->EventTask->find('first');
		$eventTaskId = $eventTask['Event']['id'];
		$this->utils->EventTaskLog->data = array(
			'EventTaskLog' => array(
				'player_id' => $playerId,
				'event_task_id' => $eventTaskId
			)
		);
		$this->assertTrue($this->utils->EventTaskLog->uniqueTaskPerPlayerRule());
	}

	public function testAllPendingFromPlayer() {
		$all = $this->utils->EventTaskLog->allPendingFromPlayer(PLAYER_ID_2);
		$this->assertEquals(3, count($all));
	}

	public function testCountPendingFromPlayer() {
		$count = $this->utils->EventTaskLog->countPendingFromPlayer(PLAYER_ID_2);
		$this->assertEquals(3, $count);
	}

	public function testCountNotReviewed() {
		$result = $this->utils->EventTaskLog->countNotReviewed();
		$this->assertEquals(3, $result);
	}

	public function testAllNotReviewed() {
		$result = $this->utils->EventTaskLog->allNotReviewed();
		$this->assertNotEmpty($result);
		foreach ($result as $row) {
			$this->assertEquals(null, $row['EventTaskLog']['reviewed']);
		}
	}

	public function testReviewWithoutId() {
		$log = $this->utils->EventTaskLog->findByReviewed(null);
		$this->assertNull($log['EventTaskLog']['reviewed']);
		$this->utils->EventTaskLog->review();
		$log = $this->utils->EventTaskLog->read();
		$this->assertNotNull($log['EventTaskLog']['reviewed']);
	}

	public function testReviewNotExists() {
		try {
			$this->assertEquals(false, $this->utils->EventTaskLog->review(1000));
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Log not found', $ex->getMessage());
		}
	}

	public function testReviewWithId() {
		$log = $this->utils->EventTaskLog->findByReviewed(null);
		$this->assertNull($log['EventTaskLog']['reviewed']);
		$id = $log['EventTaskLog']['id'];
		$this->utils->EventTaskLog->review($id);
		$log = $this->utils->EventTaskLog->read();
		$this->assertNotNull($log['EventTaskLog']['reviewed']);
	}

	public function testReviewWithIdAlreadyReviewed() {
		$log = $this->utils->EventTaskLog->findByReviewed(null);
		$this->assertNull($log['EventTaskLog']['reviewed']);
		$id = $log['EventTaskLog']['id'];
		$this->utils->EventTaskLog->review($id);
		try {
			$this->utils->EventTaskLog->review($id);
			$this->fail();
		} catch (Exception $ex){
			$this->assertEquals('Log already reviewed', $ex->getMessage());
		}
	}

}