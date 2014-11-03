<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_plugin_lang extends plugin {
	public function controller_before() {
		$ml = new model_lang;
		$ids = $ml->fetch_col('stitle', array(
			'show_it' => 1
		));
		if (application::get_instance()->config->resource->lang->type == 'session') {
			if ($ids) {
				$lang_session = session::get('lang');
				$lang_cookie = @$_COOKIE['lang'];
				if ($lang_session && !in_array($lang_session, $ids)) {
					session::remove('lang');
					$lang_session = null;
				}
				if ($lang_cookie && !in_array($lang_cookie, $ids) || ($lang_cookie && $lang_session && $lang_session != $lang_cookie)) {
					setcookie(
						'lang',
						null,
						time() + 86400 * 30,
						'/'
					);
				}
				if ($lang_cookie && !$lang_session) {
					session::set('lang', $lang_cookie);
					$lang_session = $lang_cookie;
				}
				if (application::get_instance()->config->resource->lang->detect && !$lang_session) {
					$lang_detected = $this->prefered_language($ids, @$_SERVER['HTTP_ACCEPT_LANGUAGE']);
					if ($lang_detected) {
						session::set('lang', $lang_detected);
					}
				}
			}
		}
		else {
			if ($ids && !in_array(application::get_instance()->controller->request->param->lang, $ids)) unset(application::get_instance()->controller->request->param->lang);
			if (!application::get_instance()->controller->request->param->lang) {
				application::get_instance()->controller->request->param->lang = application::get_instance()->controller->view->lang()->default->stitle;
			}
			if (application::get_instance()->request->url == '/control') {
				header('Location: /'.application::get_instance()->controller->view->lang()->default->stitle.'/control');
				exit();
			}
			else if (application::get_instance()->request->url == '/error') {
				header('Location: /'.application::get_instance()->controller->view->lang('stitle').'/error');
				exit();
			}
		}
	}

	public function prefered_language(array $available_languages, $http_accept_language) {
		$available_languages = array_flip($available_languages);
		$langs = array();
		preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			list($a, $b) = explode('-', $match[1]) + array('', '');
			$value = isset($match[2]) ? (float) $match[2] : 1.0;
			if(isset($available_languages[$match[1]])) {
				$langs[$match[1]] = $value;
				continue;
			}
			if(isset($available_languages[$a])) {
				$langs[$a] = $value - 0.1;
			}
		}
		arsort($langs);
		$keys = array_keys($langs);
		return @$keys[0];
	}
}