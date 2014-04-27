<?php

App::uses('TestUtils', 'Lib');

class EventCompleteLogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateEvents();
	}

	public function testLog() {
		$playerId = DEVELOPER_1_ID;
		$event = $this->utils->Event->find('first');
		$eventId = $event['Event']['id'];
		$this->utils->EventCompleteLog->_log($playerId, $eventId);
		$this->assertNotEmpty($this->utils->EventCompleteLog->findByPlayerIdAndEventId($playerId, $eventId));
	}

	public function testLogException() {
		try {
			$this->utils->EventCompleteLog->_log('', '');
			$this->fail();
		} catch (InternalErrorException $ex) {
			$this->assertEquals('Could not save EventCompleteLog', $ex->getMessage());
		}

	}
}