<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_database_expr {
	public $str = '';

	function __construct($str) {
		$this->str = $str;
	}

	public function __toString() {
		return '('.(string)$this->str.')';
	}
}