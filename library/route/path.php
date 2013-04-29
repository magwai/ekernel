<?php

class k_route_path extends route {
	public function route($request) {
		$url = trim($request->url, '/ ');
		$p = explode('?', $url);
		if (count($p) > 1) $url = $p[0];
		if ($url) {
			$parts = explode('/', $url);
			$request->controller = $parts ? (string)array_shift($parts) : 'index';
			$request->action = $parts ? (string)array_shift($parts) : 'index';
			if ($parts) {
				for ($i = 0; $i < count($parts); $i += 2) {
					if ($parts[$i]) $request->param[$parts[$i]] = (string)$parts[$i + 1];
				}
			}
			return true;
		}
		return false;
	}

	public function match($data, $request) {
		$data_controller = @$data['controller'] ? $data['controller'] : 'index';
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
		return false;
	}

	public function assemble($data, $request) {
		if (!isset($data['controller'])) $data['controller'] = $request->controller;
		if (!isset($data['action'])) $data['action'] = $request->action;
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