<?php

if (count($this->data)) {
	$reg = $this->lang(true);

?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $reg->title ?> <span class="caret"></span></a>
	<ul class="dropdown-menu">
<?php

	foreach ($this->data as $el) {
		if (application::get_instance()->config->resource->lang->type == 'session') {
			$h = $this->url(array('controller' => 'lang', 'action' => 'set', 'id' => $el->stitle), 'default');
		}
		else {
			$h = str_ireplace('/'.$reg->stitle.'/', '/'.$el->stitle.'/', application::get_instance()->request->url);
		}

?>
		<li><a href="<?php echo $h ?>"><?php echo $el->title ?></a></li>
<?php

	}

?>
	</ul>
</li>
<?php

}