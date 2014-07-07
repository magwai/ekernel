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
		if (!strlen($str)) $str = '_';
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

	static function stitle_unique($model, $stitle, $field = 'stitle', $where = array()) {
		$where = is_array($where) ? $where : array();
		$stitle = $stitle ? $stitle : '_';
    	$stitle_n = $stitle;
		$stitle_p = -1;
		do {
			$stitle_p++;
			$stitle_n = $stitle.($stitle_p == 0 ? '' : $stitle_p);
			$w = $where;
			$w['`'.$field.'` = ?'] = $stitle_n;
			$stitle_c = (int)$model->fetch_count($w);
		}
		while ($stitle_c > 0);
		return $stitle_n;
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

	static function truncate_text($text, $length = 100, $dots = false) {
		global $m;
		$text = $ftext = str_ireplace(array('&nbsp;'), array(' '), trim(strip_tags($text)));
		$pos_dot = $pos_com = $pos_sp = 0;
		for ($i = $length + 10; $i > $length - 11; $i--) {
			if (@($text[$i] == '.') && !$pos_dot) $pos_dot = $i;
			else if (@($text[$i] == ',' || $text[$i] == ';') && !$pos_com) $pos_com = $i;
			else if (@($text[$i] == ' ' || $text[$i] == '-' || $text[$i] == '_') && !$pos_sp) $pos_sp = $i;
		}
		if ($pos_dot) $pos = $pos_dot;
		else if ($pos_com) $pos = $pos_com;
		else if ($pos_sp) $pos = $pos_sp;
		else $pos = $length;
		$text = mb_substr($text, 0, $pos);
		return $text.($dots ? ($text == $ftext ? '' : '&nbsp;...') : '');
	}
}