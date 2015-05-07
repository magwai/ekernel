<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$before = function($control) {
	if (count($control->config->data)) {
		$d = array();
		foreach ($control->config->data as $k => $v) {
			if ($k != 'url') $d[$k] = $v;
		}
		$control->config->data->data = json_encode($d);
	}
};

if ($this->config->action == 'edit' && $this->config->param->id)
{
	$mm = new model_meta;
	$res = $mm->fetch_row(array('id' => $this->config->param->id));
	if ($res->controller){
		$model = 'model_'.$res->controller;
		$m = new $model;
		$mc = new model_cmenu;
		$mname = $mc->fetch_one('title', array('controller' => $res->controller));
		$sname = $m->fetch_one('title', array('id' => $res->parentid));
		$text = '<h2>'.$mname.': '.$sname.'</h2>';
	}
	else{
		$text = '<h2>'.$res->url.'</h2>';
	}
	
}

$c = array(
	'text' => $this->config->action == 'edit' ? $text : '',
	'field' => array(
		'url' => array(
			'search' => true,
			'sortable' => true,
			'title' => $this->view->translate('control_meta_url_title'),
			'description' => $this->view->translate('control_meta_url_description'),
			'order' => 1,
			'required' => true,
			'active' => $this->config->action == 'add' ? true : false
		),
		'title' => array(
			'title' => 'TITLE',
			'description' => $this->view->translate('control_meta_title_description'),
			'order' => 2
		),
		'keywords' => array(
			'title' => 'META: KEYWORDS',
			'description' => $this->view->translate('control_meta_keywords_description'),
			'order' => 3
		),
		'description' => array(
			'title' => 'META: DESCRIPTION',
			'description' => $this->view->translate('control_meta_description_description'),
			'order' => 4
		),
		'data' => array(
			'active' => false
		),
		'controller' => array(
			'active' => false
		)
	),
	'config_action' => array(
		'index' => array(
			'field' => array(
				'url' => array(
				    'active' => true
				),
				'title' => array(
					'active' => false
				),
				'keywords' => array(
					'active' => false
				),
				'description' => array(
					'active' => false
				)
			)
		),
		'add' => array(
			'callback' => array(
				'before' => $before
			)
		),
		'edit' => array(
			'callback' => array(
				'before' => $before,
				'preset' => function($control) {
					$d = @json_decode($control->config->data->data);
					if ($d && count($d)) foreach ($d as $k => $v) $control->config->data->$k = $v;
				}
			)
		)
	)
);

if (@$this->return_only) return $c;
else $this->control($c);