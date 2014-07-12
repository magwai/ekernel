<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_registry {
	public static $data = array();

	public static function set($key, $value) {
		self::$data[$key] = $value;
	}

	public static function get($key) {
		return isset(self::$data[$key])
			? self::$data[$key]
			: null;
	}

	public static function exist($key) {
		return isset(self::$data[$key]);
	}
}