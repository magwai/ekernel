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

		if (checkSignature($param["Order_ID"], $param["Status"], $param["Signature"], $config->password) && ($param['Status'] == 'authorized' || $param['Status'] == 'paid')) {
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

	function pay_yandex_form($order, $param = array()) {
		$config = $this->config->yandex ? $this->config->yandex : new data;
		if ($param) $config->set($param);

		$card = $this->view->basket()->pay_card($order);
		if (@!$card) return false;

		$data = array(
			'shopId' => $config->shopId,
			'scid' => $config->scid,
			'sum' => $config->price,
			'customerNumber' => $config->author,
			'paymentType' => $config->paymentType ? $config->paymentType : 'AC',
			'orderNumber' => $card->id,
			'cps_email' => $config->mail
		);

		echo $this->gen_form($config->test ? 'https://demomoney.yandex.ru/eshop.xml' : 'https://money.yandex.ru/eshop.xml', $data);

		exit();
	}

	function pay_yandex_check($order, $param = array()) {
		header('Content-Type: text/xml');
		$res = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

		$config = $this->config->yandex ? $this->config->yandex : new data;
		if (@$param['shopId'] != $config->shopId || @$param['scid'] != $config->scid) {
			echo '<checkOrderResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.@$param['invoiceId'].'" shopId="'.@$param['shopId'].'" message="Неверный магазин" />';
			exit();
		}

		if ($param) $config->set($param);

		$arr = array(
			$config->action,
			$config->orderSumAmount,
			$config->orderSumCurrencyPaycash,
			$config->orderSumBankPaycash,
			$config->shopId,
			$config->invoiceId,
			$config->customerNumber,
			$config->password
		);
		if ($config->md5 != strtoupper(md5(implode(';', $arr)))) {
			echo $res.'<checkOrderResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="1" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Ошибка авторизации" />';
			exit();
		}

		if (!$order) $order = @(int)$param['orderNumber'];
		$card = $this->view->basket()->pay_card($order);
		if (@!$card) {
			echo $res.'<checkOrderResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Заказ не найден" />';
			exit();
		}

		if (number_format(@(float)$config->orderSumAmount, 2, '.', '') != number_format(@(float)$card->total, 2, '.', '')) {
			echo $res.'<checkOrderResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Неверная сумма заказа" />';
			exit();
		}
		if (@(int)$card->payed) {
			echo $res.'<checkOrderResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Заказ был оплачен ранее" />';
			exit();
		}

		echo $res.'<checkOrderResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="0" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" />';
		exit();
	}

	function pay_yandex_result($order, $param = array(), $callback_success = null) {
		$this->pay_yandex_aviso($order, $param, $callback_success);
	}

	function pay_yandex_aviso($order, $param = array(), $callback_success = null) {
		header('Content-Type: text/xml');
		$res = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

		$config = $this->config->yandex ? $this->config->yandex : new data;
		if (@$param['shopId'] != $config->shopId || @$param['scid'] != $config->scid) {
			echo '<paymentAvisoResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.@$param['invoiceId'].'" shopId="'.@$param['shopId'].'" message="Неверный магазин" />';
			exit();
		}

		if ($param) $config->set($param);

		$arr = array(
			$config->action,
			$config->orderSumAmount,
			$config->orderSumCurrencyPaycash,
			$config->orderSumBankPaycash,
			$config->shopId,
			$config->invoiceId,
			$config->customerNumber,
			$config->password
		);
		if ($config->md5 != strtoupper(md5(implode(';', $arr)))) {
			echo $res.'<paymentAvisoResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="1" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Ошибка авторизации" />';
			exit();
		}

		if (!$order) $order = @(int)$param['orderNumber'];
		$card = $this->view->basket()->pay_card($order);
		if (@!$card) {
			echo $res.'<paymentAvisoResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Заказ не найден" />';
			exit();
		}

		if (number_format(@(float)$config->orderSumAmount, 2, '.', '') != number_format(@(float)$card->total, 2, '.', '')) {
			echo $res.'<paymentAvisoResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="100" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" message="Неверная сумма заказа" />';
			exit();
		}
		if (@(int)$card->payed) {
			$callback_success = null;
		}

		if ($callback_success !== null) $callback_success($card);

		echo $res.'<paymentAvisoResponse performedDatetime="'.date('Y-m-d\TH:i:s').'" code="0" invoiceId="'.$config->invoiceId.'" shopId="'.$config->shopId.'" />';
		exit();
	}

	function pay_yandex_success($order, $param = array(), $callback_success = null) {
		$config = $this->config->yandex ? $this->config->yandex : new data;
		if ($param) $config->set($param);

		if (!$order) $order = @(int)$param['orderNumber'];
		$card = $this->view->basket()->pay_card($order);
		if (@!$card) return false;

		if ($callback_success !== null) $callback_success($card);

		exit();
	}

	function pay_yandex_fail($order, $param = array(), $callback_success = null) {
		$this->pay_yandex_success($order, $param, $callback_success);
	}

	function pay_robokassa_form($order, $param = array()) {
		
		$config = $this->config->robokassa ? $this->config->robokassa : new data;
		if ($param) $config->set($param);

		$card = $this->view->basket()->pay_card($order);
		if (@!$card) return false;
		
		$mrh_login = $config->login;
		$mrh_pass1 = $config->password;

		$inv_id = $card->id;

		$inv_desc = "Оплата заказа №" . $inv_id;

		$out_summ = number_format($card->total, 2, '.', '');

		$in_curr = $config->curr ? $config->curr : "";

		$culture = $config->culture ? $config->culture : "ru";

		$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");

		$data = array(
			'MrchLogin' => $mrh_login,
			'OutSum' => $out_summ,
			'InvId' => $inv_id,
			'Desc' => $inv_desc,
			'SignatureValue' => $crc,
			'IncCurrLabel' => $in_curr,
			'Culture' => $culture
		);

		echo $this->gen_form($config->test ? 'http://test.robokassa.ru/Index.aspx' : 'http://robokassa.ru/Index.aspx', $data);

		exit();
		
	}
	
	function pay_robokassa_result($order, $param = array(), $callback_success = null) {
	
		$config = $this->config->robokassa ? $this->config->robokassa : new data;
		if ($param) $config->set($param);
		
		$mrh_pass2 = $config->password2;

		$tm = getdate(time() + 9 * 3600);
		$date = "$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

		$out_summ = $config->OutSum;
		$inv_id = $config->InvId;
		$crc = $config->SignatureValue;

		$crc = strtoupper($crc);

		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));

		if ($my_crc != $crc) {
			echo "Bad sign\n";
			exit();
		}

		$card = $this->view->basket()->pay_card($inv_id);
		if (@!$card) {
			echo "Bad sign\n";
			exit();
		}
		
		if (@(int)$card->payed) {
			$callback_success = null;
		}

		if ($callback_success !== null) $callback_success($card);
		
		echo "OK$inv_id\n";

		exit();

	}
}