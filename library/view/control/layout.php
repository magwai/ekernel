<?php

if (stripos($this->control()->config->content, 'c-fancy') !== false) {
	$this->js->prepend('/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.js');
	$this->css->prepend('/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.css');
}

$p = clone $this->control()->config->param;
unset($p['page']);
$p['replace'] = 'replace';

$this->js	->prepend('/'.DIR_KERNEL.'/ctl/control/noty/layouts/top.js')
			->prepend('/'.DIR_KERNEL.'/ctl/control/noty/themes/default.js')
			->prepend('/'.DIR_KERNEL.'/ctl/noty/jquery.noty.js')
			->prepend('/'.DIR_KERNEL.'/ctl/bootstrap/js/bootstrap.js')
			->prepend('/'.DIR_KERNEL.'/js/respond.js')
			->prepend('/'.DIR_KERNEL.'/js/jquery/jquery-migrate.js')
			->prepend('/'.DIR_KERNEL.'/js/jquery/jquery.js')
			->set(1000, '/'.DIR_KERNEL.'/ctl/control/main.js')->set_inline(1000, '$(function() { c.init('.json_encode(array(
				'url' => $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control'),
				'url_current' => str_replace('/replace/replace', '', $this->url($p, 'control'))
			)).') });');

$this->css	->prepend('/'.DIR_KERNEL.'/ctl/bootstrap/css/bootstrap-theme.css')
			->prepend('/'.DIR_KERNEL.'/ctl/bootstrap/css/bootstrap.css')
			->set(1000, '/'.DIR_KERNEL.'/ctl/control/main.css');

if (stripos($this->control()->config->content_bottom, 'navbar-fixed-bottom') !== false) {
	$this->css->append('/'.DIR_KERNEL.'/ctl/control/bottom.css');
}

$this->meta	->set('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');

$this->title('CP');

$place_title = $this->translate('control_place_'.$this->control()->config->place);
if (!$place_title) $place_title = $this->control()->config->place;
$finish = $this->control()->config->bread->to_array();
$finish[] = array(
	'title' => $place_title
);
$bread = (string)$this->navigation()->bread(array(
	'finish' => $finish
));

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

$reg = $this->lang(true);

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<?php echo (string)$this->meta() ?>
		<?php echo (string)$this->title() ?>
		<?php echo (string)$this->css() ?>
		<link href="/<?php echo DIR_KERNEL ?>/ctl/control/favicon.ico" rel="icon" type="image/x-icon" />
	</head>
	<body>
		<?php echo $top_left || $top_right ? '' : '' ?>
		<div class="navbar container-fluid navbar-inverse navbar-fixed-top c-navbar">
			<div class="col-sm-9"><ul class="nav navbar-nav">
				<li class="navbar-brand hidden-xs"><a href="<?php echo $this->url(array('controller' => 'cindex'), 'control') ?>" class="glyphicon glyphicon-home"></a></li>
				<li>
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#d_menu">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</li>
				<li class="c-button-top" id="d_button_top"><?php echo $this->xlist(array(
					'fetch' => array(
						'data' => $this->control()->config->button_top
					),
					'view' => array(
						'script' => 'control/button'
					)
				)) ?></li>
			</ul></div>
			<?php echo $this->user('login') ? '<div class="col-sm-3 hidden-xs"><ul class="nav navbar-nav c-auth" id="d_auth">'.($reg ? $this->xlist(array(
					'fetch' => array(
						'model' => 'lang',
						'method' => 'list_control'
					),
					'view' => array(
						'script' => 'control/lang'
					)
				)) : '').'<li class="h navbar-brand">'.$this->user('login').'</li>
				<li><a href="'.$this->url(array('ccontroller' => 'cuser', 'caction' => 'logout'), 'control').'">'.$this->translate('control_logout').'</a></li>
			</ul></div>' : '' ?>
		</div>
		<div class="c-middle">
			<div class="sidebar collapse c-menu" id="d_menu"><?php echo $menu ?></div>
			<div class="c-inner-menu c-invisible" id="d_inner_menu"></div>
			<?php echo $bread ?>
			<div id="d_content"><?php echo $this->control()->config->content ?></div>
		</div>
		<?php echo $this->control()->config->content_bottom ?>
		<script type="text/javascript">window.CKEDITOR_BASEPATH = '/<?php echo DIR_KERNEL ?>/ctl/ckeditor/';</script>
		<?php echo (string)$this->js() ?>
	</body>
</html>