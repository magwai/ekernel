<?php

class k_model_cnotify extends database_model {
	public $name = 'cnotify';

	function fetch_control_no_read_count() {
		return $this->fetch_count(array(
			'`is_read` = ?' => 0,
			'`menu` != ?' => 0
		));
	}

	function fetch_control_no_read() {
		$select = new database_select();
		$select->from($this->name, array(
			'id',
			'title',
			'style'
		));
		$select->where(array(
			'`is_read` = ?' => 0,
			'`menu` = ?' => 0
		));
		$select->order('date');
		$select->limit(50);
		return $this->adapter->fetch_all($select);
	}

	function set_control_read($id) {
		$this->update(array(
			'is_read' => 1
		), array(
			'id' => (int)$id
		));
	}
}