<?php

App::uses('TestUtils', 'Lib');

class TeamTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
	}

	public function test() {
	}
}