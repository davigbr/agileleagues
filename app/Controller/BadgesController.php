<?php

App::uses('AppController', 'Controller');

class BadgesController extends AppController {

	public function index() {
        $smId = $this->gameMasterId();
		$this->set('activitiesById', $this->Activity->allFromOwnerById($smId));
		$this->set('badgesById', $this->Badge->allFromOwnerById($smId));
		$this->Badge->recursive = 1;
		$this->set('badges', $this->Badge->allFromOwner($smId));
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
        $this->_save($domainId);
    }

    public function edit($domainId, $id) {
        $this->_save($domainId, $id);
    }

    public function _save($domainId, $id = null) {
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