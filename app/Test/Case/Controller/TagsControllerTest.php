<?php

App::uses('TestUtils', 'Lib');
App::uses('ControllerTestCaseUtils', 'Lib');

class TagsControllerTest extends ControllerTestCase {
	
	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
		$this->utils->generatePlayers();
		$this->utils->generateTags();
		$this->controllerUtils = new ControllerTestCaseUtils($this);
	}

	public function testAddNotGA() {
		$this->controllerUtils->mockAuthUser(DEVELOPER_ID_1);
		$this->setExpectedException('ForbiddenException');
		$this->testAction('/tags/add');
	}

	public function testIndex() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$vars = $this->testAction('/tags/', array('return' => 'vars'));
		$this->assertNotEmpty($vars['tags']);
	}

	public function testInactivateNotFound() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$this->testAction('/tags/inactivate/0', array('return' => 'vars'));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testInactivateSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);
		$this->testAction('/tags/inactivate/1', array('return' => 'vars'));
		$tag = $this->utils->Tag->findById(1);
		$this->assertEquals(true, (bool)$tag['Tag']['inactive']);
		$this->assertNotNull($this->controller->flashSuccess);
	}

	public function testAddGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);		
		$this->testAction('/tags/add/', array('return' => 'vars', 'method' => 'GET'));
	}

	public function testAddPostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);		
		$name = 'name new tag';
		$data = array(
			'Tag' => array(
				'name' => $name,
				'description' => 'blablabla',
				'color' => '#123265',
				'new' => '0'
			)
		);
		$this->testAction('/tags/add/', array('return' => 'vars', 'data' => $data));
		$tag = $this->utils->Tag->findByName($name);
		$this->assertEquals($tag['Tag']['player_id_owner'], SCRUMMASTER_ID_1);
	}

	public function testAddPostValidationError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);		
		$id = 1;
		$data = array(
			'Tag' => array(
				'name' => '',
				'description' => 'blablabla',
				'color' => '#123265',
				'new' => '0'
			)
		);
		$this->testAction('/tags/add/', array('return' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}

	public function testEditGet() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);		
		$this->testAction('/tags/edit/1', array('return' => 'vars', 'method' => 'GET'));
		$this->assertNotEmpty($this->controller->request->data['Tag']);
	}

	public function testEditPostSuccess() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);		
		$id = 1;
		$name = 'name changed';
		$data = array(
			'Tag' => array(
				'id' => $id,
				'name' => $name,
				'description' => ''
			)
		);
		$this->testAction("/tags/edit/$id", array('return' => 'vars', 'data' => $data));
		$tag = $this->utils->Tag->findById($id);
		$this->assertEquals($name, $tag['Tag']['name']);
	}

	public function testEditPostValidationError() {
		$this->controllerUtils->mockAuthUser(SCRUMMASTER_ID_1);		
		$id = 1;
		$name = 'name changed';
		$data = array(
			'Tag' => array(
				'id' => $id,
				'name' => '',
				'description' => ''
			)
		);
		$this->testAction("/tags/edit/$id", array('return' => 'vars', 'data' => $data));
		$this->assertNotNull($this->controller->flashError);
	}
}