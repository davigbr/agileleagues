<?php

App::uses('AppController', 'Controller');

class ActivitiesController extends AppController {

	public function index() {
		$activities = $this->Activity->allActive(
			$this->Player->scrumMasterId($this->Auth->user('id')));
		$this->set('activities', $activities);
	}

	public function inactivate($id) {
		if (!$this->isScrumMaster) {
			return $this->redirect('/activities');
		}

		$this->Activity->id = $id;
		if (!$this->Activity->exists()) {
			$this->flashError(__('Activity not found!'));
		} else {
			$update = array('id' => $id, 'inactive' => 1);
			if ($this->Activity->save($update)) {
				$this->flashSuccess(__('Activity inactivated successfully!'));
			} else {
				//@codeCoverageIgnoreStart
				$this->flashError(__('An error occurred while trying to inactivate the activity.'));
				//@codeCoverageIgnoreEnd
			}
		}
		
		return $this->redirect('/activities');
	}

	public function myreviewed() {
		$playerId = (int)$this->Auth->user('id');
		$logs = $this->PlayerActivityCoins->allFromPlayer($playerId);
		$this->set('logs', $logs);
	}

	public function day($date, $playerId = null) {
		if (!$playerId) {
			$playerId = (int)$this->Auth->user('id');
		}
		$logs = $this->Log->findAllByPlayerIdAndAcquired($playerId, $date);
		$this->set('date', $date);
		$this->set('logs', $logs);
		$this->set('player', $this->Player->findById($playerId));
	}

	public function mypending() {
		$playerId = (int)$this->Auth->user('id');
		$logs = $this->Log->allPendingFromPlayer($playerId);
		$this->set('logs', $logs);
	}

	public function calendar($playerId = null) {
		if (!$playerId) {
			$playerId = (int)$this->Auth->user('id');
		}
		$this->set('calendarLogs', $this->CalendarLog->findAllByPlayerId($playerId));
		$this->set('player', $this->Player->findById($playerId));
	}

	public function notReviewed() {
		if (!$this->isScrumMaster) {
			return $this->redirect('/activities');
		}
		$this->set('logs', $this->Log->allNotReviewed());
	}

	public function report($activityId = null) {
		$playerTypeId = $this->Auth->user('player_type_id');

		$this->set('activities', $this->Activity->simpleActiveFromPlayerType(
			$this->scrumMasterId(), 
			$playerTypeId
		));
		$this->set('activitiesById', $this->Activity->allFromOwnerById($this->scrumMasterId()));
		$this->set('events', $this->Event->simpleActive($this->scrumMasterId()));
		$this->set('players', $this->Player->simpleVerifiedFromPlayerTeam($this->Auth->user('id')));

		if ($this->request->is('post')) {
			$log = $this->request->data;

			$playerId = $this->Auth->user('id');
			$log['Log']['player_id'] = $playerId;
			$log['Log']['player_id_owner'] = $this->scrumMasterId();
			$activityId = $log['Log']['activity_id'];

			if ($this->Log->save($log)) {
				$activity = $this->Activity->findById($log['Log']['activity_id']);
				$this->request->data = array();
				$this->flashSuccess(__('Activity %s reported successfully!', '<strong>' .$activity['Activity']['name'] . '</strong>'));
			} else {
				$this->flashError(__('Error while trying to report activity.'));
			}
		} else {
			$this->request->data = array(
				'Log' => array(
					'activity_id' => $activityId
			));
		}
	}


	public function _save($id = null) {
		if (!$this->isScrumMaster) {
			throw new ForbiddenException();
		}

		$this->set('domains', $this->Domain->simpleFromOwner($this->scrumMasterId()));

		if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['Activity']['player_id_owner'] = $this->Auth->user('id');

			if ($this->Activity->save($this->request->data)) {
				$this->flashSuccess(__('Activity saved successfully!'));
				return $this->redirect('/activities');
			} else {
				$this->flashError(__('Error while trying to save activity.'));
			}
		} else if ($id !== null) {
			$this->request->data = $this->Activity->findById($id);
		}
	}

	public function add() {
		$this->_save();
	}


	public function edit($id = null) {
		$this->_save($id);
	}
}