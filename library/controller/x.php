<?php

class k_controller_x extends controller {
	public function suggest_action() {
		$this->view->post = $this->request->post;
	}

	public function upload_action() {
		$this->view->files = $this->request->files;
	}

	public function autocomplete_action() {
		$this->view->model = $this->request->param->model;
		$this->view->method = $this->request->param->method;
		$this->view->param = $this->request->param->param;
	}
}