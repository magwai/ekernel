<?php

class k_view_helper_mail extends view_helper {
	public function mail($param) {
		$mail = new lib_phpmailer_class;
		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';

		//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments

		$html = $this->view->render('mail/frame', array(
			'message' => @$param['body'] ? $param['body'] : $this->view->render('mail/'.$param['view'], $param)
		));

		$mail->Body    = $html;
		$mail->AltBody = strip_tags($html);

		$fm = $this->view->translate('site_mail');
		$from = @$param['from'] ? $param['from'] : $this->view->translate('site_mail');
		$to = @$param['to'] ? $param['to'] : $this->view->translate('site_mail');
		$to = preg_split('/(\;|\,)/i', $to);

		$reply_to = @$param['reply_to'];
		$from_name = @$param['from_name'] ? $param['from_name'] : ($from == $fm ? $this->view->translate('site_title') : $from);
		if ($reply_to) $mail->addReplyTo($reply_to, $from_name);

		$mail->From = $from;
		$mail->FromName = $from_name;

		$tn = @$param['to_name'] ? $param['to_name'] : $to;
		foreach ($to as $n => $el) {
			$el = trim($el);
			if (!$el) continue;
			$tn_el = is_array($tn) ? (isset($tn[$n]) ? $tn[$n] : @$tn[0]) : $tn;
			$mail->addAddress($el, $tn_el);
		}
		if (@$param['subject_full']) $mail->Subject = $param['subject_full'];
		else $mail->Subject = $this->view->translate('site_title').($param['subject'] ? ' — '.$param['subject'] : '');

		$ok = true;
		try {
			$is_sent = false;
			$config = application::get_instance()->config;
			if (@$config['mail']) {
				if (@$config['mail']['transports'] && @$config['mail']['transports']['transport']) {
					foreach ($config['mail']['transports']['transport'] as $k => $v) {
						$func = 'is'.ucfirst($v);
						$mail->$func();
						$mail->Host = $config['mail']['transports'][$v]['host'][$k];
						$mail->SMTPAuth = $config['mail']['transports'][$v]['auth'][$k] ? true : false;
						$mail->Username = $config['mail']['transports'][$v]['username'][$k];
						$mail->Password = $config['mail']['transports'][$v]['password'][$k];
						$mail->SMTPSecure = $config['mail']['transports'][$v]['ssl'][$k] ? 'ssl' : 'tls';
						$ok = $mail->send();
						if ($ok) {
							$is_sent = true;
							break;
						}
					}
				}
				else if (@$config['mail']['transport']) {
					$k = $config['mail']['transport'];
					if (@$config['mail'][$k] && @$config['mail'][$k]['host']) {
						$func = 'is'.ucfirst($k);
						$mail->$func();
						$mail->Host = $config['mail'][$k]['host'];
						$mail->SMTPAuth = $config['mail'][$k]['auth'] ? true : false;
						$mail->Username = $config['mail'][$k]['username'];
						$mail->Password = $config['mail'][$k]['password'];
						$mail->SMTPSecure = $config['mail'][$k]['ssl'] ? 'ssl' : 'tls';
						$ok = $mail->send();
						if ($ok) $is_sent = true;
					}
				}
			}
			if (!$is_sent) {
				$mail->isMail();
				$ok = $mail->send();
				if ($ok) $is_sent = true;
			}
		}
		catch (Exception $e) {
			$ok = false;
		}
		return $ok;
	}
}