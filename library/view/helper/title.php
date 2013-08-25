<?php

class k_view_helper_title extends view_helper {
	public $item = array();
	public $separator = ' / ';
	public $reverse = true;
	public $append_site_title = true;

	public function title($title = null) {
		if ($title != null) $this->append($title);
		return $this;
	}

	public function prepend($title) {
		array_unshift($this->item, $title);
		return $this;
	}

	public function append($title) {
		array_push($this->item, $title);
		return $this;
	}

	public function set($position, $title) {
		$this->item[$position] = $title;
		return $this;
	}

	public function remove($position) {
		unset($this->item[$position]);
		return $this;
	}

	public function clear() {
		$this->item = array();
		return $this;
	}

	function render() {
		if ($this->append_site_title) $this->prepend($this->view->translate('site_title'));
		if ($this->item) $this->item = array_reverse($this->item);
		return '<title>'.implode($this->separator, $this->item).'</title>';
	}

	public function __toString() {
		return (string)$this->render();
	}
}