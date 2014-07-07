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

	public function render_el($fn) {
		$c = trim(file_get_contents($fn));
		$m = md5($c);
		$nm = $this->name('css', $m);
		if (file_exists(PATH_ROOT.$nm)) {
			$c = file_get_contents(PATH_ROOT.$nm);
		}
		else {
			$c = $this->preprocess_util($c, $fn);
			$dir_full = dirname($fn);
			$matches = $files = array();
			preg_match_all('/url\((\'|\"|)(.*?)(\'|\"|)\)/si', $c, $res);
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
					$c = str_ireplace($matches[$k_1], 'url("'.$su.($el_res ? $el_res[0] : '').'")', $c);
				}
			}

			$matches = $files = array();
			preg_match_all('/src\=(\'|\"|)(.*?)(\'|\"|\,\))/si', $c, $res);

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
					$c = str_ireplace($matches[$k_1], 'src="'.$su.($el_res ? $el_res[0] : '').($modified && !$el_res ? '?'.filemtime($dir_full.'/'.$el_1_r) : '').'"', $c);
				}
			}
			//$this->save('css', PATH_ROOT.$nm, $c);
		}
		return array(
			'md5' => $m,
			'name' => $nm,
			'content' => $c
		);
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
					$items = $conts = array();
					foreach ($els as $item) {
						$ret = $this->render_el(PATH_ROOT.$item['url']);
						$items[] = $ret['name'];
						$conts[] = $ret['content'];
						$m .= $ret['md5'];
					}
					$nm = $name
						? '/'.DIR_CACHE.'/css/'.$name.'.css'
						: $this->name('css', $m);
					if (!file_exists(PATH_ROOT.$nm)) {
						$this->save('css', PATH_ROOT.$nm, implode("\n", $conts));
					}
					$media = explode('_', $media);
					$this->append($nm, $media[0], $media[1]);
				}
			}

		}
		else {
			foreach ($this->item as $offset => $item) {
				if (stripos($item['url'], 'http://') === false) {
					$ret = $this->render_el(PATH_ROOT.$item['url']);
					$this->set($offset, $ret['name'], $item['media'], $item['condition']);
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

	public function preprocess_util($content, $file) {
		if (substr($file, -5) == '.scss') {
			$config = application::get_instance()->config->util;
			$path = PATH_ROOT.'/img';
			$d = array(
				basename($file) => md5(file_get_contents($file))
			);
			if (!function_exists('recursive_scss')) {
				function recursive_scss($path, $dir, &$d) {
					$cur = $path.($dir ? '/'.$dir : '');
					foreach (scandir($cur) as $fn) {
						if ($fn == '.' || $fn == '..') continue;
						if (is_dir($cur.'/'.$fn) && !in_array($fn, array('font'))) {
							recursive_scss($path, $dir.($dir ? '/' : '').$fn, $d);
						}
						else if (preg_match('/\.('.($dir == 'sprites' ? 'png|' : '').'scss)/i', $fn)) {
							$d[$dir.($dir ? '/' : '').$fn] = md5(file_get_contents($cur.'/'.$fn));
						}
					}

				}
			}

			recursive_scss($path, '', $d);
			if ($d) {
				$res = file_get_contents($config->host.'/x/scss/ch/get/host/'.$_SERVER['HTTP_HOST'].'/file/'.basename($file));
				if ($res) {
					if (!class_exists('Zip')) require PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Zip.php';
					$zip = new Zip();
					$res = json_decode($res, true);
					$cnt = 0;
					foreach ($d as $k => $v) {
						if ($v != @$res[$k]) {
							$zip->addFile(file_get_contents($path.'/'.$k), $k);
							$cnt++;
						}
					}
					if ($cnt) {
						$data = urlencode($zip->getZipData());
						$context = stream_context_create(array(
							'http' => array(
								'method' => 'POST',
								'header' => 'Content-Type: multipart/form-data'."\r\n".'Content-Length: '.strlen($data)."\r\n",
								'content' => $data
							)
						));
						$res = file_get_contents($config->host.'/x/scss/ch/set/host/'.$_SERVER['HTTP_HOST'].'/file/'.basename($file), false, $context);
						if ($res) {
							$res = json_decode($res, true);
							file_put_contents(PATH_ROOT.'/'.DIR_CACHE.'/css/temp.zip', urldecode($res['data']));
							require PATH_ROOT.'/'.DIR_LIBRARY.'/lib/Unzip.php';
							$zip = new Unzip();
							$zip->extract(PATH_ROOT.'/'.DIR_CACHE.'/css/temp.zip', PATH_ROOT.'/'.DIR_CACHE.'/css');
							unlink(PATH_ROOT.'/'.DIR_CACHE.'/css/temp.zip');
							$nfn = str_replace('.scss', '.css', basename($file));
							$content = @file_get_contents(PATH_ROOT.'/'.DIR_CACHE.'/css/'.$nfn);
							unlink(PATH_ROOT.'/'.DIR_CACHE.'/css/'.$nfn);
						}
					}
				}
			}
		}
		return $content;
	}

	public function preprocess($content, $file) {
		if (substr($file, -5) == '.scss') {
			$dir = PATH_ROOT.'/'.DIR_CACHE.'/css/'.microtime(true);
			$dir_file = dirname($file);
			exec('mkdir "'.$dir.'" ; cd "'.$dir.'" ; compass create; chmod 777 "'.$dir.'/sass" ; mkdir "'.$dir.'/'.DIR_CACHE.'" ; ln -s "'.PATH_ROOT.'/img" "'.$dir.'/'.DIR_CACHE.'/css"');
			file_put_contents($dir.'/sass/style.scss', $content);
			file_put_contents($dir.'/config.rb', "line_comments = false\nimages_dir = \"".DIR_CACHE."/css\"\nfonts_dir = \"img\"\nadditional_import_paths = [\"".$dir_file."\", \"".PATH_ROOT."/".DIR_KERNEL."/img\"]", FILE_APPEND);
			exec('cd "'.$dir.'" ; compass compile');
			exec('cd "'.$dir.'/'.DIR_CACHE.'/css" ; cp sprites-* "'.PATH_ROOT.'/'.DIR_CACHE.'/css" ; rm sprites-*; chmod 777 '.PATH_ROOT.'/'.DIR_CACHE.'/css/*');
			$content = @file_get_contents($dir.'/stylesheets/style.css');
			exec('rm -R "'.$dir.'"');
		}
		return $content;
	}
}