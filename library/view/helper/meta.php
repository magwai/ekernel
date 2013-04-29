<?php

class k_view_helper_meta extends view_helper {
	public $item = array();

	public function set($key, $type, $content) {
		$this->item[strtolower($key.'_'.$type)] = $content;
		return $this;
	}

	public function remove($key, $type) {
		unset($this->item[strtolower($key.'_'.$type)]);
		return $this;
	}

	function render() {
		$res = '';
		if ($this->item) {
			foreach ($this->item as $key => $el) {
				$parts = explode('_', $key);
				$res .= '<meta '.htmlspecialchars($parts[0]).'="'.htmlspecialchars($parts[1]).'" content="'.htmlspecialchars($el).'" />';
			}
		}
		return $res;
	}

	public function __toString() {
		return (string)$this->render();
	}
}