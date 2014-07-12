<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_email extends validator {
	public function validate($value) {
		if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return array(
				'email' => array()
			);
		}
		return null;
	}
}
