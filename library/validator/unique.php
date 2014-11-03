<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_unique extends validator {
	public function validate($value) {
		$id = (int)$this->option['id'];
		$field = $this->option['field'];
		$model = $this->option['model'];
		$where = $this->option['where'];
		$w = array(
			'`id` != ?' => $id,
			$field => $value
		);
		if ($where) $w = array_merge($where instanceof data ? $where->to_array() : $where, $w);
		$ex = $model->fetch_count($w);
		if ($ex) return array(
			'not_unique' => array()
		);
		return null;
	}
}
