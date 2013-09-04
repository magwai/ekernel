<?php

class k_form_element_hidden extends form_element_input {
	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'hidden';
	}
}