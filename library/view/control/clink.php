<?php

if (stripos($this->control()->config->content, 'c-fancy') !== false) {
	$this->js->prepend('/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.js');
	$this->css->prepend('/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.css');
}

$p = clone $this->control()->config->param;
unset($p['page']);
$p['replace'] = 'replace';

$this->js	->prepend('/'.DIR_KERNEL.'/ctl/noty/themes/default.js')
			->prepend('/'.DIR_KERNEL.'/ctl/noty/layouts/top.js')
			->prepend('/'.DIR_KERNEL.'/ctl/noty/jquery.noty.js')
			->prepend('/'.DIR_KERNEL.'/ctl/bootstrap/js/bootstrap.js')
			->prepend('/'.DIR_KERNEL.'/js/respond.js')
			->prepend('/'.DIR_KERNEL.'/js/jquery/jquery-migrate.js')
			->prepend('/'.DIR_KERNEL.'/js/jquery/jquery.js')
			->set(1000, '/'.DIR_KERNEL.'/ctl/control/main.js')->set_inline(1000, '$(function() { c.init('.json_encode(array(
				'clink' => $p->clink,
				'url' => $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control'),
				'url_current' => str_replace('/replace/replace', '', $this->url($p, 'control'))
			)).') });');

$this->css	->prepend('/'.DIR_KERNEL.'/ctl/bootstrap/css/bootstrap-theme.css')
			->prepend('/'.DIR_KERNEL.'/ctl/bootstrap/css/bootstrap.css')
			->set(1000, '/'.DIR_KERNEL.'/ctl/control/main.css')
			->set(1010, '/'.DIR_KERNEL.'/ctl/control/clink.css');

$button_top = count($this->control()->config->button_top) ? '<li class="c-button-top">'.$this->xlist(array(
	'fetch' => array(
		'data' => $this->control()->config->button_top
	),
	'view' => array(
		'script' => 'control/button'
	)
)).'</li>' : '';

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<?php echo (string)$this->meta() ?>
		<?php echo (string)$this->css() ?>
	</head>
	<body>
		<div class="clink-frame">
			<?php echo ($button_top ? '<ul class="nav">'.$button_top.'</ul>' : '').$this->control()->config->content ?>
		</div>
		<script type="text/javascript">window.CKEDITOR_BASEPATH = '/<?php echo DIR_KERNEL ?>/ctl/ckeditor/';</script>
		<?php echo (string)$this->js() ?>
	</body>
</html>