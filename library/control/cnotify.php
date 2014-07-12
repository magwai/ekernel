<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$this->control(array(
	'param_default' => array(
		'orderdir' => 'desc',
		'orderby' => 'date'
	),
	'field' => array(
		'title' => array(
			'title' => 'Краткий текст',
			'order' => 1
		),
		'message' => array(
			'title' => 'Подробный текст',
			'order' => 2
		),
		'date' => array(
			'title' => 'Дата',
			'width' => 8,
			'sortable' => true,
			'align' => 'center',
			'order' => 3
		),
		'is_read' => array(
			'active' => false
		),
		'menu' => array(
			'active' => false
		),
		'style' => array(
			'active' => false
		)
	),
	'config_type' => array(
		'list' => array(
			'table' => array(
				'checkbox' => false
			),
			'cell_title' => false,
			'button_top' => array('', '', ''),
		)
	)
));