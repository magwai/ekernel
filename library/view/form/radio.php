<div class="e-form-radio-frame">
<?php

if (count($this->item)) {
	$n = 1;
	foreach ($this->item as $k => $v) {

?>
	<div class="e-form-radio-el">
		<input<?php echo $k == $this->value ? ' checked="checked"' : '' ?> id="e-form-radio-label-<?php echo $this->escape($this->name) ?>-<?php echo $n ?>"<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?> type="<?php echo $this->type ?>" name="<?php echo $this->escape($this->name) ?>" value="<?php echo $this->escape($k) ?>" /><label for="e-form-radio-label-<?php echo $this->escape($this->name) ?>-<?php echo $n ?>"><?php echo $v ?></label>
	</div>
<?php

		$n++;
	}
}

?>
</div>