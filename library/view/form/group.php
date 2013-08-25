<fieldset<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?>>
	<?php echo $this->legend ? '<legend>'.$this->legend.'</legend>' : '' ?>
<?php

if ($this->element) {
	$element = $this->element;
	foreach ($element as $v) {
		echo $v->frame_view_script ? $this->render($v->frame_view_script, array(
			'item' => $v
		)) : (string)$v;
	}
}

?>
</fieldset>