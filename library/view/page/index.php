<?php

echo $this->xlist(array(
	'fetch' => array(
		'model' => 'page',
		'method' => 'card',
		'param' => $this->id
	)
));