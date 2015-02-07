<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_database_select {
	public $adapter = null;
	public $parts = array();
	public $sql = '';

	function __construct($sql = '', $adapter = null) {
		$this->adapter = $adapter ? $adapter : application::get_instance()->bootstrap->resource->database->adapter;
		$this->sql = $sql;
	}

	// Выражение FROM
	public function from($name, $cols = '*') {
		return $this->_join('from', null, $name, $cols);
	}

	// Выражение JOIN
	public function join($name, $on, $cols = '') {
		return $this->_join('inner', $on, $name, $cols);
	}

	// Выражение LEFT JOIN
	public function join_left($name, $on, $cols = '') {
		return $this->_join('left', $on, $name, $cols);
	}

	public function _join($type, $on, $name, $cols = '') {
		if (is_array($name)) {
			$keys = array_keys($name);
			$alias = $keys[0];
			$table = $name[$alias];
		}
		else {
			$alias = null;
			$table = $name;
		}
		if (!isset($this->parts['join'])) $this->parts['join'] = array();
		$this->parts['join'][] = array(
			'type' => $type,
			'on' => $on,
			'table' => $table,
			'alias' => $alias,
			'cols' => $cols
		);
		return $this;
	}

	// Выражение WHERE
	public function where() {
		$args = func_get_args();
		if (!isset($this->parts['where'])) $this->parts['where'] = array();
		if ($args[0] instanceof data) $args[0] = $args[0]->to_array();
		if (is_array($args[0])) $this->parts['where'] = array_merge($this->parts['where'], $args[0]);
		else {
			$k = array_shift($args);
			if (is_string($k)) $this->parts['where'][$k] = $args;
		}
		return $this;
	}

	// Выражение ORDER
	public function order() {
		$args = func_get_args();
		if (!isset($this->parts['order'])) $this->parts['order'] = array();
		if (is_array($args[0])) $this->parts['order'] = array_merge($this->parts['order'], $args[0]);
		else $this->parts['order'][] = array_shift($args);
		return $this;
	}

	// Выражение GROUP
	public function group() {
		$args = func_get_args();
		if (!isset($this->parts['group'])) $this->parts['group'] = array();
		$this->parts['group'][] = array_shift($args);
		return $this;
	}

	// Выражение LIMIT
	public function limit($count, $offset = 0) {
		$this->parts['limit'] = array(
			'count' => $count,
			'offset' => $offset
		);
		return $this;
	}

	// Сборка запроса в строку
	public function assemble() {
		if ($this->sql) {
			$sql = $this->sql;
		}
		else {
			$sql = 'SELECT ';

			// Собираем секцию FROM/JOIN
			$joins = @$this->parts['join'];
			if ($joins) {
				$sql_col = '';
				foreach ($joins as $join) {
					$cols = $join['cols'];
					if (is_array($cols)) {
						$n = 0;
						foreach ($cols as $k => $v) {
							$sql_col .= ($sql_col ? ', ' : '').(is_numeric($k)
								? $this->adapter->quote($v, false)
								: $this->adapter->quote($v, false).' AS '.$this->adapter->quote($k, false)
							);
							$n++;
						}
					}
					else if ($cols == '*') $sql_col .= ($sql_col ? ', ' : '').($join['alias'] ? $join['alias'].'.' : '').$cols;
					else if ($cols) $sql_col .= ($sql_col ? ', ' : '').$this->adapter->quote($cols, false);
				}
				if ($sql_col) $sql .= $sql_col;
				foreach ($joins as $join) {
					$sql .= ' '.strtoupper($join['type']).($join['type'] == 'from' ? '' : ' JOIN').' '.$this->adapter->quote($join['table'], false);
					if ($join['alias']) $sql .= ' AS '.$this->adapter->quote($join['alias'], false);
					if ($join['on']) $sql .= ' ON '.$join['on'];
				}
			}

			// Собираем секцию WHERE
			if (@$this->parts['where']) $sql .= ' WHERE'.$this->adapter->where($this->parts['where']);

			// Собираем секцию GROUP
			$group =  @$this->parts['group'];
			if ($group) {
				$sql .= ' GROUP BY ';
				foreach ($group as $n => $el) $sql .= ($n ? ', ' : '').$this->adapter->quote($el, false);
			}

			// Собираем секцию ORDER
			$order =  @$this->parts['order'];
			if ($order) {
				$sql_order = '';
				foreach ($order as $k => $v) {
					if (is_numeric($k)) {
						$res = null;
						if (preg_match('/^(.*?)\ (asc|desc)$/', $v, $res)) {
							$cnd = $this->adapter->quote($res[1], false);
							$dir = strtoupper($res[2]);
						}
						else {
							$cnd = $this->adapter->quote($v, false);
							$dir = 'ASC';
						}
					}
					else {
						$cnd = $this->adapter->quote($k, false);
						$dir = strtoupper($v) == 'DESC' ? 'DESC' : 'ASC';
					}
					$sql_order .= ($sql_order ? ', ' : '').$cnd.($dir == 'DESC' ? ' DESC' : '');
				}
				if ($sql_order) $sql .= ' ORDER BY '.$sql_order;
			}
		}

		// Собираем секцию LIMIT
		$limit =  @$this->parts['limit'];
		if ($limit) {
			$sql .= ' LIMIT '.($limit['offset'] ? $limit['offset'] : 0).', '.$limit['count'];
		}

		return $sql;
	}

	public function reset($part) {
		unset($this->parts[$part]);
		return $this;
	}

	// Если приводим select к строке - сразу собираем его
	public function __toString() {
		return (string)$this->assemble();
	}
}