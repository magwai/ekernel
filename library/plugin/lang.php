<?php

class k_plugin_lang extends plugin {
	public function controller_before() {
		$ml = new model_lang;
		$ids = $ml->fetch_col('stitle', array(
			'show_it' => 1
		));
		if ($ids && !in_array(application::get_instance()->controller->request->param->lang, $ids)) unset(application::get_instance()->controller->request->param->lang);
		if (!application::get_instance()->controller->request->param->lang) {
			application::get_instance()->controller->request->param->lang = application::get_instance()->controller->view->lang()->default->stitle;
		}
		if (application::get_instance()->request->url == '/control') {
			header('Location: '.'/'.application::get_instance()->controller->view->lang()->default->stitle.'/control');
			exit();
		}
		else if (application::get_instance()->request->url == '/error') {
			header('Location: '.'/'.application::get_instance()->controller->view->lang('stitle').'/error');
			exit();
		}
	}
}