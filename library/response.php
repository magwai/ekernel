<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_response {
	public $body = array();
	public $header = array();

	// Добавляет заголовок во временное хранилище
	public function header_add($name, $value = null, $code = null) {
		$this->header[] = new data(array(
			'name' => $name,
			'value' => $value,
			'code' => $code
		));
	}

	// Отсылает все заголовки
	public function header_send() {
		if ($this->header) foreach ($this->header as $el) header($el->value
			? ucfirst($el->name).': '.$el->value
			: $el->name, true, $el->code);
	}

	// Добавляет контент по указанному ключу
	public function append($value, $key = 'content') {
		if (!isset($this->body[$key])) $this->body[$key] = '';
		$this->body[$key] .= $value;
	}

	// Задает контент указанного ключа
	public function set($value, $key = 'content') {
		$this->body[$key] = $value;
	}

	// Отсылает тело ответа
	public function send() {
		if ($this->body) {
			$res = implode('', $this->body);
			$config = application::get_instance()->config->html;
			if ($config->compress && ob_start("ob_gzhandler")) ini_set('zlib.output_compression_level', 6);
			else ob_start();
			echo $res;
		}
	}
}