<?php

App::uses('TestUtils', 'Lib');

class LogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateLogs();
		$this->utils->generateLogsNotReviewed();
		$this->utils->Log->recursive = 2;
	}

	public function testHash() {
		$log = array('Log' => array(
			'activity_id' => 1,
			'description' => 'blablablabla',
			'acquired' => '2014-01-01',
			'player_id' => 1,
		));

		$this->assertEquals(
			'127a989067316581a27be22604301dd603bc79adc821911222bf49b27a3c3e8f', 
			$this->utils->Log->hash($log));
	}

	public function testCount() {
		$this->assertEquals(16, $this->utils->Log->count(GAME_MASTER_ID_1));
		$this->assertEquals(0, $this->utils->Log->count(GAME_MASTER_ID_2));
	}

	public function testBeforeSave() {
		$playerId = PLAYER_ID_1;
		$activity = $this->utils->Activity->find('first');
		$data = array(
			'Log' => array(
				'activity_id' => $activity['Activity']['id'], 
				'acquired' => date('Y-m-d'),
				'description' => 'anything',
				'player_id' => $playerId
		));

		$this->utils->Log->create();
		$saved = $this->utils->Log->save($data);
		$log = $this->utils->Log->findById($this->utils->Log->id);

		$this->assertEquals($activity['Activity']['domain_id'], $log['Log']['domain_id']);
		$this->assertEquals($activity['Activity']['xp'], $log['Log']['xp']);
	}

	public function testReviewedAcceptIncrementedActivityReportedCounter() {
		$log = $this->utils->Log->find('first', array('conditions' => 'Log.reviewed IS NULL'));
		$this->assertNotEmpty($log);
		$this->utils->Log->_review($log['Log']['id'], PLAYER_ID_2, 'accept');
		$activity = $this->utils->Activity->findById($log['Log']['activity_id']);
		$this->assertEquals($activity['Activity']['reported'], $log['Activity']['reported'] + 1);
	}

	public function testWhenReviewedGeneratedXpLogToPlayer() {
		$log = $this->utils->Log->find('first', array('conditions' => 'Log.reviewed IS NULL'));
		$this->assertNotEmpty($log);
		$this->utils->Log->_review($log['Log']['id'], PLAYER_ID_2, 'accept');

		$xpLog = $this->utils->XpLog->findByPlayerIdAndLogId(
			$log['Log']['player_id'], 
			$log['Log']['id']
		);
		$this->assertNotNull($xpLog);
	}

	public function testReviewAcceptShouldGenerateXpLogToReviewers() {
		$log = $this->utils->Log->find('first', array('conditions' => array(
			'Log.reviewed IS NULL',
			'Log.player_id' => PLAYER_ID_1
		)));
		$this->assertNotEmpty($log);
		$this->utils->LogVote->saveMany(array(
			array(
				'log_id' => $log['Log']['id'],
				'vote' => 1,
				'player_id' => PLAYER_ID_2
			),
			array(
				'log_id' => $log['Log']['id'],
				'vote' => 1,
				'player_id' => PLAYER_ID_3
			)
		));

		$this->utils->Log->_review($log['Log']['id'], PLAYER_ID_3, 'accept');

		$xpLogPlayer2 = $this->utils->XpLog->findByPlayerIdAndLogIdReviewed(PLAYER_ID_2, $log['Log']['id']);
		$xpLogPlayer3 = $this->utils->XpLog->findByPlayerIdAndLogIdReviewed(PLAYER_ID_3, $log['Log']['id']);

		$expectedXp = floor($log['Log']['xp'] * ACCEPTANCE_XP_MULTIPLIER);
		$this->assertEquals($expectedXp, $xpLogPlayer2['XpLog']['xp']);
		$this->assertEquals($expectedXp, $xpLogPlayer3['XpLog']['xp']);
	}

	public function testAcquiredFutureRule() {
		$log = $this->utils->Log->find('first');
		$date = new DateTime();
		$date->modify('+1 day');
		$log['Log']['acquired'] = $date->format('Y-m-d');
		$this->utils->Log->data = $log;
		$this->assertFalse($this->utils->Log->acquiredFutureRule());
	}

	public function testAcquiredPastRule() {
		$log = $this->utils->Log->find('first');
		$date = new DateTime();
		$date->modify('-8 day');
		$log['Log']['acquired'] = $date->format('Y-m-d');
		$this->utils->Log->data = $log;
		$this->assertFalse($this->utils->Log->acquiredPastRule());
	}

	public function testAllNotReviewed() {
		$result = $this->utils->Log->allNotReviewed();
		if (empty($result)) {
			$this->fail('No data to test');
		}
		foreach ($result as $row) {
			$this->assertEquals(null, $row['Log']['reviewed']);
		}
	}

	public function testPlayerCount() {
		$player = $this->utils->Player->find('first');
		$this->assertNotEmpty($player, 'Player not found');
		$result = $this->utils->Log->playerCount($player['Player']['id']);
		$this->assertTrue(is_int($result));
	}

	public function testTimeline() {
		$logs = $this->utils->Log->timeline();
		$this->assertEquals(16, count($logs));
	}

	public function testAverage() {
		$avg = $this->utils->Log->average(GAME_MASTER_ID_1);
		$this->assertEquals(4.0, $avg);
	}

	public function testAverageSM2() {
		$avg = $this->utils->Log->average(GAME_MASTER_ID_2);
		$this->assertEquals(0, $avg);
	}

	public function testSimpleReviewed() {
		$simple = $this->utils->Log->simpleReviewed();
		$this->assertEquals(8, count($simple));
	}

	public function testAllReviewed() {
		$all = $this->utils->Log->allReviewed();
		$this->assertEquals(8, count($all));
	}

	public function testPendingFromPlayer() {
		$logs = $this->utils->Log->allPendingFromPlayer(1);
		$this->assertEquals(4, count($logs));
	}

	public function testCountNotReviewed() {
		$count = $this->utils->Log->countNotReviewed();
		$this->assertEquals(8, $count);
	}

	public function testCountPendingFromPlayer() {
		$count = $this->utils->Log->countPendingFromPlayer(1);
		$this->assertEquals(4, $count);
	}

	public function testCountPendingFromTeamNotFromPlayer() {
		$this->assertEquals(4, $this->utils->Log->countPendingFromTeamNotFromPlayer(PLAYER_ID_1));
	}

	public function testAllPendingFromTeamNotFromPlayer() {
		$this->assertEquals(4, count($this->utils->Log->allPendingFromTeamNotFromPlayer(PLAYER_ID_1)));
	}


	public function testReviewNotExists() {
		try {
			$this->assertEquals(false, $this->utils->Log->_review(1000, 0, 'accept'));
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Log not found', $ex->getMessage());
		}
	}

	public function testReviewWithId() {
		$log = $this->utils->Log->findByReviewed(null);
		$this->assertNull($log['Log']['reviewed']);
		$id = $log['Log']['id'];
		$this->utils->Log->_review($id, PLAYER_ID_2, 'accept');
		$log = $this->utils->Log->read();
		$this->assertNotNull($log['Log']['reviewed']);
	}

	public function testReviewFirstTimeActivity() {
        $activityId = 99;
        $playerId = PLAYER_ID_1;
        $this->utils->Activity->save(array(
        	'id' => $activityId, 
        	'name' => 'Activity 99', 
        	'reported' => 1, 
        	'domain_id' => 1, 
        	'xp' => rand(5, 100)
    	));

    	$this->utils->Log->save(array(
            'activity_id' => $activityId, 
            'player_id' => $playerId, 
            'acquired' => date('Y-m-d')
        ));

        $this->utils->Log->_review($this->utils->Log->id, PLAYER_ID_2, 'accept');
        $notifications = $this->utils->Notification->find('all', array(
        	'conditions' => array(
        		'Notification.title' => 'First Time Completion'
    		)
    	));
        $this->assertEquals(3, count($notifications));
	}

	public function testReport() {
		$logs = array(
			array('Log' => array(
				'activity_id' => 1,
				'player_id' => PLAYER_ID_1
			)),
			array('Log' => array(
				'activity_id' => 2,
				'player_id' => PLAYER_ID_1
			))
		);

		$this->utils->Log->report($logs);
		$activities = $this->utils->Activity->findAllById(array(1, 2));
		$this->assertNotEmpty($activities);

		foreach ($activities as $activity) {
			$this->assertTrue($activity['Activity']['first_report'] !== null);
			$this->assertTrue($activity['Activity']['last_report'] !== null);
			$this->assertTrue($activity['Activity']['first_report'] !== null);
			$this->assertEquals((int)$activity['Activity']['times_reported'], 1);
			$this->assertEquals((int)$activity['Activity']['reports_per_day'], 1);
		}
	}

	public function testReviewNotFirstTimeActivity() {
        $playerId = PLAYER_ID_1;
        $log = $this->utils->Log->findByPlayerId($playerId);
        $this->assertNotEmpty($log);
        $activityId = $log['Log']['activity_id'];

    	$this->utils->Log->save(array(
            'activity_id' => $activityId, 
            'player_id' => $playerId, 
            'acquired' => date('Y-m-d'),
            'description' => 'hahaha'
        ));

        $this->utils->Log->_review($this->utils->Log->id, PLAYER_ID_2, 'accept');
        $notifications = $this->utils->Notification->find('all', array(
        	'conditions' => array(
        		'Notification.title LIKE' => '%first%'
    		)
    	));
        $this->assertEquals(0, count($notifications));
	}

}