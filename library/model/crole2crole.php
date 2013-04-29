<?php

class k_model_crole2crole extends database_model {
	public $name = 'crole2crole';

	function fetch_role_title_by_role($rule) {
		$mr = new model_crole;
		$select = new database_select();
		$select->from(array('i' => $this->name), array(
			'r.title'
		));
		$select->join(array('r' => $mr->name), 'r.id = i.role', '');
		$select->where(array(
			'i.parentid' => $rule
		));
		$select->order('r.title');
		$select->group('r.id');
		return $this->adapter->fetch_col($select);
	}
}