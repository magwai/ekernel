<?php

class k_view_helper_page extends view_helper {
	public function page($stitle, $field = 'message_valid') {
		$m = new model_page;
		$card = $m->fetch_row(array(
			'stitle' => $stitle
		));
		return (string)$card->$field;
	}
}