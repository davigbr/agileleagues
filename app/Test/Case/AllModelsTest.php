<?php

class AllModelsTest extends CakeTestSuite {
    public static function suite() {
        $suite = new CakeTestSuite('All models');
        $suite->addTestDirectoryRecursive(TESTS . 'Case' . DS . 'Model');
        return $suite;
    }
}