<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$yn = array(
	'0' => $this->view->translate('control_cmenu_is_inner_0'),
	'1' => $this->view->translate('control_cmenu_is_inner_1')
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
			'title' => $this->view->translate('control_cmenu_controller_title'),
			'description' => $this->view->translate('control_cmenu_controller_description'),
			'order' => 2
		),
		'action' => array(
			'title' => $this->view->translate('control_cmenu_action_title'),
			'description' => $this->view->translate('control_cmenu_action_description'),
			'order' => 3
		),
		'param' => array(
			'title' => $this->view->translate('control_cmenu_param_title'),
			'description' => $this->view->translate('control_cmenu_param_description'),
			'order' => 4
		),
		'map' => array(
			'title' => $this->view->translate('control_cmenu_map_title'),
			'description' => $this->view->translate('control_cmenu_map_description'),
			'order' => 5
		),
		'is_inner' => array(
			'title' => $this->view->translate('control_cmenu_is_inner_title'),
			'type' => 'select',
			'item' => $yn,
			'value' => '0',
			'order' => 6
		),
		'resource' => array(
			'title' => $this->view->translate('control_cmenu_resource_title'),
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