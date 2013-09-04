<textarea<?php echo $this->class ? ' class="'.$this->class.'"' : '' ?><?php echo $this->name ? ' name="'.$this->escape($this->name).'"' : '' ?><?php echo $this->cols ? ' cols="'.(int)$this->cols.'"' : '' ?><?php echo $this->rows ? ' rows="'.(int)$this->rows.'"' : '' ?><?php

if ($this->attr) foreach ($this->attr as $k => $v) {
	echo ' '.$this->escape($k).'="'.$this->escape($v).'"';
}

?>><?php echo $this->value ?></textarea>