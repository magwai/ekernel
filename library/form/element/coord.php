<?php

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
		$this->view->js		->append('/library/ctl/coord/coord.js')
							->append($this->map_type == 'yandex' ? 'http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU' : '')
							->append_inline('$("input[name=\''.$this->name.'\']").coord('.Zend\Json\Json::encode($opt, false, array(
								'enableJsonExprFinder' => true
							)).');');
		return parent::render();
	}
}