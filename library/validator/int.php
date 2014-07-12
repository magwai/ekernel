<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_int extends validator {
	public function validate($value) {
		if (strlen($value)) {
			if ($value != (int)$value) return array(
				'not_int' => array()
			);
		}
		return null;
	}
}
