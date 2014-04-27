<?php

App::uses('TestUtils', 'Lib');

class DomainTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generateDomains();
		$this->utils->generateActivities();
	}

	public function testActivitiesCount(){
		$domains = $this->utils->Domain->activitiesCount();
		foreach ($domains as $id => $activities) {
			if ($id == 1) {
				$this->assertEquals(4, $activities);
			} else if ($id == 2) {
				$this->assertEquals(6, $activities);
			}
		}
		$this->assertEquals(2, count($domains));
	}
}