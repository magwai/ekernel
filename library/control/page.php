<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$mp = new model_page;
$is_visible = $mp->fetch_one('show_it', array('id' => $this->config->param->id));
 
$this->control(array(
	'meta' => true,
	'static_field' => true,
	'field' => array(
		'title' => array(
			'sortable' => true,
			'order' => 1,
			'required' => true
		),
		'stitle' => array(
			'active' => $this->view->user()->is_allowed_by_key('admin') ? true : ($is_visible ? true : false),
			'title' => $this->view->translate('control_page_stitle_title'),
			'order' => 2,
			'required' => true
		),
		'message' => array(
			'type' => 'textarea',
			'title' => $this->view->translate('control_page_message_title'),
			'order' => 6,
			'ckeditor' => true
		),
		'show_it' => array(
			'active' => $this->view->user()->is_allowed_by_key('admin'),
			'title' => $this->view->translate('control_page_show_it_title'),
			'order' => 7,
			'align' => 'center',
			'width' => 10,
			'value' => 1,
			'script' => 'control/cell/yesno',
			'type' => 'checkbox'
		)
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'message' => array(
					'active' => false
				),
				'stitle' => array(
					'active' => false
				),
				'url_valid' => array(
					'order' => 2
				)
			)
		),
		'delete' => array(
			'callback' => array(
				'before_el' => function(&$control) {
					if (!$control->config->data->show_it && !$control->view->user()->is_allowed_by_key('admin')) {
						$control->config->skip_el = true;
					}
				}
			)
		)
	)
));