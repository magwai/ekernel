<?php

class k_validator_regex extends validator {
	public function validate($value) {
		if (is_string($this->option)) {
			$error = 'regex';
			$mask = $this->option;
		}
		else {
			$error = $this->option['error'];
			$mask = $this->option['mask'];
		}
		$cnt = @(int)preg_match($mask, $value);
		if ($value && !$cnt) {
			return array(
				$error => array()
			);
		}
		return null;
	}
}