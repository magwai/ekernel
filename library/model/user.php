<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_model_user extends database_model {
	public $name = 'user';

	function fetch_card_by_login($login) {
		return $this->fetch_row(array(
			'login' => $login
		));
	}

	function fetch_card_by_id($id) {
		return $this->fetch_row(array(
			'id' => (int)$id
		));
	}

	function fetch_id_by_hash($salt, $hash) {
		$meta = $this->metadata();
		return (int)$this->fetch_one('id', array(
			'SHA1(CONCAT('.
				(array_key_exists('profile', $meta) ? '`profile`, ' : '').
				(array_key_exists('login', $meta) ? '`login`, ' : '').
				(array_key_exists('password', $meta) ? '`password`, ' : '').
			(string)$this->adapter->quote($salt).')) = ?' => (string)$hash
		));
	}

	function login($login, $password, $salt) {
		return (int)$this->fetch_one('id', array(
			'login' => (string)$login,
			'password = SHA1(?)' => (string)$password.(string)$salt
		));
	}

	function fetch_card_by_profile($profile) {
		return $this->fetch_row(array(
			'profile' => (string)$profile
		));
	}

	function usersoc_register($data = array()) {
		return @$data['id'];
	}
}