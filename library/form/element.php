<?php

class k_form_element {
	public $frame_view_script = null;
	public $view = null;
	public $value = null;
	public $class = '';
	public $class_error = '';
	public $class_frame = '';
	public $class_label = '';
	public $class_control = '';
	public $id = '';
	public $name = '';
	public $label = '';
	public $attr = array();
	public $description = '';
	public $required = false;
	public $attribute = array();
	public $error = array();
	public $item = array();
	public $error_user = array();
	public $validator = array();

	public function __construct($name, $param = array()) {
		$this->name = $name;
		if (isset($param['id'])) $this->id = $param['id'];
		if (isset($param['class'])) $this->class = $param['class'];
		if (isset($param['view_script'])) $this->view_script = $param['view_script'];
		if (isset($param['frame_view_script'])) $this->frame_view_script = $param['frame_view_script'];
		if (isset($param['class_error'])) $this->class_error = $param['class_error'];
		if (isset($param['class_frame'])) $this->class_frame = $param['class_frame'];
		if (isset($param['class_control'])) $this->class_control = $param['class_control'];
		if (isset($param['class_label'])) $this->class_label = $param['class_label'];
		if (isset($param['label'])) $this->label = $param['label'];
		if (isset($param['attr'])) $this->attr = $param['attr'];
		if (isset($param['validator'])) $this->validator = $param['validator'];
		if (isset($param['description'])) $this->description = $param['description'];
		if (isset($param['required'])) $this->required = $param['required'];
		if (isset($param['value'])) $this->value = $param['value'];
		if (isset($param['attribute'])) $this->attribute = new data($param['attribute']);
		if (isset($param['item'])) $this->item = new data($param['item']);

		$this->view = application::get_instance()->controller->view;
	}

	public function get($for_render = false) {
		return $for_render ? $this->value : $this->value;
	}

	public function set($value) {
		$this->value = $value;
	}

	public function get_error() {
		$error = array();
		if ($this->error) {
			foreach ($this->error as $k => $v) {
				$r1 = array();
				$r2 = array();
				if ($v) {
					foreach ($v as $k1 => $v1) {
						$r1 = '{'.$k1.'}';
						$r2 = $v1;
					}
				}
				$error[] = str_ireplace($r1, $r2, $this->view->translate('form_element_error_'.$k));
			}
		}
		if ($this->error_user) $error = array_merge($error, $this->error_user);
		return $error;
	}

	public function validate($value) {
		$validators = $this->validator instanceof data ? $this->validator->to_array() : $this->validator;;
		if ($this->required) array_unshift($validators, 'notempty');
		if ($validators) {
			foreach ($validators as $k => $v) {
				if (is_numeric($k)) {
					$validator = $v;
					$option = array();
				}
				else {
					$validator = $k;
					$option = $v;
				}
				$class = 'validator_'.$validator;
				if ($validator == 'unique') {
					if (!isset($option['field'])) {
						$option['field'] = $this->name;
					}
				}
				$obj = new $class($option);
				$valid = $obj->validate($value);
				//if ($this->name=='tag'){print_r($valid);exit();}
				if ($valid) {
					$this->error = array_merge($this->error, $valid);
					if ($validator == 'notempty') break;
				}
			}
		}
		$this->set($value);
	}

	public function render() {
		$param = $this;
		$param->value = $this->get(true);
		return $this->view->render($this->view_script, $param);
	}

	public function __toString() {
		return (string)$this->render();
	}
}
