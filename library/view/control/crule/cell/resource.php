<?php

$m = new model_crule2cresource;
$ret = $m->fetch_resource_title_by_rule($this->data->id);
echo $ret ? implode(', ', $ret) : $this->translate('control_cell_all');