<?php

class k_entity_page extends entity {
	function get_url_valid_control() {
		return $this->show_it ? '<a href="/page/'.$this->stitle.'" target="_blank">/page/'.$this->stitle.'</a>' : '';
	}
}