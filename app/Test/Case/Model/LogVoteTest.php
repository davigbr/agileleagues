<?php

App::uses('TestUtils', 'Lib');

class LogVoteTest extends CakeTestCase {

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
	}

	public function testSaveVotes() {
		$logs = $this->utils->Log->find('all', array('conditions' => array('Log.reviewed IS NULL')));
		$votes = array();
		foreach ($logs as $log) {
			$votes[] = array('LogVote' => array(
				'log_id' => (int)$log['Log']['id'],
				'player_id' => PLAYER_ID_1,
				'vote' => $log['Log']['id'] % 2? '1' : '-1',
				'comment' => 'some very very very long comment'
			));
		}
		$this->assertNotEmpty($votes);

		$this->utils->LogVote->saveVotes($votes);

		foreach ($votes as $vote) {
			$log = $this->utils->Log->findById($vote['LogVote']['log_id']);
			// Positive vote
			if ($vote['LogVote']['vote'] === 1) {
				if ((int)$log['Activity']['acceptance_votes'] == 1) {
					$this->assertNotNull($log['Log']['accepted']);
					$this->assertNotNull($log['Log']['reviewed']);
				}
			} 
			// Negative vote
			else {
				if ((int)$log['Activity']['rejection_votes'] == -1) {
					$this->assertNotNull($log['Log']['rejected']);
					$this->assertNotNull($log['Log']['reviewed']);
				}
			}
		}
	}
}