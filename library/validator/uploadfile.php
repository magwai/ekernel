<?php

class k_validator_uploadfile extends validator {
	public function validate($key, $opt) {
		$error = array();
		if (isset($_FILES[$key]) && $_FILES[$key]['error'] != UPLOAD_ERR_NO_FILE) {
			if (!file_exists($opt->path)) {
				@mkdir($opt->path, 0777, true);
				@chmod($opt->path, 0777);
			}
			$old_path = @$_FILES[$key]['tmp_name'];
			$old_name = @$_FILES[$key]['name'];
			$filter = new filter_filename(array(
				'directory' => $opt->path,
				'prefix' => $opt->prefix,
				'length' => $opt->name_filer_length
			));
			$name = $filter->filter($old_name);
			$ok = @move_uploaded_file($old_path, $opt->path.'/'.$name);
			if ($ok) {
				@chmod($opt->path.'/'.$name, 0777);
			}
			else $error['fileerror'] = array();
		}
		else $error['fileerror'] = array();
		return $error ? $error : null;
	}
}
