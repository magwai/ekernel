<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_hidden extends form_element_input {
	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'hidden';
	}
}