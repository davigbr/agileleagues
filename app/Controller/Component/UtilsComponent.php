<?php

App::uses('Component', 'Controller');

class UtilsComponent extends Component {
	
	public function playerHash($playerId) {
		return Security::hash($playerId, 'sha256', true);
	}
}