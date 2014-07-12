<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_filter_filename extends filter {
	public function __construct($option) {
		parent::__construct($option);
		if (!isset($this->option['length'])) $this->option['length'] = 20;
		$this->option['prefix'] = @$this->option['prefix'];
	}

	public function filter($value) {
		$ext = strrpos($value, '.');
    	if ($ext !== false) {
    		$t = substr($value, $ext + 1);
    		$value = substr($value, 0, $ext);
    		$ext = strtolower($t);
    	}
    	$stitle = common::stitle($value, $this->option['length']);
    	$p = '';
    	while (file_exists($this->option['directory'].'/'.$this->option['prefix'].$stitle.$p.($ext ? '.'.$ext : ''))) $p = $p ? $p + 1 : 1;
    	return $this->option['prefix'].$stitle.$p.($ext ? '.'.$ext : '');
	}
}