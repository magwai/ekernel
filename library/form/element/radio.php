<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_radio extends form_element_input {
	public $view_script = 'form/radio';
	public $uniform = false;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'radio';
		if (isset($param['uniform'])) {
			if (!$param['uniform'] instanceof data) $param['uniform'] = new data();
			if (!isset($param['uniform']->css)) $param['uniform']->css = true;
			$this->uniform = $param['uniform'];
		}
	}

	public function render() {
		if ($this->uniform) {
			$opt = array();
			$this->view->messify->append('js', '/'.DIR_KERNEL.'/ctl/uniform/jquery.uniform.js')
								->append_inline('js', '$("input[type=radio][name=\''.$this->name.'\']").uniform('.Zend\Json\Json::encode($opt, false, array(
									'enableJsonExprFinder' => true
								)).');');
			if ($this->uniform->css) $this->view->messify->append('css', '/'.DIR_KERNEL.'/ctl/uniform/themes/default/css/uniform.default.css');
		}
		return parent::render();
	}
}