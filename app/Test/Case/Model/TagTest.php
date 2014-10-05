<?php

App::uses('TestUtils', 'Lib');

class TagTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTags();
	}

	public function testAllActive() {
		$tags = $this->utils->Tag->allActive(GAME_MASTER_ID_1);
		$this->assertNotEmpty($tags);
		foreach ($tags as $tag) {
			$this->assertEqual(GAME_MASTER_ID_1, (int)$tag['Tag']['player_id_owner']);
			$this->assertEqual(0, (int)$tag['Tag']['inactive']);
		}
	}

	public function testSimpleActive() {
		$tags = $this->utils->Tag->simpleActive(GAME_MASTER_ID_1);
		$this->assertNotEmpty($tags);
	}

	public function testTopFromPlayer() {
		$this->utils->generatePlayers();
		$this->utils->generateTeams();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateLogTags();

		$tags = $this->utils->Tag->topFromPlayer(PLAYER_ID_1);
		$this->assertNotEmpty($tags);
		foreach ($tags as $tag) {
			$this->assertTrue(isset($tag['Tag']['reports']));
		}
	}

}