<?php

class k_plugin_menu extends plugin {
	public $model_name = 'model_menu';
	public $model = null;

	function __construct($option) {
		if (isset($option->model_name)) $this->model_name = $option->model_name;
		$this->model = new $this->model_name();
	}

	function controller_before() {
		$menu = $this->go_deeper();
		registry::set('menu', $menu);
	}

	private function go_deeper($pid = 0) {
		$list = $this->model->fetch_menu_list($pid);
		if ($list) {
			foreach ($list as $el) {
				$param_array = array();
				$parts = explode('/', $el->param);
				for ($i = 0; $i < count($parts); $i += 2) if ($parts[$i]) $param_array[$parts[$i]] = @$parts[$i + 1];
				$d = new data(array(
					'title' => $el->title,
					'route' => $el->route ? $el->route : 'default',
					'param' => $param_array,
					'inner' => $this->go_deeper($el->id)
				));
				$data[] = $d;
			}
		}
		return $data;
	}
}