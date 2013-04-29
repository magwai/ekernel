<?php

class k_model_cmenu extends model_menu {
	public $name = 'cmenu';

	function fetch_menu_list($pid = 0) {
		$mn = new model_cnotify;
		$select = new database_select();
		$select	->from(array(
					'i' => $this->name
				))
				->join_left(array(
					'n' => $mn->name
				), 'i.id = n.menu', array(
					'notify_count' => '(COUNT(n.id))'
				))
				->group('i.id')
				->order('i.orderid')
				->where('i.parentid = ?', $pid)
				->where('i.is_inner = ?', 0);
		return $this->entity_all($this->adapter->fetch_all($select));
	}
}