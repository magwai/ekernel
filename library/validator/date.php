<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_date extends validator {
	public function validate($value) {
		if ($value && !strtotime($value)) {
			return array(
				'date' => array()
			);
		}
		return null;
	}
}
