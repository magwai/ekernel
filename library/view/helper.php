<?php

class k_view_helper {
	public $view = null;

	public function __call($method, $args) {
		$method = null;
		$args = null;
		// Пусто. При несуществующем методе прсто проваливаемся сюда вместо ошибки
		return $this;
	}
}