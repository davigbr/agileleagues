<?php

class ControllerTestCaseUtils {

	public function __construct(ControllerTestCase $controllerTestCase) {
		$this->controllerTestCase = $controllerTestCase;
        $this->controllerName = substr(get_class($this->controllerTestCase), 0, -strlen('ControllerTest'));
        CakeSession::destroy();
    }

    public function mockAuthLogin() {
        $that = $this->controllerTestCase;
        $this->controllerMock = $that->generate($this->controllerName, array(
            'helpers' => array(
                'Notifications',
                'Form',
                'Bootstrap',
                'Format'
            ),
            'components' =>array(
                'RequestHandler',
                'Session',
                'Email' => array('send'),
                'Auth' => array('login')
        )));

        $this->controllerMock->Auth->expects($that->any())
            ->method('login')
            ->will($that->returnCallback(function() {
                return true;
            })
        );
    }

    public function mockEmailError() {
        $that = $this->controllerTestCase;
        $this->controllerMock->Email->expects($that->any())
            ->method('send')
            ->will($that->returnCallback(function() {
                throw new Exception();
            }));
    }

    public function mockAuthUser($userId = 1) {
        $player = array('id' => $userId);

        $that = $this->controllerTestCase;
        $this->controllerMock = $that->generate($this->controllerName, array(
            'helpers' => array(
                'Notifications',
                'Form',
                'Bootstrap',
                'Format',
            ),
            'components' =>array(
                'RequestHandler',
                'Session',
                'Auth' => array('user'),
                'Email' => array('send')
        )));
        
        $this->controllerMock->Auth->staticExpects($that->any())
            ->method('user')
            ->will($that->returnCallback(function($which) use ($player) {
                return $which === null? $player : $player['id'];
            }));

        $that->assertEquals($userId, (int)$this->controllerMock->Auth->user('id'));
        $that->assertEquals($player, $this->controllerMock->Auth->user());
    }
}