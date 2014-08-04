<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_file_extension extends validator {
	public function validate($value) {
		$ret = null;
		if (is_file($this->option->path.'/'.$value)) {
			
			$info = @pathinfo($this->option->path.'/'.$value);

			$extensions = explode(',', $this->option->extensions);

			if (is_array($info)) {
				foreach ($extensions as $extension) {
					if (strtolower($extension) == strtolower($info['extension'])) {
						return $ret;
					}
				}
			}
			
			$ret = array(
				'file_extensions' => array()
			);
		}
		return $ret;
	}
}