<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_button extends form_element_input {
	public $onclick = '';
	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'button';
		if (isset($param['onclick'])) $this->onclick = $param['onclick'];
	}

	public function set($value) { }
}