<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_notempty extends validator {
	public function validate($value) {
		if (is_scalar($value) ? strlen($value) == 0 : !$value) {
			return array(
				'empty' => array()
			);
		}
		return null;
	}
}
