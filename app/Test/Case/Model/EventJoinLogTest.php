<?php

App::uses('TestUtils', 'Lib');

class EventJoinLogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateEvents();
	}

	public function testJoin() {
		$playerId = DEVELOPER_ID_1;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];

		$this->utils->EventJoinLog->join($playerId, $eventId);
		$this->assertNotEmpty($this->utils->EventJoinLog->findByPlayerIdAndEventId($playerId, $eventId));
	}

	public function testJoinException() {
		$playerId = DEVELOPER_ID_1;
		try {
			$this->utils->EventJoinLog->join($playerId, 0);
			$this->fail();
		} catch (Exception $ex) {

		}
	}
}