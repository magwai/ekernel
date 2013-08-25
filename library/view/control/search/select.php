<?php

if ($this->item && count($this->item)) {

?>
<select data-field="<?php echo $this->escape($this->name) ?>" class="col-12" onchange="c.filter_change(this, event);">
<?php

	foreach ($this->item as $k => $v) {

?>
	<option<?php echo $this->control()->config->param->{'search_'.$this->name} == $k ? ' selected="selected"' : '' ?> value="<?php echo $k ?>"><?php echo $v ?></option>
<?php

	}

?>
</select>
<?php

}
else echo '&nbsp;';
