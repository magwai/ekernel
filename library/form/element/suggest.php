<?php

class k_form_element_suggest extends form_element_text {
	public $method = '';
	public $model = '';
	public $quant = 0;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->model = isset($param['model']) ? $param['model'] : $name;
		$this->method = isset($param['method']) ? $param['method'] : $name;
		if (isset($param['quant'])) $this->quant = $param['quant'];
		if (!isset($param['ui'])) $param['ui'] = new data;
		if (!isset($param['ui']->theme)) $param['ui']->theme = 'base';
		if (!isset($param['ui']->opt)) $param['ui']->opt = new data(array());
		$this->ui = $param['ui'];
	}

	public function render() {
		$url = $this->view->url(array(
			'controller' => 'x',
			'action' => 'suggest'
		));
		$opt = array_merge(array(
			'width' => '100%',
			'height' => '49px',
			'unique' => new Zend\Json\Expr(true),
			'autocomplete_url' => $url,
			'autocomplete' => array(
				'source' => new Zend\Json\Expr('function(request, response) {
					request["model"] = "'.$this->model.'";
					request["method"] = "'.$this->method.'";
					$.ajax({
						url: "'.$url.'",
						type: "post",
						dataType: "json",
						data: request,
						success: function(data) {
							if (data.length == 0) o.val("");
							response(data);
						}
					});
				}')
			)
		), $this->ui->opt->to_array());
		$this->view->js->append('/library/ctl/ui/ui/jquery.ui.core.js');
		$this->view->js->append('/library/ctl/ui/ui/jquery.ui.widget.js');
		$this->view->js->append('/library/ctl/ui/ui/jquery.ui.position.js');
		$this->view->js->append('/library/ctl/ui/ui/jquery.ui.menu.js');
		$this->view->js->append('/library/ctl/ui/ui/jquery.ui.autocomplete.js');
		$this->view->js->append('/library/ctl/tagsinput/jquery.tagsinput.js');
		$this->view->js->append_inline('var o = $("input[name=\''.$this->name.'\']");o.tagsInput('.Zend\Json\Json::encode($opt, false, array(
			'enableJsonExprFinder' => true
		)).');');
		
		$this->view->css	->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.core.css')
							->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.theme.css')
							->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.menu.css')
							->append('/library/ctl/ui/themes/'.$this->ui->theme.'/jquery.ui.autocomplete.css')
							->append('/library/ctl/tagsinput/jquery.tagsinput.css');
		return parent::render();
	}
}