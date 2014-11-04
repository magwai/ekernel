<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_checkbox extends form_element_input {
	public $view_script = 'form/checkbox';
	public $multiple = false;
	public $uniform = false;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'checkbox';
		if (isset($param['uniform']) && $param['uniform'] != false) {
			if (!$param['uniform'] instanceof data) $param['uniform'] = new data();
			if (!isset($param['uniform']->css)) $param['uniform']->css = true;
			$this->uniform = $param['uniform'];
		}
		if (isset($param['multiple'])) $this->multiple = $param['multiple'];
	}

	public function render() {
		if (is_array($this->value)) $this->value = new data($this->value);
		if ($this->uniform) {
			$opt = array();
			$this->view->messify->append('js', '/'.DIR_KERNEL.'/ctl/uniform/jquery.uniform.js')
								->append_inline('js', '$("input[type=checkbox][name=\''.$this->name.($this->multiple ? '\[\]' : '').'\']").uniform('.Zend\Json\Json::encode($opt, false, array(
									'enableJsonExprFinder' => true
								)).');');
			if ($this->uniform->css) $this->view->messify->append('css', '/'.DIR_KERNEL.'/ctl/uniform/themes/default/css/uniform.default.css');
		}
		return parent::render();
	}
}