<?php

class k_route_control extends route {
	public function route($request) {
		$url = trim($request->url, '/ ');
		$p = explode('?', $url);
		if (count($p) > 1) $url = $p[0];
		if ($url) {
			$parts = explode('/', $url);
			$request->controller = array_shift($parts);
			if ($request->controller != 'control') return false;
			$request->action = 'index';
			$request->ccontroller = $parts ? (string)array_shift($parts) : 'cindex';
			$request->caction = $parts ? (string)array_shift($parts) : 'index';
			if ($parts) {
				for ($i = 0; $i < count($parts); $i += 2) {
					if ($parts[$i]) $request->param[$parts[$i]] = urldecode((string)$parts[$i + 1]);
				}
			}
			return true;
		}
		return false;
	}

	public function match($data, $request) {
		$data_controller = @$data['ccontroller'] ? $data['ccontroller'] : $data['controller'];
		$data_action = @$data['caction'] ? $data['caction'] : $data['action'];
		unset($data['ccontroller']);
		unset($data['caction']);
		unset($data['controller']);
		unset($data['action']);
		if ((!$data_controller || $data_controller == $request->ccontroller) && (!$data_action || $data_action == $request->caction)) {
			if (@$request->cparam) {
				foreach ($request->cparam as $k => $v) {
					if ($v != @$data[$k]) return false;
				}
			}
			return true;
		}
		return false;
	}

	public function assemble($data, $request, $default = array()) {
		if (!isset($data['ccontroller'])) $data['ccontroller'] = isset($data['controller']) ? $data['controller'] : $request->ccontroller;
		if (!isset($data['caction'])) $data['caction'] =  isset($data['action']) ? $data['action'] : $request->caction;
		$ccontroller = $data['ccontroller'];
		unset($data['ccontroller']);
		$caction = $data['caction'];
		unset($data['caction']);
		if (!$ccontroller) $ccontroller = 'cindex';
		if (!$caction) $caction = 'index';
		unset($data['controller']);
		unset($data['action']);
		$url = '';
		if ($data) {
			foreach ($data as $k => $v) {
				if ($k && $v && $v != @$default[$k]) $url .= '/'.urlencode($k).'/'.urlencode($v);
			}
		}

		return '/control'.($url || $ccontroller != 'cindex' || $caction != 'index' ? '/'.$ccontroller : '').($url || $caction != 'index' ? '/'.$caction : '').$url;
	}
}