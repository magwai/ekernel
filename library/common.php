<?php

class k_common {
	static function translit($str) {
		$tr = array(
			"Ґ"=>"G","Ё"=>"YO","Є"=>"E","Ї"=>"YI","І"=>"I",
			"і"=>"i","ґ"=>"g","ё"=>"yo","№"=>"#","є"=>"e",
			"ї"=>"yi","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
			"Д"=>"D","Е"=>"E","Ж"=>"ZH","З"=>"Z","И"=>"I",
			"Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
			"О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
			"У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
			"Ш"=>"SH","Щ"=>"SCH","Ъ"=>"'","Ы"=>"YI","Ь"=>"",
			"Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"zh",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"'",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
		);
		return strtr($str, $tr);
	}

	static function stitle($str, $length = 56000) {
		$str = self::translit($str);
		$str = strtolower($str);
		$str = @preg_replace('/[^\w]/', '_', $str);
		while (strpos($str, '__') !== false) $str = str_replace('__', '_', $str);
		$str = trim($str, '_');
		if (strlen($str) > $length) {
			$p = explode('_', $str);
			$c = array();
			foreach ($p as $el) $c[] = strlen($el);
			while (strlen($str) > $length) {
				$i = array_search(max($c), $c);
				if ($i === false) break;
				unset($c[$i]);
				unset($p[$i]);
				$str = implode('_', $p);
			}
		}
		return $str;
	}

	static function get_date($date, $template = 'd.m.Y') {
    	if (!$date || $date == '0000-00-00 00:00:00') return '';
		return @date($template, @strtotime($date));
    }
	
	static function del_recursive($dir) { 
		$files = glob($dir . '*', GLOB_MARK); 
		foreach ($files as $file) { 
			if (substr($file, -1) == '/') 
				self::del_recursive($file); 
			else 
				@unlink($file); 
		} 
		@rmdir($dir); 
	}
}