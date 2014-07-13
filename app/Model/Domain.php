<?php

App::uses('AppModel', 'Model');

class Domain extends AppModel {
	
	public $useTable = 'domain';

	public $order = array('Domain.player_type_id' => 'ASC', 'Domain.name' => 'ASC');

	public $validate = array(
		'abbr' => 'notEmpty',
		'color' => array(
			'notEmpty',
			'color' => array(
				'rule' => array('custom', '/\\#[A-Fa-f0-9]{6}/'),
				'message' => 'Invalid color'
			)
		),
		'player_type_id' => 'notEmpty',
		'icon' => 'notEmpty',
		'name' => 'notEmpty',
		'description' => 'notEmpty'
	);

	public $hasMany = array(
		'Badge',
		'Activity' => array(
			'conditions' => array('Activity.inactive' => false),
			'order' => 'Activity.name'
		)
	);

	public function inactivate($domainId) {
		$this->begin();
		try {
			$this->updateAll(array('Domain.inactive' => 1), array('Domain.id' => $domainId));
			$this->Activity->updateAll(array('Activity.inactive' => 1), array('Activity.domain_id' => $domainId));
			$this->Badge->updateAll(array('Badge.inactive' => 1), array('Badge.domain_id' => $domainId));

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			throw $ex;
		}
	}

	public function allFromOwner($playerIdOwner) {
		return $this->all(array(
			'Domain.player_id_owner' => $playerIdOwner
		));
	}

	public function simpleFromOwner($playerIdOwner) {
		return $this->simple(array(
			'Domain.player_id_owner' => $playerIdOwner
		));
	}

	public function activitiesCount($playerIdOwner) {
		$data = $this->query('
			SELECT * FROM domain_activities_count AS Domain
			WHERE player_id_owner = ?
		', array($playerIdOwner));
		$list = array();
		// Return a list where the key is the domain_id and the value is the activities count
		foreach ($data as $row) {
			$list[$row['Domain']['domain_id']] = $row['Domain']['count'];
		}
		return $list;
	}
}