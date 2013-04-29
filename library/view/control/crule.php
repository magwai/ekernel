<?php

$yn = array(
	'0' => 'Запретить',
	'1' => 'Разрешить'
);

$mr = new model_crole;
$role = $mr->fetch_pairs('id', 'title', null, 'title');

$ms = new model_cresource;
$resource = $ms->fetch_pairs('id', 'title', null, 'title');

$this->control(array(
	'field' => array(
		'is_allow' => array(
			'title' => 'Правило',
			'order' => 1,
			'type' => 'select',
			'item' => $yn,
			'script' => 'control/crule/cell/is_allow'
		),
		'role' => array(
			'title' => 'Роли',
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
			'title' => 'Ресурсы',
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