<?php

if (count($this->data)) {

?>
	<ul class="e-form-el-error">
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
