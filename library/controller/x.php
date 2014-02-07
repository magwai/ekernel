<?php

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
}