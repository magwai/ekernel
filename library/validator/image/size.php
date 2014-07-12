<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_validator_image_size extends validator {
	public function validate($value) {
		$ret = null;
		if (is_file($this->option->path.'/'.$value)) {
			$size = @getimagesize($this->option->path.'/'.$value);
			if ($size) {
				if (isset($this->option->minwidth) && $this->option->minwidth > $size[0]) {
					if (!$ret) $ret = array();
					$ret['imagesize_width_much'] = array();
				}
				if (isset($this->option->maxwidth) && $this->option->maxwidth < $size[0]) {
					if (!$ret) $ret = array();
					$ret['imagesize_width_less'] = array();
				}
				if (isset($this->option->minheight) && $this->option->minheight > $size[1]) {
					if (!$ret) $ret = array();
					$ret['imagesize_height_much'] = array();
				}
				if (isset($this->option->maxheight) && $this->option->maxheight < $size[1]) {
					if (!$ret) $ret = array();
					$ret['imagesize_height_less'] = array();
				}
			}
			else $ret = array(
				'imagesize_nf' => array()
			);
		}
		return $ret;
	}
}