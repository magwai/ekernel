<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$yn = array(
	'0' => $this->view->translate('control_crule_is_allow_0'),
	'1' => $this->view->translate('control_crule_is_allow_1')
);

$mr = new model_crole;
$role = $mr->fetch_pairs('id', 'title', null, 'title');

$ms = new model_cresource;
$resource = $ms->fetch_pairs('id', 'title', null, 'title');

$this->control(array(
	'field' => array(
		'is_allow' => array(
			'title' => $this->view->translate('control_crule_is_allow_title'),
			'order' => 1,
			'type' => 'select',
			'item' => $yn,
			'script' => 'control/crule/cell/is_allow'
		),
		'role' => array(
			'title' => $this->view->translate('control_crule_role_title'),
			'order' => 2,
			'type' => 'checkbox',
			'multiple' => true,
			'item' => $role,
			'm2m' => array(
				'model' => new model_crule2crole,
				'self' => 'parentid',
				'foreign' => 'role'
			),
			'script' => 'control/crule/cell/role'
		),
		'resource' => array(
			'title' => $this->view->translate('control_crule_resource_title'),
			'order' => 3,
			'type' => 'checkbox',
			'multiple' => true,
			'item' => $resource,
			'm2m' => array(
				'model' => new model_crule2cresource,
				'self' => 'parentid',
				'foreign' => 'resource'
			),
			'script' => 'control/crule/cell/resource'
		)
	)
));