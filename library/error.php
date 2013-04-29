<?php

class k_error {
	public static function call($message = 'Application Error', $code = 500) {
		$_SERVER['REQUEST_URI'] = '/error';
		header('HTTP/1.1 '.$code.' '.$message);
		application::$instance = new application();
		application::get_instance()->bootstrap()->run();
		exit();
	}
}