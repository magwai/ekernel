<?php

if (count($this->data)) {
	$reg = $this->lang(true);

?>
<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $reg->title ?> <span class="caret"></span></a>
	<ul class="dropdown-menu">
<?php

	foreach ($this->data as $el) {

?>
		<li><a href="<?php echo str_ireplace('/'.$reg->stitle.'/', '/'.$el->stitle.'/', application::get_instance()->request->url) ?>"><?php echo $el->title ?></a></li>
<?php

	}

?>
	</ul>
</li>
<?php

}