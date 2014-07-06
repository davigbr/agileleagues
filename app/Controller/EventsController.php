<?php

App::uses('AppController', 'Controller');

class EventsController extends AppController {

	public function index() {
		$this->set('activeEvents', $this->Event->allActive($this->gameMasterId(), 10));
		$this->set('pastEvents', $this->Event->allPast($this->gameMasterId(), 5));
		$this->set('futureEvents', $this->Event->allFuture($this->gameMasterId(), 5));
	}

	public function notReviewed() {
		$eventTaskLogs = $this->EventTaskLog->allNotReviewed($this->Auth->user('id'));
		$this->set('eventTaskLogs', $eventTaskLogs);
	}

	public function pending() {
		$eventTaskLogs = $this->EventTaskLog->allPendingFromPlayer($this->Auth->user('id'));
		$this->set('eventTaskLogs', $eventTaskLogs);
	}

	public function review($id) {
		try {
			$this->EventTaskLog->review($id);
			$this->flashSuccess(__('Event task reviewed successfully!'));
		} catch (Exception $ex) {
			$this->flashError('Error while trying to review this event task log: ' . $ex->getMessage());
		}
		return $this->redirect($this->referer());
	}

	public function deleteTask($eventTaskLogId) {
		if ($this->EventTaskLog->delete($eventTaskLogId)) {
			$this->flashSuccess('Event task log deleted successfully!');
		} else {
			$this->flashError('Error while trying to delete this event task log.');
		}
		return $this->redirect($this->referer());
	}


	public function complete($id) {
		try {
			$playerId = $this->Auth->user('id');
			$this->Event->complete($playerId, $id);
			$this->flashSuccess('Event completed! I bet you\'re Japanese ^_^ ');
		} catch (ModelException $ex) {
			$this->flashError($ex->getMessage());
		}
		return $this->redirect($this->referer());
	}

	public function report() {
		$this->set('events', $this->Event->simpleActive($this->gameMasterId()));
		$this->set('allEvents', $this->Event->allActive($this->gameMasterId(), false));

		if ($this->request->is('post') || $this->request->is('put')) {
			
			$playerId = $this->Auth->user('id');
			$this->request->data['EventTaskLog']['player_id'] = $playerId;

			if ($this->EventTaskLog->save($this->request->data)) {
				$this->flashSuccess(__('Event task reported successfully!'));
				$this->request->data = array();
			} else {
				$this->flashError(__('Error while trying to report event task.'));
			}
		}
	}

	public function details($id) {
		$playerId = $this->Auth->user('id');
		try {
			$this->set('event', $this->Event->playerProgress($playerId, $id));
			$this->set('eventCompleted', $this->EventCompleteLog->findByPlayerIdAndEventId($playerId, $id));
		} catch (Exception $ex) {
			$this->flashError($ex->getMessage());
			return $this->redirect('/events');
		}
	}

	private function _save($id = null) {
		if (!$this->isGameMaster) {
			throw new ForbiddenException();
		}

		$this->set('eventTypes', $this->EventType->simple());
		$this->set('activities', $this->Activity->simpleActive($this->gameMasterId()));

		if ($this->request->is('post') || $this->request->is('put')) {
			
			$this->request->data['Event']['player_id_owner'] = $this->Auth->user('id');

			foreach ($this->request->data['EventActivity'] as $key => $value) {
	            if (!$value['activity_id']) {
	            	unset($this->request->data['EventActivity'][$key]);
	            } else {
	            	if (!$this->request->data['EventActivity'][$key]['count']) {
	            		$this->request->data['EventActivity'][$key]['count'] = 1;
	            	}
	            }
	        }
	        foreach ($this->request->data['EventTask'] as $key => $value) {
	            if (!$value['name']) {
	            	unset($this->request->data['EventTask'][$key]);
	            } else {
	            	if (!$this->request->data['EventTask'][$key]['xp']) {
	            		$this->request->data['EventTask'][$key]['xp'] = 0;
	            	}
	            }
	        }

	        if (empty($this->request->data['EventActivity'])) unset($this->request->data['EventActivity']);
	        if (empty($this->request->data['EventTask'])) unset($this->request->data['EventTask']);

	        $ds = $this->Event->getDataSource();
	        $ds->begin();
	        
	        try {
		        if ($id) {
			        $this->EventTask->query('DELETE FROM event_task WHERE event_id = ? ', array($id));
			        $this->EventActivity->query('DELETE FROM event_activity WHERE event_id = ? ', array($id));
		    	}

		        if ($this->Event->saveAssociated($this->request->data)) {
					$ds->commit();
					$this->flashSuccess(__('Event saved succesfully!'));
					return $this->redirect('/events');
				} else {
					$ds->rollback();
					$this->flashError(__('Error while trying to save event.'));
				}
			} catch (PDOException $ex) {
				$ds->rollback();
				$this->flashError(__('This event cannot be edited anymore. Some players have already reported activities.'));
				return $this->redirect('/events');
			}	
			
		} else if ($id !== null) {
			$this->Event->recursive = 2;

			$this->request->data = $this->Event->findById($id);

			if (!$this->request->data) {
				throw new NotFoundException();
			}
		}

	}


	public function create() {
		$this->_save();
	}

	public function edit($id = null) {
		$this->_save($id);		
	}
	
	public function join($id) {		
		$playerId = $this->Auth->user('id');
		$player = $this->Player->findById($playerId);

		$eventJoinLog = $this->EventJoinLog->findByEventIdAndPlayerId($id, $playerId);
		$event = $this->Event->findById($id);

		if (!$event) {
			$this->flashError(__('Event not found!'));
		} else if ($eventJoinLog) {
			$this->flashError(__('You have already joined this event.'));
		} else if ($event['Event']['event_type_id'] == EVENT_TYPE_MISSION && $player['Player']['level'] < EVENT_LEVEL_REQUIRED_MISSION) {
			$this->flashError(__('You cannot join this event.'));
		} else if ($event['Event']['event_type_id'] == EVENT_TYPE_CHALLENGE && $player['Player']['level'] < EVENT_LEVEL_REQUIRED_CHALLENGE) {
			$this->flashError(__('You cannot join this event.'));
		} else {
			$data = array(
				'player_id' => $playerId,
				'event_id' => $id
			);
			if ($this->EventJoinLog->save($data)) {
				if ($event['Event']['event_type_id'] == EVENT_TYPE_MISSION) {
					$this->flashSuccess(__('Mission joined! Hell yeah!'));
				} else {
					$this->flashSuccess(__('Challenge reviewed! \m/'));
				}
			}
		}
		return $this->redirect('/events');
	}
}