<?php

if (count($this->data)) {

?>
	<div class="nNote nFailure">
<?php

	foreach ($this->data as $el) {

?>
	<p><?php echo $el ?></p>
<?php

	}

?>
	</div>
<?php
}