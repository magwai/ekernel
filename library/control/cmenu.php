<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$yn = array(
	'0' => 'Слева в основном меню',
	'1' => 'Внутри раздела'
);

$mr = new model_cresource;
$resource = $mr->fetch_pairs('id', 'title', null, 'title');

$this->control(array(
	'post_field_extend' => array(
		'route' => 'control'
	),
	'tree' => true,
	'field' => array(
		'controller' => array(
			'title' => 'Контроллер',
			'description' => 'Контроллер можно не указывать у пунктов меню, содержащих вложенные пункты',
			'order' => 2
		),
		'action' => array(
			'title' => 'Действие',
			'description' => 'Если не указано - будет index',
			'order' => 3
		),
		'param' => array(
			'title' => 'Параметры',
			'description' => 'Через запятую без пробелов',
			'order' => 4
		),
		'map' => array(
			'title' => 'Значения параметров',
			'description' => 'Через запятую без пробелов',
			'order' => 5
		),
		'is_inner' => array(
			'title' => 'Расположение',
			'type' => 'select',
			'item' => $yn,
			'value' => '0',
			'order' => 6
		),
		'resource' => array(
			'title' => 'Ресурс',
			'type' => 'select',
			'item' => $resource,
			'order' => 7
		),
		'route' => array(
			'active' => false
		)

	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'controller' => array(
					'active' => false
				),
				'action' => array(
					'active' => false
				),
				'param' => array(
					'active' => false
				),
				'map' => array(
					'active' => false
				),
				'is_inner' => array(
					'active' => false
				),
				'resource' => array(
					'active' => false
				)
			)
		)
	)
));