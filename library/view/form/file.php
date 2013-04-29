<input type="file" name="<?php echo $this->escape($this->name) ?>"<?php echo $this->id ? ' id="'.$this->escape($this->id).'"' : '' ?> />
<?php

if ($this->value) {

?>
<div class="e-form-element-file">
	<input type="hidden" name="<?php echo $this->escape($this->name) ?>" value="<?php echo $this->escape($this->value) ?>" />
	<?php echo $this->required ? '' : '<label'.($this->class_delete ? ' class="'.$this->class_delete.'"' : '').' for="'.$this->escape($this->name).'_delete"><input type="checkbox" value="1" name="'.$this->escape($this->name).'_delete" id="'.$this->escape($this->name).'_delete" />'.$this->translate('form_element_file_delete').'</label>' ?>
	<div class="e-form-element-file-value"><a href="<?php echo $this->url.'/'.$this->value ?>" target="_blank"><?php echo $this->value ?></a></div>
</div>
<?php

}