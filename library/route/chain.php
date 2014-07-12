<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_route_chain extends route {
	public function __construct($param = array()) {
		parent::__construct($param);
		if (count($this->param->part)) {
			foreach($this->param->part as $k => $v) {
				$class = 'route_'.$v->type;
				$this->param->part->$k = class_exists($class) ? new $class($v->param) : new data($v->param);
			}
		}
	}

	public function route($request) {
		$original = $request->url;
		$all_ok = true;
		if (count($this->param->part)) {
			$request_clone = clone $request;
			foreach($this->param->part as $k => $v) {
				if (!method_exists($v, 'route')) {
					$all_ok = false;
					continue;
				}
				$ok = $v->route($request_clone);
				if ($ok) {
					$repl = $v->assemble($request_clone->param, $request_clone);
					if ($repl && stripos($request_clone->url, $repl) === 0) $request_clone->url = trim(str_ireplace($repl, '', $request_clone->url), '/ ');
					if (@$request_clone->ccontroller) $request->ccontroller = $request_clone->ccontroller;
					if (@$request_clone->caction) $request->caction = $request_clone->caction;
					if (@$request_clone->controller) $request->controller = $request_clone->controller;
					if (@$request_clone->action) $request->action = $request_clone->action;
					if (@$request_clone->type) $request->type = $request_clone->type;
					if (@$request_clone->param) $request->param->set($request_clone->param);
				}
				else $all_ok = false;
			}
		}
		$request->url = $original;
		return $all_ok;
	}

	public function match($data, $request) {
		$all_ok = true;
		if (count($this->param->part)) {
			$request_clone = clone $request;

			foreach($this->param->part as $k => $v) {
				$maps = $v->param ? explode(',', $v->param->map) : array();
				if ($maps) foreach ($maps as $el) if ($el && !isset($data[$el])) $data[$el] = $request_clone->param->$el;
			}

			foreach($this->param->part as $k => $v) {
				if (!method_exists($v, 'match')) {
					$all_ok = false;
					continue;
				}
				$ok = $v->match($data, $request_clone);

				if ($ok) {
					$repl = $v->assemble($request_clone->param, $request_clone);
					if ($repl && stripos($request_clone->url, $repl) === 0) $request_clone->url = trim(str_ireplace($repl, '', $request_clone->url), '/ ');

					$maps = $v->param ? explode(',', $v->param->map) : array();
					if ($maps) foreach ($maps as $el) unset($data[$el]);
				}
				else $all_ok = false;
			}
		}
		return $all_ok;
	}

	public function assemble($data, $request, $default = array()) {
		$total = '';
		if (count($this->param->part)) {
			foreach($this->param->part as $k => $v) {
				if (!method_exists($v, 'assemble')) continue;
				$part = $v->assemble($data, $request, $default);
				if ($part && $part != '/') $total .= $part;
				$maps = $v->param ? explode(',', $v->param->map) : array();
				if ($maps) foreach ($maps as $el) unset($data[$el]);
			}
		}
		return $total;
	}
}