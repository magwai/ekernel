<div class="e-form-clink-frame">
	<iframe src="<?php echo $this->url(array(
		'controller' => $this->controller,
		'action' => $this->action,
		'cid' => $this->cid,
		'clink' => $this->name
	), 'control') ?>" frameborder="0" class="form-control c-iframe"><?php echo $this->value ?></iframe>
	<input type="hidden"<?php echo $this->name ? ' name="'.$this->escape($this->name).'"' : '' ?> value="" />
</div>
