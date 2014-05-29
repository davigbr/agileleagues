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
		$teams = $this->utils->Team->simpleFromScrumMaster(SCRUMMASTER_ID);
		$this->assertEquals(2, count($teams));
	}
}