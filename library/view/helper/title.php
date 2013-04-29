<?php

class k_view_helper_title extends view_helper {
	public $item = array();
	public $separator = ' / ';

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

	function render() {
		return '<title>'.implode($this->separator, $this->item).'</title>';
	}

	public function __toString() {
		return (string)$this->render();
	}
}