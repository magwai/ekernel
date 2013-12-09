<?php

if ($this->item && count($this->item)) {
	if (!$this->level) {

?>
<select data-field="<?php echo $this->escape($this->name) ?>" class="col-md-12" onchange="c.filter_change(this, event);">
<?php
	}
	foreach ($this->item as $k => $v) {
		if ($v instanceof data) {
			$inner = trim($this->render('control/search/select', array(
				'item' => $v,
				'level' => 1
			)));
			if ($inner) echo '<optgroup label="'.$this->escape($k).'">'.$inner.'</optgroup>';
		}
		else {

?>
	<option value="<?php echo $this->escape($k) ?>"<?php echo $this->control()->config->param->{'search_'.$this->name} == $k ? ' selected="selected"' : '' ?>><?php echo $v ?></option>
<?php

		}
	}
	if (!$this->level) {

?>
</select>
<?php

	}
}
else echo '&nbsp;';
