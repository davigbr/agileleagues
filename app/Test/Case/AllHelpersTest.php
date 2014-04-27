<?php

class AllHelpersTest extends CakeTestSuite {
    public static function suite() {
        $suite = new CakeTestSuite('All helpers');
        $suite->addTestDirectoryRecursive(TESTS . 'Case' . DS . 'View' . DS . 'Helper');
        return $suite;
    }
}