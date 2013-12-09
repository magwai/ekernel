<?php

class k_view_helper_minify extends view_helper {
	public $item = array();
	public $item_inline = array();

	public function prepend_inline($url) {
		return $this->prepend($url, true);
	}

	public function append_inline($url) {
		return $this->append($url, true);
	}

	public function set_inline($position, $url) {
		return $this->set($position, $url, true);
	}

	public function remove_inline($position) {
		return $this->remove($position, true);
	}

	public function prepend($url, $inline = false) {
		if ($inline) {
			if (array_search($url, $this->item_inline) === false) array_unshift($this->item_inline, $url);
		}
		else if (array_search($url, $this->item) === false) array_unshift($this->item, $url);
		return $this;
	}

	public function append($url, $inline = false) {
		if ($inline) {
			if (array_search($url, $this->item_inline) === false) array_push($this->item_inline, $url);
		}
		else if (array_search($url, $this->item) === false) array_push($this->item, $url);
		return $this;
	}

	public function set($position, $url, $inline = false) {
		if ($inline) $this->item_inline[$position] = $url;
		else $this->item[$position] = $url;
		return $this;
	}

	public function remove($position, $inline = false) {
		if ($inline) unset($this->item_inline[$position]);
		else unset($this->item[$position]);
		return $this;
	}

	public function name($type, $modified, $prefix = '') {
		$md5 = substr(md5($modified), 0, 5);
		return '/'.DIR_CACHE.'/'.$type.'/'.$prefix.$md5.'.'.$type;
	}

	public function save($type, $path, $content) {
		$config = application::get_instance()->config->$type;
		if (!@file_exists(PATH_ROOT.'/'.DIR_CACHE.'/'.$type)) {
			@mkdir(PATH_ROOT.'/'.DIR_CACHE.'/'.$type, 0777, true);
			@chmod(PATH_ROOT.'/'.DIR_CACHE.'/'.$type, 0777);
		}
		if ($config->compress) {
			$content = $this->minify($content, $type);
		}
		file_put_contents($path, $content);
		@chmod($path, 0777);

		if ($config->gzip_static && function_exists('gzopen')) {
			$zp = gzopen($path.'.gz', 'wb9');
			gzwrite($zp, $content);
			gzclose($zp);
			@chmod($path.'.gz', 0777);
		}
	}

	public function minify($res, $type) {
		if (substr($res, 0, 12) == '/* minified_') return $res;
		$config = application::get_instance()->config->$type;
		$ret = '';
		if ($config->compressor) foreach ($config->compressor as $el) {
			$method = 'minify_'.$el;
			$ret = $this->$method($res, $type);
			if ($ret !== false) break;
		}
		return $ret;
	}

	public function minify_yui($res, $type) {
		$process = popen('java -client -Xmx64m 2>&1', 'r');
		$ret = false;
		if (is_resource($process)) {
			$read = fread($process, 2096);
			pclose($process);
			if (stripos($read, 'Usage:') !== false) $ret = true;
		}
		if (!$ret) return false;
		$desc = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w")
		);
		$process = proc_open('java -client -Xmx64m -jar "'.PATH_ROOT.'/'.DIR_LIBRARY.'/jar/yuicompressor.jar" --charset utf-8 --type '.$type, $desc, $pipes);
		if (is_resource($process)) {
			fwrite($pipes[0], $res);
			fclose($pipes[0]);
			$res_1 = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			if ($res_1) {
				$res = "/* minified_yuicompressor */\n".$res_1;
			}
			proc_close($process);
		}
		return $res;
	}

	public function __toString() {
		return (string)$this->render();
	}
}