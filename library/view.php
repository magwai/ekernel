<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view extends data {
	public $name;
	public $helper;

	public function __construct($name = null) {
		if ($name) $this->name = $name;
	}

	public function render($name = null, $param = array()) {
		if ($name == null ) $name = $this->name;
		if ($name) {
			// Захватываем буфер
			ob_start();

			$name_valid = substr($name, 0, 2) == 'k_' ? substr($name, 2) : $name;
			$fn = 'view/'.$name_valid.'.php';

			/*$old = array();
			if (count($this)) foreach ($this as $k => $v) {
				$old[$k] = $v;
				unset($this->$k);
			}*/

			// Устанавливаем параметры во вьюшке
			if ($param) foreach ($param as $k => $v) {
				unset($this->$k);
				$this->$k = $v;
			}

			// Сначала подключаем вьюшку из каталога приложения, затем - из ядра
			if ($name_valid == $name && file_exists(PATH_ROOT.'/'.DIR_APPLICATION.'/'.$fn)) include(PATH_ROOT.'/'.DIR_APPLICATION.'/'.$fn);
			else if (file_exists(PATH_ROOT.'/'.DIR_LIBRARY.'/'.$fn)) include(PATH_ROOT.'/'.DIR_LIBRARY.'/'.$fn);

			if ($param) foreach ($param as $k => $v) unset($this->$k);
			//if ($old) foreach ($old as $k => $v) $this->$k = $v;

			// Чистим буфер и выходим
			return ob_get_clean();
		}
	}

	public function escape($val) {
		return htmlspecialchars($val);
	}

	public function __get($key) {
		return isset($this->_data[$key])
			? $this->_data[$key]
			: $this->get_helper($key);
	}

	public function get_helper($key) {
		$ret = null;
		$in_helper = false;
		if (isset($this->helper[$key])) {
			$ret = $this->helper[$key];
			$in_helper = true;
		}
		else {
			$class = 'view_helper_'.$key;
			if (class_exists($class)) {
				$this->helper[$key] = new $class();
				$ret = $this->helper[$key];
				$in_helper = true;
			}
			else {
				$ret = parent::get($key);
			}
		}
		if ($in_helper) $ret->view = $this;
		return $ret;
	}


	public function __call($method, $args) {
		$inst = $this->get_helper($method);
		if ($inst != null) return call_user_func_array(array($inst, $method), $args);
	}
}