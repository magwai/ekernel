<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_model_page extends database_model {
	public $name = 'page';
	public $lang_field = array(
		'title',
		'message'
	);

	function fetch_list($w = array()) {
		$select = new database_select();
		$select	->from(array(
			'i' => $this->name
		));
		if (@(string)$w['id']) $select->where('stitle', (string)$w['id']);
		return $select;
	}

	function fetch_card($id) {
		return $this->fetch_list(array(
			'id' => $id
		));
	}
}