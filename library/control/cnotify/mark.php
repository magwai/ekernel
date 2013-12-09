<?php

application::get_instance()->controller->layout = null;

$m = new model_cnotify;
$m->set_control_read($this->control()->config->post->id);

echo $this->view->json(array());