<?php

if (stripos($this->control()->config->content, 'c-fancy') !== false) {
	$this->js->prepend('/library/ctl/fancybox2/jquery.fancybox.js');
	$this->css->prepend('/library/ctl/fancybox2/jquery.fancybox.css');
}

$p = clone $this->control()->config->param;
unset($p['page']);
$p['replace'] = 'replace';

$this->js	->prepend('/library/ctl/noty/themes/default.js')
			->prepend('/library/ctl/noty/layouts/top.js')
			->prepend('/library/ctl/noty/jquery.noty.js')
			->prepend('/library/ctl/bootstrap/bootstrap.js')
			->prepend('/library/js/respond.js')
			->prepend('/library/js/jquery/jquery-migrate.js')
			->prepend('/library/js/jquery/jquery.js')
			->set(1000, '/library/ctl/control/main.js')->set_inline(1000, '$(function() { c.init('.json_encode(array(
				'clink' => $p->clink,
				'url' => $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control'),
				'url_current' => str_replace('/replace/replace', '', $this->url($p, 'control'))
			)).') });');

$this->css	->prepend('/library/ctl/bootstrap/css/bootstrap-theme.css')
			->prepend('/library/ctl/bootstrap/css/bootstrap.css')
			->set(1000, '/library/ctl/control/main.css')
			->set(1010, '/library/ctl/control/clink.css');

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
		<script type="text/javascript">window.CKEDITOR_BASEPATH = '/library/ctl/ckeditor/';</script>
		<?php echo (string)$this->js() ?>
	</body>
</html>