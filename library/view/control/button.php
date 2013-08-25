<?php

if (count($this->data)) {
	
?>
<div class="navbar-btn btn-group c-buttons">
<?php

	foreach ($this->data as $el) {
		$p = clone $this->control()->config->param;
		if ($el->param) $p = array_merge($p, $el->param);
		$p['controller'] = $el->controller;
		$p['action'] = $el->action;
		if ($el->key == 'cid') {
			unset($p['cid']);
			$p['replace'] = 'replace';
		}
		$class = array('btn', 'btn-small', $el->class, 'c-button');
		if ($el->action == $this->control()->config->action) $class[] = 'active';
		if ($el->confirm) $class[] = 'c-confirm';
		if ($el->default) $class[] = 'c-default';

?>
<a<?php echo $el->pid ? ' data-pid="'.$el->pid.'"' : '' ?><?php echo $el->key ? ' data-key="'.$el->key.'"' : '' ?><?php echo $class ? ' class="'.implode(' ', $class).'"' : '' ?><?php echo $el->confirm ? ' onclick="return false"' : '' ?> href="<?php echo str_replace('/replace/replace', '', $this->url($p, 'control')) ?>" /><?php echo $el->title ?></a>
<?php

	}
?>
</div>
<?php

}