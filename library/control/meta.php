<?php

$before = function($control) {
	if (count($control->config->data)) {
		$d = array();
		foreach ($control->config->data as $k => $v) {
			if ($k != 'url') $d[$k] = $v;
		}
		$control->config->data->data = json_encode($d);
	}
};

$c = array(
	'field' => array(
		'url' => array(
			'search' => true,
			'sortable' => true,
			'title' => 'Маска URL страницы',
			'description' => 'Адрес должен быть внутренним. Напимер: /news/*, /page/about',
			'order' => 1,
			'required' => true
		),
		'title' => array(
			'title' => 'TITLE',
			'description' => 'Отображается в заголовке окна браузера. Если вначале +, то добавит к содержимому, если -, то вставит перед содержимым. Если в самом начале вписать @, то будет дописано название сайта',
			'order' => 2
		),
		'keywords' => array(
			'title' => 'META: KEYWORDS',
			'description' => 'Ключевые слова перечисляются через запятую. Если вначале +, то добавит к содержимому, если -, то вставит перед содержимым',
			'order' => 3
		),
		'description' => array(
			'title' => 'META: DESCRIPTION',
			'description' => 'Описание должно характеризовать содержимое страницы. Если вначале +, то добавит к содержимому, если -, то вставит перед содержимым',
			'order' => 4
		),
		'data' => array(
			'active' => false
		),
		'controller' => array(
			'active' => false
		)
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'title' => array(
					'active' => false
				),
				'keywords' => array(
					'active' => false
				),
				'description' => array(
					'active' => false
				)
			)
		),
		'add' => array(
			'callback' => array(
				'before' => $before
			)
		),
		'edit' => array(
			'callback' => array(
				'before' => $before,
				'preset' => function($control) {
					$d = @json_decode($control->config->data->data);
					if ($d && count($d)) foreach ($d as $k => $v) $control->config->data->$k = $v;
				}
			)
		)
	)
);

if (@$this->return_only) return $c;
else $this->control($c);