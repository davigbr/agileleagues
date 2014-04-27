<?php

define('DEVELOPER_1_ID', 1);
define('DEVELOPER_2_ID', 2);
define('SCRUMMASTER_ID', 3);

define('XP_TO_REACH_LEVEL_10', 2200);
define('XP_TO_REACH_LEVEL_20', 8000);

class TestUtils {

    private $models = array(
        'XpLog',    
        'EventCompleteLog',
        'EventTaskLog',
        'EventTask',
        'EventJoinLog',
        'EventActivity',
        'Notification',
        'Timeline',
        'Log', 
        'BadgeLog', 
        'BadgeRequisite',
        'ActivityRequisite',
        'Badge', 
        'Activity', 
        'Domain', 
        'Event',
        'EventType',
        'Player',
        'PlayerType'
    );
    
    private $views = array(
        'PlayerActivityCoins',
        'PlayerTotalActivityCoins',
        'LastWeekLog',
        'BadgeClaimed',
        'BadgeActivityProgress'
    );

    public function __construct() {
        foreach ($this->models as $model) {
            $this->$model = ClassRegistry::init($model);
        }
        foreach ($this->views as $model) {
            $this->$model = ClassRegistry::init($model);
        }
    }

    public function clearDatabase() {
        foreach ($this->models as $model) {
            $deleted = $this->$model->deleteAll(array($model . '.id <>' => 0), false);
            $table = $this->$model->table;
        }
    }

    public function generatePlayers() {
        $this->PlayerType->saveMany(array(
            array('id' => PLAYER_TYPE_DEVELOPER, 'name' => 'Developer'),
            array('id' => PLAYER_TYPE_SCRUMMASTER, 'name' => 'ScrumMaster'),
            array('id' => PLAYER_TYPE_PRODUCT_OWNER, 'name' => 'Product Owner'),
        ));
        $this->Player->saveMany(array(
            array('id' => DEVELOPER_1_ID, 'player_type_id' => 1, 'name' => 'Developer 1', 'email' => 'email1@email.com', 'password' => '123456', 'repeat_password' => '123456', 'xp' => 500),
            array('id' => DEVELOPER_2_ID, 'player_type_id' => 1, 'name' => 'Developer 2', 'email' => 'email2@email.com', 'password' => '123456', 'repeat_password' => '123456', 'xp' => 100),
            array('id' => SCRUMMASTER_ID, 'player_type_id' => 2, 'name' => 'ScrumMaster', 'email' => 'scrummaster@email.com', 'password' => '123456', 'repeat_password' => '123456', 'xp' => 999),
        ));
    }

    public function generateDomains() {
        $this->Domain->saveMany(array(
            array('id' => 1, 'name' => 'Domain 1', 'abbr' => 'DM1', 'color' => '#aaa'),
            array('id' => 2, 'name' => 'Domain 2', 'abbr' => 'DM2', 'color' => '#bbb'),
        ));
    }

    public function generateActivities() {
        $this->Activity->saveMany(array(
            array('id' => 1, 'name' => 'Activity 1', 'code' => 1, 'reported' => 1, 'domain_id' => 1, 'xp' => rand(5, 100)),
            array('id' => 2, 'name' => 'Activity 2', 'code' => 2, 'reported' => 10, 'domain_id' => 1, 'xp' => rand(5, 100)),
            array('id' => 3, 'name' => 'Activity 3', 'code' => 3, 'reported' => 100, 'domain_id' => 1, 'xp' => rand(5, 100)),
            array('id' => 4, 'name' => 'Activity 4', 'code' => 4, 'reported' => 1000, 'domain_id' => 1, 'xp' => rand(5, 100)),
            array('id' => 5, 'name' => 'Activity 5', 'code' => 5, 'reported' => 10000, 'domain_id' => 2, 'xp' => rand(5, 100)),
            array('id' => 6, 'name' => 'Activity 6', 'code' => 6, 'reported' => 100000, 'domain_id' => 2, 'xp' => rand(5, 100)),
            array('id' => 7, 'name' => 'Activity 7', 'code' => 7, 'reported' => 1000000, 'domain_id' => 2, 'xp' => rand(5, 100)),
            array('id' => 8, 'name' => 'Activity 8', 'code' => 8, 'reported' => 10000000, 'domain_id' => 2, 'xp' => XP_TO_REACH_LEVEL_10),
            array('id' => 9, 'name' => 'Activity 9', 'code' => 9, 'reported' => 100000000, 'domain_id' => 2, 'xp' => XP_TO_REACH_LEVEL_20),
            array('id' => 10, 'name' => 'Activity 10', 'code' => 10, 'reported' => 0, 'domain_id' => 2, 'xp' => 1000),
        ));
    }

    public function generateInactiveActivities() {
        $this->Activity->saveMany(array(
            array('id' => 11, 'name' => 'Inactive Activity 1', 'code' => 11, 'domain_id' => 1, 'inactive' => 1),
            array('id' => 12, 'name' => 'Inactive Activity 2', 'code' => 12, 'domain_id' => 1, 'inactive' => 1),
            array('id' => 13, 'name' => 'Inactive Activity 3', 'code' => 13, 'domain_id' => 1, 'inactive' => 1),
            array('id' => 14, 'name' => 'Inactive Activity 4', 'code' => 14, 'domain_id' => 1, 'inactive' => 1),
            array('id' => 15, 'name' => 'Inactive Activity 5', 'code' => 15, 'domain_id' => 2, 'inactive' => 1),
            array('id' => 16, 'name' => 'Inactive Activity 6', 'code' => 16, 'domain_id' => 2, 'inactive' => 1),
            array('id' => 17, 'name' => 'Inactive Activity 7', 'code' => 17, 'domain_id' => 2, 'inactive' => 1),
            array('id' => 18, 'name' => 'Inactive Activity 8', 'code' => 18, 'domain_id' => 2, 'inactive' => 1),
        ));
    }


    public function generateBadges() {
        $this->Badge->saveMany(array(
            array('id' => 1, 'name' => 'Badge 1', 'code' => 1, 'domain_id' => 1, 'abbr' => 'BG1'),
            array('id' => 2, 'name' => 'Badge 2', 'code' => 2, 'domain_id' => 1, 'abbr' => 'BG2'),
            array('id' => 3, 'name' => 'Badge 3', 'code' => 3, 'domain_id' => 2, 'abbr' => 'BG3'),
            array('id' => 4, 'name' => 'Badge 4', 'code' => 4, 'domain_id' => 2, 'abbr' => 'BG4'),
        ));
    }

    public function generateBadgeRequisites() {
        $this->BadgeRequisite->saveMany(array(
            array('badge_id' => 4, 'badge_id_requisite' => 3),
            array('badge_id' => 3, 'badge_id_requisite' => 2),
            array('badge_id' => 2, 'badge_id_requisite' => 1)
        ));
    }

    public function generateActivityRequisites() {
        $this->ActivityRequisite->saveMany(array(
            array('badge_id' => 1, 'activity_id' => 1, 'count' => 1),
            array('badge_id' => 2, 'activity_id' => 2, 'count' => 1),
            array('badge_id' => 3, 'activity_id' => 3, 'count' => 1),
            array('badge_id' => 4, 'activity_id' => 4, 'count' => 1)
        ));
    }

    public function generateNotifications() {
        $this->Notification->saveMany(array(
            array('id' => 1, 'title' => 'Notification title', 'player_id' => 1, 'type' => 'success', 'read' => 0),
            array('id' => 2, 'title' => 'Notification title', 'player_id' => 1, 'type' => 'success', 'read' => 0),
            array('id' => 3, 'title' => 'Notification title', 'player_id' => 2, 'type' => 'success', 'read' => 0),
            array('id' => 4, 'title' => 'Notification title', 'player_id' => 2, 'type' => 'success', 'read' => 0),
            array('id' => 5, 'title' => 'Notification title', 'player_id' => 2, 'type' => 'success', 'read' => 1),
            array('id' => 6, 'title' => 'Notification title', 'player_id' => 2, 'type' => 'success', 'read' => 1),
            array('id' => 7, 'title' => 'Notification title', 'player_id' => 1, 'type' => 'success', 'read' => 1),
            array('id' => 8, 'title' => 'Notification title', 'player_id' => 1, 'type' => 'success', 'read' => 1),
        ));
    }



    public function generateBadgeLogs() {
        $this->BadgeLog->saveMany(array(
            array('badge_id' => 1, 'player_id' => 1),
            array('badge_id' => 2, 'player_id' => 1),
            array('badge_id' => 3, 'player_id' => 1),
            array('badge_id' => 4, 'player_id' => 1),
            array('badge_id' => 1, 'player_id' => 2),
            array('badge_id' => 2, 'player_id' => 2),
            array('badge_id' => 3, 'player_id' => 2),
            array('badge_id' => 4, 'player_id' => 2),
        ));
    }

    public function generateLogs() {
        $currentDate = (new DateTime())->format('Y-m-d');
        
        $lastWeek = new DateTime();
        $lastWeek->modify('-7 day');
        $lastWeek = $lastWeek->format('Y-m-d');

        $lastMonth = new DateTime();
        $lastMonth->modify('-1 month - 1 day');
        $lastMonth = $lastMonth->format('Y-m-d');

        $this->Log->saveMany(array(
            array('activity_id' => 1, 'player_id' => 1, 'acquired' => $currentDate),
            array('activity_id' => 2, 'player_id' => 1, 'acquired' => $currentDate),
            array('activity_id' => 3, 'player_id' => 1, 'acquired' => $lastWeek),
            array('activity_id' => 4, 'player_id' => 1, 'acquired' => $lastMonth),
            array('activity_id' => 5, 'player_id' => 2, 'acquired' => $currentDate),
            array('activity_id' => 6, 'player_id' => 2, 'acquired' => $currentDate),
            array('activity_id' => 7, 'player_id' => 2, 'acquired' => $lastWeek),
            array('activity_id' => 8, 'player_id' => 2, 'acquired' => $lastMonth),
        ), array('validate' => false));
        $result = $this->Log->query('UPDATE log SET reviewed = NOW()');
    }

    public function generateLogsNotReviewed() {
        $this->Log->saveMany(array(
            array('activity_id' => 1, 'player_id' => 1, 'acquired' => '2014-01-01'),
            array('activity_id' => 2, 'player_id' => 1, 'acquired' => '2014-01-01'),
            array('activity_id' => 3, 'player_id' => 1, 'acquired' => '2014-01-01'),
            array('activity_id' => 4, 'player_id' => 1, 'acquired' => '2014-01-01'),
            array('activity_id' => 5, 'player_id' => 2, 'acquired' => '2014-01-01'),
            array('activity_id' => 6, 'player_id' => 2, 'acquired' => '2014-01-01'),
            array('activity_id' => 7, 'player_id' => 2, 'acquired' => '2014-01-01'),
            array('activity_id' => 8, 'player_id' => 2, 'acquired' => '2014-01-01'),
        ), array('validate' => false));
    }

    public function generateEvents() {
        $this->EventType->saveMany(array(
            array('id' => EVENT_TYPE_MISSION, 'Mission', 'level_required' => EVENT_LEVEL_REQUIRED_MISSION),
            array('id' => EVENT_TYPE_CHALLENGE, 'Challenge', 'level_required' => EVENT_LEVEL_REQUIRED_CHALLENGE),
        ));
        
        $today = (new DateTime())->format('Y-m-d');
        $lastWeek = (new DateTime())->modify('-7 day')->format('Y-m-d');
        $nextWeek = (new DateTime())->modify('+7 day')->format('Y-m-d');
        $lastMonth = (new DateTime())->modify('-1 month')->format('Y-m-d');
        $nextMonth = (new DateTime())->modify('+1 month')->format('Y-m-d');

        $this->Event->saveMany(array(
            array('id' => 1, 'event_type_id' => EVENT_TYPE_MISSION, 'name' => 'Active Mission', 'start' => $today, 'end' => $nextWeek),
            array('id' => 2, 'event_type_id' => EVENT_TYPE_MISSION, 'name' => 'Future Mission', 'start' => $nextWeek, 'end' => $nextMonth),
            array('id' => 3, 'event_type_id' => EVENT_TYPE_MISSION, 'name' => 'Past Mission', 'start' => $lastMonth, 'end' => $lastWeek),
            array('id' => 4, 'event_type_id' => EVENT_TYPE_CHALLENGE, 'name' => 'Active Challenge', 'start' => $today, 'end' => $nextWeek),
            array('id' => 5, 'event_type_id' => EVENT_TYPE_CHALLENGE, 'name' => 'Future Challenge', 'start' => $nextWeek, 'end' => $nextMonth),
            array('id' => 6, 'event_type_id' => EVENT_TYPE_CHALLENGE, 'name' => 'Past Challenge', 'start' => $lastMonth, 'end' => $lastWeek),
        ), array('validate' => false));
    }

    public function generateEventTasks() {
        $this->EventTask->saveMany(array(
            array('id' => 1, 'event_id' => 1, 'name' => 'Task 1', 'description' => 'Description 1', 'xp' => rand(0, 100)),
            array('id' => 2, 'event_id' => 2, 'name' => 'Task 2', 'description' => 'Description 2', 'xp' => rand(0, 100)),
            array('id' => 3, 'event_id' => 3, 'name' => 'Task 3', 'description' => 'Description 3', 'xp' => rand(0, 100)),
            array('id' => 4, 'event_id' => 4, 'name' => 'Task 4', 'description' => 'Description 4', 'xp' => rand(0, 100)),
            array('id' => 5, 'event_id' => 5, 'name' => 'Task 5', 'description' => 'Description 5', 'xp' => rand(0, 100)),
            array('id' => 6, 'event_id' => 6, 'name' => 'Task 6', 'description' => 'Description 6', 'xp' => rand(0, 100))
        ), array('validate' => false));
    }

    public function generateEventActivities() {
        $this->EventActivity->saveMany(array(
            array('id' => 1, 'event_id' => 1, 'activity_id' => 1, 'count' => 1),
            array('id' => 2, 'event_id' => 2, 'activity_id' => 2, 'count' => 1),
            array('id' => 3, 'event_id' => 3, 'activity_id' => 3, 'count' => 1),
            array('id' => 4, 'event_id' => 4, 'activity_id' => 4, 'count' => 1),
            array('id' => 5, 'event_id' => 5, 'activity_id' => 5, 'count' => 1),
            array('id' => 6, 'event_id' => 6, 'activity_id' => 6, 'count' => 1)
        ), array('validate' => false));
    }

    public function generateEventActivityLogs() {
        $this->Log->saveMany(array(
            array('event_id' => 1, 'activity_id' => 1, 'reviewed' => date('Y-m-d'), 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 2, 'activity_id' => 2, 'reviewed' => date('Y-m-d'), 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 3, 'activity_id' => 3, 'reviewed' => date('Y-m-d'), 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 4, 'activity_id' => 4, 'reviewed' => date('Y-m-d'), 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 5, 'activity_id' => 5, 'reviewed' => date('Y-m-d'), 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 6, 'activity_id' => 6, 'reviewed' => date('Y-m-d'), 'player_id' => DEVELOPER_2_ID)
        ), array('validate' => false));
    }

    public function generateEventJoinLogs() {
        $this->EventJoinLog->saveMany(array(
            array('event_id' => 1, 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 2, 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 3, 'player_id' => DEVELOPER_2_ID)
        ), array('validate' => false));
    }

    public function generateEventTaskLogs() {
        $this->EventTaskLog->saveMany(array(
            array('event_id' => 1, 'event_task_id' => 1, 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 2, 'event_task_id' => 2, 'player_id' => DEVELOPER_2_ID),
            array('event_id' => 3, 'event_task_id' => 3, 'player_id' => DEVELOPER_2_ID)
        ), array('validate' => false));
    }

}