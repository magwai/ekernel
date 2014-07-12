<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

application::get_instance()->controller->layout = null;

$m = new model_cnotify;
$m->set_control_read($this->control()->config->post->id);

echo $this->view->json(array());