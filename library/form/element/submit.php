<?php

class k_form_element_submit extends form_element_button {
	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'submit';
	}
}