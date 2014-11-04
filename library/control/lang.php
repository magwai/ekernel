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
			'sortable' => true,
			'order' => 1,
			'required' => true
		),
		'stitle' => array(
			'title' => $this->view->translate('control_lang_stitle_title'),
			'description' => $this->view->translate('control_lang_stitle_description'),
			'validator' => array(
				'regex' => '/^[a-z0-9\-\_]+$/si'
			),
			'order' => 2,
			'required' => true
		),
		'is_default' => array(
			'title' => $this->view->translate('control_lang_is_default_title'),
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
			'title' => $this->view->translate('control_lang_show_it_title'),
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
				$control->config->form->element->show_it->error_user[] = $control->view->translate('control_lang_stitle_is_default_active');
			}
		},
		'before_el' => function($control) {
			if ($control->config->data->is_default) {
				$control->config->skip_el = true;
				$control->config->notify[] = array(
					'title' => $control->view->translate('control_lang_stitle_is_default_delete'),
					'style' => 'warning'
				);
			}
		}
	)
));