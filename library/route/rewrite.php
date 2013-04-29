<?php

class k_route_rewrite extends route {
	public function route($request) {
		$url = trim($request->url, '/ ');
		$res = null;
		if ($url && preg_match('#^'.$this->param['url'].'#i', $url, $res)) {
			$request->controller = isset($this->param['controller']) ? $this->param['controller'] : 'index';
			$request->action = isset($this->param['action']) ? $this->param['action'] : 'index';
			if (count($res) > 1) {
				$map = @$this->param['map'];
				if (!is_array($map)) $map = explode(',', $map);
				for ($i = 1; $i < count($res); $i++) if (isset($map[$i - 1])) $request->param[$map[$i - 1]] = (string)$res[$i];
			}
			return true;
		}
		return false;
	}
}