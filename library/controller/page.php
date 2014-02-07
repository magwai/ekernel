<?php

class k_controller_page extends controller {
	function index_action() {
		$this->view->id = (string)$this->request->param->id;
	}

	function get_routes() {
		$m = new model_page;
		$list = $m->fetch_all(array(
			'show_it' => 1
		), 'title');
		$ret = array();
		if ($list) {
			foreach ($list as $el) {
				$ret[] = new entity_page(array(
					'title' => $el->title_valid,
					'route' => 'page_card',
					'param' => array(
						'id' => $el->stitle
					)
				));
			}
		}
		return $ret;
	}
}