<?php

App::uses('AppController', 'Controller');

class DomainsController extends AppController {

	public function index() {
		$this->set('domains', $this->Domain->all());
	}

    public function badges($domainId) {
        $this->Domain->id = $domainId;
        if (!$this->Domain->exists()) {
            throw new NotFoundException('Domain not found');
        }
        $this->Badge->recursive = 1;

        $playerId = $this->Auth->user('id');
        $badges = $this->Badge->allFromDomainById($domainId);
        $playerBadgesById = $this->BadgeLog->allFromPlayerByBadgeId($playerId);
        $badgeActivitiesProgress = $this->BadgeActivityProgress->allFromPlayerByBadgeIdAndActivityId($playerId);

        foreach ($badges as $badgeId => $badge) {
            $claimed =  isset($playerBadgesById[$badgeId]);
            $badges[$badgeId]['claimed'] = $claimed;
            // Caso o jogador não possua uma das badges de pré-requisito, remove a badge da lista
            // Ou seja, só exibe as badges "próximas"
            foreach ($badge['BadgeRequisite'] as $badgeRequisiteIndex => $badgeRequisite) {
                if (!@$playerBadgesById[$badgeRequisite['badge_id']]) {
                    unset($badges[$badgeId]);
                    continue 2;
                }
            }
            if (!isset($badgeActivitiesProgress[$badgeId])) {
                $badgeActivitiesProgress[$badgeId] = array();
            }
            $activitiesProgress = $badgeActivitiesProgress[$badgeId];
            $progress = 0;
            if ($claimed || empty($activitiesProgress)) {
                $progress = 100;
            } else {
                foreach ($activitiesProgress as $activityProgress) {
                    $progress += $activityProgress['BadgeActivityProgress']['progress'];
                }
                $progress /= count($activitiesProgress);
            }
            $badges[$badgeId]['progress'] = $progress;
        }
        //Ordena as badges por percentual completo
        uasort($badges, function($a, $b){
            if ($a['progress'] == $b['progress']) return 0;
            else return ($a['progress'] > $b['progress']) ? -1: 1;
        });

        $this->set('badgeActivitiesProgress', $badgeActivitiesProgress);
        $this->set('domain', $this->Domain->findById($domainId));
        $this->set('badges', $badges);
        $this->set('players', $this->Player->all(array(), 'id'));
    }
}