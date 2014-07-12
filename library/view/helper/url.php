<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_url extends view_helper  {
	public $default = array();

	public function url($data = null, $route = 'default') {
		if ($data === null) return $this;
		$router = application::get_instance()->router;
		$request = application::get_instance()->request;
		return isset($router->route[$route]) ? $router->route[$route]->assemble($data, $request, $this->default) : '';
	}
}