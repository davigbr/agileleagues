<?php

App::uses('FormatHelper', 'View/Helper');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('ComponentCollection', 'Controller');

class FormatHelperTest extends CakeTestCase {

    public function setUp() {
        parent::setUp();
        $this->Format = new FormatHelper(new View(new Controller()), array(
            'timezone' => 'America/Sao_Paulo'
        ));
    }

    public function testTrunc() {
        $text = $this->Format->trunc('A VERY BIG TEXT !!!!', 15, true);
        $this->assertEquals('A VERY BIG TEX&#8230;', $text);
        $text = $this->Format->trunc('A VERY BIG TEXT !!!!', 10, false);
        $this->assertEquals('A VERY BI', $text);
    }

    public function testDate() {
        $date = $this->Format->date('2000-01-01 23:59:00');
        $this->assertEquals('Jan 1st, 2000', $date);
    }

    public function testDateWithDateOnly() {
        $date = $this->Format->date('2000-01-02');
        $this->assertEquals('Jan 2nd, 2000', $date);
    }

    public function testDateWithSameYear() {
        $date = $this->Format->date(date('Y') . '-01-02');
        $this->assertEquals('Jan 2nd (Thu)', $date);
    }

    public function testDateTime() {
        $date = $this->Format->dateTime('2001-01-02');
        $this->assertEquals('Jan 1st, 2001', $date);
    }

    public function testDateTimeWithSameYear() {
        $date = $this->Format->dateTime(date('Y') . '-01-02');
        $this->assertEquals('Jan 1st (Wed) 10:00 PM', $date);
    }

    public function testTime() {
        $time = $this->Format->time('2000-01-02 23:59:00');
        $this->assertEquals('09:59 PM', $time);
    }
}