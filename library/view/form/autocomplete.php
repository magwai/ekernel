<?php

$name = (string)$this->name;
$this->name = $name.'_fake';
$value = (string)$this->value;
$this->value = '';
echo $this->render('form/input');

?>
<input type="hidden"<?php echo $this->name ? ' name="'.$this->escape($name).'"' : '' ?> value="<?php echo $this->escape($value) ?>" />