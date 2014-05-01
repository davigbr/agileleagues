<?php

App::uses('TestUtils', 'Lib');

class ConfigurationTest extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->utils = new TestUtils();
		$this->utils->clearDatabase();
	}

	public function testRead() {
		$this->assertNull($this->utils->Configuration->_read('invalid'));
		$this->utils->Configuration->_write('key', 'value');
		$this->assertEquals('value', $this->utils->Configuration->_read('key'));
	}

	public function testWriteExistent() {
		$this->utils->Configuration->_write('key', 'value');
		$this->assertEquals('value', $this->utils->Configuration->_read('key'));
		$this->utils->Configuration->_write('key', 'new value');
		$this->assertEquals('new value', $this->utils->Configuration->_read('key'));
	}

	public function testWriteNew() {
		$this->utils->Configuration->_write('key', 'value');
		$this->assertEquals('value', $this->utils->Configuration->_read('key'));
	}
}