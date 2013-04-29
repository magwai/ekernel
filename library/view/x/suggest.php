<?php

application::get_instance()->controller->layout = null;

$data = array();

if (@$this->post['term']) {
	$model = 'model_'.@$this->post['model'];
	if (class_exists($model)) {
		$m = new $model;
		$method = 'fetch_suggest_'.@$this->post['method'];
		if (method_exists($m, $method)) {
			$data = $m->$method($this->post['term']);
		}
	}
}

echo $this->json($data);