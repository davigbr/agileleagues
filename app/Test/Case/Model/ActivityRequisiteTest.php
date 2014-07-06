<?php

App::uses('TestUtils', 'Lib');

class ActivityRequisiteTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
		$this->utils->generateTags();
		$this->utils->generateBadges();
	}

	public function testUpdateActivityRequisiteSummaryNotExists() {
		$this->generateActivityRequisite();
		$this->utils->ActivityRequisite->_updateActivityRequisiteSummary($this->log);
		$badgeSummary = $this->utils->ActivityRequisiteSummary->find('first');
		$this->assertEquals(1, (int)$badgeSummary['ActivityRequisiteSummary']['times']);
	}

	public function testUpdateActivityRequisiteSummaryAlreadyExists() {
		$this->generateActivityRequisite();
		$this->utils->ActivityRequisiteSummary->save(array(
			'player_id' => PLAYER_ID_1,
			'badge_id' => $this->badgeId,
			'activity_requisite_id' => $this->utils->ActivityRequisite->id,
			'times' => 1
		));
		$this->utils->ActivityRequisite->_updateActivityRequisiteSummary($this->log);
		$badgeSummary = $this->utils->ActivityRequisiteSummary->find('first');
		$this->assertEquals(2, (int)$badgeSummary['ActivityRequisiteSummary']['times']);
	}

	public function generateActivityRequisite() {
		$activity = $this->utils->Activity->find('first');
		$this->activityId = $activity['Activity']['id'];
		$badge = $this->utils->Badge->find('first');
		$this->badgeId = $badge['Badge']['id'];
		$tags = $this->utils->Tag->find('all');
		$this->tagId1 = $tags[0]['Tag']['id'];
		$this->tagId2 = $tags[1]['Tag']['id'];

		$this->utils->ActivityRequisite->save(array(
			'ActivityRequisite' => array(
				'activity_id' => $this->activityId,
				'badge_id' => $this->badgeId
			),
			'Tags' => array(
				'Tags' => array(
					0 => $this->tagId1,
					1 => $this->tagId2
				)
			)
		));
		$this->log = array(
			'Log' => array(
				'player_id' => PLAYER_ID_1,
				'activity_id' => $this->activityId
			),
			'Tags' => array(
				array('id' => $this->tagId1),
				array('id' => $this->tagId2)
			)
		);

	}

	public function testGetLogMatches() {
		$this->generateActivityRequisite();
		$activityRequisiteId = $this->utils->ActivityRequisite->id;
		$expectedMatches = array($activityRequisiteId);
		$this->assertEquals($expectedMatches, $this->utils->ActivityRequisite->_getLogMatches($this->log));
	}
}