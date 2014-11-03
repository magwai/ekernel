<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_model_cmenu extends model_menu {
	public $name = 'cmenu';
	public $lang_field = array();

	function fetch_menu_list($pid = 0) {
		//$mn = new model_cnotify;
		$select = new database_select();
		$select	->from(array(
					'i' => $this->name
				))
				/*->join_left(array(
					'n' => $mn->name
				), 'i.id = n.menu', array(
					'notify_count' => '(COUNT(n.id))'
				))
				->group('i.id')*/
				->order('i.orderid')
				->where('i.parentid = ?', $pid);

		return $this->entity_all($this->adapter->fetch_all($select));
	}
}