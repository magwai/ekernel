<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_database_adapter_json extends database_adapter {
	public $cache = array();
	public $history = array();

	public function query($sql, $type = null) {
		$this->history[] = array(
			'sql' => (string)$sql,
			'type' => $type
		);
		//$sql = preg_replace('/ORDER\ BY\ \`([^\`]+?)\`/si', 'ORDER BY $1', $sql);
		$sql = str_replace('`', '', $sql);
		$ret = null;

		$parser = new k_lib_phpsqlparser_class($sql, true);









		/*
		$name = null;

		preg_match('/(DESCRIBE|FROM|INTO|UPDATE)\ \`([^\`]+?)\`/si', $sql, $rres);
		if (@$rres[2]) $name = $rres[2];
		if (!$name) return $ret;

		$all = @$this->cache[$name];
		if (!isset($this->cache[$name])) {
			$this->cache[$name] = $all = @json_decode(file_get_contents(PATH_ROOT.'/'.DIR_DATA.'/db/'.$name.'.json'), true);
		}
		if (!$all) return $ret;

		$sql = str_replace('`', '', $sql);

		$parser = new k_lib_phpsqlparser_class($sql, true);*/


		if (isset($parser->parsed['DESCRIBE'])) {
			$all = $this->load_table($parser->parsed['DESCRIBE'][1]);
			$field = $all['meta']['field'];
			$result = array();
			if ($field) foreach ($field as $el) {
				$result[$el] = array(
					'Field' => $el
				);
			}
		}
		else if (isset($parser->parsed['SELECT'])) {
			$all = array();
			$tables = array();
			$fields = array();
			foreach ($parser->parsed['SELECT'] as $num => $el) {
				$name = $el['alias'] ? $el['alias']['name'] : $el['base_expr'];
				$fields[$name] = $el['base_expr'];
			}
			foreach ($parser->parsed['FROM'] as $num => $el) {
				$name = $el['alias'] ? $el['alias']['name'] : $el['table'];
				$tables[$name] = $this->load_table($el['table']);
				if ($tables[$name]) {
					foreach ($tables[$name]['data'] as $k => $v) {





						if (!isset($all[$k])) $all[$k] = array();
						$all[$k] = array_merge($all[$k], $v);
					}
				}
			}
			/*if (@$parser->parsed['FROM'][0]['expr_type'] == 'subquery') {
				$all = array('data' => $this->query($parser->parsed['FROM'][0]['base_expr'], 'all'));
			}*/
			$result = $all;

			if ($sql == 'SELECT i.id FROM crole AS i INNER JOIN crole2crole AS r ON i.id = r.role WHERE (r.parentid = 1) GROUP BY i.id') {
				echo $sql;
				print_r($parser->parsed);
				exit();
			}

			/*switch (@$parser->parsed['SELECT'][0]['expr_type']) {
				case 'aggregate_function':
					switch (@$parser->parsed['SELECT'][0]['base_expr']) {
						case 'COUNT':

							$result = count($all);
							break;
					}
					break;
				case 'colref':
					$result = $all;
					break;
			}*/




			//$parser = new k_lib_phpsqlparser_class('SELECT * FROM `cuser`', true);

			/*echo $sql;
			print_r($result);
			exit();*/


		}

		// Разбираем ответ в соответствии с типом запроса
		if ($result) {
			switch ($type) {
				// Вернет все колонки одного ряда
				case 'row':
					$ret = $result[0];
					break;
				// Вернет пары ключ => значение
				case 'pairs':
					$col1 = $parser->parsed['SELECT'][0]['base_expr'];
					$col2 = $parser->parsed['SELECT'][1]['base_expr'];
					if ($result) {
						$ret = array();
						foreach ($result as $el) {
							$ret[$el[$col1]] = $el[$col2];
						}
					}
					break;
				// Вернет одно значение
				case 'one':
					$ret = $result;
					break;
				// Вернет одну колонку запрошенных рядов
				case 'col':
					$col1 = $parser->parsed['SELECT'][0]['base_expr'];
					if ($result) {
						$ret = array();
						foreach ($result as $el) {
							$ret[] = $el[$col1];
						}
					}
					break;
				default:
					$ret = $result;
					break;
			}
		}
		return $ret;
	}

	function load_table($name) {
		$all = @$this->cache[$name];
		if (!isset($this->cache[$name])) {
			$this->cache[$name] = $all = @json_decode(file_get_contents(PATH_ROOT.'/'.DIR_DATA.'/db/'.$name.'.json'), true);
		}
		return $all;
	}

	// Упрощенный запрос на выборку одной колонки
	function fetch_col($sql) {
		return $this->query($sql, 'col');
	}

	// Упрощенный запрос на выборку одного значения
	function fetch_one($sql) {
		return $this->query($sql, 'one');
	}

	// Упрощенный запрос на выборку пары ключ => значение
	function fetch_pairs($sql) {
		$ret = $this->query($sql, 'pairs');
		return $ret ? new data($ret) : array();
	}

	// Упрощенный запрос на выборку всех колонок и всех рядов
	function fetch_all($sql) {
		$ret = $this->query($sql, 'all');
		return $ret ? $ret : array();
	}

	// Упрощенный запрос на выборку всех колонок одного ряда
	function fetch_row($sql) {
		$ret = $this->query($sql, 'row');
		return $ret ? $ret : array();
	}

	// Универсальное экранирование. is_value - тип экранирования: значение или идентификатор ('' или ``)
	public function quote($value, $is_value = true) {
		$q = $is_value ? "'" : '`';
		$parts = explode('.', $value);
		$is_mix = preg_match('/^(\(|\"|\').*?(\)|\"|\')$/', $value);
		if (count($parts) > 1 && !$is_value && !$is_mix) {
			foreach ($parts as &$el) $el = $this->quote($el, false);
			return implode('.', $parts);
		}
		if ($is_mix || $value == '*') return $value;
        return $q.addcslashes($value, "\000\n\r\\'\"\032").$q;
    }

	public function where($where = array()) {
		$sql = '';
		foreach ($where as $k => $v) {
			if (!preg_match('/[^a-zA-Z\.\_]/', $k)) $k = $this->quote($k, false).' = ?';
			$stmt = str_replace('?', '%s', $k);
			$vals = is_array($v) ? $v : array($v);
			foreach ($vals as &$el) {
				if (!is_numeric($el)) $el = $this->quote($el);
			}
			$sql .= ' '.($sql ? 'AND ' : '').'('.vsprintf($stmt, $vals).')';
		}
		return $sql;
	}

	function set($data = array()) {
		$sql = '';
		if ($data) {
			$n = 0;
			foreach ($data as $k => $v) {
				$is_expr = $v instanceof database_expr;
				if (!is_scalar($v) && !$is_expr) continue;
				$sql .= ($n ? ', ' : '').$this->quote($k, false).' = '.(is_numeric($v) ? $v : ($is_expr ? $v : $this->quote($v)));
				$n++;
			}
		}
		return $sql;
	}
}