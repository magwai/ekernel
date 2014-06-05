<form<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?><?php echo $this->autocomplete ? ' autocomplete="'.$this->autocomplete.'"' : '' ?> action="<?php echo $this->action ?>" method="<?php echo $this->method ?>" enctype="<?php echo $this->enctype ?>">
	<div class="e-form<?php echo $this->class_wrap ? ' '.$this->class_wrap : '' ?>">
<?php

if ($this->element) {
	$element = $this->element;
	foreach ($element as $v) {
		$error = $this->xlist(array(
			'fetch' => array(
				'data' => $v->get_error()
			),
			'view' => array(
				'script' => $this->error_view_script
			)
		));
		echo $v->frame_view_script ? $this->render($v->frame_view_script, array(
			'item' => $v,
			'error' => $error
		)) : (string)$v.$error;
	}
}

if ($this->group) {
	$group = $this->group;
	unset($this->element);
	unset($this->group);
	foreach ($group as $v) {
		echo (string)$v;
	}
}

?>
	</div>
</form>