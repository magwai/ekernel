<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_request {
	public $url = '';
	public $get = array();
	public $post = array();
	public $controller = 'index';
	public $action = 'index';
	public $param = array();

	public function __construct() {
		// Заполняем данные request из запроса
		if(stripos($_SERVER['REQUEST_URI'], '?') !== FALSE) {
			$parts = explode('?', $_SERVER['REQUEST_URI']);
			$this->url = $parts[0];
		} else {
			$this->url = $_SERVER['REQUEST_URI'];
		}
		$this->get = new data($_GET);
		$this->post = new data($_POST);
		$this->files = new data($_FILES);
		$this->param = new data;
	}

	public function is_ajax() {
		return @$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}
}