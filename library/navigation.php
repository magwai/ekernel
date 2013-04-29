<?php

class k_navigation extends data {
	public $pages = array();
	public $active = null;
	public $parent = null;

	public function __construct($param = array()) {
		$this->set($param);
		$this->param = isset($param['param']) ? $param['param'] : array();
		$this->route = isset($param['route']) ? $param['route'] : 'default';
		if (@$param['pages']) {
			foreach ($param['pages'] as $el) {
				$page = new navigation($el);
				$page->parent = $this;
				unset($this->_data['pages']);
				$this->pages[] = $page;
			}
		}
	}

	public function find_active() {
		if ($this->pages) {
			foreach ($this->pages as $el) {
				$active = $el->find_active();
				if ($active) return $active;
			}
		}
		return $this->is_active() ? $this : false;
	}

	public function is_active($inner = false) {
		$was_active = false;
		if ($inner && $this->pages) {
			foreach ($this->pages as $el) {
				$active = $el->is_active(true);
				if ($active) {
					$was_active = true;
					break;
				}
			}
		}
		if (!$was_active) {
			if ($this->active === null) {
				$router = application::get_instance()->router;
				$request = application::get_instance()->request;
				$route = $this->route ? $this->route : 'default';
				$this->active = $was_active = isset($router->route[$route])
					? $router->route[$route]->match($this->get_param(), $request)
					: false;
			}
			else $was_active = $this->active;
		}
		return $was_active;
	}
	
	function get_param() {
		$p = array(
			'controller' => $this->controller,
			'action' => $this->action
		);
		$param = is_string($this->param) ? explode(',', $this->param) : array();
		$map = is_string($this->map) ? explode(',', $this->map) : array();;
		if ($map) {
			foreach ($map as $n => $el) {
				$el = trim($el);
				if ($el) $p[$el] = trim(@$param[$n]);
			}
		}
		return $p;
	}
	
	function __get($k) {
		if ($k == 'href') {
			$view = application::get_instance()->controller->view;
			$route = $this->route ? $this->route : 'default';
			$href = $view->url($this->get_param(), $route);
			return $href;
		}
		else return $this->get($k);
	}
}