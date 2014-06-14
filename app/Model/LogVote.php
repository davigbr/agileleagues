<?php

App::uses('AppModel', 'Model');

class LogVote extends AppModel {
	
	public $useTable = 'log_votes';

	public $belongsTo = array('Log', 'Player');


	public function saveVotes($votes) {
		$this->begin();
		try {
			if (!$this->saveMany($votes)) {
				throw new ModelException('Could not save votes');
			}
			foreach ($votes as $vote) {
				$log = $this->Log->findById($vote['LogVote']['log_id']);
				$currentAcceptanceVotes = $log['Log']['acceptance_votes'];
				$currentRejectionVotes = $log['Log']['rejection_votes'];
				$requiredAcceptanceVotes = $log['Activity']['acceptance_votes'];
				$requiredRejectionVotes = $log['Activity']['rejection_votes'];
				$alreadyReviewed = $log['Log']['reviewed'] !== null;

				// Positive vote
				if ($vote['LogVote']['vote'] == 1) {
					
					$this->query('UPDATE log SET acceptance_votes = acceptance_votes + 1 WHERE id = ?', array($log['Log']['id']));

					if (!$alreadyReviewed && $currentAcceptanceVotes + 1 >= $requiredAcceptanceVotes) {
						// Activity accepted
						$this->Log->_review($log['Log']['id'], $vote['LogVote']['player_id'], 'accept');
					} 
				} 
				// Negative vote
				else if ($vote['LogVote']['vote'] == -1) {
					$this->query('UPDATE log SET rejection_votes = rejection_votes + 1 WHERE id = ?', array($log['Log']['id']));

					if (!$alreadyReviewed && $currentRejectionVotes + 1 >= $requiredRejectionVotes) {
						// Activity rejected
						$this->Log->_review($log['Log']['id'], $vote['LogVote']['player_id'], 'reject');
					}
				}
			}
			$this->commit();
		} catch (ModelException $e) {
			$this->rollback();
			throw $e;
		}
	}

}