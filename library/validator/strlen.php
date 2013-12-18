<?php

class k_validator_strlen extends validator {
	public function validate($value) {
		if ($value) {
			$length = mb_strlen($value, 'utf-8');
			if (@$this->option['max'] && $length > $this->option['max']) return array(
				'strlen_max' => array(
					'value' => $this->option['max']
				)
			);
			else if (@$this->option['min'] && $length < $this->option['min']) return array(
				'strlen_min' => array(
					'value' => $this->option['min']
				)
			);
		}
		return null;
	}
}
