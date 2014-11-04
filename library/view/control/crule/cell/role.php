<?php

$m = new model_crule2crole;
$ret = $m->fetch_role_title_by_rule($this->data->id);
echo $ret ? implode(', ', $ret) : $this->translate('control_cell_all');