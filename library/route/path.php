<?php

class k_route_path extends route {
	public function route($request) {
		$url = trim($request->url, '/ ');
		$p = explode('?', $url);
		if (count($p) > 1) $url = $p[0];
		$parts = explode('/', $url);
		if (!@$parts[0]) $parts = array();
		$controller = $parts ? (string)array_shift($parts) : 'index';
		$action = $parts ? (string)array_shift($parts) : 'index';
		$param = new data;
		if ($parts) {
			for ($i = 0; $i < count($parts); $i += 2) {
				if ($parts[$i]) $param[$parts[$i]] = @(string)$parts[$i + 1];
			}
		}
		if (isset($this->param)) {
			foreach ($this->param as $k => $v) {
				if ($v != @$param[$k]) return false;
			}
		}
		$request->controller = $controller;
		$request->action = $action;
		$request->param = $param;
		$request->type = 'path';
		return true;
	}

	public function match($data, $request) {
		$request_param = array(
			'controller' => $request->controller,
			'action' => $request->action
		);
		if ($request->param && count($request->param)) $request_param = array_merge($request_param, $request->param->to_array());
		$assemble_request = $this->assemble($request_param, $request);
		$assemble_data = $this->assemble($data, $request);
		return $assemble_data == '/' ? $assemble_request == $assemble_data : stripos($assemble_request, $assemble_data) === 0;// $assemble_request === $assemble_data;
		/*$data_controller = @$data['controller'] ? $data['controller'] : 'index';
		unset($data['controller']);
		$data_action = @$data['action'] ? $data['action'] : 'index';
		unset($data['action']);
		if ($data_controller == $request->controller && $data_action == $request->action) {
			if ($request->param) {
				foreach ($request->param as $k => $v) {
					if ($v != @$data[$k]) return false;
				}
				return true;
			}
		}
		return false;*/
	}

	public function assemble($data, $request) {
		if ($request->type === 'path') {
			if (!isset($data['controller'])) $data['controller'] = 'index';
			if (!isset($data['action'])) $data['action'] = 'index';
		}
		$controller = $data['controller'];
		unset($data['controller']);
		$action = $data['action'];
		unset($data['action']);
		if (!$controller) $controller = 'index';
		if (!$action) $action = 'index';

		$url = '';
		if ($data) {
			foreach ($data as $k => $v) {
				if ($k && $v) $url .= '/'.urlencode($k).'/'.urlencode($v);
			}
		}

		return '/'.($url || $controller != 'index' || $action != 'index' ? $controller : '').($url || $action != 'index' ? '/'.$action : '').$url;
	}
}