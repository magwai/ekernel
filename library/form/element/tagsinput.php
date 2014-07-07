<?php

class k_form_element_tagsinput extends form_element_text {
	public $method = '';
	public $model = '';
	public $quant = 0;

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->model = isset($param['model']) ? $param['model'] : $name;
		$this->method = isset($param['method']) ? $param['method'] : $name;
		$this->opt = isset($param['opt']) ? $param['opt'] : new data(array());
		if (isset($param['quant'])) $this->quant = $param['quant'];
	}

	public function render() {
		$url = $this->view->url(array(
			'controller' => 'x',
			'action' => 'suggest'
		));
		$opt = array_merge(array(
			'typeahead' => array(
				'source' => new Zend\Json\Expr('adapter.ttAdapter()')
			)
		), $this->opt->to_array());
		$this->view->js	->append('/'.DIR_KERNEL.'/js/jquery/typeahead.bundle.js')
						->append('/'.DIR_KERNEL.'/ctl/bootstrap/tokenfield/bootstrap-tokenfield.js');
		$this->view->js->append_inline('var adapter = new Bloodhound('.Zend\Json\Json::encode(array(
			'datumTokenizer' => new Zend\Json\Expr('Bloodhound.tokenizers.obj.whitespace("value")'),
			'queryTokenizer' => new Zend\Json\Expr('Bloodhound.tokenizers.whitespace'),
			'prefetch' => array(
				'url' => $url,
				'ajax' => array(
					'type' => 'post',
					'data' => array(
						'term' => 1,
						'model' => $this->model,
						'method' => $this->method
					)
				)
			)
		), false, array(
			'enableJsonExprFinder' => true
		)).');adapter.initialize();var o = $("input[name=\''.$this->name.'\']");o.tokenfield('.Zend\Json\Json::encode($opt, false, array(
			'enableJsonExprFinder' => true
		)).');');

		$this->view->css	->append('/'.DIR_KERNEL.'/ctl/bootstrap/tokenfield/bootstrap-tokenfield.css')
							->append('/'.DIR_KERNEL.'/ctl/bootstrap/tokenfield/tokenfield-typeahead.css');
		return parent::render();
	}
}