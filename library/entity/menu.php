<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_entity_menu extends entity {
	function get_url_valid() {
		if ($this->url) return $this->url;
		$route = $this->route ? $this->route : 'default';

		$p = array(
			'controller' => $this->controller,
			'action' => $this->action
		);
		if ($this->param) {
			if (is_string($this->param)) {
				$param = explode(',', $this->param);
				$map = is_string($this->map) ? explode(',', $this->map) : array();
				if ($map) {
					foreach ($map as $n => $el) {
						$el = trim($el);
						if ($el) $p[$el] = trim(@$param[$n]);
					}
				}
			}
			else {
				$pp = clone $this->param;
				if ($pp instanceof data) $pp = $pp->to_array();
				$p = array_merge($p, $pp);
			}
		}
		$view = application::get_instance()->controller->view;
		$href = $view->url($p, $route);
		return $href;
	}
}