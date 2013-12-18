<?php

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
