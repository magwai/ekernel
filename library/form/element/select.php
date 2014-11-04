<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_select extends form_element {
	public $view_script = 'form/select';
	public $chosen = false;
	public $multiple = false;
	public $uniform = false;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['chosen']) && $param['chosen'] != false) {
			if (!$param['chosen'] instanceof data) $param['chosen'] = new data();
			if (!isset($param['chosen']->css)) $param['chosen']->css = true;
			$this->chosen = $param['chosen'];
		}
		if (isset($param['uniform'])) {
			if (!$param['uniform'] instanceof data) $param['uniform'] = new data();
			if (!isset($param['uniform']->css)) $param['uniform']->css = true;
			$this->uniform = $param['uniform'];
		}
		if (isset($param['multiple'])) $this->multiple = $param['multiple'];
	}

	public function render() {
		if ($this->chosen) {
			$opt = array(
				'no_results_text' => $this->view->translate('form_element_select_no_results_text'),
				'placeholder_text' => $this->view->translate('form_element_select_placeholder_text'),
				'placeholder_text_multiple' => $this->view->translate('form_element_select_placeholder_text_multiple')
			);
			$this->view->messify->append('js', '/'.DIR_KERNEL.'/ctl/chosen/chosen.jquery.js')
								->append_inline('js', '$("select[name=\''.$this->name.($this->multiple ? '\[\]' : '').'\']").chosen('.Zend\Json\Json::encode($opt, false, array(
									'enableJsonExprFinder' => true
								)).');');
			if ($this->chosen->css) $this->view->messify->append('css', '/'.DIR_KERNEL.'/ctl/chosen/chosen.css');
		}
		if ($this->uniform) {
			$opt = array();
			$this->view->messify->append('js', '/'.DIR_KERNEL.'/ctl/uniform/jquery.uniform.js')
								->append_inline('js', '$("select[name=\''.$this->name.'\']").uniform('.Zend\Json\Json::encode($opt, false, array(
									'enableJsonExprFinder' => true
								)).');');
			if ($this->chosen->css) $this->view->messify->append('css', '/'.DIR_KERNEL.'/ctl/uniform/themes/default/css/uniform.default.css');
		}
		return parent::render();
	}
}