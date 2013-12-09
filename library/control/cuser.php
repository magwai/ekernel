<?php

$meta = $this->config->model->metadata();
if ($meta) foreach ($meta as $k => $k) {
	$this->control(array(
		'field' => array(
			$k => array(
				'active' => false
			)
		)
	));
}

$mr = new model_crole;
$role = $mr->fetch_pairs('id', 'title', null, 'title');

$cb = function($control) {
	if ($control->config->data->password) $control->config->data->password = sha1((string)$control->config->data->password.$control->view->user()->salt);
	else unset($control->config->data->password);
};

$this->control(array(
	'field' => array(
		'login' => array(
			'title' => 'Логин',
			'order' => 1,
			'active' => true,
			'required' => true
		),
		'password' => array(
			'title' => 'Пароль',
			'type' => 'password',
			'active' => true,
			'order' => 2
		),
		'role' => array(
			'title' => 'Роль',
			'type' => 'password',
			'type' => 'select',
			'active' => true,
			'item' => $role,
			'order' => 2
		)
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'password' => array(
					'active' => false
				),
				'role' => array(
					'active' => false
				)
			)
		),
		'add' => array(
			'callback' => array(
				'before' => $cb
			)
		),
		'edit' => array(
			'callback' => array(
				'before' => $cb
			)
		)
	)
));