<?php

if (count($this->data) > 1) {
	$p = clone $this->control()->config->param;

?>
<ul class="pagination c-pager">
<?php

	$p['page'] = 1;

?>
	<li<?php echo $this->control()->config->param->page > 1 ? '' : ' class="disabled"' ?>><a href="<?php echo $this->url($p, 'control') ?>"><?php echo $this->translate('control_pager_first') ?></a></li>
<?php

	if ($this->control()->config->param->page - 1 < 2) $p['page'] = 1;
	else $p['page'] = $this->control()->config->param->page - 1;

?>
	<li<?php echo $this->control()->config->param->page > 1 ? '' : ' class="disabled"' ?>><a href="<?php echo $this->url($p, 'control') ?>"><?php echo $this->translate('control_pager_prev') ?></a></li>
<?php

	foreach ($this->data as $el) {
		if ($el == 1) unset($p['page']);
		else $p['page'] = $el;
?>
	<li<?php echo $el == $this->control()->config->param->page ? ' class="active"' : '' ?>><a<?php echo is_numeric($el) ? ' href="'.$this->url($p, 'control').'"' : '' ?>><?php echo $el ?></a></li>
<?php

	}
	$p['page'] = $this->control()->config->param->page + 1 > $this->pages
		? $this->pages
		: $this->control()->config->param->page + 1;

?>
	<li<?php echo $this->control()->config->param->page >= $this->pages ? ' class="disabled"' : '' ?>><a href="<?php echo $this->url($p, 'control') ?>"><?php echo $this->translate('control_pager_next') ?></a></li>
<?php

	$p['page'] = $this->pages;

?>
	<li<?php echo $this->control()->config->param->page >= $this->pages ? ' class="disabled"' : '' ?>><a href="<?php echo $this->url($p, 'control') ?>"><?php echo $this->translate('control_pager_last') ?></a></li>
</ul>
<?php

}