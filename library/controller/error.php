<?php

class k_controller_error extends controller {
	function index_action() {
		$this->forward('index', 'page', array(
			'id' => 'error'
		));
	}
}