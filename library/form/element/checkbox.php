<?php

class k_form_element_checkbox extends form_element_input {
	public $view_script = 'form/checkbox';
	public $multiple = false;
	public $uniform = false;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->type = 'checkbox';
		if (isset($param['uniform'])) {
			if (!$param['uniform'] instanceof data) $param['uniform'] = new data();
			if (!isset($param['uniform']->css)) $param['uniform']->css = true;
			$this->uniform = $param['uniform'];
		}
		if (isset($param['multiple'])) $this->multiple = $param['multiple'];
	}

	public function render() {
		if ($this->uniform) {
			$opt = array();
			$this->view->js->append('/kernel/ctl/uniform/jquery.uniform.js');
			if (!class_exists('Zend\Json\Encoder')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Encoder.php';
			if (!class_exists('Zend\Json\Json')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Json.php';
			if (!class_exists('Zend\Json\Expr')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Expr.php';
			$this->view->js->append_inline('$("input[type=checkbox][name=\''.$this->name.($this->multiple ? '\[\]' : '').'\']").uniform('.Zend\Json\Json::encode($opt, false, array(
				'enableJsonExprFinder' => true
			)).');');
			if ($this->uniform->css) $this->view->css->append('/kernel/ctl/uniform/themes/default/css/uniform.default.css');
		}
		return parent::render();
	}
}