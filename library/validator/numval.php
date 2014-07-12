<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_numval extends validator {
	public function validate($value) {
		if (strlen($value)) {
			if (@$this->option['max'] && $value > $this->option['max']) return array(
				'numval_max' => array(
					'value' => $this->option['max']
				)
			);
			else if (@$this->option['min'] && $value < $this->option['min']) return array(
				'numval_min' => array(
					'value' => $this->option['min']
				)
			);
		}
		return null;
	}
}
