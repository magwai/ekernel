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
				'url' => $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control'),
				'url_current' => str_replace('/replace/replace', '', $this->url($p, 'control'))
			)).') });');

$this->css	->prepend('/library/ctl/bootstrap/bootstrap.css')
			->prepend('/library/ctl/bootstrap/bootstrap-glyphicons.css')
			->set(1000, '/library/ctl/control/main.css');


if (stripos($this->control()->config->content_bottom, 'navbar-fixed-bottom') !== false) {
	$this->css->append('/library/ctl/control/bottom.css');
}

$this->meta	->set('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');

$this->title('CP');

//$bread = (string)$this->navigation()->bread();

$active = $this->navigation()->find_active();

$p['controller'] = $active ? $active->controller : '';
$p['action'] = $active ? $active->action : '';
unset($p['replace']);
unset($p['perpage']);
unset($p['orderby']);
unset($p['orderdir']);
unset($p['oid']);

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
			($active ? '<li class="c-place"><a href="'.$this->url($p, 'control').'">'.$active->title.'</a></li>' : '').
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
		<link href="/library/ctl/control/favicon.ico" rel="icon" type="image/x-icon" />
	</head>
	<body>
		<?php echo $top_left || $top_right ? '' : '' ?>
		<div class="navbar navbar-inverse navbar-fixed-top c-navbar">
			<div class="row-fluid">
				<div class="col-12 col-lg-9 col-md-9 col-sm-9"><ul class="nav navbar-nav">
					<li class="navbar-brand"><a href="<?php echo $this->url(array('controller' => 'cindex'), 'control') ?>" class="glyphicon glyphicon-home"></a></li>
					<li class="c-inner-menu c-invisible" id="d_inner_menu"></li>
					<li class="c-button-top" id="d_button_top"><?php echo $this->xlist(array(
						'fetch' => array(
							'data' => $this->control()->config->button_top
						),
						'view' => array(
							'script' => 'control/button'
						)
					)) ?></li>
				</ul></div>
				<?php echo $this->user('login') ? '<div class="col-12 col-lg-3 col-md-3 col-sm-3"><ul class="nav navbar-nav pull-right c-auth" id="d_auth">
					<li class="h navbar-brand">'.$this->user('login').'</li>
					<li><a href="'.$this->url(array('ccontroller' => 'cuser', 'caction' => 'logout'), 'control').'">'.$this->translate('control_logout').'</a></li>
				</ul></div>' : '' ?>
			</div>
		</div>
		<div class="container-fluid c-middle clearfix">
			<div class="sidebar pull-left c-menu" id="d_menu"><?php echo $menu ?></div>
			<div class="row-fluid"><div class="col-12" id="d_content"><?php echo $this->control()->config->content ?></div></div>
		</div>
		<?php echo $this->control()->config->content_bottom ?>
		<script type="text/javascript">window.CKEDITOR_BASEPATH = '/library/ctl/ckeditor/';</script>
		<?php echo (string)$this->js() ?>
	</body>
</html>