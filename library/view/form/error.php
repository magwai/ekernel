<?php

if (count($this->data)) {

?>
	<ul class="e-form-el-error<?php echo $this->class_error_frame ? ' '.$this->class_error_frame : '' ?>">
<?php

	foreach ($this->data as $el) {

?>
	<li><?php echo $el ?></li>
<?php

	}

?>
	</ul>
<?php
}