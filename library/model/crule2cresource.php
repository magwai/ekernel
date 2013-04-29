<?php

class k_model_crule2cresource extends database_model {
	public $name = 'crule2cresource';
	
	function fetch_resource_title_by_rule($resource) {
		$mr = new model_cresource;
		$select = new database_select();
		$select->from(array('i' => $this->name), array(
			'r.title'
		));
		$select->join(array('r' => $mr->name), 'r.id = i.resource', '');
		$select->where(array(
			'i.parentid' => $resource
		));
		$select->order('r.title');
		$select->group('r.id');
		return $this->adapter->fetch_col($select);
	}
}