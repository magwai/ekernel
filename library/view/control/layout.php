<?php

if (stripos($this->control()->config->content, 'c-fancy') !== false) {
	$this->js->prepend('/kernel/ctl/fancybox2/jquery.fancybox.js');
	$this->css->prepend('/kernel/ctl/fancybox2/jquery.fancybox.css');
}

$p = clone $this->control()->config->param;
unset($p['page']);
$p['replace'] = 'replace';

$this->js	->prepend('/kernel/ctl/noty/themes/default.js')
			->prepend('/kernel/ctl/noty/layouts/top.js')
			->prepend('/kernel/ctl/noty/jquery.noty.js')
			->prepend('/kernel/ctl/bootstrap/js/bootstrap.js')
			->prepend('/kernel/js/jquery/jquery-migrate.js')
			->prepend('/kernel/js/jquery/jquery.js')
			->set(1000, '/kernel/ctl/control/main.js')->set_inline(1000, '$(function() { c.init('.json_encode(array(
				'url' => $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control'),
				'url_current' => str_replace('/replace/replace', '', $this->url($p, 'control'))
			)).') });');

$this->css	->prepend('/kernel/ctl/bootstrap/css/bootstrap-responsive.css')
			->prepend('/kernel/ctl/bootstrap/css/bootstrap.css')
			->set(1000, '/kernel/ctl/control/main.css');


if (stripos($this->control()->config->content_bottom, 'navbar-fixed-bottom') !== false) {
	$this->css->append('/kernel/ctl/control/bottom.css');
}

$this->meta	->set('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');

$this->title('CP');

//$bread = (string)$this->navigation()->bread();

$active = $this->navigation()->find_active();

$menu = trim($this->navigation());

$button_top = count($this->control()->config->button_top) ? '<li class="c-button-top">'.$this->xlist(array(
	'fetch' => array(
		'data' => $this->control()->config->button_top
	),
	'view' => array(
		'script' => 'control/button'
	)
)).'</li>' : '';

$top_left =	$menu.
			($active ? '<li class="c-place"><a href="'.$active->href.'">'.$active->title.'</a></li>' : '').
			$button_top;

$top_right = $this->user('id')
	? '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">'.$this->user('login').' <span class="caret"></span></a><ul class="dropdown-menu pull-right"><li><a href="'.$this->url(array('ccontroller' => 'cuser', 'caction' => 'logout'), 'control').'">'.$this->translate('control_logout').'</a></li></ul></li>'
	: '';

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<?php echo (string)$this->meta() ?>
		<?php echo (string)$this->title() ?>
		<?php echo (string)$this->css() ?>
		<link href="/kernel/ctl/control/favicon.ico" rel="icon" type="image/x-icon" />
	</head>
	<body>
		<?php echo $top_left || $top_right ? '' : '' ?>
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<div class="nav-collapse collapse">
						<div class="row-fluid">
							<div class="span10"><ul class="nav"><?php echo $top_left ? $top_left : '<li class="brand">Панель управления</li>' ?></ul></div>
							<?php echo $top_right ? '<div class="span2"><ul class="nav pull-right c-auth">'.$top_right.'</ul></div>' : '' ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fluid c-middle"><?php echo $this->control()->config->content ?></div>
		<?php echo $this->control()->config->content_bottom ?>
		<?php echo (string)$this->js() ?>
	</body>
</html>