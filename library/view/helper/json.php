<?php

class k_view_helper_json extends view_helper  {
	public function json($data) {
		application::get_instance()->response->header_add('Content-Type', 'application/json');
		return json_encode($data);
	}
}