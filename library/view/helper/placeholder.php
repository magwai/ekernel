<?php

class k_view_helper_placeholder extends view_helper  {
	public $data = array();
	public $key = null;

	public function placeholder($key) {
		$this->key = $key;
		return $this;
	}

	public function capture_start() {
		if (!isset($this->data[$this->key])) $this->data[$this->key] = '';
		ob_start();
		return $this;
	}

	public function capture_end() {
		$this->data[$this->key] .= ob_get_clean();
		return $this;
	}

	public function set($data) {
		$this->data[$this->key] = $data;
		return $this;
	}

	public function get() {
		return (string)$this->data[$this->key];
	}

	public function __toString() {
		return $this->get();
	}
}