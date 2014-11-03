<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_controller_lang extends controller {
	function set_action() {
		$id = (string)$this->request->param->id;
		$ml = new model_lang;
		$lang = $ml->fetch_one('id', array(
			'stitle' => $id
		));
		$url = @$_SERVER['HTTP_REFERER'];
		if (stripos($url, @$_SERVER['HTTP_HOST']) === false) $url = '/';
		if ($id) {
			$this->view->lang()->set($id);
		}
		header('Location: '.$url);
		exit();
	}
}