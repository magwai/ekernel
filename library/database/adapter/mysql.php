<?php

class k_database_adapter_mysql extends database_adapter {
	private $_charset_default = 'utf8';
	private $_host_default = 'localhost';
	public $connection = null;

	public function connect() {
		// Подключаемся к БД mysql
		$charset = isset($this->config->charset) ? (string)$this->config->charset : $this->_charset_default;
		$host = isset($this->config->host) ? (string)$this->config->host : $this->_host_default;
		$this->connection = new PDO(
			'mysql:host='.$host.';dbname='.(string)$this->config->database,
			(string)$this->config->user,
			(string)$this->config->password,
			array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.(string)$charset
			)
		);
	}

	public function query($sql, $type = null) {
		// Если не подключены - подключаемся к БД
		if (!$this->connection) $this->connect();

		// Делаем запрос к БД, передавая числый SQL в PDO
		$ret = null;
		$result = $this->connection->query($sql);

		// Разбираем ответ в соответствии с типом запроса
		if ($result) {
			switch ($type) {
				// Вернет все колонки запрошенных рядов
				case 'all':
					$ret = $result->fetchAll(PDO::FETCH_ASSOC);
					break;
				// Вернет все колонки одного ряда
				case 'row':
					$ret = $result->fetch(PDO::FETCH_ASSOC);
					break;
				// Вернет пары ключ => значение
				case 'pairs':
					$res = $result->fetchAll(PDO::FETCH_NUM);
					if ($res) {
						$ret = array();
						foreach ($res as $el) $ret[$el[0]] = $el[1];
					}
					break;
				// Вернет одно значение
				case 'one':
					$ret = $result->fetchColumn();
					break;
				// Вернет одну колонку запрошенных рядов
				case 'col':
					$ret = $result->fetchAll(PDO::FETCH_COLUMN, 0);
					break;
				default:
					$ret = $result;
					break;
			}
		}
		return $ret;
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
		if ($is_mix || strpos($value, '*') !== false) return $value;
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
				if (!is_scalar($v)) continue;
				$sql .= ($n ? ', ' : '').$this->quote($k, false).' = '.(is_numeric($v) ? $v : $this->quote($v));
				$n++;
			}
		}
		return $sql;
	}
}