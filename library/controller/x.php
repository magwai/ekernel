<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_controller_x extends controller {
	public function suggest_action() {
		$this->view->post = $this->request->post;
	}

	public function upload_action() {
		$this->view->files = $this->request->files;
	}

	public function autocomplete_action() {
		$this->view->action = $this->request->param->action ? $this->request->param->action : 'list';
		$this->view->model = $this->request->param->model;
		$this->view->method = $this->request->param->method;
		$this->view->param = $this->request->param->param;
		$this->view->term = @urldecode($this->request->param->term);
		$this->view->value = @urldecode($this->request->param->value);
	}

	public function scss_action() {
		$this->view->file = $this->request->param->file;
		$this->view->host = $this->request->param->host;
		$this->view->ch = $this->request->param->ch;
	}

	public function minify_action() {
		$this->view->type = $this->request->param->type;
		$this->view->compressor = $this->request->param->compressor;
	}
}