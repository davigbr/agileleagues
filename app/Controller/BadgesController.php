<?php

App::uses('AppController', 'Controller');

class BadgesController extends AppController {

	public function index() {
		$this->set('activitiesById', $this->Activity->all(array(), 'id'));
		$this->set('badgesById', $this->Badge->all(array(), 'id'));
		$this->Badge->recursive = 1;
		$this->set('badges', $this->Badge->all());
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

    public function edit($badgeId) {
    	if (!$this->isScrumMaster) {
			return $this->redirect('/badges');
		}
        
		$this->Badge->recursive = 1; 
        $badge = $this->Badge->findById($badgeId);

        if ($this->request->is('post') || $this->request->is('put')) {
            foreach ($this->request->data['ActivityRequisite'] as $key => $value) {
                if (!$value['activity_id']) unset($this->request->data['ActivityRequisite'][$key]);
            }
            foreach ($this->request->data['BadgeRequisite'] as $key => $value) {
                if (!$value['badge_id_requisite']) unset($this->request->data['BadgeRequisite'][$key]);
            }
            if (empty($this->request->data['ActivityRequisite'])) unset($this->request->data['ActivityRequisite']);
            if (empty($this->request->data['BadgeRequisite'])) unset($this->request->data['BadgeRequisite']);

            $this->BadgeRequisite->query('DELETE FROM badge_requisite WHERE badge_id = ? ', array($badgeId));
            $this->ActivityRequisite->query('DELETE FROM activity_requisite WHERE badge_id = ? ', array($badgeId));

            if ($this->Badge->saveAssociated($this->request->data)) {
                $this->flashSuccess(__('Badge edited successfully!'));
                return $this->redirect('/badges');
            } else {
                $this->flashError(__('Error while trying to edit badge.'));
            }
        } else {
            $this->request->data = $badge;
		}

        $domainId = $badge['Badge']['domain_id'];
		$this->set('badges', $this->Badge->simpleFromDomain($domainId));
		$this->set('activities', $this->Activity->simpleFromDomain($domainId));
    }
}