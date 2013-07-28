<?php

$this->control(array(
	'static_field' => true,
	'field' => array(
		'title' => array(
			'title' => 'Название',
			'sortable' => true,
			'order' => 1,
			'required' => true
		),
		'stitle' => array(
			'title' => 'Псевдоним',
			'active' => false,
			'order' => 2,
			'required' => true
		),
		'message' => array(
			'type' => 'textarea',
			'title' => 'Текст',
			'order' => 6,
			'ckeditor' => true
		),
		'show_it' => array(
			'active' => $this->user()->is_allowed_by_key('admin'),
			'title' => 'Отображать',
			'order' => 7,
			'align' => 'center',
			'width' => 10,
			'value' => 1,
			'script' => 'control/cell/yesno',
			'type' => 'checkbox'
		)
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'message' => array(
					'active' => false
				),
				'url_valid' => array(
					'order' => 2
				)
			)
		),
		'edit' => array(
			'field' => array(
				'stitle' => array(
					'active' => true
				)
			)
		)
	)
));