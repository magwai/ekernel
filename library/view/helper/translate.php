<?php

class k_view_helper_translate extends view_helper  {
	public $data = array();

	public function init() {
		if ($this->data) return;
		$lang = application::get_instance()->config->translate->lang;
		if (file_exists(PATH_ROOT.'/'.DIR_LIBRARY.'/translate/'.$lang.'.php')) {
			$data = include(PATH_ROOT.'/'.DIR_LIBRARY.'/translate/'.$lang.'.php');
			if ($data) $this->data = array_merge($this->data, $data);
		}
		if (file_exists(PATH_ROOT.'/'.DIR_APPLICATION.'/translate/'.$lang.'.php')) {
			$data = include(PATH_ROOT.'/'.DIR_APPLICATION.'/translate/'.$lang.'.php');
			if ($data) $this->data = array_merge($this->data, $data);
		}
		if (class_exists('model_translate')) {
			$m = new model_translate;
			$data_db = $m->fetch_pairs('key', 'value');
			if ($data_db) $this->data = array_merge($this->data, $data_db->to_array());
		}
	}

	public function translate($text) {
		$this->init();
		return isset($this->data[$text]) ? $this->data[$text] : '';
	}
}