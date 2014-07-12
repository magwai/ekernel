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
		if (isset($_FILES[$key]) && $_FILES[$key]['error'] != UPLOAD_ERR_NO_FILE) {
			if (!file_exists($this->option->path)) {
				@mkdir($this->option->path, 0777, true);
				@chmod($this->option->path, 0777);
			}
			$old_path = @$_FILES[$key]['tmp_name'];
			$old_name = @$_FILES[$key]['name'];
			$filter = new filter_filename(array(
				'directory' => $this->option->path,
				'prefix' => $this->option->prefix,
				'length' => $this->option->name_filer_length
			));
			$name = $filter->filter($old_name);
			$ok = @move_uploaded_file($old_path, $this->option->path.'/'.$name);
			if ($ok) {
				@chmod($this->option->path.'/'.$name, 0777);
				return $name;
			}
			else $error['fileerror'] = array();
		}
		else $error['fileerror'] = array();
		return $error;
	}
}
