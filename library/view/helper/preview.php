<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_preview extends view_helper  {
	public function preview($dir, $name, $param = array()) {
    	if (!$dir || !$name) return @$param['default'];
		$prefix = $param['prefix'] = @$param['prefix'] ? $param['prefix'].'_' : '';
		$ext = strrpos($name, '.');
		$ext = $ext === false ? '' : @substr($name, $ext + 1);
		$ctype = 'image';
		if ($ext == 'flv' || $ext == 'avi' || $ext == '3gp' || $ext == 'wmv') $ctype = 'video';
		$modified_o = @filemtime(PATH_ROOT.'/'.DIR_UPLOAD.'/'.$dir.'/'.$name);
		if (!$modified_o) return @$param['default'];

		$png_name = (@$param['corner'] && $ctype == 'image')? preg_replace('/\.(.+)$/','.png',$name):'';
		$param['new_name'] = $png_name;

		$dir_dest = @$param['cache_dir_folder'] ? $param['cache_dir_folder'] : $dir;

		$modified = @filemtime(PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest.'/'.$prefix.@$param['crop'].($png_name ? $png_name : $name));

		if ($modified < $modified_o) {
			if (!@file_exists(PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest)) {
				@mkdir(PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest, 0777, true);
				@chmod(PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest, 0777);
			}
			if ($ctype == 'image') {
				$preview = new preview_image(
					PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest,
					PATH_ROOT.'/'.DIR_UPLOAD.'/'.$dir
				);
				$preview->create($name, $param);
			}
			else if ($ctype == 'video') {
				$preview = new preview_image(
					PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest,
					PATH_ROOT.'/'.DIR_UPLOAD.'/'.$dir
				);
				$preview->create($name, $param);
			}
			@chmod(PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest.'/'.$prefix.@$param['crop'].$name, 0777);
	    }
	    return $modified || @file_exists(PATH_ROOT.'/'.DIR_CACHE.'/'.$dir_dest.'/'.$prefix.@$param['crop'].(($png_name) ? $png_name : $name))
	    	? '/'.DIR_CACHE.'/'.$dir_dest.'/'.$prefix.@$param['crop'].(($png_name)?$png_name:$name).'?'.$modified
	    	: @$param['default'];
    }
}