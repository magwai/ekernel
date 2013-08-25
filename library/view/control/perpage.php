<?php

if (count($this->data)) {
	$p = clone $this->control()->config->param;
	unset($p['page']);

?>
<ul class="pagination c-perpager">
<?php

	foreach ($this->data as $el) {
		$p['perpage'] = $el;

?>
	<li<?php echo $this->control()->config->param->perpage == $el ? ' class="active"' : '' ?>><a href="<?php echo $this->url($p, 'control') ?>"><?php echo $el == 999 ? $this->translate('control_perpager_all') : $el ?></a></li>
<?php
	
	}

?>
</ul>
<?php

}