<?php

App::uses('AppController', 'Controller');

class ActivitiesController extends AppController {

	public function index() {
		$activities = $this->Activity->all(array('inactive' => 0));
		$this->set('activities', $activities);
	}

	public function add() {
		$this->set('domains', $this->Domain->simple());

		if ($this->request->is('post')) {
			if ($this->Activity->save($this->request->data)) {
				$this->flashSuccess(__('Activity saved successfully!'));
				return $this->redirect('/activities');
			} else {
				$this->flashError(__('Error while trying to save activity.'));
			}
		}
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
				$this->flashError(__('An error occurred while trying to inactivate the activity.'));
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
		$this->set('activities', $this->Activity->simpleActive());
		$this->set('activitiesById', $this->Activity->all(array(), 'id'));
		$this->set('events', $this->Event->simpleActive());
		$this->set('players', $this->Player->simple());	

		if ($this->request->is('post')) {
			$log = $this->request->data;

			$playerId = $this->Auth->user('id');
			$log['Log']['player_id'] = $playerId;

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

	public function edit($id = null) {
		if (!$this->isScrumMaster) {
			return $this->redirect('/activities');
		}

		$this->set('domains', $this->Domain->simple());

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Activity->save($this->request->data)) {
				$this->flashSuccess(__('Activity edited successfully!'));
				return $this->redirect('/activities');
			} else {
				$this->flashError(__('Error while trying to edit activity.'));
			}
		} else {
			$this->request->data = $this->Activity->findById($id);
		}
		
	}
}