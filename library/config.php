<?php

return array(
	// Секция ресурсов. Все перечисленные ресурсы будут проинитены в процессе бутстрапа
	'resource' => array(),
	// Секция плагинов. Все перечисленные плагины будут активны
	'plugin' => array(),
	// Название вьюшки лейаута по-умолчанию
	'layout' => 'layout',
	// Настройки постобработки CSS
	'css' => array(
		// Сливать в один файл
		'merge' => true,
		// Сжимать
		'compress' => true,
		// Генерировать сжатый GZIP файл рядом с оригиналом с расширением GZ
		'gzip_static' => true,
		// Порядок использования компрессоров
		'compressor' => array('yui', 'cssmin')
	),
	// Настройки постобработки JS
	'js' => array(
		// Сливать в один файл
		'merge' => true,
		// Сжимать
		'compress' => true,
		// Генерировать сжатый GZIP файл рядом с оригиналом с расширением GZ
		'gzip_static' => true,
		// Порядок использования компрессоров
		'compressor' => array('gcc', 'jsmin')
	),
	'util' => array(
		'host' => 'http://util.magwai.ru'
	),
	'translate' => array(
		'lang' => 'ru'
	),
	'route' => array(
		'default' => array(
			'type' => 'path'
		),
		'control' => array(
			'type' => 'control'
		)
	),
	'navigation' => array(
		'model' => 'menu'
	),
	'controller' => array(
		'control' => array(
			'navigation' => array(
				'model' => 'cmenu',
				'script' => 'control/menulist',
				'script_bread' => 'control/bread'
			),
			'user' => array(
				'acl' => true,
				'model' => array(
					'user' => 'cuser',
					'role' => 'crole',
					'rule' => 'crule',
					'resource' => 'cresource',
					'role2role' => 'crole2crole',
					'rule2role' => 'crule2crole',
					'rule2resource' => 'crule2cresource'
				)
			),
			'control' => array(
				'ui' => array(
					'theme' => 'smoothness',
					'lang' => 'ru'
				),
				'field_map' => array(
					'parentid' => 'parentid',
					'orderid' => 'orderid',
					'title' => 'title',
					'stitle' => 'stitle'
				),
				'post_field_extend' => array(),
				'post_field_unset' => array(),
				'type' => '',
				'text' => '',
				'notify' => array(),
				'controller' => 'cindex',
				'action' => 'index',
				'tree' => false,
				'static_field' => false,
				'param' => array(
					'orderby' => '',
					'orderdir' => '',
					'page' => 1,
					'perpage' => 50,
					'oid' => 0
				),
				'model' => null,
				'field' => array(),
				'data' => array(),
				'where' => array(),
				'param_default' => array(
					'page' => 1,
					'perpage' => 50,
					'orderdir' => 'asc'
				),
				'config_clink' => array(
					'param' => array(
						'perpage' => 999
					),
					'param_default' => array(
						'perpage' => 999
					),
					'config_type' => array(
						'list' => array(
							'perpage_show' => false
						)
					)
				),
				'config_type' => array(
					'list' => array(
						'table' => array(
							'checkbox' => true
						),
						'button_top' => array('add', 'edit', 'delete'),
						'perpage_list' => array(10, 30, 50, 100, 200, 500, 999),
						'perpage_show' => true
					),
					'add' => array(
						'oac' => array(
							'ok' => true,
							'apply' => false,
							'cancel' => true
						),
						'stop_info' => false
					),
					'edit' => array(
						'oac' => array(
							'ok' => true,
							'apply' => true,
							'cancel' => true
						),
						'stop_info' => false
					)
				),
				'config_action' => array(

				),
				'form' => null,
				'callback' => array(
					'before' => null,
					'after' => null,
					'success' => null,
					'fail' => null,
					'preset' => null,
					'postconfig' => null,
					'prelayout' => null
				),
				'use_db' => true,
				'request' => array(
					'success' => array(
						'controller' => '',
						'action' => 'index',
						'param' => array()
					),
					'fail' => array(
						'controller' => '',
						'action' => 'index',
						'param' => array()
					),
					'cancel' => array(
						'controller' => '',
						'action' => 'index',
						'param' => array()
					),
					'current' => array()
				)
			),
			'control_field' => array(
				'type' => 'text',
				'active' => true,
				'hidden' => false,
				'order' => 100,
				'sortable' => false,
				'script' => null,
				'formatter' => null,
				'align' => 'left'
			)
		)
	)
);