<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_captcha extends validator {
	public function validate($value) {
		if ($this->option['captcha'] && !$this->option['captcha']->isValid(array(
			'input' => $value,
			'id' => @$this->option['id']
		))) {
			return array(
				'captcha' => array()
			);
		}
		return null;
	}
}
