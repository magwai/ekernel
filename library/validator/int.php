<?php

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
