<?php

application::get_instance()->controller->layout = null;
$name = null;
if (count($this->files)) {
	foreach ($this->files as $k => $v) {
		$name = $k;
		break;
	}
}
if ($name) {
	$data = session::get('uploadifive_'.$name);
	if ($data) {
		$path = @$data['path'];
		if ($path) {
			$control = new form_element_file($name, array(
				'path' => $path,
				'length' => @$data['name_filer_length']
			));
			if (@$data['validator']) $control->validator = $data['validator'];
			$control->validate(null);
			$error = $control->get_error();
			if ($error) echo implode('|', $error);
			else {
				$value = $control->get();
				if ($value) echo 'ok|'.$value;
			}
		}
		else echo $this->translate('form_element_error_uploadifive_nodir');
	}
	else echo $this->translate('form_element_error_uploadifive_session');
}
else echo $this->translate('form_element_error_uploadifive_name');