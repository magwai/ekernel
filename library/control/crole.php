<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$mr = new model_crole;
$role = $mr->fetch_pairs('id', 'title', null, 'title');

$this->control(array(
	'field' => array(
		'key' => array(
			'title' => 'Ключ',
			'order' => 2
		),
		'role' => array(
			'title' => 'Родительские роли',
			'order' => 3,
			'type' => 'checkbox',
			'multiple' => true,
			'item' => $role,
			'm2m' => array(
				'model' => new model_crole2crole,
				'self' => 'parentid',
				'foreign' => 'role'
			),
			'script' => 'control/crole/cell/role'
		)
	)
));