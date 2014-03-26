<?php

class k_view_helper_css extends view_helper_minify {
	public function prepend($url, $media = 'all', $condition = '') {
		array_unshift($this->item, array(
			'url' => $url,
			'media' => $media,
			'condition' => $condition
		));
		return $this;
	}

	public function append($url, $media = 'all', $condition = '') {
		array_push($this->item, array(
			'url' => $url,
			'media' => $media,
			'condition' => $condition
		));
		return $this;
	}

	public function set($position, $url, $media = 'all', $condition = '') {
		$this->item[$position] = array(
			'url' => $url,
			'media' => $media,
			'condition' => $condition
		);
		return $this;
	}

	public function render($name = null, $modified = true) {
		$config = application::get_instance()->config->css;
		ksort($this->item);
		if ($config->merge) {
			$css = array();
			foreach ($this->item as $offset => $item) {
				if (stripos($item['url'], 'http://') === false) {
					$css[$item['media'].'_'.$item['condition']] = isset($css[$item['media'].'_'.$item['condition']]) ? $css[$item['media'].'_'.$item['condition']] : array();
					$css[$item['media'].'_'.$item['condition']][$offset] = $item;
					$this->remove($offset);
				}
			}
			if ($css) {
				$m = '';
				foreach ($css as $media => $els) {
					$c = '';
					$items = array();
					foreach ($els as $item) {
						$i = PATH_ROOT.$item['url'];
						$items[] = $i;
						$m .= filemtime($i).filesize($i);
					}
					$nm = $name
						? '/'.DIR_CACHE.'/css/'.$name.'.css'
						: $this->name('css', $m);
					$ex = file_exists(PATH_ROOT.$nm);
					if (!$ex) {
						foreach ($items as $k => $el) {
							$dir_full = dirname($el);
							$str = file_get_contents($el);
							$matches = $files = array();
							preg_match_all('/url\((\'|\"|)(.*?)(\'|\"|)\)/si', $str, $res);
							if (@$res[2]) foreach ($res[2] as $k_1 => $el_1) {
								$matches[] = $res[0][$k_1];
								$files[] = $el_1;
							}
							if ($files) {
								foreach ($files as $k_1 => $el_1) {
									if (stripos($el_1, 'http://') !== false || substr($el_1, 0, 1) == '/') continue;
									$el_1_r = preg_replace(array('/\?.*$/si', '/\#.*$/si'), '', $el_1);
									preg_match('/\?(.*)$/si', $el_1, $el_res);
									if (!$el_res) preg_match('/\#(.*)$/si', $el_1, $el_res);
									$su = realpath($dir_full.'/'.$el_1_r);
									if (!$su) continue;
									$su = str_ireplace(array(
										realpath(PATH_ROOT),
										realpath(PATH_ROOT.'/'.DIR_KERNEL),
										'\\'
									), array(
										'',
										'/'.DIR_KERNEL,
										'/'
									), $su);
									$str = str_ireplace($matches[$k_1], 'url("'.$su.($el_res ? $el_res[0] : '').'")', $str);
								}
							}

							$matches = $files = array();
							preg_match_all('/src\=(\'|\"|)(.*?)(\'|\"|\,\))/si', $str, $res);

							if (@$res[2]) foreach ($res[2] as $k_1 => $el_1) {
								$matches[] = $res[0][$k_1];
								$files[] = $el_1;
							}
							if ($files) {
								foreach ($files as $k_1 => $el_1) {
									if (stripos($el_1, 'http://') !== false || substr($el_1, 0, 1) == '/') continue;
									$el_1_r = preg_replace(array('/\?.*$/si', '/\#.*$/si'), '', $el_1);
									preg_match('/\?(.*)$/si', $el_1, $el_res);
									if (!$el_res) preg_match('/\#(.*)$/si', $el_1, $el_res);
									$su = realpath($dir_full.'/'.$el_1_r);
									if (!$su) continue;
									$su = str_ireplace(array(
										realpath(PATH_ROOT),
										realpath(PATH_ROOT.'/'.DIR_KERNEL),
										'\\'
									), array(
										'',
										'/'.DIR_KERNEL,
										'/'
									), $su);
									$str = str_ireplace($matches[$k_1], 'src="'.$su.($el_res ? $el_res[0] : '').($modified && !$el_res ? '?'.filemtime($dir_full.'/'.$el_1_r) : '').'"', $str);
								}
							}

							$c .= $str."\n";
						}
						$this->save('css', PATH_ROOT.$nm, trim($c));
					}
					$media = explode('_', $media);
					$this->append($nm, $media[0], $media[1]);
				}
			}

		}
		else {
			foreach ($this->item as $offset => $item) {
				if (stripos($item['url'], 'http://') === false) {
					$m = @filemtime(PATH_ROOT.$item['url']).filesize(PATH_ROOT.$item['url']);
					if ($m) {
						$nm = $this->name('css', $m);
						$this->save('css', PATH_ROOT.$nm, trim(file_get_contents(PATH_ROOT.$item['url'])));
						$this->set($offset, $nm, $item['media'], $item['condition']);
					}
				}
			}
		}
		$res = '';
		if ($this->item) {
			foreach ($this->item as $el) {
				$res .= ($el['condition'] ? '<!--[if '.$el['condition'].']>' : '').
							'<link href="'.$el['url'].'" rel="stylesheet" media="'.$el['media'].'" />'.
						($el['condition'] ? '<![endif]-->' : '');
			}
		}
		return $res;
	}

	function minify_cssmin($res) {
		if (!class_exists('CssMin')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/cssmin/cssmin.php';
		return "/* minified_cssmin */\n".CssMin::minify($res);
	}
}