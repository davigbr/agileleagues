<?php

App::uses('AppModel', 'Model');

class Tag extends AppModel {
	
	public $useTable = 'tag';
	public $order = array('Tag.name' => 'ASC');

	public $validate = array(
		'name' => 'notEmpty',
		'color' => array(
			'notEmpty',
			'color' => array(
				'rule' => array('custom', '/\\#[A-Fa-f0-9]{6}/'),
				'message' => 'Invalid color'
			)
		),
		'bonus_type' => 'notEmpty',
		'bonus_value' => 'notEmpty'
	);

	public function allActive($playerIdOwner) {
		return $this->find('all', array(
			'conditions' => array(
				'Tag.inactive' => 0,
				'Tag.player_id_owner' => $playerIdOwner
			)
		));
	}
	
	public function simpleActive($playerIdOwner) {
		return $this->find('list', array(
			'conditions' => array(
				'Tag.inactive' => 0,
				'Tag.player_id_owner' => $playerIdOwner
			)
		));
	}


	public function topFromPlayer($playerId, $limit = 10) {
		$tags = $this->query(
			'SELECT COUNT(Log.id) as reports, Tag.* ' .  
			'FROM log_tag AS LogTag ' .
			'INNER JOIN tag AS Tag ON Tag.id = LogTag.tag_id ' .
			'INNER JOIN log AS Log ON LogTag.log_id = Log.id ' . 
			'WHERE player_id = ? ' . 
			'GROUP BY tag_id ' . 
			'ORDER BY reports DESC ' . 
			'LIMIT ' . $limit, array($playerId));
		foreach ($tags as &$tag) {
			$tag['Tag']['reports'] = $tag[0]['reports'];
			unset($tag[0]);
		}
		return $tags;
	} 
}