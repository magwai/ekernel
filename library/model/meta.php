<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_model_meta extends database_model {
	public $name = 'meta';
	public $lang_field = array(
		'data'
	);

	function fetch_by_url($url) {
		$list = $this->fetch_all('`url` != ""', '(LENGTH(`url`))');
		if (count($list)) {
			foreach ($list as $v) {
				if (preg_match('/^'.str_replace(array(
					'-',
					'/',
					'?',
					'*'
				), array(
					'\-',
					'\/',
					'+[\w]?',
					'+[\w]*'
				), $v->url).'$/i', $url)) {
					return (array)@json_decode($v->data_lang);
				}
			}
		}
		return false;
	}

	function fetch_by_controller($name, $id) {
		$ret = $this->fetch_row(array(
			'controller' => $name,
			'parentid' => $id
		));
		return $ret ? (array)@json_decode($ret->data_lang) : array();
	}
}