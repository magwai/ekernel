<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_lang extends view_helper {
	private $_inited = false;
	public $data = null;
	public $default = null;

	public function init() {
		if ($this->_inited) return;
		$this->_inited = true;
		$ml = new model_lang;
		$this->data = $ml->fetch_row(array(
			'show_it' => '1'
		), array(
			'(`stitle` = '.$ml->adapter->quote(application::get_instance()->request->param->lang).')' => 'desc',
			'(`is_default` = 1)' => 'desc'
		));
		$this->default = $ml->fetch_row(array(
			'is_default' => '1'
		));
	}

	public function lang($p = null) {
		if (!application::get_instance()->config->resource->lang) return false;
		$this->init();
		if ($p === true) return $this->data;
		else if ($p !== null) return @$this->data->$p;
    	return $this;
    }
}