<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_pay extends view_helper {
	public $config = null;

	function init() {
		$this->config = application::get_instance()->config->pay ? clone application::get_instance()->config->pay : new data;
		$mt = new model_translate;
		$config_db = $mt->fetch_col('key', '(SUBSTRING(`key`, 1, 4) = "pay_")');
		if ($config_db) {
			foreach ($config_db as $v) {
				$p = explode('_', $v);
				array_shift($p);
				$p0 = array_shift($p);
				if ($p0 && $p) {
					$p = implode('_', $p);
					$this->config[$p0] = isset($this->config[$p0]) ? $this->config[$p0] : array();
					$this->config[$p0][$p] = $this->view->translate($v);
				}
			}
		}
	}

	function pay($type = null, $action = 'form') {
		$this->init();
		if ($type === null) return $this;
		else {
			$f = 'pay_'.strtolower($type).'_'.strtolower($action);
			$pp = func_get_args();
			array_shift($pp);
			array_shift($pp);
			return method_exists($this, $f) ? @call_user_method_array($f, $this, $pp) : false;
		}
	}

	function gen_form($url, $data) {
		$res = '';
		if ($data) {
			foreach ($data as $k => $v) $res .= '<input type="hidden" name="'.$k.'" value="'.$this->view->escape($v).'">';
		}
		return $res ? '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body onload="document.frm.submit();return false;">
		<form name="frm" action="'.$url.'" method="post" name="form" >'.$res.'</form>
	</body>
</html>' : '';
	}

	function pay_uniteller_form($order, $param = array()) {
		$config = $this->config->uniteller ? $this->config->uniteller : new data;
		if ($param) $config->set($param);

		$card = $this->view->basket()->pay_card($order);
		if (@!$card) return false;

		function getSignature($Shop_IDP, $Order_ID, $Subtotal_P, $MeanType, $EMoneyType, $Lifetime, $Customer_IDP, $Card_IDP, $IData, $PT_Code, $password) {
			$Signature = strtoupper(
				md5(
					md5($Shop_IDP) . "&" .
					md5($Order_ID) . "&" .
					md5($Subtotal_P) . "&" .
					md5($MeanType) . "&" .
					md5($EMoneyType) . "&" .
					md5($Lifetime) . "&" .
					md5($Customer_IDP) . "&" .
					md5($Card_IDP) . "&" .
					md5($IData) . "&" .
					md5($PT_Code) . "&" .
					md5($password)
				)
			);
			return $Signature;
		}
		$config->shopid = trim($config->shopid);
		$data = array(
			'Shop_IDP' => $config->shopid,
			'Order_IDP' => $order,
			'Subtotal_P' => $card->total,
			'Lifetime' => 3600,
			'Customer_IDP' => $card->author,
			'Signature' => getSignature($config->shopid, $order, $card->total, @$param['MeanType'] ? $param['MeanType'] : '', @$param['EMoneyType'] ? $param['EMoneyType'] : '', 3600, $card->author, '', '', '', $config->password),
			'URL_RETURN_OK' => 'http://'.$_SERVER['HTTP_HOST'].'/pay/unicardok',
			'URL_RETURN_NO' => 'http://'.$_SERVER['HTTP_HOST'].'/pay/unicardfail'
		);
		if (@$param['MeanType']) $data['MeanType'] = $param['MeanType'];
		if (@$param['EMoneyType']) $data['EMoneyType'] = $param['EMoneyType'];
		if ($card->mail) $data['Email'] = $card->mail;
		if ($card->phone) $data['Phone'] = $card->phone;

		echo $this->gen_form($config->test ? 'https://test.wpay.uniteller.ru/pay/' : 'https://wpay.uniteller.ru/pay/', $data);

		exit();
	}

	function pay_uniteller_result($order, $param = array(), $callback_success = null) {
		$config = $this->config->uniteller ? $this->config->uniteller : new data;
		if ($param) $config->set($param);

		$card = $this->view->basket()->pay_card($order);
		if (@!$card) return false;

		function checkSignature($Order_ID, $Status, $Signature, $password) {
			return ($Signature == strtoupper(md5($Order_ID . $Status . $password)));
		}

		if (checkSignature($param["Order_ID"], $param["Status"], $param["Signature"], $config->password) && ($param['Status'] == 'authorized' || $param['Status'] == 'payed')) {
			if ($callback_success !== null) $callback_success($card);
		}

		exit();
	}

	function pay_uniteller_success($order, $param = array(), $callback_success = null) {
		$config = $this->config->uniteller ? $this->config->uniteller : new data;
		if ($param) $config->set($param);

		$card = $this->view->basket()->pay_card($order);
		if (@!$card) return false;

		if ($callback_success !== null) $callback_success($card);

		exit();
	}

	function pay_uniteller_fail($order, $param = array(), $callback_success = null) {
		$this->pay_uniteller_success($order, $param, $callback_success);
	}
}