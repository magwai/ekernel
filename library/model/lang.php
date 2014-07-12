<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_model_lang extends database_model {
	public $name = 'lang';

	function fetch_list() {
		return $this->fetch_all(array(
			'show_it' => 1
		));
	}

	function fetch_list_control() {
		return $this->fetch_list();
	}
}