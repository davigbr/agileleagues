<?php

App::uses('AppController', 'Controller');

class TagsController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();

		if ($this->request->action !== 'index' && !$this->isGameMaster) {
			throw new ForbiddenException();
		}
	}

	private function save($id = null) {
		if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Tag']['player_id_owner'] = $this->Auth->user('id');

			if ($this->Tag->save($this->request->data)) {
				$this->flashSuccess(__('Tag saved successfully!'));
				return $this->redirect('/tags');
			} else {
				$this->flashError(__('Error while trying to save tag.'));
			}
		} else if ($id !== null) {
			$this->request->data = $this->Tag->findById($id);
		}
	}

	public function add() {
		$this->save();
	}


	public function edit($id) {
		$this->save($id);
	}

	public function index() {
		$this->set('tags',  $this->Tag->allActive($this->gameMasterId()));
	}

	public function inactivate($id) {
		$this->Tag->id = $id;
		if (!$this->Tag->exists()) {
			$this->flashError(__('Tag not found!'));
		} else {
			$update = array('id' => $id, 'inactive' => 1);
			if ($this->Tag->save($update)) {
				$this->flashSuccess(__('Tag inactivated successfully!'));
			} else {
				//@codeCoverageIgnoreStart
				$this->flashError(__('An error occurred while trying to inactivate the tag.'));
				//@codeCoverageIgnoreEnd
			}
		}
		
		return $this->redirect('/tags');
	}
}