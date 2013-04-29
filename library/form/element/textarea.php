<?php

class k_form_element_textarea extends form_element {
	public $cols = null;
	public $rows = 10;
	public $view_script = 'form/textarea';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['cols'])) $this->cols = $param['cols'];
		if (isset($param['rows'])) $this->rows = $param['rows'];
	}
}