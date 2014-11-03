<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_lang extends view_helper {
	public $_inited = false;
	public $data = null;
	public $default = null;
	public $_key = 'lang';

	public function init() {
		if ($this->_inited) return;
		$this->_inited = true;
		if (application::get_instance()->config->resource->lang->type == 'session') {
			$lang_cookie = @$_COOKIE[$this->_key];
			$lang_session = session::get('lang');
			if ($lang_session) {
				if ($lang_session != $lang_cookie) {
					$this->set($lang_session);
				}
				$lang_param = $lang_session;
			}
			else {
				$lang_param = $lang_cookie;
			}
		}
		else {
			$lang_param = application::get_instance()->request->param->lang;
		}

		$ml = new model_lang;
		$this->data = $ml->fetch_row(array(
			'show_it' => '1'
		), array(
			'(`stitle` = '.$ml->adapter->quote($lang_param).')' => 'desc',
			'(`is_default` = 1)' => 'desc'
		));
		$this->default = $ml->fetch_row(array(
			'is_default' => '1'
		));
		if (!$lang_param) {
			if (application::get_instance()->config->resource->lang->type == 'session') {
				session::set('lang', $this->default->stitle);
			}
		}
	}

	public function lang($p = null) {
		if (!application::get_instance()->config->resource->lang) return false;
		$this->init();
		if ($p === true) return $this->data;
		else if ($p !== null) return @$this->data->$p;
    	return $this;
    }

	public function set($lang) {
		if (application::get_instance()->config->resource->lang->type != 'session') return false;
		if ($lang) session::set('lang', $lang);
		else session::remove('lang');
		setcookie(
			$this->_key,
			$lang,
			time() + 86400 * 30,
			'/'
		);
	}

}