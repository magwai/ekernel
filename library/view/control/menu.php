<?php

if (count($this->data)) {
	$script = $this->script;
	$level = (int)$this->level;
	echo $level ? '<ul class="dropdown-menu">' : '<li class="dropdown c-menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span> Меню</a><ul class="dropdown-menu">';

	foreach ($this->data as $el) {
		$sub = trim($this->xlist(array(
			'fetch' => array(
				'data' => $el->pages
			),
			'view' => array(
				'script' => $script,
				'param' => array(
					'level' => $level + 1,
					'script' => $script
				)
			)
		)));
		$active = $el->is_active(true);
		$class = array();
		if ($active) $class[] = 'active';
		if ($sub) $class[] = 'dropdown-submenu';

?>
	<li<?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?>>
		<a<?php echo $active ? ' id="current"' : '' ?> href="<?php echo $sub ? 'javascript:;' : $el->href ?>"<?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?>><?php echo $level ? $el->title : $el->title ?></a>
		<?php echo $sub ?>
	</li>
<?php

	}
	echo $level ? '</ul>' : '</ul></li>';
}