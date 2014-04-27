<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class DomainsControllerTest extends ControllerTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateDomains();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
		$this->controllerUtils->mockAuthUser();
	}

	public function testIndex() {
		$result = $this->testAction('/domains/index', array('return' => 'vars'));
		foreach ($result['domains'] as $domain) {
			$domainFields = array('id', 'name', 'color', 'abbr', 'description', 'icon');
			$this->assertEquals($domainFields, array_keys($domain['Domain']));
		}
	}
}