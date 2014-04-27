<?php

App::uses('TestUtils', 'Lib');

class PlayerActivityCoinsTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateLogs();
	}

	public function testAllFromPlayer() {
		$playerId = DEVELOPER_1_ID;
		$result = $this->utils->PlayerActivityCoins->allFromPlayer($playerId);
		$this->assertNotEmpty($result, 'No log data to test');
		foreach ($result as $row) {
			$this->assertEquals($row['PlayerActivityCoins']['player_id'], $playerId);
			$fields = array(
  				'player_id',
		    	'player_name',
		    	'coins',
		    	'spent',
		    	'remaining',
		    	'activity_id',
		    	'log_reviewed',
		    	'activity_name',
		    	'activity_description',
		    	'domain_id',
		    	'domain_name',
		    	'domain_abbr',
		    	'domain_color'
			);
			$this->assertEquals($fields, array_keys($row['PlayerActivityCoins']));
		}
	}

	public function testAllFromPlayerWithDomain() {
		$domain = $this->utils->Domain->find('first');
		$playerId = DEVELOPER_1_ID;
		$domainId = $domain['Domain']['id'];
		$result = $this->utils->PlayerActivityCoins->allFromPlayer($playerId, $domainId);
		$this->assertNotEmpty($result, 'No log data to test');
		foreach ($result as $row) {
			$this->assertEquals($row['PlayerActivityCoins']['player_id'], $playerId);
			$this->assertEquals($row['PlayerActivityCoins']['domain_id'], $domainId);
			$fields = array(
  				'player_id',
		    	'player_name',
		    	'coins',
		    	'spent',
		    	'remaining',
		    	'activity_id',
		    	'log_reviewed',
		    	'activity_name',
		    	'activity_description',
		    	'domain_id',
		    	'domain_name',
		    	'domain_abbr',
		    	'domain_color'
			);
			$this->assertEquals($fields, array_keys($row['PlayerActivityCoins']));
		}
	}

	public function testCountFromPlayer() {
		$player = $this->utils->Player->find('first');
		$this->assertNotEmpty($player, 'Player not found');
		$playerId = $player['Player']['id'];
		$result = $this->utils->PlayerActivityCoins->countFromPlayer($playerId);
		$this->assertEquals(4, $result);
	}

}