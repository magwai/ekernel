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
	}

	public function render() {
		$url = $this->view->url(array(
			'controller' => 'x',
			'action' => 'suggest'
		));
		if (!class_exists('Zend\Json\Encoder')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Encoder.php';
		if (!class_exists('Zend\Json\Json')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Json.php';
		if (!class_exists('Zend\Json\Expr')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zend/Json/Expr.php';
		$opt = array(
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
		);
		$this->view->js->append('/kernel/ctl/ui/ui/jquery.ui.core.js');
		$this->view->js->append('/kernel/ctl/ui/ui/jquery.ui.widget.js');
		$this->view->js->append('/kernel/ctl/ui/ui/jquery.ui.position.js');
		$this->view->js->append('/kernel/ctl/ui/ui/jquery.ui.menu.js');
		$this->view->js->append('/kernel/ctl/ui/ui/jquery.ui.autocomplete.js');
		$this->view->js->append('/kernel/ctl/tagsinput/jquery.tagsinput.js');
		$this->view->js->append_inline('var o = $("input[name=\''.$this->name.'\']");o.tagsInput('.Zend\Json\Json::encode($opt, false, array(
			'enableJsonExprFinder' => true
		)).');');
		$this->view->css->append('/kernel/ctl/tagsinput/jquery.tagsinput.css');
		return parent::render();
	}
}