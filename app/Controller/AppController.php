<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $uses = array(
		'AccessLog',
		'Activity', 
		'ActivityRequisite', 
		'ActivityRequisiteSummary',
		'Badge', 
		'BadgeActivityProgress',
		'BadgeClaimed',
		'BadgeLog', 
		'BadgeRequisite', 
		'CalendarLog',
		'Domain', 
		'Event',
		'EventTask',
		'EventTaskLog',
		'EventActivity',
		'EventActivityProgress',
		'EventJoinLog',
		'EventCompleteLog',
		'EventType',
		'Log', 
		'LogVote',
		'Notification',
		'Player', 
		'PlayerActivitySummary',
		'Timeline', 
		'Tag',
		'Team'
	);

	public $components = array(
		'Session',
		'RequestHandler',
	    'Auth' => array(
	        'loginAction' => array(
	            'controller' => 'players',
	            'action' => 'login',
	        ),
	        'loginRedirect' => '/dashboards/activities',
	        'authenticate' => array(
	            'Form' => array(
	                'userModel' => 'Player',
					'passwordHasher' => array(
						'hashType' => 'sha1',
	                    'className' => 'Simple'
	                ),
	                'fields' => array('username' => 'email')
	            )
	        )
	    )
	);

	public $helpers = array('Form', 'Bootstrap', 'Format', 'Notifications');

	public function beforeFilter() {
		parent::beforeFilter();

		$this->player = null;
		$this->isPlayer = false;
		$this->isGameMaster = false;

		if ($this->Auth->user()) {
			$playerId = $this->Auth->user('id');

			$this->AccessLog->save(array(
				'plugin' => $this->request->plugin,
				'controller' => $this->request->controller,
				'action' => $this->request->action,
				'params' => json_encode($this->request->params['pass']),
				'post' => json_encode($_POST),
				'get' => json_encode($_GET),
				'player_id' => $playerId
			));

			$this->player = $this->Player->findById($playerId);
			unset($this->player['Player']['password']);

			if (!$this->request->is('ajax') && $this->request->is('get')) {
				$notifications = $this->Notification->unread($playerId, 5);
				$this->set('notificationsUnread', $notifications);
			}
			
			$this->isPlayer = $this->player['Player']['player_type_id'] == PLAYER_TYPE_PLAYER;
			$this->isGameMaster = $this->player['Player']['player_type_id'] == PLAYER_TYPE_GAME_MASTER;

			$this->set('myPendingActivitiesCount', $this->Log->countPendingFromPlayer($playerId));
			$this->set('pendingTasksCount', $this->EventTaskLog->countPendingFromPlayer($playerId));
			$this->set('activitiesNotReviewedCount', $this->Log->countNotReviewed());
			$this->set('teamPendingActivities', $this->Log->countPendingFromTeamNotFromPlayer($playerId));
			$this->set('eventTasksNotReviewedCount', $this->EventTaskLog->countNotReviewed());

			$this->set('allDomains', $this->Domain->allFromOwner($this->gameMasterId()));	
		}

		$this->set('isPlayer', $this->isPlayer);
		$this->set('isGameMaster', $this->isGameMaster);
		$this->set('loggedPlayer', $this->player);
		$this->set('collapseSidebar', false);
	}

	protected function gameMasterId() {
		if (!$this->smId) {
			$this->smId = $this->Player->gameMasterId($this->Auth->user('id'));
		}
		return $this->smId;
	}

	public function flashSuccess($message) {
		if ($this->Session != null) {
			$this->Session->setFlash($message);
		}
		$this->flashSuccess = $message;
	}

	public function flashError($message) {
		if ($this->Session != null) {
			$this->Session->setFlash($message, 'default', array(), 'error');
		}
		$this->flashError = $message;
	}
}	
