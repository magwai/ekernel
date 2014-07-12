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

echo $this->view->json($m->fetch_control_no_read());