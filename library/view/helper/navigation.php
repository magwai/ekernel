<?php

class k_view_helper_navigation extends view_helper  {
	private $_inited = false;
	public $container = array();

	public function init() {
		if ($this->_inited) return;
		$this->_inited = true;
		$config = application::get_instance()->config->navigation;
		$this->load_model($config->model);
	}

	public function navigation($navigation = null) {
		$this->init();
		if ($navigation !== null) $this->container = $navigation;
		return $this;
	}

	public function find_active() {
		return $this->container->find_active();
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

	public function menu() {
		$config = application::get_instance()->config->navigation;
		$script = $config->script
			? $config->script
			: ($config->model ? $config->model : 'menu').'/list';
		return $this->container && @$this->container->pages ? $this->view->xlist(array(
			'fetch' => array(
				'data' => $this->container->pages
			),
			'view' => array(
				'script' => $script,
				'param' => array(
					'script' => $script
				)
			)
		)) : '';
	}

	public function bread() {
		$ret = '';
		$active = $this->find_active();
		if ($active) {
			$data = array($active);
			while($active->parent !== null) {
				$active = $active->parent;
				if ($active !== null && $active->title) $data[] = $active;
				
			}
			$config = application::get_instance()->config->navigation;
			$ret = $this->view->xlist(array(
				'fetch' => array(
					'data' => array_reverse($data)
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

	public function __toString() {
		return $this->menu();
	}
}