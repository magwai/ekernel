<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

$this->control(array(
	'tree' => true,
	'field' => array(
		'key' => array(
			'title' => $this->view->translate('control_cresource_key_title'),
			'order' => 2
		)
	)
));