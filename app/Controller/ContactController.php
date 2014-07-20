<?php

App::uses('AppController', 'Controller');

class ContactController extends AppController {

	public $components = array('Email');

	public function bug() {
		$this->_send('Bug');
	}

	public function feature() {
		$this->_send('Feature');
	}

	private function _send($type) {
		if ($this->request->is('post') || $this->request->is('put')){
			$this->Email->template('contact', array(
				'type' => $type,
				'user' => $this->Auth->user(),
				'data' => $this->request->data[$type]
			));
			$this->Email->subject($type);
			$this->Email->send('davi.gbr@gmail.com');
			$this->flashSuccess('Your request was sent successfully. Thank you!');
			return $this->redirect($this->referer());
		}
	}

}