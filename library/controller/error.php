<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_controller_error extends controller {
	function index_action() {
		$this->forward('index', 'page', array(
			'id' => 'error'
		));
	}
}