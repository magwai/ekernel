<?php

class k_model_meta extends database_model {
	public $name = 'meta';

	function fetch_by_url($url) {
		$list = $this->fetch_pairs('url', 'data', '`url` != ""', '(LENGTH(`url`))');
		if (count($list)) {
			foreach ($list as $k => $v) {
				if (preg_match('/^'.str_replace(array(
					'-',
					'/',
					'?',
					'*'
				), array(
					'\-',
					'\/',
					'_',
					'%'
				), $k).'$/i', $url)) {
					return (array)@json_decode($v);
				}
			}
		}
		return false;
	}

	function fetch_by_controller($name, $id) {
		return (array)@json_decode($this->fetch_one('data', array(
			'controller' => $name,
			'parentid' => $id
		)));
	}
}