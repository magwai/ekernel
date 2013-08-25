<?php

if (count($this->data)) {
	$script = $this->script;
	$level = (int)$this->level;
	$res = '';

	foreach ($this->data as $el) {
		if ($el->is_inner) continue;
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
		$res .=
'	<li'.($class ? ' class="'.implode(' ', $class).'"' : '').'>
		<a'.($active ? ' id="current"' : '').' href="'.($sub ? 'javascript:;' : $el->href).'"'.($class ? ' class="'.implode(' ', $class).'"' : '').'>'.($level ? $el->title : $el->title).'</a>
		'.$sub.'
	</li>';

	}
	if ($res) echo	($level ? '<ul>' : '<ul class="nav nav-pills nav-stacked">').
					$res.
					($level ? '</ul>' : '</ul>');
}