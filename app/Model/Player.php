<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('AppModel', 'Model');

class Player extends AppModel {
	
	public $useTable = 'player';
    public $order = array('Player.player_type_id' => 'ASC', 'Player.name' => 'ASC');
	public $belongsTo = array('PlayerType', 'Team');
    public $hasOne = array('PlayerTotalActivityCoins');
    public $hasMany = array('PlayerActivityCoins', 'Notification', 'BadgeLog');

    public $validate = array(
        'name' => 'notEmpty',
        'email' => array(
            'email' => array(
                'rule' => 'email',
                'message' => 'Invalid e-mail address.'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'It seems this e-mail address is already registered.'
            )    
        ),
        'password' => array(
            'between' => array(
                'rule' => array('between', 6, 20),
                'message' => 'Password length must be between 6 and 20 characters.'
            )
        ),
        'repeat_password' => array(
            'rule' => array('repeatPasswordRule'),
            'message' => 'Password and Repeat Password must be identical.' 
        )
    );
    
    public $virtualFields = array();

    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $alias = $this->alias;
        $this->virtualFields['level'] = "player_level({$alias}.xp)";
        $this->virtualFields['next_level_total_xp'] = "FLOOR(100 * POW(player_level({$alias}.xp), 3/2))";
        $this->virtualFields['next_level_xp'] = "FLOOR(100 * POW(player_level({$alias}.xp), 3/2)) - FLOOR(100 * POW(-1 + player_level({$alias}.xp), 3/2))";
        $this->virtualFields['next_level_xp_completed'] = "{$alias}.xp - FLOOR(100 * POW(FLOOR(0.0464159 * POW({$alias}.xp, 2/3)), 3/2))";
        $this->virtualFields['progress'] = "100*({$alias}.xp - FLOOR(100 * POW(FLOOR(0.0464159 * POW({$alias}.xp, 2/3)), 3/2)))/(FLOOR(100 * POW(player_level({$alias}.xp), 3/2)) - FLOOR(100 * POW(-1 + player_level({$alias}.xp), 3/2)))";
        $this->virtualFields['title'] = "SELECT title FROM title WHERE min_level > player_level({$alias}.xp) ORDER BY min_level ASC LIMIT 1";
    }


    public function beforeSave($options = array()) {
        if (isset($this->data['Player']['password'])) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data['Player']['password'] = $passwordHasher->hash(
                $this->data['Player']['password']
            );
        }
        return true;
    }

	public function differentActivitiesCompletedCount($playerId) {
        $query = 'SELECT different_activities_completed, domain_id, domain_name ';
        $query .= 'FROM different_activities_completed AS Data WHERE player_id = ?';
		$rows = $this->query($query, array((int)$playerId));
        $list = array();
        foreach ($rows as $row) {
            $list[$row['Data']['domain_id']] = $row['Data']['different_activities_completed'];
        }
        ksort($list);
        return $list;
	}

    public function _scrumMaster() {
        $sm = $this->find('first', array(
            'conditions' => array(
                'Player.player_type_id' => PLAYER_TYPE_SCRUMMASTER
            )
        ));
        if (!$sm) {
            throw new Exception('ScrumMaster not found!');
        }
        return $sm;
    }

    public function level($xp) {
        return (int)(1 + 0.0464159 * pow($xp, 2/3));
    }

    public function xp($level) {
        return 100*pow($level, 3/2);
    }

    public function developersCount() {
        return $this->find('count', array(
            'conditions' => array(
                'Player.player_type_id' => PLAYER_TYPE_DEVELOPER,
                'Player.verified_in IS NOT NULL'
            )
        ));
    }

    public function repeatPasswordRule($check = array()) {
        if (isset($this->data['Player']['password'])) {
            $password = $this->data['Player']['password'];
            $repeatPassword = @$this->data['Player']['repeat_password'];
            if ($password !== $repeatPassword) {
                return false;
            }
        }
        return true;
    }
	

    public function freeDeveloperList() {
        return $this->find('list', array(
            'conditions' => array(
                'Player.player_type_id' => PLAYER_TYPE_DEVELOPER,
                'Player.team_id IS NULL'
            )
        ));
    }

    public function scrumMasterList() {
        return $this->find('list', array(
            'conditions' => array(
                'Player.player_type_id' => PLAYER_TYPE_SCRUMMASTER
            )
        ));
    }

    public function productOwnerList() {
        return $this->find('list', array(
            'conditions' => array(
                'Player.player_type_id' => PLAYER_TYPE_PRODUCT_OWNER
            )
        ));
    }

    public function scrumMasterId($playerId) {
        if (!$playerId) return 0; 
        
        $player = $this->findById($playerId);
        if (!$player) return 0;

        if ($player['Player']['player_type_id'] == PLAYER_TYPE_SCRUMMASTER) {
            return $playerId;
        } else {
            $team = $this->Team->findById($player['Player']['team_id']);
            if ($team) {
                return $team['Team']['player_id_scrummaster'];
            } else {
                return 0;
            }
        }
    }

    public function visibleTeams($id) {
        $player = $this->findById($id);
        if ($player) {
            switch ((int)$player['Player']['player_type_id']) {
                case PLAYER_TYPE_DEVELOPER: 
                case PLAYER_TYPE_PRODUCT_OWNER: 
                    return array((int)$player['Player']['team_id']);
                case PLAYER_TYPE_SCRUMMASTER:
                    $teams = $this->Team->find('all', array(
                        'conditions' => array(
                            'Team.player_id_scrummaster' => $id
                        )
                    ));
                    $ids = array();
                    foreach ($teams as $team) {
                        $ids[] = (int)$team['Team']['id'];
                    }
                    return $ids;
            }
        }
        return array();
    }

    public function allFromPlayerTeam($playerId, $options = array()) {
        $conditions = array( 
            'OR' => array(
                'Player.team_id' => $this->visibleTeams($playerId),
                'Player.id' => $this->scrumMasterId($playerId)
            ),
            'Player.verified_in IS NOT NULL'
        );
        return $this->findAll('id', array_merge(
            array('conditions' => $conditions), 
            $options
        ));
    }

    public function allFromPlayerScrumMasterTeams($playerId, $options = array()) {
        $conditions = array( 
            'OR' => array(
                'Player.team_id' => $this->visibleTeams($this->scrumMasterId($playerId)),
                'Player.id' => $this->scrumMasterId($playerId)
            ),
            'Player.verified_in IS NOT NULL'
        );
        return $this->findAll('id', array_merge(
            array('conditions' => $conditions), 
            $options
        ));
    }

    public function simpleVerifiedFromPlayerTeam($playerId, $options = array()) {
        $conditions = array(
            'Player.verified_in IS NOT NULL',
            'OR' => array(
                'Player.team_id' => $this->visibleTeams($playerId),
                'Player.id' => $this->scrumMasterId($playerId)
            )
        );
        return $this->find('list', array_merge(
            array('conditions' => $conditions), $options
        ));
    }

}