<?php

App::uses('TestUtils', 'Lib');

class PlayerTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
	}

	public function testFreeDeveloperList() {
		$this->utils->generatePlayer('Developer');
		$list = $this->utils->Player->freeDeveloperList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testScrumMasterList() {
		$list = $this->utils->Player->scrumMasterList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testProductOwnerList() {
		$this->utils->generatePO('Product Owner');
		$list = $this->utils->Player->productOwnerList();
		$this->assertNotEmpty($list);
		foreach ($list as $id => $name) {
			$this->assertTrue(is_int($id));
			$this->assertTrue(is_string($name));
		}
	}

	public function testDevelopersCount() {
		$count = $this->utils->Player->developersCount();
		$this->assertEquals(2, $count);
	}

	public function testScrumMaster() {
		$sm = $this->utils->Player->_scrumMaster();
		$this->assertNotEmpty($sm);
		$this->assertEquals(PLAYER_TYPE_SCRUMMASTER, $sm['Player']['player_type_id']);
	}

	public function testScrumMasterNotFound() {
		$this->utils->Notification->query('DELETE FROM notification');
		$this->utils->XpLog->query('DELETE FROM xp_log');
		$this->utils->Player->delete(SCRUMMASTER_ID);
		try {
			$sm = $this->utils->Player->_scrumMaster();
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('ScrumMaster not found!', $ex->getMessage());
		}
	}

	public function testBeforeSave() {
		$player = array(
			'name' => 'New Player',
			'password' => '123456'
		);
		$this->utils->Player->save($player);
		$player = $this->utils->Player->read();
		$this->assertEquals('24bae80ca7f5a1fd95e9ae0388b7e79bdb9b7c0d', $player['Player']['password']);
	}

	public function testVirtualFields() {
		$player = $this->utils->Player->find('first');
		$this->assertEquals(500, (int)$player['Player']['xp']);
		$this->assertEquals(519, (int)$player['Player']['next_level_total_xp']);
		$this->assertEquals(237, (int)$player['Player']['next_level_xp']);
		$this->assertEquals(218, (int)$player['Player']['next_level_xp_completed']);
		$this->assertEquals(91.9831, (float)$player['Player']['progress']);
	}
	
	public function testDifferentActivitiesCompleted(){
		$activitiesByDomain = $this->utils->Player->differentActivitiesCompletedCount(1);
		$this->assertEquals(1, count($activitiesByDomain));
		foreach ($activitiesByDomain as $domainId => $count) {
			$this->assertEquals(4, (int)$count);
		}
	}

	public function testLevelAndXP() {
		$this->assertEquals(11, (int)$this->utils->Player->level(3162.2776601684));
		$this->assertEquals(3162, (int)$this->utils->Player->xp(10));
	}


	public function testRepeatPasswordRule() {
		$this->utils->Player->data = array(
			'Player' => array(
				'password' => '123456',
				'repeat_password' => '986532'
			)
		);
		$this->assertEquals(false, $this->utils->Player->repeatPasswordRule());

	}
}