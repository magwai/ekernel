<select<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?><?php echo $this->multiple ? ' multiple="multiple"' : '' ?> name="<?php echo $this->escape($this->name).($this->multiple ? '[]' : '') ?>">
<?php

if ($this->item) {
	foreach ($this->item as $k => $v) {

?>
	<option value="<?php echo $this->escape($k) ?>"<?php echo ($this->multiple ? ($this->value ? in_array($k, $this->value->to_array()) : false) : $k == $this->value) ? ' selected="selected"' : '' ?>><?php echo $v ?></option>
<?php

	}
}

?>
</select>