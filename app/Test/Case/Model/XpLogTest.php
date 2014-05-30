<?php

App::uses('TestUtils', 'Lib');

class XpLogTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateTeams();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateEvents();
		$this->utils->generateEventTasks();
		$this->utils->generateEventTaskLogs();
		$this->utils->generateEventJoinLogs();
	}
	
	public function testSaveAddedXP(){
		$playerId = DEVELOPER_ID_1;
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
		$playerId = DEVELOPER_ID_2;
		$eventTask = $this->utils->EventTaskLog->find('first');
		$eventTaskId = $eventTask['EventTask']['id'];
		$this->utils->XpLog->_eventTaskReported($playerId, $eventTaskId);		
		$xpLog = $this->utils->XpLog->findByEventTaskIdAndPlayerId($eventTaskId, $playerId);
		$this->assertNotEmpty($xpLog);
	}

	public function testEventTaskReviewed() {
		$eventTask = $this->utils->EventTaskLog->find('first');
		$eventTaskId = $eventTask['EventTask']['id'];
		$this->utils->XpLog->_eventTaskReviewed(SCRUMMASTER_ID_1, $eventTaskId);		
		$xpLog = $this->utils->XpLog->findByEventTaskIdReviewedAndPlayerId($eventTaskId, SCRUMMASTER_ID_1);
		$this->assertNotEmpty($xpLog);
	}

	public function testEventTaskReviewedEventTaskNotFound() {
		try {
			$this->utils->XpLog->_eventTaskReviewed(SCRUMMASTER_ID_1, 0);		
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('EventTask not found',  $ex->getMessage());
		}
	}

	public function testEventTaskReportedEventTaskNotFound() {
		$playerId = DEVELOPER_ID_2;
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
		$playerId = DEVELOPER_ID_1;
		$activity = $this->utils->Activity->findByXp(1000);
		$activityId = $activity['Activity']['id'];

		$playerBefore = $this->utils->Player->findById($playerId);
		$countPlayers = 4;

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

	public function testActivityReportedUnlockedMissions() {
		$playerId = DEVELOPER_ID_1;
		$activity = $this->utils->Activity->findByXp(XP_TO_REACH_LEVEL_10);
		$activityId = $activity['Activity']['id'];

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
		$this->assertEquals(3, count ($otherPlayersNotifications));
	}

	public function testActivityReportedUnlockedChallenges() {
		$playerId = DEVELOPER_ID_1;
		$activity = $this->utils->Activity->findByXp(XP_TO_REACH_LEVEL_20);
		$activityId = $activity['Activity']['id'];

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
		$this->assertEquals(3, count ($otherPlayersNotifications));
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
			$this->utils->XpLog->_activityReported(DEVELOPER_ID_1, 0);
			$this->fail();
		} catch (Exception $ex) {
			$this->assertEquals('Activity not found', $ex->getMessage());
		}
	}

	public function testEventJoined() {
		$event = $this->utils->Event->allActive(SCRUMMASTER_ID_1)[0];
		$eventId = $event['Event']['id'];
		$this->utils->XpLog->_eventJoined(DEVELOPER_ID_1, $eventId);
		$xpLog = $this->utils->XpLog->findByPlayerIdAndEventIdJoined(DEVELOPER_ID_1, $eventId);
		$this->assertEquals(EVENT_JOIN_XP, $xpLog['XpLog']['xp']);
	}

	public function testEventCompleted() {
		$event = $this->utils->Event->allActive(SCRUMMASTER_ID_1)[0];
		$eventId = $event['Event']['id'];
		$this->utils->XpLog->_eventCompleted(DEVELOPER_ID_1, $eventId);
		$xpLog = $this->utils->XpLog->findByPlayerIdAndEventIdCompleted(DEVELOPER_ID_1, $eventId);
		$this->assertEquals($event['Event']['xp'], $xpLog['XpLog']['xp']);
	}

	public function testEventCompletedEventNotFound() {
		try {
			$this->utils->XpLog->_eventCompleted(DEVELOPER_ID_1, 0);
			$this->fail();
		} catch (ModelException $ex) {
			$this->assertEquals('Event not found.', $ex->getMessage());
		}
	}

	public function testActivityReviewed() {
		$activity = $this->utils->Activity->find('first');
		$activityId = $activity['Activity']['id'];
		$this->utils->XpLog->_activityReviewed(SCRUMMASTER_ID_1, $activityId);
		$xpLog = $this->utils->XpLog->findByActivityIdReviewed($activityId);
		$this->assertNotEmpty($xpLog);
	}

	public function testActivityReviewedNotFound() {
		try {
			$this->utils->XpLog->_activityReviewed(SCRUMMASTER_ID_1, 0);
			$this->fail();	
		} catch (Exception $ex) {
			$this->assertEquals('Activity not found', $ex->getMessage());
		}
	}

	public function testLevelUpNotification() {
		$playerId = DEVELOPER_ID_1;
		$playerBefore = $this->utils->Player->findById($playerId);
		$xp = 20;
		// Raise the player xp points (to level 5)
		$playerUpdate = array('Player' => array(
			'id' => $playerId,
			'xp' => $playerBefore['Player']['xp'] + $xp
		));
		$this->utils->Player->save($playerUpdate);
		$this->utils->XpLog->_levelUpNotification($playerBefore, $xp);
		$notification = $this->utils->Notification->find('first', array(
			'conditions' => array(
				'Notification.player_id' => $playerId,
				'Notification.title LIKE' => '%Level Up%'
			)
		));
		$this->assertNotEmpty($notification);
	}
}