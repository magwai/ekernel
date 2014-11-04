<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_coord extends form_element_input {
	public $map_type = 'yandex';
	public $center = null;
	public $zoom = null;
	public $url = '';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (isset($param['map_type'])) $this->map_type = $param['map_type'];
		if (isset($param['center'])) $this->center = $param['center'];
		if (isset($param['zoom'])) $this->zoom = $param['zoom'];
		$this->type = 'hidden';
	}

	public function render() {
		$opt = array();
		if ($this->map_type) $opt['map_type'] = $this->map_type;
		if ($this->center) $opt['center'] = $this->center->to_array();
		if ($this->zoom) $opt['zoom'] = $this->zoom;
		$this->view->messify->append('js', '/'.DIR_KERNEL.'/ctl/coord/coord.js');
		if ($this->map_type == 'yandex') $this->view->messify->append('js', 'http://api-maps.yandex.ru/2.1/?load=package.standard&lang=ru', array('remote' => true));
		else if ($this->map_type == 'google') $this->view->messify->append('js', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&language=ru', array('remote' => true));
		$this->view->messify->append_inline('js', '$("input[name=\''.$this->name.'\']").coord('.Zend\Json\Json::encode($opt, false, array(
								'enableJsonExprFinder' => true
							)).');');
		return parent::render();
	}
}