<?php

App::uses('AppController', 'Controller');

class BadgesController extends AppController {

    public $components = array('Credly');

	public function index() {
        $smId = $this->gameMasterId();
		$this->set('activitiesById', $this->Activity->allFromOwnerById($smId));
		$this->set('badgesById', $this->Badge->allFromOwnerById($smId));
		$this->Badge->recursive = 1;
		$this->set('badges', $this->Badge->allFromOwner($smId));
	}

    public function beforeFilter() {
        parent::beforeFilter();
        $gameMaster = $this->gameMaster();
        $this->set('gameMasterCredlyAccountSetup', (bool)$gameMaster['Player']['credly_id']);
    }

    public function credlyGive($badgeLogId) {
        if (!$this->isGameMaster) {
            throw new ForbiddenException();
        }
        $badgeLog = $this->BadgeLog->findById($badgeLogId);
        if (!$badgeLog) {
            throw new NotFoundException();
        }
        try {
            $gameMaster = $this->gameMaster();
            $token = $gameMaster['Player']['credly_access_token'];
            $memberId = $badgeLog['Player']['credly_id'];
            $badgeId = $badgeLog['Badge']['credly_badge_id'];

            $data = $this->Credly->giveCreditById($token, $memberId, $badgeId);
            $this->BadgeLog->save(array(
                'id' => $badgeLogId,
                'credly_given' =>  1
            ));
            $this->flashSuccess(__('Credly Badge successfully given!'));
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            $this->flashError(__('Error while trying to give credit. Please try again later.'));
            
        }
        return $this->redirect('/badges/claimed');
    }

    public function credlyUpdate($badgeId, $credlyBadgeId = null, $redirect = true) {
        if (!$this->isGameMaster) {
            throw new ForbiddenException();
        }
        if ($credlyBadgeId === null) {
            $badge = $this->Badge->findById($badgeId);
            if (!$badge) {
                throw new NotFoundException();
            }
            $credlyBadgeId = $badge['Badge']['credly_badge_id'];
        }

        try {
            $gameMaster = $this->gameMaster();
            $token = $gameMaster['Player']['credly_access_token'];
            $data = $this->Credly->badgeData($token, $credlyBadgeId);
            $badgeUpdate = array(
                'id' => $badgeId,
                'credly_badge_name' => $data->title,
                'credly_badge_image_url' => $data->image_url
            );
            $this->Badge->save($badgeUpdate);
            if ($redirect) {
                $this->flashSuccess(__('Credly data updated successfully!'));
                return $this->redirect('/badges/claimed');
            }
        } catch (Exception $ex) {
            $this->flashWarning(__('Could not find Credly badge data.'));
        }
    }

    public function inactivate($badgeId, $confirm = false) {
        if (!$this->isGameMaster) {
            throw new ForbiddenException();
        }
        $badge = $this->Badge->findById($badgeId);
        if (!$badge) {
            throw new NotFoundException();
        }

        if ($confirm) {
            $data = array(
                'id' => $badgeId,
                'inactive' => 1
            );
            if ($this->Badge->save($data)) {
                $this->flashSuccess(__('Badge inactivated successfully!'));
            } else {
                $this->flashError(__('Error while trying to inactivate badge.'));
            }
            return $this->redirect('/badges');
        } 

        $this->set('badge', $badge);
    }

    public function view($badgeId) {
    	$playerId = $this->Auth->user('id');
    	$requiredActivitiesProgress = $this->BadgeActivityProgress->allFromBadgeAndPlayer($badgeId, $playerId);
    	$this->Badge->recursive = 1;
    	$canClaim = true;
    	foreach ($requiredActivitiesProgress as $requiredActivityProgress) {
    		if ($requiredActivityProgress['BadgeActivityProgress']['progress'] != 100) {
    			$canClaim = false;
    		}
    	}

    	$badgesClaimed = $this->BadgeClaimed->allFromPlayerByBadgeId($playerId);
        $this->set('badge', $this->Badge->findById($badgeId));
        $this->set('requiredActivitiesProgress', $requiredActivitiesProgress);
        $this->set('badgesClaimed', $badgesClaimed);
        $this->set('claimed', (bool)$badgesClaimed[$badgeId]['BadgeClaimed']['claimed']);
        $this->set('canClaim', $canClaim);
    }

    public function claim($badgeId) {
		$playerId = $this->Auth->user('id');
        try {
            $this->Badge->claim($playerId, $badgeId);

            $funnyMessages = array(
                'Mother of god, you are AWESOME!',
                'Has someone been taking classes?',
                "We don't accept anything less than your BEST!"
            );

            $badge = $this->Badge->findById($badgeId);
            $badgeName = $badge['Badge']['name'];

    		$this->flashSuccess(__('Congratulations! You have claimed the <strong>%s</strong> badge. %s', $badgeName, $funnyMessages[rand(0, count($funnyMessages)-1)]));
    		return $this->redirect('/domains/badges/' . $badge['Badge']['domain_id']);
    	} catch (ModelException $ex) {
    		$this->flashError(__('Error while trying to claim badge: ' . $ex->getMessage()));
    	}

		return $this->redirect('/badges');
    }

    public function add($domainId) {
        $this->save($domainId);
    }

    public function edit($domainId, $id) {
        $this->save($domainId, $id);
    }

    // List all claimed badges
    public function claimed() {
        if (!$this->isGameMaster) {
            throw new ForbiddenException();
        }
        $this->set('logs', $this->BadgeLog->find('all', array(
            'conditions' => array(
                'Badge.player_id_owner' => $this->gameMasterId(),
            ),
            'contain' => array(
                'Badge' => array(
                    'Domain'
                ),
                'Player'
            )
        )));
    }

    private function save($domainId, $id = null) {
        if (!$this->isGameMaster) {
            throw new ForbiddenException();
        }

        if ($this->request->is('post') || $this->request->is('put')) {

            $this->request->data['Badge']['player_id_owner'] = $this->Auth->user('id');
            $this->request->data['Badge']['domain_id'] = $domainId;

            foreach ($this->request->data['ActivityRequisite'] as $key => $value) {
                if (!$value['activity_id']) unset($this->request->data['ActivityRequisite'][$key]);
            }
            foreach ($this->request->data['BadgeRequisite'] as $key => $value) {
                if (!$value['badge_id_requisite']) unset($this->request->data['BadgeRequisite'][$key]);
            }
            if (empty($this->request->data['ActivityRequisite'])) unset($this->request->data['ActivityRequisite']);
            if (empty($this->request->data['BadgeRequisite'])) unset($this->request->data['BadgeRequisite']);

            if ($this->Badge->saveBadge($id, $this->request->data, $this->gameMasterId())) {
                // Search for Credly data
                $credlyBadgeId = $this->request->data['Badge']['credly_badge_id'];
                if ($credlyBadgeId) {
                    $this->credlyUpdate($id, $credlyBadgeId, false);
                }
                $this->flashSuccess(__('Badge saved successfully!'));
                return $this->redirect('/badges');
            } else {
                $this->flashError(__('Error while trying to edit badge.'));
            }
        } else {
            if ($id !== null) {
                $badge = $this->Badge->find('first', array(
                    'conditions' => array(
                        'Badge.id' => $id
                    ),
                    'contain' => array(
                        'Domain',
                        'BadgeRequisite',
                        'ActivityRequisite' => array(
                            'Tags' => array('id')
                        )
                    )
                ));
                if (!$badge) {
                    throw new NotFoundException();
                }
                $this->request->data = $badge;
            }
        }
        $this->set('domain', $this->Domain->findById($domainId));
        $this->set('badges', $this->Badge->simpleFromDomain($domainId));
        $this->set('activities', $this->Activity->simpleFromDomain($domainId));
        $this->set('tags', $this->Tag->allActive($this->gameMasterId()));
    }

}