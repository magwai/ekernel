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
			'order' => 1,
			'required' => true
		),
		'key' => array(
			'title' => $this->view->translate('control_menu_key_title'),
			'order' => 2,
			'active' => $this->view->user()->is_allowed_by_key('admin')
		),
		'rubric' => array(
			'title' => $this->view->translate('control_menu_rubric_title'),
			'type' => 'select',
			'item' => $this->view->navigation()->control_get_rubric(),
			'order' => 3
		),
		'url' => array(
			'title' => 'URL',
			'description' => $this->view->translate('control_menu_url_description'),
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
			$control->view->navigation()->control_encode($control);
		},
		'preset' => function($control) {
			$control->view->navigation()->control_decode($control);
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
		),
		'delete' => array(
			'callback' => array(
				'before_el' => function(&$control) {
					if ($control->config->data && $control->config->data->key && !$control->view->user()->is_allowed_by_key('admin')) {
						$control->config->skip_el = true;
						$control->config->notify[] = array(
							'title' => $control->view->translate('control_menu_notify_key'),
							'style' => 'warning'
						);
					}
				}
			)
		)
	)
));