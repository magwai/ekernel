<?php

class k_view_helper_page extends view_helper {
	public function page($stitle, $field = 'message') {
		$m = new model_page;
		return (string)$m->fetch_one($field, array(
			'stitle' => $stitle
		));
	}
}