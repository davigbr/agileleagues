<?php

App::uses('TestUtils', 'Lib');

class EventTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateEvents();
		$this->utils->generateEventTasks();
		$this->utils->generateEventActivities();
	}

	public function testCompleteCannotBeCompletedYet() {
		$playerId = DEVELOPER_1_ID;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];
		try {
			$this->utils->Event->complete($playerId, $eventId);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Event cannot be completed yet.', $ex->getMessage());
		}
	}

	public function testCompleteAlreadyCompleted() {
		$this->utils->generateEventActivityLogs();
		$playerId = DEVELOPER_2_ID;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];
		$this->utils->EventCompleteLog->_log($playerId, $eventId);
		try {
			$this->utils->Event->complete($playerId, $eventId);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Event already completed.', $ex->getMessage());
		}
	}

	public function testComplete() {
		$this->utils->generateEventActivityLogs();
		$this->utils->generateEventTaskLogs();
		$playerId = DEVELOPER_2_ID;
		$event = $this->utils->Event->find('first', array('order' => array('Event.id' => 'ASC')));
		$eventId = $event['Event']['id'];
		$this->utils->Event->complete($playerId, $eventId);
		$this->assertNotEmpty($this->utils->EventCompleteLog->findByPlayerIdAndEventId($playerId, $eventId));
	}


	public function testPlayerProgress() {
		$this->utils->generateEventActivityLogs();
		$playerId = DEVELOPER_2_ID;
		$event = $this->utils->Event->find('first', array('order' => array('Event.id' => 'ASC')));
		$eventId = $event['Event']['id'];
		$event = $this->utils->Event->playerProgress($playerId, $eventId);
		$this->assertEquals(50, $event['Event']['progress']);
	}

	public function testPlayerProgressEventNotFound() {
		$playerId = DEVELOPER_2_ID;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];
		try {
			$event = $this->utils->Event->playerProgress($playerId, 0);
		} catch (ModelException $ex) {
			$this->assertEquals('Event not found', $ex->getMessage());
		}
	}

	public function testSimpleActive() {
		$events = $this->utils->Event->simpleActive();
		foreach ($events as $key => $value) {
			$this->assertTrue(is_int($key));
			$this->assertTrue(is_string($value));
		}
		$this->assertEquals(2, count($events));
	}

	public function testAllActive() {
		$events = $this->utils->Event->allActive(1);
		$this->assertNotEmpty($events);
		$this->assertEquals(1, count($events));
		foreach ($events as $event) {
			$now = new DateTime(date('Y-m-d') . ' 00:00:00');
			$start = new DateTime($event['Event']['start']);
			$end = new DateTime($event['Event']['end']);
			$this->assertTrue($start <= $now);
			$this->assertTrue($end >= $now);
		}
	}

	public function testAllPast() {
		$events = $this->utils->Event->allPast(1);
		$this->assertNotEmpty($events);
		$this->assertEquals(1, count($events));
		foreach ($events as $event) {
			$now = new DateTime(date('Y-m-d') . ' 00:00:00');
			$start = new DateTime($event['Event']['start']);
			$end = new DateTime($event['Event']['end']);
			$this->assertTrue($start < $now);
			$this->assertTrue($end < $now);
		}
	}

	public function testAllFuture() {
		$events = $this->utils->Event->allFuture(1);
		$this->assertNotEmpty($events);
		$this->assertEquals(1, count($events));
		foreach ($events as $event) {
			$now = new DateTime(date('Y-m-d') . ' 00:00:00');
			$start = new DateTime($event['Event']['start']);
			$end = new DateTime($event['Event']['end']);
			$this->assertTrue($start > $now);
			$this->assertTrue($end > $now);
		}
	}

}