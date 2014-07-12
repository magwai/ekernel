<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_point extends form_element_input {
	public $point = 'point';
	public $url = '';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['point'])) $this->point = $param['point'];
		if (isset($param['url'])) $this->url = $param['url'];
		$this->type = 'hidden';
	}

	public function render() {
		$opt = array(
			'type' => $this->point,
			'url' => $this->url
		);
		$this->view->js		->append('/'.DIR_KERNEL.'/ctl/point/point.js')
							->append_inline('$("input[name=\''.$this->name.'\']").point('.Zend\Json\Json::encode($opt, false, array(
								'enableJsonExprFinder' => true
							)).');');
		return parent::render();
	}
}