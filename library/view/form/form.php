<form<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?><?php echo $this->autocomplete ? ' autocomplete="'.$this->autocomplete.'"' : '' ?> action="<?php echo $this->action ?>" method="<?php echo $this->method ?>" enctype="<?php echo $this->enctype ?>"<?php

if ($this->attr) foreach ($this->attr as $k => $v) {
	echo ' '.$this->escape($k).'="'.$this->escape($v).'"';
}

?>>
	<div class="e-form<?php echo $this->class_wrap ? ' '.$this->class_wrap : '' ?>">
<?php

$group = $this->group;
unset($this->group);
if ($this->element) {
	$element = $this->element;
	foreach ($element as $k => $v) {
		if (!$v) {
			$kk = str_replace('group_', '', $k);
			if (isset($group[$kk])) {
				echo (string)$group[$kk];
				unset($group[$kk]);
			}
			continue;
		}
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

if ($group) {
	unset($this->element);
	foreach ($group as $v) {
		echo (string)$v;
	}
}

?>
	</div>
</form>