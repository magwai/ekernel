<?php

if (count($this->data)) {
	$el = $this->data[0];
	
	$this->title = $el->title;
	$this->meta_collector->auto('@+'.$el->title);
	$this->meta_collector->controller('page', $el->id);

?>
<h1><?php echo $el->title ?></h1>
<?php echo $el->message ?>
<?php

}