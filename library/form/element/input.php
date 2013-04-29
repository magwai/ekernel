<?php

class k_form_element_input extends form_element {
	public $view_script = 'form/input';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = isset($param['type']) ? $param['type'] : 'text';
	}
}