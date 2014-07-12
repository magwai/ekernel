<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_plugin {
	public $param = null;

	public function __construct($param = array()) {
		$this->param = $param;
	}

	public function controller_before() {

	}

	public function controller_after() {

	}

	public function controller_render() {

	}
}