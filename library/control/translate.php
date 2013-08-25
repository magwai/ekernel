<?php

$this->control(array(
	'tree' => true,
	'field' => array(
		'title' => array(
			'title' => 'Название',
			'sortable' => true,
			'order' => 1,
			'active' => $this->user()->is_allowed_by_key('admin'),
			'required' => true
		),
		'key' => array(
			'title' => 'Ключ',
			'sortable' => true,
			'order' => 2,
			'active' => $this->user()->is_allowed_by_key('admin'),
			'required' => true
		),
		'value' => array(
			'type' => 'textarea',
			'title' => 'Значение',
			'order' => 3
		)
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'value' => array(
					'active' => false
				),
				'title' => array(
					'active' => true
				)
			)
		)
	)
));