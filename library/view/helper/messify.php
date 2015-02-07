<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_messify extends view_helper {
	private $_messify = null;
	public function __call($method, $args) {
		if ($this->_messify === null) {
			$messify = application::get_instance()->config->messify;
			if (!class_exists('\Magwai\Messify\Messify')) include PATH_ROOT.'/'.DIR_LIBRARY.'/lib/messify/Messify.php';
			try {
				$opt = clone $messify;
				common::to_array($opt);

				$this->_messify = new \Magwai\Messify\Messify(array_merge(array(
					'path_root' => PATH_ROOT,
					'cache_dir' => DIR_CACHE.'/messify',
					'scss' => array(
						'images_dir' => './',
						'fonts_dir' => 'img'
					)
				), $opt));
			}
			catch (Exception $e) {
				die($e->getMessage());
			}
		}
		if (method_exists($this->_messify, $method)) {
			try {
				return call_user_func_array(array($this->_messify, $method), $args);
			}
			catch (Exception $e) {
				die($e->getMessage());
			}
		}
		return $this;
	}
}