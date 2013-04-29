<?php

class k_form_element_password extends form_element_input {
	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'password';
	}

	public function get($for_render = false) {
		return $for_render ? '' : $this->value;
	}
}