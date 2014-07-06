<?php

App::uses('AppController', 'Controller');

class DashboardsController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('players', 'leaderboards');
    }

    public function index() {
        
    }

    public function badges() {
        
    }

    public function players($teamId = null) {
        $options = array(
            'order' => array('Player.xp' => 'DESC'),
            'contain' => array(
                'Team',
                'PlayerType',
                'BadgeLog' => array(
                    'Badge' => array(
                        'Domain'
                    )
                )
            )
        );
        $players = array();
        if ($teamId !== null) {
            $options['conditions'] = array(
                'Player.team_id' => $teamId
            );
            $players = $this->Player->find('all', $options);
        } else if ($this->Auth->user()) {
            $players = $this->Player->allFromPlayerTeam(
                $this->Auth->user('id'), 
                $options
            );
        } else {
            throw new NotFoundException('Team not found');
        }

        $this->set('players', $players);
        $this->set('domains', $this->Domain->allFromOwner($this->gameMasterId()));
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
        $activitiesSummary = $this->PlayerActivitySummary->allFromPlayer($this->Auth->user('id'), $id);
        
        //Torna a propriedade activity_id a chave do array
        array_replace_keys($activitiesSummary, function($value){
            return $value['PlayerActivitySummary']['activity_id'];
        });

        $this->set('domain', $domain);
        $this->set('activitiesSummary', $activitiesSummary);
        return $this->render('/Dashboards/activities_domain_details');
    }

    public function activities($domainId = null) {
        if ($this->isGameMaster) {
            return $this->redirect('/domains');
        }

        if ($domainId != null) {
            return $this->domainDetails($domainId);
        }

        $differentActivitiesCompleted = $this->Player->differentActivitiesCompletedCount(
            $this->gameMasterId(),
            $this->Auth->user('id')
        );
        $activitiesCount = $this->Domain->activitiesCount($this->gameMasterId());
        $totalActivitiesCount = 0;
        $totalDifferentActivitiesCompleted = 0;

        foreach ($activitiesCount as $domainId => $count) {
            $totalActivitiesCount += $count;
        }
        foreach ($differentActivitiesCompleted as $domainId => $count) {
            $totalDifferentActivitiesCompleted += $count;
        }

        // Bring only the domains from the logged in player type
        $domains = $this->Domain->all(array(
            'Domain.player_id_owner' => $this->gameMasterId(),
            'Domain.player_type_id' => $this->Auth->user('player_type_id')
        ), 'id');

        $this->set(compact(
            'differentActivitiesCompleted', 
            'activitiesCount', 
            'domains', 
            'totalActivitiesCount', 
            'totalDifferentActivitiesCompleted')
        );

        $this->set('activitiesCompletedCount', $this->Log->playerCount($this->Auth->user('id')));
        $this->set('badgesCompletedCount', $this->BadgeLog->playerCount($this->Auth->user('id')));
        $this->set('totalActivities', $this->Activity->count($this->gameMasterId()));
        $this->set('activitiesLogged', $this->Log->count($this->gameMasterId()));
        $this->set('averageActivitiesLogged', $this->Log->average($this->gameMasterId()));
        $this->set('neverReportedActivities', $this->Activity->neverReported($this->Auth->user('player_type_id')));
        $this->set('leastReportedActivities', $this->Activity->leastReported($this->Auth->user('player_type_id')));
        $this->set('mostReportedActivities', $this->Activity->mostReported($this->Auth->user('player_type_id')));
    }

    public function leaderboards() {
        $this->set('activityLeaderboardsEver', $this->Activity->leaderboardsEver($this->gameMasterId()));
        $this->set('activityLeaderboardsThisWeek', $this->Activity->leaderboardsThisWeek($this->gameMasterId()));
        $this->set('activityLeaderboardsThisMonth', $this->Activity->leaderboardsThisMonth($this->gameMasterId()));
        $this->set('activityLeaderboardsLastWeek', $this->Activity->leaderboardsLastWeek($this->gameMasterId()));
        $this->set('activityLeaderboardsLastMonth', $this->Activity->leaderboardsLastMonth($this->gameMasterId()));
    }
}


