<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

	public $recursive = 0;

	public $actsAs = array('Containable');

	public $uses = array();

	public function _add($data) {
		$saved = $this->save($data);
		if ($saved === false) {
			throw new Exception('Could not save ' . $this->name);
		}
	}

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		foreach ($this->uses as $model) {
			if (!isset($this->$model)) {
				$this->$model = ClassRegistry::init($model);
			}
		}
	}

	public function beforeSave($options = array()) {
		$id = isset($this->data[$this->name]['id']);
		return $id? $this->beforeUpdate($options) : $this->beforeInsert($options);
	}

	public function beforeInsert($options = array()) {
	}

	public function beforeUpdate($options = array()) {
	}

	public function begin() {
		return $this->getDataSource()->begin();
	}
	
	public function commit() {
		return $this->getDataSource()->commit();
	}

	public function rollback() {
		return $this->getDataSource()->rollback();
	}

	public function simple($conditions = array()) {
		return $this->find('list', array('conditions' => $conditions));
	}

	public function all($conditions = array(), $key = false) {
		$rows = $this->find('all', array('conditions' => $conditions));
		if ($key) {
			$list = array();
			foreach ($rows as $row) {
				$list[$row[$this->name][$key]] = $row;
			}
			return $list;
		} else {
			return $rows;
		}
	}
}
