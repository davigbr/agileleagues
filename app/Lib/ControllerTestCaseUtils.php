<?php

class ControllerTestCaseUtils {

	public function __construct(ControllerTestCase $controllerTestCase) {
		$this->controllerTestCase = $controllerTestCase;
        $this->controllerName = substr(get_class($this->controllerTestCase), 0, -strlen('ControllerTest'));
    }

    public function mockAuthLogin() {
        $that = $this->controllerTestCase;
        $controllerMock = $that->generate($this->controllerName, array(
            'helpers' => array(
                'Notifications',
                'Form',
                'Bootstrap',
                'Format'
            ),
            'components' =>array(
                'RequestHandler',
                'Session',
                'Auth' => array('login')
        )));

        $controllerMock->Auth->expects($that->any())
            ->method('login')
            ->will($that->returnCallback(function() {
                return true;
            })
        );
    }

    public function mockAuthUser($userId = 1) {
        $player = array('id' => $userId);

        $that = $this->controllerTestCase;
        $controllerMock = $that->generate($this->controllerName, array(
            'helpers' => array(
                'Notifications',
                'Form',
                'Bootstrap',
                'Format'
            ),
            'components' =>array(
                'RequestHandler',
                'Session',
                'Auth' => array('user')
        )));

        $controllerMock->Auth->staticExpects($that->any())
            ->method('user')
            ->will($that->returnCallback(function($which) use ($player) {
                return $which === null? $player : $player['id'];
            }));

        $that->assertEquals($userId, (int)$controllerMock->Auth->user('id'));
        $that->assertEquals($player, $controllerMock->Auth->user());
    }
}