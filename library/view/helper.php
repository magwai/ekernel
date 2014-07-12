<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper {
	public $view = null;

	public function __call($method, $args) {
		$method = null;
		$args = null;
		// Пусто. При несуществующем методе прсто проваливаемся сюда вместо ошибки
		return $this;
	}
}