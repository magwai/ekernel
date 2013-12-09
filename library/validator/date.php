<?php

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
