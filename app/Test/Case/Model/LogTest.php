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

	public function testCount() {
		$this->assertEquals(16, $this->utils->Log->count(SCRUMMASTER_ID_1));
		$this->assertEquals(0, $this->utils->Log->count(SCRUMMASTER_ID_2));
	}

	public function testBeforeSave() {
		$playerId = DEVELOPER_ID_1;
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
		$this->utils->Log->_review($log['Log']['id'], DEVELOPER_ID_2, 'accept');
		$activity = $this->utils->Activity->findById($log['Log']['activity_id']);
		$this->assertEquals($activity['Activity']['reported'], $log['Activity']['reported'] + 1);
	}

	public function testWhenReviewedGeneratedXpLogToPlayer() {
		$log = $this->utils->Log->find('first', array('conditions' => 'Log.reviewed IS NULL'));
		$this->assertNotEmpty($log);
		$this->utils->Log->_review($log['Log']['id'], DEVELOPER_ID_2, 'accept');

		$xpLog = $this->utils->XpLog->findByPlayerIdAndActivityId(
			$log['Log']['player_id'], 
			$log['Log']['activity_id']
		);
		$this->assertNotNull($xpLog);
	}

	public function testReviewAcceptShouldGenerateXpLogToReviewers() {
		$log = $this->utils->Log->find('first', array('conditions' => array(
			'Log.reviewed IS NULL',
			'Log.player_id' => DEVELOPER_ID_1
		)));
		$this->assertNotEmpty($log);
		$this->utils->LogVote->saveMany(array(
			array(
				'log_id' => $log['Log']['id'],
				'vote' => 1,
				'player_id' => DEVELOPER_ID_2
			),
			array(
				'log_id' => $log['Log']['id'],
				'vote' => 1,
				'player_id' => DEVELOPER_ID_3
			)
		));

		$this->utils->Log->_review($log['Log']['id'], DEVELOPER_ID_3, 'accept');

		$xpLogPlayer2 = $this->utils->XpLog->findByPlayerIdAndLogIdReviewed(DEVELOPER_ID_2, $log['Log']['id']);
		$xpLogPlayer3 = $this->utils->XpLog->findByPlayerIdAndLogIdReviewed(DEVELOPER_ID_3, $log['Log']['id']);

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
		$avg = $this->utils->Log->average(SCRUMMASTER_ID_1);
		$this->assertEquals(4.0, $avg);
	}

	public function testAverageSM2() {
		$avg = $this->utils->Log->average(SCRUMMASTER_ID_2);
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
		$this->assertEquals(4, $this->utils->Log->countPendingFromTeamNotFromPlayer(DEVELOPER_ID_1));
	}

	public function testAllPendingFromTeamNotFromPlayer() {
		$this->assertEquals(4, count($this->utils->Log->allPendingFromTeamNotFromPlayer(DEVELOPER_ID_1)));
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
		$this->utils->Log->_review($id, DEVELOPER_ID_2, 'accept');
		$log = $this->utils->Log->read();
		$this->assertNotNull($log['Log']['reviewed']);
	}

	public function testReviewFirstTimeActivity() {
        $activityId = 99;
        $playerId = DEVELOPER_ID_1;
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

        $this->utils->Log->_review($this->utils->Log->id, DEVELOPER_ID_2, 'accept');
        $notifications = $this->utils->Notification->find('all', array(
        	'conditions' => array(
        		'Notification.title' => 'First Time Completion'
    		)
    	));
        $this->assertEquals(4, count($notifications));
	}

	public function testReviewNotFirstTimeActivity() {
        $playerId = DEVELOPER_ID_1;
        $log = $this->utils->Log->findByPlayerId($playerId);
        $this->assertNotEmpty($log);
        $activityId = $log['Log']['activity_id'];

    	$this->utils->Log->save(array(
            'activity_id' => $activityId, 
            'player_id' => $playerId, 
            'acquired' => date('Y-m-d')
        ));

        $this->utils->Log->_review($this->utils->Log->id, DEVELOPER_ID_2, 'accept');
        $notifications = $this->utils->Notification->find('all', array(
        	'conditions' => array(
        		'Notification.title LIKE' => '%first%'
    		)
    	));
        $this->assertEquals(0, count($notifications));
	}

}