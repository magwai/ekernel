<?php

application::get_instance()->controller->layout = null;

$m = new model_cnotify;

echo $this->view->json($m->fetch_control_no_read());