<?php

App::uses('TestUtils', 'Lib');

class PlayerActivitySummaryTest extends CakeTestCase {

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
		$playerId = PLAYER_ID_1;
		$result = $this->utils->PlayerActivitySummary->allFromPlayer($playerId);
		$this->assertNotEmpty($result, 'No log data to test');
		foreach ($result as $row) {
			$this->assertEquals($row['PlayerActivitySummary']['player_id'], $playerId);
			$fields = array(
  				'player_id',
		    	'player_name',
		    	'count',
		    	'activity_id',
		    	'log_reviewed',
		    	'activity_name',
		    	'activity_description',
		    	'domain_id',
		    	'domain_name',
		    	'domain_abbr',
		    	'domain_color'
			);
			$this->assertEquals($fields, array_keys($row['PlayerActivitySummary']));
		}
	}

	public function testAllFromPlayerWithDomain() {
		$domain = $this->utils->Domain->find('first');
		$playerId = PLAYER_ID_1;
		$domainId = $domain['Domain']['id'];
		$result = $this->utils->PlayerActivitySummary->allFromPlayer($playerId, $domainId);
		$this->assertNotEmpty($result, 'No log data to test');
		foreach ($result as $row) {
			$this->assertEquals($row['PlayerActivitySummary']['player_id'], $playerId);
			$this->assertEquals($row['PlayerActivitySummary']['domain_id'], $domainId);
			$fields = array(
  				'player_id',
		    	'player_name',
		    	'count',
		    	'activity_id',
		    	'log_reviewed',
		    	'activity_name',
		    	'activity_description',
		    	'domain_id',
		    	'domain_name',
		    	'domain_abbr',
		    	'domain_color'
			);
			$this->assertEquals($fields, array_keys($row['PlayerActivitySummary']));
		}
	}

	public function testCountFromPlayer() {
		$player = $this->utils->Player->find('first');
		$this->assertNotEmpty($player, 'Player not found');
		$playerId = $player['Player']['id'];
		$result = $this->utils->PlayerActivitySummary->countFromPlayer($playerId);
		$this->assertEquals(4, $result);
	}

}