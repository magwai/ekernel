<?php

application::get_instance()->controller->layout = null;

$content = @urldecode(file_get_contents('php://input'));
if ($this->type && $this->compressor) {
	if ($this->type == 'js') {
		if ($this->compressor == 'gcc') {
			$desc = array(
				1 => array("pipe", "w")
			);
			$fn = sys_get_temp_dir().'/'.md5(microtime(true)).'.js';
			file_put_contents($fn, $content);
			$process = proc_open('java -client -Xmx64m -jar '.PATH_ROOT.'/'.DIR_LIBRARY.'/jar/compiler.jar --warning_level=QUIET --js='.$fn.' 2>&1', $desc, $pipes);
			if (is_resource($process)) {
				$res_1 = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
				if ($res_1) {
					$content = $res_1;
				}
				proc_close($process);
			}
			unlink($fn);
		}
		else if ($this->compressor == 'yui') {
			$desc = array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w")
			);
			$process = proc_open('java -client -Xmx64m -jar "'.PATH_ROOT.'/'.DIR_LIBRARY.'/jar/yuicompressor.jar" --charset utf-8 --type js', $desc, $pipes);
			if (is_resource($process)) {
				fwrite($pipes[0], $content);
				fclose($pipes[0]);
				$res_1 = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
				if ($res_1) {
					$content = $res_1;
				}
				proc_close($process);
			}
		}
		else if ($this->compressor == 'jsmin') {
			require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/jsmin/jsmin.php';
			$content = JSMin::minify($content);
		}
	}
	else if ($this->type == 'css') {
		if ($this->compressor == 'yui') {
			$desc = array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w")
			);
			$process = proc_open('java -client -Xmx64m -jar "'.PATH_ROOT.'/'.DIR_LIBRARY.'/jar/yuicompressor.jar" --charset utf-8 --type css', $desc, $pipes);
			if (is_resource($process)) {
				fwrite($pipes[0], $content);
				fclose($pipes[0]);
				$res_1 = stream_get_contents($pipes[1]);
				fclose($pipes[1]);
				if ($res_1) {
					$content = $res_1;
				}
				proc_close($process);
			}
		}
		else if ($this->compressor == 'jsmin') {
			require_once PATH_ROOT.'/'.DIR_LIBRARY.'/lib/cssmin/cssmin.php';
			$content = CssMin::minify($content);
		}
	}
}
header('Content-Type: application/json');
echo json_encode(array(
	'data' => $content
));