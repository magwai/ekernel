<?php

if (count($this->data)) {
	foreach ($this->data as $n => $el) {
		if ($n) echo ' / ';
		echo $el->title;
	}
}