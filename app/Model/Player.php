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
        'email' => array(
            'rule' => 'email',
            'message' => 'Invalid e-mail address.'
        ),
        'password' =>  array(
            'rule' => array('minLength', 6),
            'message' => 'Password must contain at least 6 chars.'
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
                'Player.player_type_id' => PLAYER_TYPE_DEVELOPER
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
}