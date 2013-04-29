<?php

class k_model_menu extends database_model {
	public $name = 'menu';

	function fetch_menu_list($pid) {
		return $this->fetch_all(array(
			'parentid' => (int)$pid
		), 'orderid');
	}
}