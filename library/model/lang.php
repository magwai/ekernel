<?php

class k_model_lang extends database_model {
	public $name = 'lang';

	function fetch_list() {
		return $this->fetch_all(array(
			'show_it' => 1
		));
	}

	function fetch_list_control() {
		return $this->fetch_list();
	}
}