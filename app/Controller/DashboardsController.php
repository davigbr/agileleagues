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

    public function xp($ownerEmail = null) {
        $player = null;
        if ($ownerEmail) {
            $player = $this->Player->findByEmail($ownerEmail);
        } else {
            $player = $this->gameMaster();
        }

        if (!$player) {
            throw new NotFoundException();
        }

        $logs = $this->XpLog->query(
            'SELECT Player.*, DATE(XpLog.created) AS day, SUM(XpLog.xp) AS xp ' .
            'FROM xp_log AS XpLog ' .
            'INNER JOIN player AS Player ON Player.id = XpLog.player_id ' . 
            'INNER JOIN team AS Team ON Team.id = Player.team_id ' .
            'WHERE Team.player_id_owner = ? ' .
            'GROUP BY player_id, DATE(XpLog.created)',
            array($player['Player']['id'])
        );
        // Group logs by player and date
        $logsGrouped = array();
        $players = array();
        $dates = array();
        foreach ($logs as $log) {
            $day = $log[0]['day'];
            $dates[$day] = $day;
            $playerId = $log['Player']['id'];
            $players[$playerId] = $log['Player'];
            $logsGrouped[$day][] = array(
                'XpLog' => array('xp' => $log[0]['xp']),
                'Player' => $log['Player']
            );
        }
        krsort($logsGrouped);

        function sortByXP($a, $b) {
            if ($a['XpLog']['xp'] == $b['XpLog']['xp']) {
                return 0;
            }
            return ($a['XpLog']['xp'] > $b['XpLog']['xp']) ? -1 : 1;
        }

        foreach ($dates as $day) {
            usort($logsGrouped[$day], 'sortByXP');
        }
        $logsGrouped = array_slice($logsGrouped, 0, 20);

        $this->set('players', $players);
        $this->set('dates', $dates);
        $this->set('logs', $logsGrouped);
    }

    public function stats() {
        $activitiesReported = $this->Log->query(
            'SELECT COUNT(*) AS count, UNIX_TIMESTAMP(acquired) AS acquired FROM log ' .
            'WHERE acquired >= CURRENT_DATE - INTERVAL 30 DAY AND player_id_owner = ? ' .
            'GROUP BY acquired', array($this->gameMasterId()));
        $activitiesReportedJSON = array();
        foreach ($activitiesReported as $day) {
            $activitiesReportedJSON[] = array(
                (int)$day[0]['acquired']*1000,
                (int)$day[0]['count']
            );
        }

        $badgesClaimed = $this->Log->query(
            'SELECT COUNT(*) AS count, UNIX_TIMESTAMP(DATE(creation)) AS creation FROM badge_log ' .
            'INNER JOIN domain ON domain.id = badge_log.domain_id ' . 
            'WHERE creation >= CURRENT_DATE - INTERVAL 30 DAY AND domain.player_id_owner = ? ' .
            'GROUP BY DATE(creation)', array($this->gameMasterId()));
        $badgesClaimedJSON = array();
        foreach ($badgesClaimed as $day) {
            $badgesClaimedJSON[] = array(
                (int)$day[0]['creation']*1000,
                (int)$day[0]['count']
            );
        }

        $comments = $this->Log->query(
            'SELECT COUNT(*) AS count, UNIX_TIMESTAMP(DATE(creation)) AS creation FROM log_votes ' .
            'INNER JOIN log ON log.id = log_votes.log_id ' .
            'WHERE creation >= CURRENT_DATE - INTERVAL 30 DAY AND log.player_id_owner = ? ' .
            'GROUP BY DATE(creation)', array($this->gameMasterId()));
        $commentsJSON = array();
        foreach ($comments as $day) {
            $commentsJSON[] = array(
                (int)$day[0]['creation']*1000,
                (int)$day[0]['count']
            );
        }

        $this->set('activitiesReported', $activitiesReportedJSON);
        $this->set('badgesClaimed', $badgesClaimedJSON);
        $this->set('commentsAdded', $commentsJSON);
    }

    public function players($teamId = null) {
        $options = array(
            'order' => array('Player.name' => 'ASC'),
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
            'Domain.inactive' => 0,
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
        $this->set('neverReportedActivities', $this->Activity->neverReported($this->gameMasterId()));
        $this->set('leastReportedActivities', $this->Activity->leastReported($this->gameMasterId()));
        $this->set('mostReportedActivities', $this->Activity->mostReported($this->gameMasterId()));
    }

    public function leaderboards() {
        $this->set('activityLeaderboardsEver', $this->Activity->leaderboardsEver($this->gameMasterId()));
        $this->set('activityLeaderboardsThisWeek', $this->Activity->leaderboardsThisWeek($this->gameMasterId()));
        $this->set('activityLeaderboardsThisMonth', $this->Activity->leaderboardsThisMonth($this->gameMasterId()));
        $this->set('activityLeaderboardsLastWeek', $this->Activity->leaderboardsLastWeek($this->gameMasterId()));
        $this->set('activityLeaderboardsLastMonth', $this->Activity->leaderboardsLastMonth($this->gameMasterId()));
    }
}


