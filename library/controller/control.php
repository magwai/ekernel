<?php

class k_controller_control extends controller {
	public function before() {
		$this->layout = 'control/layout';
	}

	public function index_action() {
		$this->view_script = 'control/main';
		$this->view->controller = $this->request->ccontroller;
		$this->view->action = $this->request->caction;
		$this->view->param = $this->request->param;
		$this->view->post = $this->request->post;
	}
}