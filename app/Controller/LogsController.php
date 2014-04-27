<?php

App::uses('AppController', 'Controller');

class LogsController extends AppController {

	public function delete($id) {
		if ($this->Log->delete($id)) {
			$this->flashSuccess('Activity log deleted successfully!');
		} else {
			$this->flashError('Error while trying to delete this activity log.');
		}
		return $this->redirect($this->referer());
	}

	public function review($id) {
		try {
			$this->Log->review($id);
			$this->flashSuccess(__('Activity reviewed successfully!'));
		} catch (ModelException $ex) {
			$this->flashError('Error while trying to review this activity log: ' . $ex->getMessage());
		}
		return $this->redirect($this->referer());
	}
}