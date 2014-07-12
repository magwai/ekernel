<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$this->control(array(
	'type' => 'add',
	'field' => array(
		'title' => array(
			'title' => 'Название',
			'description' => 'Например: "Новости"',
			'order' => 1,
			'required' => true
		),
		'stitle' => array(
			'title' => 'Название в коде',
			'description' => 'Латиницей, без пробелов и спецсимволов. Например: "news"',
			'order' => 2,
			'required' => true
		)
	)
));