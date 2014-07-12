<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_clink extends form_element_input {
	public $view_script = 'form/clink';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		$this->controller = isset($param['controller']) ? $param['controller'] : 'cindex';
		$this->action = isset($param['action']) ? $param['action'] : 'index';
		$this->cid = isset($param['cid']) ? $param['cid'] : $this->view->control()->config->param->id;
		if (!$this->cid) {
			$s = (int)session::get($this->controller.'_clink');
			if ($s) {
				$this->cid = $s;
			}
			else {
				$this->cid = time();
				session::set($this->controller.'_clink', $this->cid);
			}
		}
	}
}