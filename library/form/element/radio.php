<?php

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
			$this->view->js->append('/'.DIR_KERNEL.'/ctl/uniform/jquery.uniform.js');
			$this->view->js->append_inline('$("input[type=radio][name=\''.$this->name.'\']").uniform('.Zend\Json\Json::encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');');
			if ($this->uniform->css) $this->view->css->append('/'.DIR_KERNEL.'/ctl/uniform/themes/default/css/uniform.default.css');
		}
		return parent::render();
	}
}