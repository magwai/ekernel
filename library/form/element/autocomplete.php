<?php

class k_form_element_autocomplete extends form_element_input {
	public $ui = false;
	public $model = null;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (!isset($param['ui'])) $param['ui'] = new data;
		if (!isset($param['ui']->theme)) $param['ui']->theme = 'base';
		if (!isset($param['ui']->opt)) $param['ui']->opt = array();
		if (!isset($param['fetch'])) $param['fetch'] = new data;
		$this->ui = $param['ui'];
		$this->fetch = $param['fetch'];
		$this->type = 'text';
	}

	public function render() {
		if ($this->ui) {
			$opt = array();
			if ($this->ui->opt) {
				$opt = array_merge($opt, $this->ui->opt->to_array());
			}
			$opt['source'] = new Zend\Json\Expr('function(request, response) {
	$.ajax({
		url: "/x/autocomplete/model/'.$this->fetch->model.($this->fetch->method ? '/method/'.$this->fetch->method : '').($this->fetch->param ? '/param/'.$this->fetch->param : '').'",
		dataType: "json",
		success: function(data) {
			response();
		}
	});
}');
			$this->view->js		->append('/library/ctl/ui/ui/jquery.ui.core.js')
								->append('/library/ctl/ui/ui/jquery.ui.widget.js')
								->append('/library/ctl/ui/ui/jquery.ui.position.js')
								->append('/library/ctl/ui/ui/jquery.ui.menu.js')
								->append('/library/ctl/ui/ui/jquery.ui.autocomplete.js')
								->append_inline('$("input[name=\''.$this->name.'\']").autocomplete('.Zend\Json\Json::encode($opt, false, array(
									'enableJsonExprFinder' => true
								)).');');
			
			$this->view->css	->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.core.css')
								->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.theme.css')
								->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.autocomplete.css');
		}
		return parent::render();
	}
}