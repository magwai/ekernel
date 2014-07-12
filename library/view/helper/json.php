<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_json extends view_helper  {
	public function json($data) {
		application::get_instance()->response->header_add('Content-Type', 'application/json');
		return json_encode($data);
	}
}