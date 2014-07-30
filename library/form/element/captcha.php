<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_form_element_captcha extends form_element_text {
	public $view_script = 'form/captcha';
	public $captcha_type = 'image';

	public function __construct($name, $param = array()) {
		parent::__construct($name, $param);
		if (!file_exists(PATH_ROOT.'/'.DIR_CACHE.'/captcha')) {
			mkdir(PATH_ROOT.'/'.DIR_CACHE.'/captcha');
			chmod(PATH_ROOT.'/'.DIR_CACHE.'/captcha', 0777);
		}
		$opt = new data(array(
			'font' => PATH_ROOT.'/'.DIR_LIBRARY.'/font/arial.ttf',
			'imgDir' => PATH_ROOT.'/'.DIR_CACHE.'/captcha',
			'imgUrl' => '/'.DIR_CACHE.'/captcha',
			'wordlen' => 4,
			'dotNoiseLevel' => 0,
			'lineNoiseLevel' => 0,
			'fsize' => 20,
			'width' => 100,
			'height' => 60
		));
		if (isset($param['captcha'])) $opt->set($param['captcha']);
		$this->captcha = new Zend\Captcha\Image($opt->to_array());
		$this->validator['captcha'] = array(
			'captcha' => $this->captcha
		);
	}

	public function validate($value) {
		if (@$_POST[$this->name.'_id']) {
			$this->validator['captcha']['id'] = $_POST[$this->name.'_id'];
		}
		return parent::validate($value);
	}

	public function get($for_render = false) {
		return $for_render ? '' : $this->value;
	}
}