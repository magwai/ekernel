<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$this->control(array(
	'tree' => true,
	'field' => array(
		'title' => array(
			'sortable' => true,
			'order' => 1,
			'active' => $this->view->user()->is_allowed_by_key('admin'),
			'required' => true
		),
		'key' => array(
			'title' => $this->view->translate('control_translate_key_title'),
			'sortable' => true,
			'order' => 2,
			'active' => $this->view->user()->is_allowed_by_key('admin'),
			'required' => true
		),
		'value' => array(
			'type' => 'textarea',
			'title' => $this->view->translate('control_translate_value_title'),
			'order' => 3
		)
	),
	'config_action' => array(
		'index' => array(
			'button_top' => $this->view->user()->is_allowed_by_key('admin') ? array('add', 'edit', 'delete') : array('', 'edit', ''),
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