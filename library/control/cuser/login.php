<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$this->config_include('cuser');

$this->control(array(
	'use_db' => false,
	'type' => 'add',
	'oac' => array(
		'cancel' => false,
		'apply' => false,
		'ok' => array(
			'value' => $this->view->translate('control_cuser_login_ok_value'),
		)
	),
	'place' => $this->view->translate('control_cuser_login_place'),
	'field' => array(
		'login' => array(
			'required' => true,
			'order' => 1
		),
		'password' => array(
			'type' => 'password',
			'required' => true,
			'order' => 2
		),
		'remember' => array(
			'type' => 'checkbox',
			'title' => $this->view->translate('control_cuser_remember_title'),
			'value' => 1,
			'order' => 3
		),
		'role' => array(
			'active' => false
		),
		'date' => array(
			'active' => false
		),
		'active' => array(
			'active' => false
		),
		'pic' => array(
			'active' => false
		)
	),
	'callback' => array(
		'success' => function($control) {
			$ok = $control->view->user()->login($control->config->data->login, $control->config->data->password, $control->config->data->remember);
			unset($control->config->notify);
			$control->config->notify = array(array(
				'title' => $ok ? $control->view->translate('control_cuser_login_notify_ok').', '.$control->config->data->login : $control->view->translate('control_cuser_login_notify_nok'),
				'style' => $ok ? 'success' : 'warning'
			));
			if ($ok) $control->config->request->current = array(
				'controller' => 'cindex',
				'action' => 'index'
			);
		}
	)
));