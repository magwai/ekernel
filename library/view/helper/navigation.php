<?php

class k_view_helper_navigation extends view_helper  {
	private $_inited = false;
	public $container = array();
	public $config = null;

	public function init() {
		if ($this->_inited) return;
		$this->_inited = true;
		$this->config = application::get_instance()->config->navigation;
		$this->load_model($this->config->model);
	}

	public function navigation($navigation = null) {
		$this->init();
		if ($navigation !== null) $this->container = $navigation;
		return $this;
	}

	public function find_active() {
		return $this->container->find_active();
	}

	public function find_by($key, $value) {
		return $this->container ? $this->container->find_by($key, $value) : null;
	}

	public function load_model($model) {
		$class = 'model_'.$model;
		if (!class_exists($class)) return;
		$this->navigation(new navigation(array('pages' => $this->load_tree(new $class))));
	}

	public function load_tree($model, $pid = 0) {
		$ret = array();
		$list = $model->fetch_menu_list($pid);
		if ($list) {
			foreach ($list as $el) {
				if ($el->resource && (!$this->view->user()->is_allowed($el->resource))) continue;
				$el->pages = $this->load_tree($model, $el->id);
				$ret[] = $el;
			}
		}
		return $ret;
	}

	public function menu($container = null, $option = array()) {
		$opt = clone $this->config;
		if ($option) $opt->set($option);
		$container = $container ? $container : $this->container;
		$script = $opt->script
			? $opt->script
			: ($opt->model ? $opt->model : 'menu').'/list';


		return $container && @$container->pages ? $this->view->xlist(array(
			'fetch' => array(
				'data' => $container->pages
			),
			'view' => array(
				'script' => $script,
				'param' => array(
					'script' => $script,
					'class_ul' => $opt->class_ul,
					'class_li' => $opt->class_li
				)
			)
		)) : '';
	}

	public function sitemap($container = null, $param = array()) {
		$container = $container ? $container : $this->container;
		$data = $container->pages;
		if (@$param['start']) {
			$data = array_merge($param['start'], $data);
		}
		if (@$param['finish']) {
			$data = array_merge($data, $param['finish']);
		}
		$config = application::get_instance()->config->navigation;
		$ret = $this->view->xlist(array(
			'fetch' => array(
				'data' => $data
			),
			'view' => array(
				'script' => $config->script_sitemap
					? $config->script_sitemap
					: ($config->model ? $config->model : 'menu').'/sitemap',
				'param' => $param
			)
		));
		header('Content-Type: text/xml');
		return $ret;
	}

	public function bread($param = array()) {
		$ret = '';
		$active = $this->find_active();
		if ($active) {
			$data = array($active);
			while($active->parent !== null) {
				$active = $active->parent;
				if ($active !== null && $active->title) $data[] = $active;

			}
			$data = array_reverse($data);
			if (@$param['start']) {
				$data = array_merge($param['start'], $data);
			}
			if (@$param['finish']) {
				$data = array_merge($data, $param['finish']);
			}
			$config = application::get_instance()->config->navigation;
			$ret = $this->view->xlist(array(
				'fetch' => array(
					'data' => $data
				),
				'view' => array(
					'script' => $config->script_bread
						? $config->script_bread
						: ($config->model ? $config->model : 'menu').'/bread',
				)
			));
		}
		return $ret;
	}

	function control_get_rubric() {
		$rubric = array(
			'' => '[ Использовать URL ]'
		);
		foreach (application::get_instance()->config->route as $k => $route) {
			if (!$route->title) continue;
			$inner = null;
			if ($route->inner) {
				$c = 'controller_'.$route->inner;
				$c = new $c;
				if ($c && method_exists($c, 'get_routes')) $inner = $c->get_routes();
			}
			if ($route->type != 'path') {
				unset($route->param->controller);
				unset($route->param->action);
			}
			unset($route->param->map);
			unset($route->param->url);
			unset($route->param->reverse);
			if (is_array($inner)) {
				if ($route->param) $rubric[json_encode(array(
					'route' => $k,
					'param' => $route->param->to_array()
				))] = $route->title;
				if ($inner) {
					$arr = array();
					foreach ($inner as $el) {
						if ($el->type != 'path') {
							unset($el->param->controller);
							unset($el->param->action);
						}
						unset($el->param->map);
						unset($el->param->url);
						unset($el->param->reverse);
						$arr[json_encode(array(
							'route' => $el->route,
							'param' => $el->param ? $el->param->to_array() : array()
						))] = ($route->param ? '- ' : '').$el->title;
					}
					if ($route->param) $rubric = array_merge($rubric, $arr);
					else $rubric[$route->title] = $arr;
				}
			}
			else $rubric[json_encode(array(
				'route' => $k,
				'param' => $route->param ? $route->param->to_array() : null
			))] = $route->title;
		}
		return $rubric;
	}

	function control_encode(&$control) {
		$control->config->data->controller = '';
		$control->config->data->action = '';
		$control->config->data->param = '';
		$control->config->data->route = '';
		$control->config->data->map = '';
		$control->config->data->param = '';
		if (!$control->config->data->url) {
			$rubric = json_decode($control->config->data->rubric);
			if ($rubric) {
				$control->config->data->route = $rubric->route;
				$param = new data;
				if (application::get_instance()->config->route->{$rubric->route}->param) $param->set(application::get_instance()->config->route->{$rubric->route}->param->to_array());
				$param->set((array)$rubric->param);
				$control->config->data->controller = @(string)$param->controller;
				$control->config->data->action = @(string)$param->action;
				unset($param->controller);
				unset($param->action);
				unset($param->map);
				unset($param->url);
				unset($param->reverse);
				$map = array();
				$params = array();
				if ($param && count($param)) {
					foreach ($param as $k => $v) {
						$map[] = $k;
						$params[] = $v;
					}
				}
				$control->config->data->map = implode(',', $map);
				$control->config->data->param = implode(',', $params);
			}
		}
	}

	function control_decode(&$control) {
		if (!$control->config->data->url) {
			$param = array();
			if (@application::get_instance()->config->route->{$control->config->data->route}->type == 'path') {
				$param['controller'] = $control->config->data->controller;
				if ($control->config->data->action == 'index') $control->config->data->action = '';
				$param['action'] = $control->config->data->action ? $control->config->data->action : 'index';
			}
			$map = explode(',', $control->config->data->map);
			if (!@$map[0]) $map = array();
			if ($map) {
				$params = explode(',', $control->config->data->param);
				foreach ($map as $k => $v) $param[$v] = @$params[$k];
			}
			$control->config->data->rubric = json_encode(array(
				'route' => $control->config->data->route,
				'param' => $param
			));
		}
	}

	public function __toString() {
		return $this->menu();
	}
}