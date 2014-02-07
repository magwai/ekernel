<?php

class k_model_menu extends database_model {
	public $name = 'menu';
	public $lang_field = array(
		'title'
	);

	function fetch_menu_list($pid) {
		return $this->fetch_all(array(
			'parentid' => (int)$pid
		), 'orderid');
	}
}