<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_resource_database {
	private $default_adpter = 'mysql';
	private $default_host = 'localhost';
	public $adapter = null;

	function __construct($config) {
		$this->adapter = isset($config->adapter) ? $config->adapter : $this->default_adpter;
		$config_local = $config;
		$config_local->host = isset($config_local->host) ? $config_local->host : $this->default_host;
		$this->adapter = $this->load_adapter($this->adapter, $config_local);
	}

	// Загружаем адаптер по имени с параметрами
	public function load_adapter($name, $config) {
		$class = 'database_adapter_'.$name;
		return new $class($config);
	}
}