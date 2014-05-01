<?php

App::uses('AppModel', 'Model');

class Configuration extends AppModel {
	
	public $useTable = 'configuration';

	public function _read($key) {
		$config = $this->findByKey($key);
		return empty($config)? null : $config['Configuration']['value'];
	}

	public function _write($key, $value) {
		$config = $this->findByKey($key);
		if (!$config) {
			$config = array('Configuration' => array('key' => $key));
		}
		$config['Configuration']['value'] = $value;
		$this->create();
		return $this->save($config);
	}
}