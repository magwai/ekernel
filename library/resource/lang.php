<?php

class k_resource_lang {
	function __construct($config) {
		$route = new data;
		if (count(application::get_instance()->config->route)) {
			$ml = new model_lang;
			$default = $ml->fetch_one('stitle', array(
				'is_default' => 1
			));
			foreach (application::get_instance()->config->route as $k => $v) {
				$route->$k = array(
					'type' => 'chain',
					'param' => array(
						'part' => array(
							array(
								'type' => 'rewrite',
								'param' => array(
									'url' => '([^\/]+)',
									'map' => 'lang',
									'default' => $default
								)
							),
							clone $v
						),
						'controller' => $v->param ? $v->param->controller : null,
						'action' => $v->param ? $v->param->action : null,
						'inner' => $v->inner,
						'title' => $v->title
					)
				);
			}
		}
		application::get_instance()->router = new router($route);
		application::get_instance()->config->plugin[] = 'lang';
	}
}