<?php

class k_view_helper_mail extends view_helper {
	public function mail($param) {
		$html = $this->view->render('mail/frame', array(
			'message' => @$param['body'] ? $param['body'] : $this->view->render('mail/'.$param['view'], $param)
		));
		$htmlPart = new Zend\Mime\Part($html);
		$htmlPart->type = 'text/html';
		/*$htmlPart->encoding = Zend\Mime\Mime::ENCODING_BASE64;
		$htmlPart->disposition = Zend\Mime\Mime::DISPOSITION_INLINE;*/
		$body = new Zend\Mime\Message;
		$body->setParts(array($htmlPart));

		$message = new Zend\Mail\Message;
		$message->setEncoding("UTF-8");
		$message->setBody($body);

		$fm = $this->view->translate('site_mail');
		$from = @$param['from'] ? $param['from'] : $this->view->translate('site_mail');
		$to = @$param['to'] ? $param['to'] : $this->view->translate('site_mail');
		$to = preg_split('/(\;|\,)/i', $to);

		$reply_to = @$param['reply_to'];
		$from_name = @$param['from_name'] ? $param['from_name'] : ($from == $fm ? $this->view->translate('site_title') : $from);
		if ($reply_to) $message->setReplyTo(
			$reply_to,
			$from_name
		);
		$message->setFrom(
			$from,
			$from_name
		);
		$tn = @$param['to_name'] ? $param['to_name'] : $to;
		foreach ($to as $n => $el) {
			$el = trim($el);
			if (!$el) continue;
			$tn_el = is_array($tn) ? (isset($tn[$n]) ? $tn[$n] : @$tn[0]) : $tn;
			$message->addTo(
				$el,
				$tn_el
			);
		}
		if (@$param['subject_full']) $message->setSubject(
			$param['subject_full']
		);
		else $message->setSubject(
			$this->view->translate('site_title').($param['subject'] ? ' — '.$param['subject'] : '')
		);

		$ok = true;
		try {
			$tr = null;
			$config = application::get_instance()->config;
			if (@$config['mail']) {
				if (@$config['mail']['transports'] && @$config['mail']['transports']['transport']) {
					foreach ($config['mail']['transports']['transport'] as $k => $v) {
						$class = 'Zend\\Mail\\Transport\\'.ucfirst($v);
						$tr = new $class($config['mail']['transports'][$v]['host'][$k], array(
							'host' => $config['mail']['transports'][$v]['host'][$k],
							'port' => $config['mail']['transports'][$v]['port'][$k],
							'auth' => $config['mail']['transports'][$v]['auth'][$k],
							'username' => $config['mail']['transports'][$v]['username'][$k],
							'password' => $config['mail']['transports'][$v]['password'][$k],
							'ssl' => $config['mail']['transports'][$v]['ssl'][$k]
						));
						try {
							$ok = true;
							$tr->send($message);
							break;
						}
						catch (Exception $e) {
							$ok = false;
						}
					}
					$tr = null;
				}
				else if (@$config['mail']['transport']) {
					$k = $config['mail']['transport'];
					if (@$config['mail'][$k] && @$config['mail'][$k]['host']) {
						try {
							$class = 'Zend\\Mail\\Transport\\'.ucfirst($k);
							$tr = new $class($config['mail']['smtp']['host'], $config['mail'][$k]);
						}
						catch (Exception $e) {
							$tr = null;
							$ok = false;
						}
					}
				}
			}
			else $ok = false;
			if (!$ok) {
				$tr = new Zend\Mail\Transport\Sendmail;
				$tr->send($message);
			}
		}
		catch (Exception $e) {
			$ok = false;
		}
		return $ok;
	}
}