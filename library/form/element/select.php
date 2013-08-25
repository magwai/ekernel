<?php

class k_form_element_select extends form_element {
	public $view_script = 'form/select';
	public $chosen = false;
	public $multiple = false;
	public $uniform = false;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['chosen'])) {
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
			$this->view->js->append('/library/ctl/chosen/chosen.jquery.js');
			$this->view->js->append_inline('$("select[name=\''.$this->name.($this->multiple ? '\[\]' : '').'\']").chosen('.common::json_encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');');
			if ($this->chosen->css) $this->view->css->append('/library/ctl/chosen/chosen.css');
		}
		if ($this->uniform) {
			$opt = array();
			$this->view->js->append('/library/ctl/uniform/jquery.uniform.js');
			$this->view->js->append_inline('$("select[name=\''.$this->name.'\']").uniform('.Zend\Json\Json::encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');');
			if ($this->chosen->css) $this->view->css->append('/library/ctl/uniform/themes/default/css/uniform.default.css');
		}
		return parent::render();
	}
}