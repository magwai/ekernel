<?php

if (count($this->data)) {
	$level = (int)$this->level;
	$class_ul = $this->class_ul ? $this->class_ul->to_array() : array('navigation');

?>
<ul<?php echo $level ? '' : ' class="'.implode(' ', $class_ul).'"' ?>>
<?php

	foreach ($this->data as $el) {
		$class = $this->class_li ? $this->class_li->to_array() : array();
		if ($el->is_active(true)) $class[] = 'active';

?>
	<li<?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?>>
		<a href="<?php echo $el->href ?>"><?php echo $el->title ?></a>
		<?php echo $this->xlist(array(
			'fetch' => array(
				'data' => $el->pages
			),
			'view' => array(
				'script' => $this->script,
				'param' => array(
					'level' => $level + 1,
					'script' => $this->script,
					'class_li' => $this->class_li
				)
			)
		)) ?>
	</li>
<?php

	}

?>
</ul>
<?php

}