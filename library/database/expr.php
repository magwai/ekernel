<?php

class k_database_expr {
	public $str = '';

	function __construct($str) {
		$this->str = $str;
	}

	public function __toString() {
		return '('.(string)$this->str.')';
	}
}