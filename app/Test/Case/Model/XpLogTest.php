<?php

App::uses('TestUtils', 'Lib');

class XpLogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generatePlayers();
		$this->utils->generateEvents();
		$this->utils->generateEventTasks();
		$this->utils->generateEventTaskLogs();
		$this->utils->generateEventJoinLogs();
	}
	
	public function testSaveAddedXP(){
		$playerId = DEVELOPER_1_ID;
		$xp = 10;

		$data = array(
			'player_id' => $playerId,
			'xp' => $xp
		);

		$playerBefore = $this->utils->Player->findById($playerId);
		$this->assertNotEmpty($this->utils->XpLog->save($data));
		$xpLog = $this->utils->XpLog->findByPlayerId($playerId);

		$this->assertEquals((int)$xp, $xpLog['Player']['xp'] - $playerBefore['Player']['xp']);

		$this->assertEmpty($this->utils->Notification->findAllByPlayerId($playerId));
	}

	public function testEventTaskReported() {
		$playerId = DEVELOPER_2_ID;
		$eventTask = $this->utils->EventTaskLog->find('first');
		$eventTaskId = $eventTask['EventTask']['id'];
		$this->utils->XpLog->_eventTaskReported($playerId, $eventTaskId);		
		$xpLog = $this->utils->XpLog->findByEventTaskIdAndPlayerId($eventTaskId, $playerId);
		$this->assertNotEmpty($xpLog);
	}

	public function testEventTaskReviewed() {
		$eventTask = $this->utils->EventTaskLog->find('first');
		$eventTaskId = $eventTask['EventTask']['id'];
		$this->utils->XpLog->_eventTaskReviewed($eventTaskId);		
		$xpLog = $this->utils->XpLog->findByEventTaskIdReviewedAndPlayerId($eventTaskId, SCRUMMASTER_ID);
		$this->assertNotEmpty($xpLog);
	}

	public function testEventTaskReviewedEventTaskNotFound() {
		try {
			$this->utils->XpLog->_eventTaskReviewed(0);		
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('EventTask not found',  $ex->getMessage());
		}
	}

	public function testEventTaskReportedEventTaskNotFound() {
		$playerId = DEVELOPER_2_ID;
		try {
			$this->utils->XpLog->_eventTaskReported($playerId, 0);		
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('EventTask not found',  $ex->getMessage());
		}
	}

	public function testEventTaskReportedPlayerNotFound() {
		$eventTask = $this->utils->EventTaskLog->find('first');
		$eventTaskId = $eventTask['EventTask']['id'];
		try {
			$this->utils->XpLog->_eventTaskReported(0, $eventTaskId);		
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Player not found',  $ex->getMessage());
		}
	}

	public function testActivityReported() {
		$playerId = DEVELOPER_1_ID;
		$activity = $this->utils->Activity->findByXp(1000);
		$activityId = $activity['Activity']['id'];

		$countPlayers = $this->utils->Player->find('count');
		$playerBefore = $this->utils->Player->findById($playerId);

		$this->utils->XpLog->_activityReported($playerId, $activityId);

		$xpLog = $this->utils->XpLog->findByPlayerId($playerId);

		$levelUp = $xpLog['Player']['level'] > $playerBefore['Player']['level'];
		$this->assertTrue($levelUp);

		$playerNotifications = $this->utils->Notification->findAllByPlayerId($playerId);
		$this->assertNotEmpty($playerNotifications);

		$otherPlayersNotifications = $this->utils->Notification->find('all', array(
			'conditions' => array(
				'Player.id <>' => $playerId
			)
		));
		$this->assertEquals($countPlayers - 1, count ($otherPlayersNotifications));
	}

	public function testActivityReportedUnlockedMission() {
		$playerId = DEVELOPER_1_ID;
		$activity = $this->utils->Activity->findByXp(XP_TO_REACH_LEVEL_10);
		$activityId = $activity['Activity']['id'];

		$countPlayers = $this->utils->Player->find('count');
		$playerBefore = $this->utils->Player->findById($playerId);

		$this->utils->XpLog->_activityReported($playerId, $activityId);

		$xpLog = $this->utils->XpLog->findByPlayerId($playerId);

		$levelUp = $xpLog['Player']['level'] > $playerBefore['Player']['level'];
		$this->assertTrue($levelUp);

		$playerNotifications = $this->utils->Notification->findAllByPlayerId($playerId);
		$this->assertNotEmpty($playerNotifications);

		$otherPlayersNotifications = $this->utils->Notification->find('all', array(
			'conditions' => array(
				'Player.id <>' => $playerId
			)
		));
		$this->assertEquals($countPlayers - 1, count ($otherPlayersNotifications));
	}

	public function testActivityReportedUnlockedChallenge() {
		$playerId = DEVELOPER_1_ID;
		$activity = $this->utils->Activity->findByXp(XP_TO_REACH_LEVEL_20);
		$activityId = $activity['Activity']['id'];

		$countPlayers = $this->utils->Player->find('count');
		$playerBefore = $this->utils->Player->findById($playerId);

		$this->utils->XpLog->_activityReported($playerId, $activityId);

		$xpLog = $this->utils->XpLog->findByPlayerId($playerId);
		$levelUp = $xpLog['Player']['level'] > $playerBefore['Player']['level'];
		$this->assertTrue($levelUp);

		$playerNotifications = $this->utils->Notification->findAllByPlayerId($playerId);
		$this->assertNotEmpty($playerNotifications);

		$otherPlayersNotifications = $this->utils->Notification->find('all', array(
			'conditions' => array(
				'Player.id <>' => $playerId
			)
		));
		$this->assertEquals($countPlayers - 1, count ($otherPlayersNotifications));
	}

	public function testActivityReportedPlayerNotFound() {
		try {
			$activity = $this->utils->Activity->find('first');
			$activityId = $activity['Activity']['id'];
			$this->utils->XpLog->_activityReported(0, $activityId);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Player not found', $ex->getMessage());
		}
	}

	public function testActivityReportedActivityNotFound() {
		try {
			$this->utils->XpLog->_activityReported(DEVELOPER_1_ID, 0);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Activity not found', $ex->getMessage());
		}
	}

	public function testEventJoined() {
		$event = $this->utils->Event->allActive()[0];
		$eventId = $event['Event']['id'];
		$this->utils->XpLog->_eventJoined(DEVELOPER_1_ID, $eventId);
		$xpLog = $this->utils->XpLog->findByPlayerIdAndEventIdJoined(DEVELOPER_1_ID, $eventId);
		$this->assertEquals(EVENT_JOIN_XP, $xpLog['XpLog']['xp']);
	}

	public function testEventCompleted() {
		$event = $this->utils->Event->allActive()[0];
		$eventId = $event['Event']['id'];
		$this->utils->XpLog->_eventCompleted(DEVELOPER_1_ID, $eventId);
		$xpLog = $this->utils->XpLog->findByPlayerIdAndEventIdCompleted(DEVELOPER_1_ID, $eventId);
		$this->assertEquals($event['Event']['xp'], $xpLog['XpLog']['xp']);
	}

	public function testEventCompletedEventNotFound() {
		try {
			$this->utils->XpLog->_eventCompleted(DEVELOPER_1_ID, 0);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Event not found.', $ex->getMessage());
		}
	}

	public function testActivityReviewed() {
		$activity = $this->utils->Activity->find('first');
		$activityId = $activity['Activity']['id'];
		$this->utils->XpLog->_activityReviewed($activityId);
		$xpLog = $this->utils->XpLog->findByActivityIdReviewed($activityId);
		$this->assertNotEmpty($xpLog);
	}

	public function testActivityReviewedNotFound() {
		try {
			$this->utils->XpLog->_activityReviewed(0);
			$this->fail();	
		} catch (Exception $ex) {
			$this->assertEquals('Activity not found', $ex->getMessage());
		}
	}
}