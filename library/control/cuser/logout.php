<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$login = $this->view->user('login');
$ok = $this->view->user()->logout();

$this->control(array(
	'notify' => array(
		array(
			'title' => $ok ? $this->view->translate('control_cuser_logout_notify_ok').', '.$login : $this->view->translate('control_cuser_logout_notify_nok'),
			'style' => $ok ? 'success' : 'warning'
		)
	),
	'request' => array(
		'current' => array(
			'controller' => 'cindex',
			'action' => 'index'
		)
	)
));