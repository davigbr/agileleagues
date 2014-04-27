<?php

App::uses('TestUtils', 'Lib');

class PlayerTotalActivityCoinsTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
	}

	public function testCoins() {
		$all = $this->utils->PlayerTotalActivityCoins->all();
		$this->assertNotEmpty($all);
		foreach ($all as $row) {
			$this->assertEquals(4, (int)$row['PlayerTotalActivityCoins']['coins']);
		}
	}
}