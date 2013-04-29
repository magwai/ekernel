<?php

$login = $this->user('login');
$ok = $this->user()->logout();

$this->control(array(
	'notify' => array(
		array(
			'title' => $ok ? 'До свидания, '.$login : 'Вы не были авторизованы',
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