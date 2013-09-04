<?php

$this->control(array(
	'tree' => true,
	'field' => array(
		'title' => array(
			'title' => 'Название',
			'order' => 1,
			'required' => true
		),
		'key' => array(
			'title' => 'Ключ',
			'order' => 2,
			'active' => $this->view->user()->is_allowed_by_key('admin')
		),
		'rubric' => array(
			'title' => 'Раздел сайта',
			'type' => 'select',
			'item' => $this->view->navigation()->control_get_rubric(),
			'order' => 3
		),
		'url' => array(
			'title' => 'URL',
			'description' => 'Если указан URL, то он будет использован вместо указанного выше раздела сайта',
			'order' => 4
		),
		'controller' => array(
			'active' => false
		),
		'action' => array(
			'active' => false
		),
		'param' => array(
			'active' => false
		),
		'route' => array(
			'active' => false
		),
		'map' => array(
			'active' => false
		)
	),
	'callback' => array(
		'before' => function($control) {
			$this->view->navigation()->control_encode($control);
		},
		'preset' => function($control) {
			$this->view->navigation()->control_decode($control);
		}
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'url' => array(
					'active' => false
				),
				'rubric' => array(
					'active' => false
				)
			)
		)
	)
));