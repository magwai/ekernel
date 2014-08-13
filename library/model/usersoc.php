<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_model_usersoc extends database_model {
	public $name = 'user_soc';

	function fetch_card_by_profile($profile) {
		return $this->fetch_row(array(
			'profile' => (string)$profile
		));
	}

	function usersoc_register($data) {
		$ok = $this->insert($data);
		if ($ok) {
			return $data['author'];
		}
		return false;
	}
}