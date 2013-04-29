<?php

if (count($this->data)) {
	$level = (int)$this->level;

?>
<ul<?php echo $level ? '' : ' class="navigation"' ?>>
<?php

	foreach ($this->data as $el) {

?>
	<li<?php echo $el->is_active(true) ? ' class="active"' : '' ?>>
		<a href="<?php echo $el->href ?>"><?php echo $el->title ?></a>
		<?php echo $this->xlist(array(
			'fetch' => array(
				'data' => $el->pages
			),
			'view' => array(
				'script' => $this->script,
				'param' => array(
					'level' => $level + 1,
					'script' => $this->script
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