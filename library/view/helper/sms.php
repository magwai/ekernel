<?php

class k_view_helper_sms extends view_helper {
	function sms($operator, $message, $phones, $config = array()) {
		if ($phones) {
			foreach ($phones as $k => $v) {
				$v = preg_replace(array(
					'/[^\d]/i'
				), array(
					''
				), $v);
				if (substr($v, 0, 1) == '8') $v = '7'.substr($v, 1);
				if (strlen($v) != 11) unset($phones[$k]);
				else $phones[$k] = $v;
			}
		}
		return $phones ? $this->$operator($message, $phones, $config) : false;
	}
	
	function iqsms($message, $phones, $config) {
		$y = application::get_instance()->config->sms->iqsms;
		$ok = 0;
		foreach ($phones as $phone) {
			$ch = curl_init('http://gate.iqsms.ru/send/?phone='.rawurlencode($phone).'&text='.rawurlencode($message).(@$config['sender'] ? '&sender='.rawurlencode($config['sender']) : ''));
			curl_setopt($ch, CURLOPT_USERPWD, $y['login'].':'.$y['password']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);
			$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($status_code == 200 && stripos($res, 'accepted') !== false) $ok++;
		}
		return $ok;
	}
}