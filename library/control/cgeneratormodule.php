<?php

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