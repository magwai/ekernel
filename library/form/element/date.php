<?php

class k_form_element_date extends form_element_input {
	public $ui = false;
	public $time = false;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['ui'])) {
			if (!$param['ui'] instanceof data) $param['ui'] = new data($param['ui']);
			if (!isset($param['ui']->theme)) $param['ui']->theme = 'base';
			if (!isset($param['ui']->lang)) $param['ui']->lang = 'ru';
			$this->ui = $param['ui'];
		}
		$this->validator[] = 'date';
		$this->type = 'text';
		if (isset($param['time'])) $this->time = $param['time'];
	}

	public function set($value) {
		$value = $value && $value != '0000-00-00 00:00:00' ? strtotime($value) : '';
		parent::set($value > 0 ? date('Y-m-d '.($this->time ? 'H:i:00' : '00:00:00'), $value) : '');
	}

	public function get($for_render = false) {
		$value = $this->value && $this->value != '0000-00-00 00:00:00' ? strtotime($this->value) : '';
		return $value ? date($for_render ? 'd.m.Y' : 'Y-m-d', $value).($this->time ? ' '.date('H:i', $value) : '') : '';
	}

	public function render() {
		if ($this->ui) {
			$opt = array();
			if ($this->ui->opt) {
				$opt = array_merge($opt, $this->ui->opt->to_array());
			}
			
			$this->view->js		->append('/library/ctl/ui/ui/jquery.ui.core.js')
								->append('/library/ctl/ui/ui/jquery.ui.datepicker.js')
								->append('/library/ctl/ui/ui/i18n/jquery.ui.datepicker-'.$this->ui->lang.'.js')
								->append_inline('$("input[name=\''.$this->name.'\']").'.($this->time ? 'datetimepicker' : 'datepicker').'('.Zend\Json\Json::encode($opt, false, array(
									'enableJsonExprFinder' => true
								)).');');
			if ($this->time) $this->view->js
								->append('/library/ctl/timepicker/jquery-ui-timepicker-addon.js');
			
			$this->view->css	->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.core.css')
								->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.theme.css')
								->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.datepicker.css');
			if ($this->time) $this->view->css
								->append('/library/ctl/timepicker/jquery-ui-timepicker-addon.css');
		}
		return parent::render();
	}
}