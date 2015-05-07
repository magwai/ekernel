<?php

class k_entity_meta extends entity{
	function get_url_control()
	{
		if ($this->controller){
			$model = 'model_'.$this->controller;
			$m = new $model;
			$mc = new model_cmenu;
			$mname = $mc->fetch_one('title', array('controller' => $this->controller));
			$sname = $m->fetch_one('title', array('id' => $this->parentid));
			return $mname.': '.$sname;
		}
		return $this->url;
	}
}
