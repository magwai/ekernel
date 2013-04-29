<?php

$m = new model_crole2crole;
$ret = $m->fetch_role_title_by_role($this->data->id);
echo $ret ? implode(', ', $ret) : 'Нет';