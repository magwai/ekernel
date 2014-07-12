<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_entity extends data {
	public $view = null;

	function __set($k, $v = null) {
		if ($k == 'view') $this->view = $v;
		else $this->set(array($k => $v));
	}

	function __get($k) {
		if ($k == 'view') return $this->view;
		// Смысл класса сущности - в создании виртуальных переменных класса, не связанных с данными сущности. Виртуальная переменная транслируется в метод get_имя_паременной класса сущности, который должен вернуть ее значение
		$method = 'get_'.$k;
		$k_replaced = preg_replace('/\_(valid|control|lang)$/i', '', $k);
		if (method_exists($this, $method)) $ret = $this->$method();
		else {
			$ret = $this->get($k);
			if ($ret === null && $k != $k_replaced) {
				$ret = $this->get($k_replaced);
			}
		}
		if ($k != $k_replaced) {
			$reg = application::get_instance()->controller->view->lang(true);
			if ($reg && array_key_exists('ml_'.$k_replaced.'_'.$reg->id, $this->_data)) {
				$ret_lang = $this->get('ml_'.$k_replaced.'_'.$reg->id);
				if (strlen($ret_lang) == 0) {
					if (array_key_exists('ml_'.$k_replaced.'_'.application::get_instance()->controller->view->lang()->default->id, $this->_data)) {
						$ret_lang = $this->get('ml_'.$k_replaced.'_'.application::get_instance()->controller->view->lang()->default->id);
						if (strlen($ret_lang) > 0) $ret = $ret_lang;
					}
				}
				else $ret = $ret_lang;
			}
		}
		return $ret;
	}

	// Виртуальная переменная date_valid. Упрощает вывод даты до ДД.ММ.ГГГГ
	function get_date_valid() {
		$time = strtotime($this->date);
		return $time ? date('d.m.Y', $time) : '';
	}

	function get_date_control() {
		$time = strtotime($this->date);
		return $time ? date('d.m.Y', $time) : '';
	}

	// Виртуальная переменная title_valid. Эранирует поле title
	function get_title_valid() {
		return htmlspecialchars($this->title, ENT_QUOTES);
	}
}