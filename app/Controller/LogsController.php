<?php

App::uses('AppController', 'Controller');

class LogsController extends AppController {

	public function delete($id) {
		if ($this->Log->delete($id, true)) {
			$this->flashSuccess('Activity log deleted successfully!');
		} else {
			$this->flashError('Error while trying to delete this activity log.');
		}
		return $this->redirect($this->referer());
	}
}