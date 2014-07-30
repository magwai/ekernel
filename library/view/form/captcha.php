<?php

$id = $this->captcha->generate();

?>
<div class="e-form-captcha-frame">
	<div class="e-form-captcha-image"><img src="<?php echo $this->captcha->getImgUrl().'/'.$id.'.png' ?>" alt="" /></div>
	<div class="e-form-captcha-input"><input type="hidden" value="<?php echo $id ?>" name="<?php echo $this->name ?>_id" /><?php echo $this->render('form/input') ?></div>
</div>