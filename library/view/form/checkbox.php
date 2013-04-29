<div class="e-form-checkbox-frame">
<?php

if (count($this->item)) {
	$n = 1;
	foreach ($this->item as $k => $v) {

?>
	<div class="e-form-checkbox-el">
		<input<?php echo ($this->multiple ? ($this->value ? in_array($k, $this->value->to_array()) : false) : $k == $this->value) ? ' checked="checked"' : '' ?> id="e-form-checkbox-label-<?php echo $this->escape($this->name) ?>-<?php echo $n ?>"<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?> type="<?php echo $this->type ?>" name="<?php echo $this->escape($this->name).($this->multiple ? '[]' : '') ?>" value="<?php echo $this->escape($k) ?>" /><label for="e-form-checkbox-label-<?php echo $this->escape($this->name) ?>-<?php echo $n ?>"><?php echo $v ?></label>
	</div>
<?php

		$n++;
	}
}
else {

?>
	<div class="e-form-checkbox-el">
		<input<?php echo $this->value ? ' checked="checked"' : '' ?> id="e-form-checkbox-label-<?php echo $this->escape($this->name) ?>"<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?> type="<?php echo $this->type ?>" name="<?php echo $this->escape($this->name).($this->multiple ? '[]' : '') ?>" value="1" />
	</div>
<?php

}

?>
</div>