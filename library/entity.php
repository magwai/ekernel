<?php

class k_entity extends data {
	private $view = null;

	function __set($k, $v = null) {
		if ($k == 'view') $this->view = $v;
		else $this->set(array($k => $v));
	}

	function __get($k) {
		if ($k == 'view') return $this->view;
		// Смысл класса сущности - в создании виртуальных переменных класса, не связанных с данными сущности. Виртуальная переменная транслируется в метод get_имя_паременной класса сущности, который должен вернуть ее значение
		$method = 'get_'.$k;
		if (method_exists($this, $method)) return $this->$method();
		else {
			$ret = $this->get($k);
			if ($ret === null) {
				$k_replaced = preg_replace('/\_(valid|control)$/i', '', $k);
				if ($k != $k_replaced) $ret = $this->get($k_replaced);
			}
			return $ret;
		}
		
		return method_exists($this, $method)
			? $this->$method()
			: $this->get($k);
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