<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_crop extends form_element_hidden {
	public $target = '';
	public $jcrop = array();

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['target'])) $this->target = $param['target'];
		if (isset($param['jcrop'])) $this->jcrop = $param['jcrop'];
		$this->label = '';
		$this->jcrop = new data($this->jcrop);
	}

	public function render() {
		if (!function_exists('to_array')) {
			function to_array(&$obj) {
				if ($obj instanceof data) $obj = $obj->to_array();
				if (is_array($obj)) foreach ($obj as &$el) to_array($el);
			}
		}
		$opt = array(
			'target' => $this->target,
			'jcrop' => $this->jcrop
		);
		to_array($opt);
		$this->view->messify->append('js', '/'.DIR_KERNEL.'/ctl/crop/crop.js')
							->append('js', '/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.js')
							->append('js', '/'.DIR_KERNEL.'/ctl/jcrop/js/jquery.Jcrop.js')
							->append_inline('js', '$("input[name=\''.$this->name.'\']").crop('.Zend\Json\Json::encode($opt, false, array(
								'enableJsonExprFinder' => true
							)).');')
							->append('css', '/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.css')
							->append('css', '/'.DIR_KERNEL.'/ctl/jcrop/css/jquery.Jcrop.css');
		return parent::render();
	}
}