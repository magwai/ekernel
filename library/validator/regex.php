<?php

class k_validator_regex extends validator {
	public function validate($value) {
		$cnt = @(int)preg_match($this->option, $value);
		if ($value && !$cnt) {
			return array(
				'regex' => array()
			);
		}
		return null;
	}
}