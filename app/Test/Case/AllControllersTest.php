<?php

class AllControllersTest extends CakeTestSuite {
    public static function suite() {
        $suite = new CakeTestSuite('All controllers');
        $suite->addTestDirectoryRecursive(TESTS . 'Case' . DS . 'Controller');
        return $suite;
    }
}