<?php

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
