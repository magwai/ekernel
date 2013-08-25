<?php

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
	if (is_array($inner)) {
		if ($route->param) $rubric[json_encode(array(
			'route' => $k,
			'param' => $route->param->to_array()
		))] = $route->title;
		if ($inner) {
			$arr = array();
			foreach ($inner as $el) {
				$arr[json_encode(array(
					'route' => $el->route,
					'param' => $el->param->to_array()
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

$this->control(array(
	'tree' => true,
	'field' => array(
		'title' => array(
			'title' => 'Название',
			'order' => 1,
			'required' => true
		),
		'rubric' => array(
			'title' => 'Раздел сайта',
			'type' => 'select',
			'item' => $rubric,
			'order' => 2
		),
		'url' => array(
			'title' => 'URL',
			'order' => 3
		),
		'controller' => array(
			'active' => false
		),
		'action' => array(
			'active' => false
		),
		'param' => array(
			'active' => false
		),
		'route' => array(
			'active' => false
		),
		'map' => array(
			'active' => false
		)
	),
	'callback' => array(
		'before' => function($control) {
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
					$param->set(application::get_instance()->config->route->{$rubric->route}->param->to_array());
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
		},
		'preset' => function($control) {
			if (!$control->config->data->url) {
				$param = array();
				if (application::get_instance()->config->route->{$control->config->data->route}->type == 'path') {
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
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'url' => array(
					'active' => false
				),
				'rubric' => array(
					'active' => false
				)
			)
		)
	)
));