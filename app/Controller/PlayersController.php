<?php

App::uses('AppController', 'Controller');

class PlayersController extends AppController {

	public $uses = array('Team');
	public $components = array('Email', 'Utils', 'Credly');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login', 'logout', 'join', 'signup', 'signin', 'reset', 'resend');
	}

	public function index() {
		$this->set('players', $this->Player->allFromPlayerTeam($this->Auth->user('id')));
	}

	public function invited() {
		$this->set('players', $this->Player->allNotVerified($this->Auth->user('id')));
	}

	public function credly() {
		if ($this->request->is('post') || $this->request->is('put')) {
			// Validation is not necessary
			unset($this->Player->validate);
			$data = $this->request->data['Credly'];

			if ($this->isGameMaster) {
				if (!$data['accept']) {
					$this->flashError(__('You must authorize Agile Leagues to give credits using your Credly account in order to procede with the integration.'));
					return; 
				}
				try {
					$tokens = $this->Credly->token($data['credly_email'], $data['credly_password']);
					if (!$tokens) {
						$this->flashError(__('Unable to authenticate. Please be sure that your email and password are correct.'));
						return;
					}
					$member = $this->Credly->findMemberByEmail($tokens['access_token'], $data['credly_email']);

					$player = array(
						'id' => $this->Auth->user('id'),
						'credly_id' => $member->id,
						'credly_email' => $data['credly_email'],
						'credly_access_token' => $tokens['access_token'],
						'credly_refresh_token' => $tokens['refresh_token']
					);

					$this->Player->save($player);
					$this->flashSuccess(__('Credly account setup successfully!'));
					return $this->redirect('/players/myaccount');

				} catch (Exception $ex) {
					error_log($ex->getMessage());
					$this->flashError(__('An error occured while trying to get your token. Please try again later.'));
				}
			} else {
				try {
					$gameMaster = $this->gameMaster();
					$token = $gameMaster['Player']['credly_access_token'];
					$member = $this->Credly->findMemberByEmail(
						$token, 
						$data['credly_email']
					);

					$player = array(
						'id' => $this->Auth->user('id'),
						'credly_id' => $member->id,
						'credly_email' => $data['credly_email']
					);

					$this->Player->save($player);
					$this->flashSuccess(__('Credly account setup successfully!'));
					return $this->redirect('/players/myaccount');

				} catch (Exception $ex) {
					error_log($ex->getMessage());
					$this->flashError(__('An error occured while trying to communicate with Credly. Please try again later.'));
				}
			}
		}
	}

	public function signup() {
		$this->layout = 'institutional';
		$this->set('title_for_layout', 'Sign Up');

		if ($this->request->is('post') || $this->request->is('put')) {
			// Ignore repeat password validation rule
			unset($this->Player->validate['repeat_password']);

			$this->request->data['Player']['player_type_id'] = PLAYER_TYPE_GAME_MASTER;

			if ($this->Player->save($this->request->data)) {

				$name = $this->request->data['Player']['name'];
				$email = $this->request->data['Player']['email'];
				$hash = $this->Utils->playerHash($this->Player->id);

				$this->Email->template(
					'signup', array(
						'name' => $name,
						'hash' => $hash
					)
				);
				$this->Email->subject(__('%s, Welcome to Agile Leagues', $name));
				$this->Email->send($email);
				
				// Update the player record with the hash
				$this->Player->save(array(
					'Player' => array(
						'id' => $this->Player->id,
						'hash' => $hash
					)
				));

				$this->flashSuccess(__('Account created successfully! A verification email message was sent to your address: %s.', $email));
				return $this->redirect('/');
			} else {
				$this->flashError(__('Please check the fields below, there are validation errors :('));
			}
		}

		$this->set('playerTypes', array(PLAYER_TYPE_GAME_MASTER => 'GameMaster'));
	}

	private function verify($player) {
		$player['Player']['verified_in'] = date('Y-m-d H:i:s');
		if ($this->Player->save($player)) {
			$this->flashSuccess(__('Account verified successfully!'));
			$player = $this->Player->findById($player['Player']['id']);
			$this->Auth->login($player['Player']);
			switch ($player['Player']['player_type_id']) {
				case PLAYER_TYPE_PLAYER:
					return $this->redirect($this->Auth->redirectUrl());
				case PLAYER_TYPE_GAME_MASTER:
				default :
					return $this->redirect('/pages/welcome');
			}
		} else {
			$this->flashError(__('There are validation errors.'));
		}
	}

	public function join($hash = null) {
		$this->set('title_for_layout', 'Join');

		if ($hash === null || empty($hash)) {
			throw new NotFoundException();
		}

		$player = $this->Player->findByHash($hash);
		$this->set('player', $player);

		if (!$player) {
			throw new NotFoundException();
		} else if ($player['Player']['verified_in']) {
			$this->flashError(__('This account has already been verified.'));
			return $this->redirect('/');
		} else {
			$id = $player['Player']['id'];

			// If the password is already defined, proceed with instant verification
			if ($player['Player']['password']) {
				$this->verify(array(
					'Player' => array(
						'id' => $player['Player']['id']
					)
				));
			}

			if ($this->request->is('post') || $this->request->is('put')) {
				$player = $this->request->data;
				$this->verify($player);
			} else {
				$this->request->data = array(
					'Player' => array('id' => $id)
				);
			}
		}
	}

	public function resendInvitation($playerId) {
		$this->_sendInvitationMessage($playerId);
		return $this->redirect('/players/invited');
	}

	private function _sendInvitationMessage($playerId) {
		$player = $this->Player->findById($playerId);

		$team = $this->Team->findById($player['Player']['team_id']);
		$gameMasterName = $this->Auth->user('name');
		$teamName = $team['Team']['name'];
		$email = $player['Player']['email'];

		$hash = $this->Utils->playerHash($playerId);
		$this->Email->template(
			'game_master_invitation', array(
				'gameMasterName' => $gameMasterName, 
				'teamName' => $teamName,
				'hash' => $hash
			)
		);
		// Update player hash
		$this->Player->query('UPDATE player SET hash = ? WHERE id = ?', array($hash, $playerId));

		$this->Email->subject(__('%s invited you to join %s on Agile Leagues', $gameMasterName, $teamName));
		$this->Email->send($email);
		$this->flashSuccess(__('Player invited successfully! An account verification email message was sent to %s.', $email));
	}

	public function invite() {
		$this->set('title_for_layout', 'Invite');

		if (!$this->isGameMaster) {
			throw new ForbiddenException();
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			// Ignore password validation rules because the password is set after the account is verified
			unset($this->Player->validate['password']);
			unset($this->Player->validate['repeat_password']);

			$this->Player->validate['team_id'] = 'notEmpty';
			$this->request->data['Player']['player_type_id'] = PLAYER_TYPE_PLAYER;

			if ($this->Player->save($this->request->data)) {
				$this->_sendInvitationMessage($this->Player->id);
				return $this->redirect('/players');
			} else {
				$this->flashError(__('There are validation errors.'));
			}
		}

		$this->set('teams', $this->Team->simpleFromGameMaster($this->Auth->user('id')));
	}

	public function signin() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirectUrl());
			} else {
				$this->flashError(__('Invalid email and/or password.'));
			}
		}
		return $this->redirect('/pages/home/');
	}

	public function login(){
		$this->set('title_for_layout', 'Login');

		if ($this->Auth->user() != null) {
			return $this->redirect($this->Auth->redirectUrl());
		}
		if ($this->request->is('post')) {
			// Login por AJAX
			if ($this->Auth->login()) {
				$this->set('login_status', 'success');
			} else {
				$this->set('login_status', 'invalid');
			}
			$this->set('_serialize', array('login_status'));
			$this->layout = 'ajax';
		} else {
			$this->layout = 'login';
		}
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}

	// Change team
	public function team($id) {
		$this->set('title_for_layout', 'Change Team');

		$this->set('teams', $this->Team->simple());

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Player->save($this->request->data)) {
				$this->flashSuccess(__('Player saved successfully!'));
				return $this->redirect('/players');
			} 
			else {
				//@codeCoverageIgnoreStart
				$this->flashError(__('Error while trying to save player.'));
				// @codeCoverageIgnoreEnd
			}
		} else {
			$this->request->data = $this->Player->findById($id);
			if (!$this->request->data) {
				throw new NotFoundException();
			}
		}
	}

	public function myaccount() {
		$this->gameMasterCredlyAccountSetup = false;
		if (!$this->isGameMaster) {
			$gameMaster = $this->gameMaster();
			if ($gameMaster['Player']['credly_email']) {
				$this->gameMasterCredlyAccountSetup = true;
			}
		}
		$this->set('gameMasterCredlyAccountSetup', $this->gameMasterCredlyAccountSetup);

		$this->set('title_for_layout', 'Account');

		if ($this->request->is('get')) {
			$this->request->data = $this->player;
		} else {
			if ($this->Player->save($this->request->data)) {
				unset ($this->request->data['Player']['password']);
				unset ($this->request->data['Player']['repeat_password']);
				$this->flashSuccess(__('Data updated successfully!'));
			} else {
				$this->flashError(__('Error while trying to edit your data :('));
			}
		}
	}

	public function reset($hash = null) {
		$this->layout = 'institutional';
		$this->set('title_for_layout', 'Reset Password');

		if ($hash !== null) {
			$player = $this->Player->findByHash($hash);
			if (!$player) {
				throw new NotFoundException();
			}
			if ($this->request->is('put') || $this->request->is('post')) {
				if (!$player['Player']['verified_in']) {
					$this->request->data['Player']['verified_in'] = date('Y-m-d H:i:s');
				}

				if ($this->Player->save($this->request->data)) {
					$this->flashSuccess(__('Password changed successfully.'));
					$player = $this->Player->findById($player['Player']['id']);
					$this->Auth->login($player['Player']);
					return $this->redirect($this->Auth->redirectUrl());
				} else {
					$this->flashError(__('There are validation errors.'));
				}
			} else {
				$this->request->data = array('Player' => array('id' => $player['Player']['id']));
			}
			$this->render('reset_password');
		} else if ($this->request->is('put') || $this->request->is('post')) {
			$email = @$this->request->data['Player']['email'];
			$player = $this->Player->findByEmail($email);
			if (!$player) {
				$this->flashError(__('This email address is not registered.'));
				return;
			} 
			$name = $player['Player']['name'];
			$hash = $this->Utils->playerHash($player['Player']['id']);

			$this->Email->template(
				'reset', array(
					'name' => $name,
					'hash' => $hash
				)
			);
			$this->Email->subject(__('%s, please reset your password or verify your account', $name));
			$this->Email->send($email);
			
			// Update the player record with the hash
			$this->Player->save(array(
				'Player' => array(
					'id' => $player['Player']['id'],
					'hash' => $hash
				)
			));

			$this->flashSuccess(__('An email message was sent to %s containing further instructions.', $email));
			return $this->redirect('/');
		}
	}
}