<?php

$model_notify = new model_cnotify;
$notify = $model_notify->fetch_control_no_read_count();

$this->messify	->prepend('js', '/'.DIR_KERNEL.'/ctl/noty/themes/default.js')
				->prepend('js', '/'.DIR_KERNEL.'/ctl/noty/layouts/top.js')
				->prepend('js', '/'.DIR_KERNEL.'/ctl/noty/jquery.noty.js')
				->prepend('js', '/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.js')
				->prepend('js', '/'.DIR_KERNEL.'/ctl/control/js/plugins/ui/jquery.collapsible.min.js')
				->prepend('js', '/'.DIR_KERNEL.'/ctl/uniform/jquery.uniform.js')
				->prepend('js', '/'.DIR_KERNEL.'/js/jquery/jquery-migrate.js')
				->prepend('js', '/'.DIR_KERNEL.'/js/jquery/jquery.js')
				->set('js', 1000, '/'.DIR_KERNEL.'/ctl/control/js/custom.js')
				->set_inline('js', 1200, '$(function() { c.init('.json_encode(array(
					'url' => $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control'),
					'url_current' => str_replace('/replace/replace', '', $this->url(array(
						'replace' => 'replace'
					), 'control'))
				)).') });')
				->prepend('css', '/'.DIR_KERNEL.'/ctl/fancybox2/jquery.fancybox.css')
				->prepend('css', '/'.DIR_KERNEL.'/ctl/control/css/ui_custom.css')
				->prepend('css', '/'.DIR_KERNEL.'/ctl/control/css/dataTable.css')
				->prepend('css', '/'.DIR_KERNEL.'/ctl/control/css/reset.css')
				->set('css', 1300, '/'.DIR_KERNEL.'/ctl/control/css/main.css')
				->append('css', 'http://fonts.googleapis.com/css?family=Cuprum');

$this->meta	->set('http-equiv', 'Content-Type', 'text/html; charset=utf-8')
			->set('name', 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');

$this->title('CP');

$bread = (string)$this->navigation()->bread();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php echo (string)$this->meta() ?>
		<?php echo (string)$this->title() ?>
		<?php echo $this->messify->render('css') ?>
		<link href="/<?php echo DIR_KERNEL ?>/ctl/control/favicon.ico" rel="icon" type="image/x-icon" />
	</head>
	<body>
		<!-- Top navigation bar -->
		<div id="topNav">
			<div class="fixed">
				<div class="wrapper">
					<div class="welcome"><a href="<?php echo $this->url(array('ccontroller' => 'cindex', 'caction' => 'index'), 'control') ?>"><span><?php echo $this->translate('control_name') ?></span></a></div>
					<div class="userNav">
						<ul>
							<?php echo $this->user('id') ? '<li><a href="'.$this->url(array('ccontroller' => 'cnotify', 'caction' => 'index'), 'control').'"><img src="/'.DIR_KERNEL.'/ctl/control/images/icons/topnav/messages.png" alt="" /><span>'.$this->translate('control_notify').'</span>'.($notify ? '<span class="numberTop">'.$notify.'</span>' : '').'</a></li>' : '' ?>
							<li><a href="<?php echo $this->url(array('controller' => 'index', 'action' => 'index')) ?>"><img src="/<?php echo DIR_KERNEL ?>/ctl/control/images/icons/topnav/mainWebsite.png" alt="" /><span><?php echo $this->translate('control_goindex') ?></span></a></li>
							<li><?php echo $this->user('id') ? '<a href="'.$this->url(array('ccontroller' => 'cuser', 'caction' => 'logout'), 'control').'"><img src="/'.DIR_KERNEL.'/ctl/control/images/icons/topnav/logout.png" alt="" /><span>'.$this->translate('control_logout').'</span></a>' : '<a href="'.$this->url(array('ccontroller' => 'cuser', 'caction' => 'login'), 'control').'"><img src="/'.DIR_KERNEL.'/ctl/control/images/icons/topnav/logout.png" alt="" /><span>'.$this->translate('control_login').'</span></a>' ?></li>
						</ul>
					</div>
					<div class="fix"></div>
				</div>
			</div>
		</div>
		<!-- Content wrapper -->
		<div class="wrapper">
			<div class="main">
				<?php echo (string)$this->navigation() ?>
				<!-- Content -->
				<div class="content">
					<?php echo $bread || $this->control()->config->place ? '<div class="title c-title"><h5>'.$bread.($bread && $this->control()->config->place ? ' / ' : '').$this->control()->config->place.'</h5></div>' : '' ?>
					<?php echo count($this->control()->config->button_top) ? '<div class="c-button-top">'.$this->xlist(array(
						'fetch' => array(
							'data' => $this->control()->config->button_top
						),
						'view' => array(
							'script' => 'control/button'
						)
					)).'</div>' : '' ?>
					<?php echo $this->control()->config->content ?>
					<?php echo count($this->control()->config->button_bottom) ? $this->xlist(array(
						'fetch' => array(
							'data' => $this->control()->config->button_bottom
						),
						'view' => array(
							'script' => 'control/button'
						)
					)) : '' ?>
				</div>
				<div class="fix"></div>
			</div>
		</div>
		<!-- Footer -->
		<div id="footer">
			<div class="wrapper">
				<span><?php echo $this->translate('control_copy') ?></span>
			</div>
		</div>
		<?php echo $this->messify->render('js') ?>
	</body>
</html>