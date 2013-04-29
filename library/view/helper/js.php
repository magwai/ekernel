<?php

class k_view_helper_js extends view_helper_minify {
	public function render($name = null) {
		$config = application::get_instance()->config->js;
		ksort($this->item);
		ksort($this->item_inline);

		if ($this->item) {
			if ($config->merge) {
				$js = array();
				$m = '';
				foreach ($this->item as $offset => $item) {
					if (stripos($item, '//') === false) {
						$js[$offset] = $item;
						$this->remove($offset);
						$m .= filemtime(PATH_ROOT.$item);
					}
				}
				if ($js) {
					$c = '';
					$nm = $name
						? '/'.DIR_CACHE.'/js/'.$name.'.js'
						: $this->name('js', $m);
					$ex = file_exists(PATH_ROOT.$nm);
					if (!$ex) {
						foreach ($js as $item) $c .= file_get_contents(PATH_ROOT.$item)."\n";
						$this->save('js', PATH_ROOT.$nm, trim($c));
					}
					$this->append($nm);
				}
			}
			else {
				foreach ($this->item as $offset => $item) {
					if (stripos($item, '//') === false) {
						$m = @filemtime(PATH_ROOT.$item);
						if ($m) {
							$nm = $this->name('js', $m);
							$this->save('js', PATH_ROOT.$nm, trim(file_get_contents(PATH_ROOT.$item)));
							$this->set($offset, $nm);
						}
					}
				}
			}
		}
		return ($this->item
			? '<script type="text/javascript" src="'.implode('"></script><script type="text/javascript" src="', $this->item).'"></script>'
			: ''
		).($this->item_inline ? '<script type="text/javascript">'.implode("\n", $this->item_inline).'</script>' : '');
	}

	function minify_gcc($res) {
		$process = popen('java -client -Xmx64m 2>&1', 'r');
		$ret = false;
		if (is_resource($process)) {
			$read = fread($process, 2096);
			pclose($process);
			if (stripos($read, 'Usage:') !== false) $ret = true;
		}
		if (!$ret) return false;
		$desc = array(
			1 => array("pipe", "w")
		);
		$fn = sys_get_temp_dir().'/'.md5(microtime(true)).'.js';
		file_put_contents($fn, $res);
		$process = proc_open('java -client -Xmx64m -jar '.PATH_ROOT.'/'.DIR_LIBRARY.'/jar/compiler.jar --warning_level=QUIET --js='.$fn.' 2>&1', $desc, $pipes);
		if (is_resource($process)) {
			$res_1 = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			if ($res_1) {
				$res = "/* minified_gcc */\n".$res_1;
			}
			proc_close($process);
		}
		unlink($fn);
		return $res;
	}

	function minify_jsmin($res) {
		if (!class_exists('JSMin')) require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/jsmin/jsmin.php';
		return "/* minified_jsmin */\n".JSMin::minify($res);
	}
}