<?php

App::uses('AppModel', 'Model');

class Event extends AppModel {
	
	public $useTable = 'event';
	public $belongsTo = array('EventType');
	public $hasMany = array(
		'EventTask', 
		'EventTaskLog', 
		'EventActivity',
		'EventJoinLog',
		'EventCompleteLog'
	);

	public $validate = array(
		'name' => 'notEmpty',
		'event_type_id' => 'notEmpty',
		'start' => array(
			'notEmpty'
		),
		'end' => array(
			'notEmpty'
		),
		'xp' => array(
			'notEmpty'
		)
	);

	public $uses = array('EventActivityProgress', 'Notification', 'Player', 'XpLog');

	public function simpleActive() {
		return $this->find('list', array(
			'conditions' => array(
				'CURRENT_DATE BETWEEN Event.start AND Event.end'
			),
			'order' => 'Event.name ASC'
		));
	}

	public function allActive($limit = 5) {
		$options = array(
			'contain' => array(
				'EventTask',
				'EventType',
				'EventJoinLog' => array('Player'),
				'EventCompleteLog' => array('Player')
			),
			'conditions' => array('CURRENT_DATE BETWEEN Event.start AND Event.end'),
			'order' => 'Event.start DESC'
		);
		if ($limit) {
			$options['limit'] = $limit;
		}
		return $this->find('all', $options);
	}

	public function complete($playerId, $id)  {
		$event = $this->playerProgress($playerId, $id);
		if ($this->EventCompleteLog->findByPlayerIdAndEventId($playerId, $id)) {
			throw new ModelException('Event already completed.');
		}
		if ($event['Event']['progress'] < 100) {
			throw new ModelException('Event cannot be completed yet.');
		}
		
		$eventType = $event['EventType']['name'];
		$eventName = $event['Event']['name'];
		$player = $this->Player->findById($playerId);
		$playerName = $player['Player']['name'];

		$this->begin();
		try {
			$this->EventCompleteLog->_log($playerId, $id);
			$this->XpLog->_eventCompleted($playerId, $id);
			$this->Notification->_broadcast(
				__('%s Completed', $eventType),
				__('%s completed the %s %s. <br/>Come on, guys!', $playerName, $eventName, $eventType),
				'success'
			);
			$this->commit();
		} catch (Exception $ex) {
			//@codeCoverageIgnoreStart
			$this->rollback();
			throw $ex;
			//@codeCoverageIgnoreEnd
		}
	}

	public function playerProgress($playerId, $id) {
		$event = $this->find('first', array(
			'contain' => array(
				'EventType',
				'EventTask',
				'EventActivity' => array(
					'Activity' => array(
						'Domain'
					)
				)
			),
			'conditions' => array(
				'Event.id' => $id
			)
		));
		if(!$event) {
			throw new ModelException('Event not found');
		}
    	$eventTaskLogs = $this->EventTaskLog->findAllByPlayerIdAndEventId($playerId, $id);
    	$eventActivityLogs = $this->EventActivityProgress->findAllByPlayerIdAndEventId($playerId, $id);

	    array_replace_keys($eventTaskLogs, function($eventTaskLog){
	        return $eventTaskLog['EventTaskLog']['event_task_id'];
	    });
	    array_replace_keys($eventActivityLogs, function($eventActivityLog){
	        return $eventActivityLog['EventActivityProgress']['activity_id'];
	    });

	    $eventProgress = 0;
	    $tasksCompleted = 0;
	    $tasksCount = count($event['EventTask']);
	    if (!empty($event['EventTask'])) {
		    foreach ($event['EventTask'] as &$task) {
		    	$completed = isset($eventTaskLogs[$task['id']]);
		    	$task['completed'] = $completed;
		    	if ($completed) {
		    		$tasksCompleted++;
		    	}
		    }
		}

		$activitiesCompleted = 0;
		$activitiesCount = count($event['EventActivity']);
		if (!empty($event['EventActivity'])) {
			foreach ($event['EventActivity'] as &$activity) {
				$activityId = $activity['activity_id'];
                $eventActivityLog = isset($eventActivityLogs[$activityId])? $eventActivityLogs[$activityId] : null;
 				$required = $activity['count'];
 				$obtained = 0;
 				$progress = 0;
 				if ($eventActivityLogs) {
					$obtained = $eventActivityLog['EventActivityProgress']['times_obtained'];
					$progress = $obtained >= $required? 100 : floor($obtained / $required * 100);
				}
				$activity['obtained'] = $obtained;
				$activity['progress'] = $progress;
				if ($progress == 100) {
					$activitiesCompleted ++;
				}
			}
		}
		if (($activitiesCount + $tasksCount) > 0) {
			$eventProgress = ($activitiesCompleted + $tasksCompleted) / ($activitiesCount + $tasksCount) * 100;
		} else {
			$eventProgress = 100;
		}
		$event['Event']['progress'] = $eventProgress;
		return $event;
	}
	
	public function allPast($limit = 5) {
		return $this->find('all', array(
			'contain' => array(
				'EventType',
				'EventCompleteLog' => array('Player')
			),
			'conditions' => array('Event.end < CURRENT_DATE'),
			'limit' => $limit,
			'order' => 'Event.end DESC'
		));
	}

	public function allFuture($limit = 5) {
		return $this->find('all', array(
			'conditions' => array('Event.start > CURRENT_DATE'),
			'limit' => $limit,
			'order' => 'Event.start DESC'
		));
	}

}