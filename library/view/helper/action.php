<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_action extends view_helper {
	public function action($action, $controller = null, $param = null) {
		$app = $this->layout = application::get_instance();
		$old_request = clone $app->request;
		$old_response = clone $app->response;

		unset($param['controller']);
		unset($param['action']);
		$param['inner'] = true;
		$app->request->controller = $controller;
		$app->request->action = $action;
		$app->request->param = new data($param);

		ob_start();
		$app->run_controller(true);
		$res = ob_get_clean();

		unset($app->request);
		$app->request = $old_request;
		unset($app->response);
		$app->response = $old_response;

		return $res;
	}
}