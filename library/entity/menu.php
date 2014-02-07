<?php

class k_entity_menu extends entity {
	function get_url_valid() {
		if ($this->url) return $this->url;
		$route = $this->route ? $this->route : 'default';

		$p = array(
			'controller' => $this->controller,
			'action' => $this->action
		);
		$param = is_string($this->param) ? explode(',', $this->param) : array();
		$map = is_string($this->map) ? explode(',', $this->map) : array();
		if ($map) {
			foreach ($map as $n => $el) {
				$el = trim($el);
				if ($el) $p[$el] = trim(@$param[$n]);
			}
		}
		$view = application::get_instance()->controller->view;
		$href = $view->url($p, $route);
		return $href;
	}
}