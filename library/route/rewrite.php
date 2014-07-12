<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_route_rewrite extends route {
	public function __construct($param = array()) {
		parent::__construct($param);
		if (!@$this->param['reverse']) $this->param['reverse'] = preg_replace('/\(.*?\)/i', '%s', $this->param['url']);
	}

	public function route($request) {
		$url = trim($request->url, '/ ');
		$res = null;
		if ($url && preg_match('#^'.$this->param['url'].'#i', $url, $res)) {
			$controller = isset($this->param['controller']) ? $this->param['controller'] : 'index';
			$action = isset($this->param['action']) ? $this->param['action'] : 'index';
			$param = new data;
			if (count($res) > 1) {
				$map = @$this->param['map'];
				$def = @$this->param['default'];

				if (!is_array($map)) $map = explode(',', $map);
				if (!is_array($def)) $def = explode(',', $def);
				for ($i = 1; $i < count($res); $i++) if (isset($map[$i - 1])) $param[$map[$i - 1]] = @(string)$res[$i] ? $res[$i] : @$def[$i - 1];
			}
			$request->controller = $controller;
			$request->action = $action;
			$request->param = $param;
			$request->type = 'rewrite';
			return true;
		}
		return false;
	}

	public function match($data, $request) {
		$regex = '#^'.$this->param['url'].'#i';
		$path = trim($request->url, '/');
		$res = preg_match($regex, $path);

        if ($res === 0) {
            return false;
        }

		$assemble_request = $this->assemble($request->param, $request);
		$assemble_data = $this->assemble($data, $request);

		return strlen($assemble_request) && $assemble_request === $assemble_data;
	}

	public function assemble($data, $request) {
		$map = @$this->param['map'];
		if (!is_array($map)) $map = explode(',', $map);
		$d = array();
		foreach ($map as $el) $d[] = @(string)$data[$el] ? $data[$el] : $request->param->$el;
		return '/'.@vsprintf($this->param['reverse'], $d);
	}
}