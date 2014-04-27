<?php

App::uses('Helper', 'View');

class AppHelper extends Helper {

	public $uses = array();

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		foreach ($this->uses as $model) {
			$this->$model = ClassRegistry::init($model);
		}
	}
}
