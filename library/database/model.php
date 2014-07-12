<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_database_model {
	public $adapter = null;
	public $name = null;
	public $lang_field = array();
	public $lang_type = null;

	function __construct() {
		$this->adapter = application::get_instance()->bootstrap->resource->database->adapter;

		if (($this->lang_field || $this->lang_type) && application::get_instance()->controller) {
			$reg = application::get_instance()->controller->view->lang(true);
			if ($reg) {
				$cols = $this->metadata();
				switch($this->lang_type){
					case 'column':
						if (!array_key_exists('lang', $cols)) {
							$this->adapter->query('ALTER TABLE `'.$this->name.'` ADD `lang` int(11)');
							$this->adapter->query('ALTER TABLE `'.$this->name.'` ADD INDEX `i_lang` (`lang`)');
						}
						break;
					default:
						foreach ($this->lang_field as $el) {
							if (!array_key_exists('ml_'.$el.'_'.$reg->id, $cols)) {
								$this->adapter->query('ALTER TABLE `'.$this->name.'` ADD `ml_'.$el.'_'.$reg->id.'` '.$cols[$el]['Type'].($cols[$el]['Default'] ? ' DEFAULT '.$cols[$el]['Default'] : ''));
							}
						}
						break;
				}
			}
		}
	}

	// Упрощенный запрос на выборку одной колонки
	function fetch_col($col, $where = null, $order = null, $count = null, $offset = 0) {
		$select = new database_select();
		$select->from($this->name, $col);
		if ($where) $select->where($where);
		if ($order) $select->order($order);
		if ($count) $select->limit($count, $offset);
		return $this->adapter->fetch_col($select);
	}

	// Упрощенный запрос на выборку одного значения
	function fetch_one($col, $where = null, $order = null) {
		$select = new database_select();
		$select->from($this->name, $col)->limit(1);
		if ($where) $select->where($where);
		if ($order) $select->order($order);
		return $this->adapter->fetch_one($select);
	}

	// Упрощенный запрос на выборку пары ключ => значение
	function fetch_pairs($key, $value, $where = null, $order = null, $count = null, $offset = 0) {
		$select = new database_select();
		$select->from($this->name, array(
			$key,
			$value
		));
		if ($where) $select->where($where);
		if ($order) $select->order($order);
		if ($count) $select->limit($count, $offset);
		return $this->adapter->fetch_pairs($select);
	}

	// Упрощенный запрос на выборку всех колонок и всех рядов
	function fetch_all($where = null, $order = null, $count = null, $offset = null) {
		$select = new database_select();
		$select->from($this->name);
		if ($where) $select->where($where);
		if ($order) $select->order($order);
		if ($count) $select->limit($count, $offset);
		return $this->entity_all($this->adapter->fetch_all($select));
	}

	function entity_all($ret, $entity = null) {
		if ($ret) foreach ($ret as $k => $v) $ret[$k] = $this->entity($v, $entity);
		return $ret;
	}

	function entity($el, $entity = null) {
		$class = 'entity_'.($entity ? $entity : $this->name);
		return class_exists($class)
			? new $class($el)
			: new entity($el);
	}

	// Упрощенный запрос на выборку всех колонок одного ряда
	function fetch_row($where = null, $order = null) {
		$select = new database_select();
		$select->from($this->name);
		if ($where) $select->where($where);
		if ($order) $select->order($order);
		$ret = $this->adapter->fetch_row($select);
		if ($ret) $ret = $this->entity($ret);
		return $ret;
	}

	function fetch_next_id() {
		$ret = $this->adapter->fetch_row('SHOW TABLE STATUS WHERE `name` = '.$this->adapter->quote($this->name, true));
		return isset($ret['Auto_increment']) ? $ret['Auto_increment'] : 0;
	}

	// Упрощенный запрос на выборку максимального значения
	function fetch_max($col, $where = null, $order = null) {
		return $this->fetch_one('(MAX('.$this->adapter->quote($col, false).'))', $where, $order);
	}

	function fetch_count($where = null) {
		return $this->fetch_one('(COUNT(*))', $where);
	}

	function insert($data) {
		if ($data && is_array($data)) {
			$this->override_lang($data);
			$sql = 'INSERT INTO '.$this->adapter->quote($this->name, false).' '.$this->adapter->set_insert($data);
			$result = $this->adapter->query($sql);
			if ($result && $result->rowCount()) return $this->adapter->connection->lastInsertId();
		}
		return null;
	}

	function update($data, $where = null) {
		if ($data && is_array($data)) {
			$this->override_lang($data);
			$sql = 'UPDATE '.$this->adapter->quote($this->name, false).' '.$this->adapter->set($data);
			if ($where) {
				$w = array();
				if (is_array($where)) $w = $where;
				else $w[$where] = array();
				$sql .= ' WHERE'.$this->adapter->where($w);
			}
			$result = $this->adapter->query($sql);
			if ($result) return $result->rowCount();
		}
		return 0;
	}

	function delete($where = null) {
		$sql = 'DELETE FROM '.$this->adapter->quote($this->name, false);
		if ($where) {
			$w = array();
			if (is_array($where)) $w = $where;
			else $w[$where] = array();
			$sql .= ' WHERE'.$this->adapter->where($w);
		}
		$result = $this->adapter->query($sql);
		if ($result) return $result->rowCount();
		return 0;
	}

	function metadata() {
		$ret = array();
		$sql = 'DESCRIBE '.$this->adapter->quote($this->name, false);
		$result = $this->adapter->fetch_all($sql);
		if ($result) foreach ($result as $el) $ret[$el['Field']] = $el;
		return $ret;
	}

	function fetch_control_list($where = null, $order = null, $count = null, $offset = null) {
		$select = new database_select();
		$select->from($this->name);
		if ($where) $select->where($where);
		if ($order) $select->order($order);
		if ($count) $select->limit($count, $offset);

		return $select;
	}

	function fetch_control_card($where = null) {
		$ret = $this->entity($this->fetch_row($where));
		return $ret;
	}

	function insert_control($data) {
		return $this->insert($data);
	}

	function update_control($data, $where) {
		return $this->update($data, $where);
	}

	function delete_control($where) {
		return $this->delete($where);
	}

    public function override_lang(&$data) {
    	if ($this->lang_field || $this->lang_type) {
			$reg = application::get_instance()->controller->view->lang(true);
			if ($reg) {
				switch($this->lang_type) {
					case 'column':
						$data['lang'] = $reg->id;
						break;
					default:
						foreach ($data as $k => $v) {
							if (in_array($k, $this->lang_field)) {
								$data['ml_'.$k.'_'.$reg->id] = $v;
								unset($data[$k]);
							}
						}
						break;
				}
			}
		}
    }
}