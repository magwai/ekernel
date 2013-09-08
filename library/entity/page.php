<?php

class k_entity_page extends entity {
	function get_url_valid_control() {
		return $this->show_it ? '<a href="/page/'.$this->stitle.'" target="_blank">/page/'.$this->stitle.'</a>' : '';
	}

	function get_description_valid() {
		$p = preg_split('/\<div\ style\=\"page\-break\-after\:\ always\;\"\>.*?\<\/div\>/si', $this->message);
		return count($p) == 2 ? $p[0] : $this->message;
	}

	function get_message_valid() {
		$p = preg_split('/\<div\ style\=\"page\-break\-after\:\ always\;\"\>.*?\<\/div\>/si', $this->message);
		return count($p) == 2 ? $p[1] : $this->message;
	}
}