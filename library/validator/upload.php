<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_upload extends validator {
	public function validate($key) {
		$error = array();
		if (isset($_FILES[$key])) {
			$files = array();
			$files_array = array();
			foreach ($_FILES[$key] as $k => $v) {
				$files[$k] = is_array($v) ? $v : array($v);
				if (!$files_array) $files_array = array_keys($files[$k]);
			}
			$ret = array();
			foreach ($files_array as $k) {
				if ($files['error'][$k] == UPLOAD_ERR_NO_FILE) $error['fileerror'] = array();
				else {
					if (!file_exists($this->option->path)) {
						@mkdir($this->option->path, 0777, true);
						@chmod($this->option->path, 0777);
					}
					$old_path = @$files['tmp_name'][$k];
					$old_name = @$files['name'][$k];
					$filter = new filter_filename(array(
						'directory' => $this->option->path,
						'prefix' => $this->option->prefix,
						'length' => $this->option->name_filer_length
					));
					$name = $filter->filter($old_name);
					$ok = @move_uploaded_file($old_path, $this->option->path.'/'.$name);
					if ($ok) {
						@chmod($this->option->path.'/'.$name, 0777);
						$ret[] = $name;
					}
					else $error['fileerror'] = array();
				}
			}
			return implode('*', $ret);
		}
		else $error['fileerror'] = array();
		return $error;
	}
}
