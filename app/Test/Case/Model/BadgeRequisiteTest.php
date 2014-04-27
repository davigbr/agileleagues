<?php

App::uses('TestUtils', 'Lib');

class BadgeRequisiteTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
	}

	public function test() {
		$this->utils->BadgeRequisite->simple();
	}
}