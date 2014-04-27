<?php

App::uses('AppController', 'Controller');

class DashboardsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('players', 'leaderboards');
    }

    public function badges() {
        
    }

    public function players() {
        $players = $this->Player->find('all', array(
            'order' => array('Player.xp' => 'DESC'),
            'contain' => array(
                'PlayerTotalActivityCoins',
                'BadgeLog' => array(
                    'Badge' => array(
                        'Domain'
                    )
                )
            )
        ));
        $this->set('players', $players);
        $this->set('collapseSidebar', true);
    }

    public function domainDetails($id) {
        $playerId = $this->Auth->user('id');
        $domain = $this->Domain->find('first', array(
            'contain' => array(
                'Activity'
            ), 
            'conditions' => array(
                'Domain.id' => $id
            )
        ));
        $activityCoins = $this->PlayerActivityCoins->allFromPlayer($this->Auth->user('id'), $id);
        
        //Torna a propriedade activity_id a chave do array
        array_replace_keys($activityCoins, function($value){
            return $value['PlayerActivityCoins']['activity_id'];
        });

        $this->set('domain', $domain);
        $this->set('activityCoins', $activityCoins);
        return $this->render('/Dashboards/activities_domain_details');
    }

    public function activities($domainId = null) {
        if ($domainId != null) {
            return $this->domainDetails($domainId);
        }

        $differentActivitiesCompleted = $this->Player->differentActivitiesCompletedCount($this->Auth->user('id'));
        $activitiesCount = $this->Domain->activitiesCount();
        $totalActivitiesCount = 0;
        $totalDifferentActivitiesCompleted = 0;

        foreach ($activitiesCount as $domainId => $count) {
            $totalActivitiesCount += $count;
        }
        foreach ($differentActivitiesCompleted as $domainId => $count) {
            $totalDifferentActivitiesCompleted += $count;
        }

        $domains = $this->Domain->all(array(), 'id');

        $this->set(compact(
            'differentActivitiesCompleted', 
            'activitiesCount', 
            'domains', 
            'totalActivitiesCount', 
            'totalDifferentActivitiesCompleted')
        );

        $this->set('activitiesCompletedCount', $this->Log->playerCount($this->Auth->user('id')));
        $this->set('badgesCompletedCount', $this->BadgeLog->playerCount($this->Auth->user('id')));
        $this->set('totalActivities', $this->Activity->count());
        $this->set('activitiesLogged', $this->Log->find('count'));
        $this->set('averageActivitiesLogged', $this->Log->average());

        $this->set('neverReportedActivities', $this->Activity->neverReported());
        $this->set('leastReportedActivities', $this->Activity->leastReported());
        $this->set('mostReportedActivities', $this->Activity->mostReported());
    }

    public function leaderboards() {
        $this->set('activityLeaderboardsEver', $this->Activity->leaderboardsEver());
        $this->set('activityLeaderboardsThisWeek', $this->Activity->leaderboardsThisWeek());
        $this->set('activityLeaderboardsThisMonth', $this->Activity->leaderboardsThisMonth());
        $this->set('activityLeaderboardsLastWeek', $this->Activity->leaderboardsLastWeek());
        $this->set('activityLeaderboardsLastMonth', $this->Activity->leaderboardsLastMonth());
    }
}


