<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_error {
	public static function call($message = 'Application Error', $code = 500) {
		$_SERVER['REQUEST_URI'] = '/error';
		header('HTTP/1.1 '.$code.' '.$message);
		application::$instance = new application();
		application::get_instance()->bootstrap()->run();
		exit();
	}
}