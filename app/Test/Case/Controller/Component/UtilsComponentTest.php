<?php

App::uses('UtilsComponent', 'Controller/Component');
App::uses('ComponentCollection', 'Controller');

class UtilsComponentTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->Utils = new UtilsComponent(new ComponentCollection(), null);
	}

	public function testPlayerHash() {
		$this->assertEquals(
			'3967e6121879e31714169258c8a7dfba3594fcdc645c65fdcd4cd26248f7cc7d', 
			$this->Utils->playerHash(1)
		);
	}


}