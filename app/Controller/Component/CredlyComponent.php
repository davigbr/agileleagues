<?php

App::uses('Component', 'Controller');
require_once APP . 'Lib' . DS . 'Httpful.phar';

define('BASE_URL', 'https://api.credly.com/v1.1/');
define('MAX_ATTEMPTS', 3);

class CredlyComponent extends Component {

	private function serializeParams($params) {
		$queryString = '';
		foreach ($params as $name => $value) {
			if ($queryString !== '') {
				$queryString .= '&';
			}
			$queryString .= $name . '=' . urlencode($value);
		}
		return $queryString;
	}

	private function get($credlyMethod, $params) {
		return $this->issue('get',  $credlyMethod . '?' . $this->serializeParams($params));
	}

	private function post($credlyMethod, $params = array(), $authentication = array()) {
		return $this->issue('post', $credlyMethod, $params, $authentication);
	}

	private function issue($httpMethod, $credlyMethod, $params = array(), $authentication = array()) {
		for ($i = 0; $i < MAX_ATTEMPTS; $i++) {
			try {
				$request = \Httpful\Request::$httpMethod(BASE_URL . $credlyMethod)
					->addHeader('X-Api-Key', 'aff0742231d413fd28361332e76eeed2')
					->addHeader('X-Api-Secret', 'dy0J36u85+B2q62z3KzAkM35l/LycO5o4lDclnOR26POY/vEdNP6LaUQR/CLAk6XZ1Bhs4liJFUhSuR0XwcpIUZxdxhDqqXb0Hx9/xDGbKkDohj0jArbWa9o2cUAbfroUeZJOQgLwQBeNqFTxTj1IBmTMg9vOBZkIYNyPthuH7w=')
					->expectsJson();
				if ($authentication) {
					$request->authenticateWith($authentication['user'], $authentication['password']);
				}
				$response = null;
				if (!empty($params)) {
					$request->body($this->serializeParams($params));
					$request->addHeader('Content-Type', 'application/x-www-form-urlencoded');
					$response = $request->send(\Httpful\Mime::FORM);
				} else {
					$response = $request->send();
				}
				return $response->body->data;
			} catch (Httpful\Exception\ConnectionErrorException $ex) {
				error_log($ex->getMessage());
				sleep(10);
			}
		}
		throw new Exception('Max attempts exceeded');
	}

	public function token($user, $password) {
		$tokens = $this->post('authenticate', array(), array(
			'user' => $user,
			'password' => $password
		));
		if (!$tokens) {
			return null;
		} else {
			return array(
				'access_token' => $tokens->token,
				'refresh_token' => $tokens->refresh_token
			);
		} 	
	}

	public function createdBadges($token, $id) {
		return $this->get('members/' . $id . '/badges', array('access_token' => $token));
	}

	public function acceptedBadges($token) {
		return $this->get('me/badges');
	}

	public function badgeData($token, $badgeId) {
		return $this->get('badges/' . $badgeId, array('access_token' => $token));
	}

	public function giveCreditById($token, $memberId, $badgeId) {
		return $this->post('member_badges?access_token=' . $token, array(
			'member_id' => $memberId,
			'badge_id' => $badgeId,
			'notify' => '1'
		));
	}

	public function findMemberByEmail($token, $email) {
		$data = $this->get('members', array('access_token' => $token, 'email' => $email));
		if (!$data || sizeof($data) !== 1) {
			return false;
		}
		$member = $data[0];
		return $member;
	}

} 