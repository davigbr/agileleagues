<?php

App::uses('AppController', 'Controller');

class PlayersController extends AppController {

	public $uses = array('Team');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout');
	}

	public function index() {
		$this->set('players', $this->Player->all());
	}

	public function login(){
		if ($this->Auth->user() != null) {
			return $this->redirect('/');
		}
		if ($this->request->is('post')) {
			// Login por AJAX
			if ($this->Auth->login()) {
				$this->set('login_status', 'success');
			} else {
				$this->set('login_status', 'invalid');
			}
			$this->set('_serialize', array('login_status'));
			$this->layout = 'ajax';
		} else {
			$this->layout = 'login';
		}
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}

	// Change team
	public function team($id) {
		$this->set('teams', $this->Team->simple());

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Player->save($this->request->data)) {
				$this->flashSuccess(__('Player saved successfully!'));
				return $this->redirect('/players');
			} 
			else {
				//@codeCoverageIgnoreStart
				$this->flashError(__('Error while trying to save player.'));
				// @codeCoverageIgnoreEnd
			}
		} else {
			$this->request->data = $this->Player->findById($id);
			if (!$this->request->data) {
				throw new NotFoundException();
			}
		}
	}

	public function myaccount() {
		if ($this->request->is('get')) {
			$this->request->data = $this->player;
		} else {
			if ($this->Player->save($this->request->data)) {
				unset ($this->request->data['Player']['password']);
				unset ($this->request->data['Player']['repeat_password']);
				$this->flashSuccess(__('Data updated successfully!'));
			} else {
				$this->flashError(__('Error while trying to edit your data :('));
			}
		}
	}
}