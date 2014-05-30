<?php

App::uses('TestUtils', 'Lib');

class TeamTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
	}

	public function testSimpleFromScrumMaster() {
		$teams = $this->utils->Team->simpleFromScrumMaster(SCRUMMASTER_ID_1);
		$this->assertEquals(2, count($teams));
	}

	public function testAllFromOwner() {
		$this->assertEquals(2, count($this->utils->Team->allFromOwner(SCRUMMASTER_ID_1)));
		$this->assertEquals(0, count($this->utils->Team->allFromOwner(SCRUMMASTER_ID_2)));
	}
}