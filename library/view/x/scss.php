<?php

application::get_instance()->controller->layout = null;

$d = array();
if ($this->host && $this->file) {
	$path = PATH_ROOT.'/'.DIR_CACHE.'/scss/'.$this->host.'/'.$this->file;
	if (!file_exists($path)) mkdir($path, 0777, true);
	if ($this->ch == 'get') {
		function recursive_scss($path, $dir, &$d) {
			$cur = $path.($dir ? '/'.$dir : '');
			foreach (scandir($cur) as $fn) {
				if ($fn == '.' || $fn == '..') continue;
				if (is_dir($cur.'/'.$fn)) {
					recursive_scss($path, $dir.($dir ? '/' : '').$fn, $d);
				}
				else if (preg_match('/\.(png|jpg|gif|scss)/i', $fn)) {
					$d[$dir.($dir ? '/' : '').$fn] = md5(file_get_contents($cur.'/'.$fn));
				}
			}

		}
		recursive_scss($path, '', $d);
	}
	else if ($this->ch == 'set') {
		$zip = file_get_contents('php://input');
		exec('cd "'.$path.'" ; compass create > /dev/null');
		file_put_contents($path.'/config.rb', "\nhttp_path = \".\"\nline_comments = false\nimages_dir = \"\"\nfonts_dir = \"/img\"\nadditional_import_paths = [\"".$path."\", \"".PATH_ROOT."/".DIR_KERNEL."/img\"]", FILE_APPEND);
		file_put_contents($path.'/temp.zip', urldecode($zip));
		exec('cd "'.$path.'" ; unzip -o temp.zip ; rm temp.zip ; cp *.scss sass');
		exec('cd "'.$path.'" ; compass compile > /dev/null');
		exec('cd "'.$path.'" ; mv -f sprites-*.png stylesheets');
		exec('cd "'.$path.'/stylesheets" ; rm ie.css ; rm print.css ; rm screen.css ; zip -r0 temp.zip *');
		$data = @file_get_contents($path.'/stylesheets/temp.zip');
		exec('cd "'.$path.'" ; rm config.rb ; rm -r stylesheets ; rm -r .sass-cache ; rm -r sass');
		$d['data'] = urlencode($data);
	}

}
header('Content-Type: application/json');
echo json_encode($d);