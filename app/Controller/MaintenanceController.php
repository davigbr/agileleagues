<?php

App::uses('AppController', 'Controller');

class MaintenanceController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		if (!Configure::read('debug')) {
			throw new ForbiddenException();
		}
	}

	public function rebuildActivityRequisiteSummary() {
		try {
			$this->ActivityRequisiteSummary->deleteAll(array('id > ' => 0));
			$this->Log->begin();
			$logs = $this->Log->find('all', array(
				'conditions' => array(
					'Log.accepted IS NOT NULL'
				),
				'contain' => array(
					'Tags'
				)
			));

			foreach ($logs as $log) {
				$this->ActivityRequisite->_updateActivityRequisiteSummary($log);
			}
			$this->Log->commit();
			$this->flashSuccess(__('Activity requisite summary rebuilt sucessfully!'));
		} catch (Exception $ex) {
			$this->Log->rollback();
			$this->flashError($ex->getMessage());
		}
		return $this->redirect('/');
	}	

}