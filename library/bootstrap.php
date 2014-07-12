<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_bootstrap {
	public $resource = array();

	public function bootstrap($resource) {
		if (count($resource)) {
			$this->resource = new data();
			// Пробегаем по всем ресурсам и инициализируем каждый
			foreach ($resource as $k => $v) {
				$this->resource->$k = $this->load_resource($k, $v);
			}
		}
		return $this;
	}

	public function load_resource($name, $config) {
		// Инициализируем ресурс. Просто создаем экземпляр класса
		$class = 'resource_'.$name;
		return new $class($config);
	}
}