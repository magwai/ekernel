<?php

application::get_instance()->controller->layout = null;

$data = array();
if (@$this->action == 'list') {
	if (@$this->term) {
		$model = 'model_'.@$this->model;
		if (class_exists($model)) {
			$m = new $model;
			$method = 'fetch_autocomplete_'.@$this->method;
			if (method_exists($m, $method)) {
				$data = $m->$method($this->term, $this->param);
			}
		}
	}
}
else if ($this->action == 'card') {
	if (@$this->value) {
		$model = 'model_'.@$this->model;
		if (class_exists($model)) {
			$m = new $model;
			$method = 'fetch_autocomplete_card_'.@$this->method;
			if (method_exists($m, $method)) {
				$data = $m->$method($this->value, $this->param);
			}
		}
	}
}

echo $this->json($data);