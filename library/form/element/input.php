<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_input extends form_element {
	public $view_script = 'form/input';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = isset($param['type']) ? $param['type'] : 'text';
	}
}