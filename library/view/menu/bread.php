<?php

if (count($this->data)) {

?>
<div class="bread">
<?php

	foreach ($this->data as $n => $el) {
		if ($n) echo ' / ';
		echo '<a href="'.$el->href.'">'.$el->title.'</a>';
	}

?>
</div>
<?php

}