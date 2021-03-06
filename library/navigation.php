<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_navigation extends data {
	public $pages = array();
	public $active = null;
	public $parent = null;

	public function __construct($param = array()) {
		$this->set($param);
		$this->param = isset($param['param']) ? $param['param'] : array();
		$this->route = isset($param['route']) ? $param['route'] : 'default';
		if (isset($param['active'])) $this->active = $param['active'];
		if (@$param['pages']) {
			foreach ($param['pages'] as $el) {
				$el->title = $el->title_lang;
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

	public function find_by($key, $value) {
		if ($this->pages) {
			foreach ($this->pages as $el) {
				if ($el->$key == $value) {
					return $el;
				}
				else {
					$ret = $el->find_by($key, $value);
					if ($ret) return $ret;
				}
			}
		}
		return null;
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
		$map = is_string($this->map) ? explode(',', $this->map) : array();
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
			$e = new entity_menu($this);
			return $e->url_valid;
		}
		else return $this->get($k);
	}
}