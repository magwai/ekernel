<?php

class k_controller_x extends controller {
	public function suggest_action() {
		$this->view->post = $this->request->post;
	}

	public function upload_action() {
		$this->view->files = $this->request->files;
	}
}