<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$this->control(array(
	'field' => array(
		'title' => array(
			'title' => 'Название',
			'sortable' => true,
			'order' => 1,
			'required' => true
		),
		'stitle' => array(
			'title' => 'Ключ языка',
			'description' => 'Используется в URL. Только латиница и символы -, _',
			'validator' => array(
				'regex' => '/^[a-z0-9\-\_]+$/si'
			),
			'order' => 2,
			'required' => true
		),
		'is_default' => array(
			'title' => 'По-умолчанию',
			'order' => 7,
			'align' => 'center',
			'width' => 10,
			'value' => 0,
			'single' => array(
				'required' => true
			),
			'script' => 'control/cell/yesno',
			'type' => 'checkbox'
		),
		'show_it' => array(
			'title' => 'Использовать',
			'order' => 8,
			'align' => 'center',
			'width' => 10,
			'value' => 1,
			'script' => 'control/cell/yesno',
			'type' => 'checkbox'
		)
	),
	'callback' => array(
		'check' => function($control) {
			if ($control->config->post->is_default && !$control->config->post->show_it) {
				$control->config->form->element->show_it->error_user[] = 'Язык по-умолчанию нельзя выключить';
			}
		},
		'before_el' => function($control) {
			if ($control->config->data->is_default) {
				$control->config->skip_el = true;
				$control->config->notify[] = array(
					'title' => 'Язык по-умолчанию нельзя удалить',
					'style' => 'warning'
				);
			}
		}
	)
));