<?php

class k_preview_image {
	var $path;
	var $image_path;

	function __construct($path = '', $image_path = '') {
		$this->path = $path;
		$this->image_path = $image_path ? $image_path : $path;
	}

	function create($name, $param = array()) {
		//$bg_fade = isset($param['bg_fade']) ? $param['bg_fade'] : 0;
		$bg_color = isset($param['bg_color']) ? $param['bg_color'] : array(255, 255, 255);
		$bg_adaptive = isset($param['bg_adaptive']) ? $param['bg_adaptive'] : false;
		$fd_r = isset($param['bg_adaptive_width']) ? $param['bg_adaptive_width'] : 10;
		if (!is_array($bg_color)) $bg_color = $m->f_hex2rgb($bg_color);
		$fit = isset($param['fit']) ? $param['fit'] : false;
		$mark = @$param['mark'];
		$mask = @$param['mask'];
		$gray = @$param['gray'];
		$lvl = @$param['lvl'];
		$wb = @$param['wb'];
		$corner = @$param['corner'];
		$align = isset($param['align']) ? $param['align'] : 'cc';
		$align = array(strtolower(substr($align, 0, 1)), strtolower(substr($align, 1, 1)));
		$stretch = isset($param['stretch']) ? $param['stretch'] : false;
		$quality = @(int)$param['quality'] ? (int)$param['quality'] : 95;
		$format = @isset($param['format']) ? $param['format'] : null;
		$width = @(int)$param['width'];
		$height = @(int)$param['height'];
		$prefix = @$param['prefix'];
		$min_width = @(int)$param['min_width'];
		$min_height = @(int)$param['min_height'];
		$filter = array();
		if ($min_width && $width && $width < $min_width) return false;
		if ($min_height && $height && $height < $min_height) return false;
		if (($min_width || $min_height) && $fit) return false;

		$thumb = new lib_phpthumb_class();
		$thumb->config_output_format = null;
		$thumb->setParameter('src',  $this->image_path.'/'.$name);
		if ($stretch) $thumb->setParameter('aoe',  1);
		$thumb->setParameter('q',  $quality);

		if ($width) $thumb->setParameter('w',  $width);
		if ($height) $thumb->setParameter('h',  $height);
		/*::create($this->image_path.'/'.$name, array(
			'jpegQuality' => $quality,
			'resizeUp' => $stretch,
			'correctPermissions' => true
		));*/

		/*if (@$param['crop']) {
			$crop_data = explode(',', $param['crop']);
			$image = $thumb->getOldImage();
			$crop_width = $crop_data[2] - $crop_data[0];
			$crop_height = $crop_data[3] - $crop_data[1];
			$new_image = imagecreatetruecolor($crop_width, $crop_height);
			imagecopyresampled($new_image, $image, 0, 0, $crop_data[0], $crop_data[1], $crop_width, $crop_height, $crop_width, $crop_height);
			$thumb->setOldImage($new_image);
			$thumb->setCurrentDimensions(array('width' => $crop_width, 'height' => $crop_height));
		}*/

		if ($fit) $thumb->setParameter('zc',  $fit);//$thumb->adaptiveResize($width, $height, $align);
		//else $thumb->resize($width, $height);

		

		/*if ($mark) {
			$image = $thumb->getOldImage();
			$this->mark($image, $mark);
			$thumb->setOldImage($image);
		}*/

		if ($gray) {
			$filter[] = 'gray';
		}
		if ($lvl) {
			$filter[] = 'lvl';
		}
		if ($wb) {
			$filter[] = 'wb';
		}
		if ($mask) {
			$filter[] = 'mask|'.$mask;
			$thumb->setParameter('f',  'png');
		}
		if ($mark) {
			$filter[] = 'wmi|'.$mark['file'].'|'.(@$mark['align'] ? $mark['align'] : 'br').'|'.(@$mark['opacity'] ? $mark['opacity'] : 100).'|'.(@$mark['margin_x'] ? $mark['margin_x'] : 10).'|'.(@$mark['margin_y'] ? $mark['margin_y'] : 10).'|'.(@$mark['rotate'] ? $mark['rotate'] : 0);
		}

		if ($filter) $thumb->setParameter('fltr',  $filter);
/*
		if($corner) {
			$format = 'PNG';
			$name = (@$param['new_name'])?$param['new_name']:$name;
			$image = $thumb->getOldImage();
			$this->corner($image, $param['corner']);
			$thumb->setOldImage($image);
		}*/
		//$thumb->setParameter('f',  'png');
		//print_r($thumb);exit();
		$thumb->GenerateThumbnail();
		
		if ($min_width || $min_height) {
			
			if ($min_width) $thumb->setParameter('w',  $min_width);
			if ($min_height) $thumb->setParameter('h',  $min_height);
			$thumb->GenerateThumbnail();
		}
		/*
		if ($min_width || $min_height) {
			$image = imagecreatefromstring($thumb->IMresizedData);
			$dim = array(
				'width' => $thumb->source_width,
				'height' => $thumb->source_height
			);
			$n_w = $dim['width'];
			$n_h = $dim['height'];
			if ($min_width > $n_w) $n_w = $min_width;
			if ($min_height > $n_h) $n_h = $min_height;

			if ($min_width > $dim['width'] || $min_height > $dim['height']) {
				if ($align[0] == 'l') $new_x = 0;
				else if ($align[0] == 'c') $new_x = floor(($n_w - $dim['width']) / 2);
				else if ($align[0] == 'r') $new_x = $n_w - $dim['width'];

				if ($align[1] == 't') $new_y = 0;
				else if ($align[1] == 'c') $new_y = floor(($n_h - $dim['height']) / 2);
				else if ($align[1] == 'b') $new_y = $n_h - $dim['height'];

				$new_image = imagecreatetruecolor($n_w, $n_h);
				if (!$new_image || !$image) return false;

				if ($bg_adaptive && ($n_w > $dim['width'] || $n_h > $dim['height'])) {
					$rgb = imagecolorat($image, 0, 0);
					$colors_lt = imagecolorsforindex($image, $rgb);

					$rgb = imagecolorat($image, $dim['width']- 1, 0);
					$colors_rt = imagecolorsforindex($image, $rgb);

					$rgb = imagecolorat($image, $dim['width']- 1, $dim['height']- 1);
					$colors_rb = imagecolorsforindex($image, $rgb);

					$rgb = imagecolorat($image, 0, $dim['height']- 1);
					$colors_lb = imagecolorsforindex($image, $rgb);

					$bg_color = array(
						(int)(array_sum(array($colors_lt['red'], $colors_rt['red'], $colors_rb['red'], $colors_lb['red'])) / 4),
						(int)(array_sum(array($colors_lt['green'], $colors_rt['green'], $colors_rb['green'], $colors_lb['green'])) / 4),
						(int)(array_sum(array($colors_lt['blue'], $colors_rt['blue'], $colors_rb['blue'], $colors_lb['blue'])) / 4),
					);
				}

				$color = imagecolorallocate($new_image, $bg_color[0], $bg_color[1], $bg_color[2]);
				imagefilledrectangle($new_image, 0, 0, $n_w - 1, $n_h - 1, $color);

				imagecopyresampled($new_image, $image, $new_x, $new_y, 0, 0, $dim['width'], $dim['height'], $dim['width'], $dim['height']);

				if ($bg_adaptive == 'image' && ($n_w > $dim['width'] || $n_h > $dim['height'])) {
					$dx = $n_w - $dim['width'];
					$dy = $n_h - $dim['height'];
					if ($dx) {
						$fd_width = ceil($dx / 2);
						$fd_height = $n_h;
					}
					else {
						$fd_width = $n_w;
						$fd_height = ceil($dy / 2);
					}

					for ($i = 0; $i < $fd_r; $i++) {
						$color = imagecolorallocatealpha($new_image, $bg_color[0], $bg_color[1], $bg_color[2], floor(($i / $fd_r) * 127));
						imageline(
							$new_image,
							$dx ? ($new_x + $i) : 0,
							$dx ? 0 : ($new_y + $i),
							$dx ? ($new_x + $i) : $n_w - 1,
							$dx ? $n_h - 1 : ($new_y + $i),
							$color
						);

						$color = imagecolorallocatealpha($new_image, $bg_color[0], $bg_color[1], $bg_color[2], floor(127 - ($i / $fd_r) * 127));
						imageline(
							$new_image,
							$dx ? ($n_w - $dx / 2 - $fd_r + $i) : 0,
							$dx ? 0 : ($n_h - $dy / 2 - $fd_r + $i),
							$dx ? ($n_w - $dx / 2 - $fd_r + $i) : $n_w - 1,
							$dx ? $n_h - 1 : ($n_h - $dy / 2 - $fd_r + $i),
							$color
						);
					}
				}
				//ob_clean();
				//print_r($thumb);
				//echo imagestring($thumb);
				//exit();
				//RenderToFile
				//$thumb->IMresizedData = imagestring($new_image);
			}
		}
		*/
		
		
		
		$thumb->RenderToFile($this->path.'/'.$prefix.@$param['crop'].$name/*, $format*/);

		return true;
	}

	private function mask($image, $param) {

	}

	private function mark($image, $param) {
		$k = is_array($param) ? array_keys($param) : array();
		if ($k) {
			$num = true;
			foreach ($k as $kk) {
				if (!is_numeric($kk)) {
					$num = false;
					break;
				}
			}
			if (!$num) $param = array($param);
		}

		if ($param) {
			foreach ($param as $param1) {
				$width = imagesx($image);
				$height = imagesy($image);
				$param1['padding_h'] = @(int)$param1['padding_h'];
				$param1['padding_v'] = @(int)$param1['padding_v'];
				$align = isset($param1['align']) ? $param1['align'] : 'cc';
				$align = array(strtolower(substr($align, 0, 1)), strtolower(substr($align, 1, 1)));

				$png = isset($param1['image']) ? $param1['image'] : @imagecreatefrompng($param1['file']);
				$p_width = imagesx($png);
				$p_height = imagesy($png);

				if (isset($param1['x']) && isset($param1['y'])) {
					$x = $param1['x'];
					$y = $param1['y'];
				}
				else {
					if ($align[0] == 'l') $x = $param1['padding_h'];
					else if ($align[0] == 'c') $x = floor(($width - $p_width) / 2);
					else if ($align[0] == 'r') $x = $width - $p_width - $param1['padding_h'];

					if ($align[1] == 't') $y = $param1['padding_v'];
					else if ($align[1] == 'c') $y = floor(($height - $p_height) / 2);
					else if ($align[1] == 'b') $y = $height - $p_height - $param1['padding_v'];
				}

				if ($x >= 0 && $x <= ($width - 1) && $y >= 0 && $y <= ($height - 1)) {
					@imagecopy($image, $png, $x, $y, 0, 0, $p_width, $p_height);
				}
				@imagedestroy($png);
			}
		}
	}

	private function corner($image, $param) {
		$radius = (@$param['radius'])?@(int)$param['radius']:10;
		$rate = (@$param['radius'])?@(int)$param['rate']:10;
    	$width = imagesx($image);
    	$height = imagesy($image);

    	imagealphablending($image, false);
    	imagesavealpha($image, true);

		$rs_radius = $radius * $rate;
		$rs_size = $rs_radius * 2;

		$corner = imagecreatetruecolor($rs_size, $rs_size);
		imagealphablending($corner, false);

		$trans = imagecolorallocatealpha($corner, 255, 255, 255, 127);
		@imagefill($corner, 0, 0, $trans);

		$positions = array(
	    	array(0, 0, 0, 0),
	    	array($rs_radius, 0, $width - $radius, 0),
	    	array($rs_radius, $rs_radius, $width - $radius, $height - $radius),
	    	array(0, $rs_radius, 0, $height - $radius),
		);

		foreach ($positions as $pos) {
	    	@imagecopyresampled($corner, $image, $pos[0], $pos[1], $pos[2], $pos[3], $rs_radius, $rs_radius, $radius, $radius);
		}

		$lx = $ly = 0;
		$i = -$rs_radius;
		$y2 = -$i;
		$r_2 = $rs_radius * $rs_radius;

		for (; $i <= $y2; $i++) {

	    	$y = $i;
	    	$x = sqrt($r_2 - $y * $y);

	    	$y += $rs_radius;
	    	$x += $rs_radius;

	    	@imageline($corner, $x, $y, $rs_size, $y, $trans);
	    	@imageline($corner, 0, $y, $rs_size - $x, $y, $trans);

	    	$lx = $x;
	    	$ly = $y;
		}

		foreach ($positions as $i => $pos) {
	    	@imagecopyresampled($image, $corner, $pos[2], $pos[3], $pos[0], $pos[1], $radius, $radius, $rs_radius, $rs_radius);
		}

		@imagedestroy($corner);
	}
}
