<?php

class k_session {
	public static $data = array();

	public static function init() {
		if (!session_id()) session_start();
		return session_id();
	}

	public static function set($key, $value) {
		if (!self::init()) return false;
		$_SESSION[$key] = $value;
	}

	public static function get($key) {
		if (!self::init()) return null;
		return isset($_SESSION[$key])
			? $_SESSION[$key]
			: null;
	}

	public static function get_id() {
		return self::init();
	}

	public static function remove($key) {
		if (!self::init()) return false;
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
			return true;
		}
		return false;
	}
}