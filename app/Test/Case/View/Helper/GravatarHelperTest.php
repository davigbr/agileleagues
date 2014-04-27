<?php

App::uses('GravatarHelper', 'View/Helper');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('ComponentCollection', 'Controller');

class GravatarHelperTest extends CakeTestCase {

    public $Gravatar = null;

    public function setUp() {
        parent::setUp();
        $this->Gravatar = new GravatarHelper(new View(new Controller()));
    }

    public function testGet() {
        $url = $this->Gravatar->get('davi.gbr@gmail.com');
        $expected = 'http://www.gravatar.com/avatar/f27a4328dbb24f5b5f21e3308e456568?s=80&d=mm&r=g';
        $this->assertEquals($expected, $url);
    }

    public function testGetImageTag() {
        $tag = $this->Gravatar->get('davi.gbr@gmail.com', $s = 80, $d = 'mm', $r = 'g', $img = true);
        $expected = '<img src="http://www.gravatar.com/avatar/f27a4328dbb24f5b5f21e3308e456568?s=80&d=mm&r=g" />';
        $this->assertEquals($expected, $tag);
    }
}