<?php

class k_validator_date {
	public function validate($value) {
		if (!strtotime($value)) {
			return array(
				'date' => array()
			);
		}
		return null;
	}
}