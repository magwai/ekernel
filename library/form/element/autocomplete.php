<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_autocomplete extends form_element_input {
	public $view_script = 'form/autocomplete';
	public $ui = false;
	public $model = null;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['ui'])) {
			if (!$param['ui'] instanceof data) $param['ui'] = new data($param['ui']);
			if (!isset($param['ui']->theme)) $param['ui']->theme = 'base';
			if (!isset($param['ui']->opt)) $param['ui']->opt = array();
			$this->ui = $param['ui'];
		}
		if (!isset($param['fetch'])) $param['fetch'] = new data;
		$this->fetch = $param['fetch'];

		if (!$this->fetch->method) $this->fetch->method = $name;

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
		url: "/x/autocomplete/model/'.$this->fetch->model.($this->fetch->method ? '/method/'.$this->fetch->method : '').($this->fetch->param ? '/param/'.$this->fetch->param : '').'/term/" + encodeURIComponent(request.term),
		dataType: "json",
		success: function(data) {
			response(data);
		}
	});
}');
			$opt['select'] = new Zend\Json\Expr('function(event, ui) {
	$("input[name=\''.$this->name.'_fake\']").val(ui.item.label);
	$("input[name=\''.$this->name.'\']").val(ui.item.value);
	$("input[name=\''.$this->name.'\']").data("was_selected", true);
	return false;
}');
			$opt['change'] = new Zend\Json\Expr('function(event, ui) {
	if (!$("input[name=\''.$this->name.'\']").data("was_selected")) {
		$("input[name=\''.$this->name.'_fake\']").val("");
		$("input[name=\''.$this->name.'\']").val("");
	}
	return false;
}');
			$opt['create'] = new Zend\Json\Expr('function(event, ui) {
	var val = $("input[name=\''.$this->name.'\']").val();
	if (val && String(val).length) $.ajax({
		url: "/x/autocomplete/action/card/model/'.$this->fetch->model.($this->fetch->method ? '/method/'.$this->fetch->method : '').($this->fetch->param ? '/param/'.$this->fetch->param : '').'/value/" + encodeURIComponent(val),
		dataType: "json",
		success: function(data) {
			if (data && String(data.label).length) {
				$("input[name=\''.$this->name.'_fake\']").val(data.label);
			}
			else {
				$("input[name=\''.$this->name.'_fake\']").val("");
				$("input[name=\''.$this->name.'\']").val("");
			}
		}
	});
	return false;
}');
			$this->view->messify	->append('js', '/'.DIR_KERNEL.'/ctl/ui/ui/jquery.ui.core.js')
									->append('js', '/'.DIR_KERNEL.'/ctl/ui/ui/jquery.ui.widget.js')
									->append('js', '/'.DIR_KERNEL.'/ctl/ui/ui/jquery.ui.position.js')
									->append('js', '/'.DIR_KERNEL.'/ctl/ui/ui/jquery.ui.menu.js')
									->append('js', '/'.DIR_KERNEL.'/ctl/ui/ui/jquery.ui.autocomplete.js')
									->append_inline('js', '$("input[name=\''.$this->name.'_fake\']").autocomplete('.Zend\Json\Json::encode($opt, false, array(
										'enableJsonExprFinder' => true
									)).');$("input[name=\''.$this->name.'_fake\']").focus(function() {
										$("input[name=\''.$this->name.'\']").data("was_selected", false);
									});')
									->append('css', '/'.DIR_KERNEL.'/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.core.css')
									->append('css', '/'.DIR_KERNEL.'/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.theme.css')
									->append('css', '/'.DIR_KERNEL.'/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.menu.css')
									->append('css', '/'.DIR_KERNEL.'/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.autocomplete.css');
		}
		return parent::render();
	}
}