<?php

class k_vk {
	public $api_url = 'https://api.vk.com/method/';
	public $last_response = null;
	private $last = null;

	static function get_key() {
		echo @file_get_contents('https://oauth.vk.com/authorize?client_id=3237255&redirect_uri=blank.html&scope=notify,friends,photos,audio,video,docs,notes,pages,status,offers,questions,wall,groups,messages,notifications,stats,ads,offline&display=page&response_type=token');
		/*$config = application::get_instance()->config->vk;
		$res = @file_get_contents('https://oauth.vk.com/access_token?client_id='.$config->app_id.'&client_secret='.$config->secret.'&grant_type=client_credentials');
		if ($res) {
			$res = json_decode($res);
			if (@$res->access_token) return $res->access_token;
		}
		return false;*/
	}

	function photos_get_all($param = array()) {
		if (@$param['offset'] < 0) return false;
		if (!@$param['offset']) $param['offset'] = 0;
		if (!@$param['count']) $param['count'] = 1000000;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		if ($param['count'] > 100) {
			$ret = array();
			$hnd = ceil($param['count'] / 100);
			$param['count'] = 100;
			$offset = $param['offset'];
			for ($i = 0; $i < $hnd; $i++) {
				$param['offset'] = $offset + $i * 100;
				$res = $this->request('photos.getAll', $param);
				if ($res) $ret += $res;
			}
		}
		else return $this->request('photos.getAll', $param);
	}

	function photos_get_albums($param) {
		if (!@$param['aids']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['gid'] = $config->group;
		return $this->request('photos.getAlbums', $param);
	}

	function wall_add_like($param) {
		if (!@$param['post_id']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		return $this->request('wall.addLike', $param);
	}

	function wall_delete_like($param) {
		if (!@$param['post_id']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		return $this->request('wall.deleteLike', $param);
	}

	function wall_post($param) {
		if (!@$param['message'] && !@$param['attachments']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		return $this->request('wall.post', $param);
	}

	function likes_add($param) {
		if (!@$param['type']) return false;
		if (!@$param['item_id']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		return $this->request('likes.add', $param);
	}

	function likes_delete($param) {
		if (!@$param['type']) return false;
		if (!@$param['item_id']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		return $this->request('likes.delete', $param);
	}

	function photos_cover($param) {
		if (!@$param['aid']) return false;
		if (!@$param['pid']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['oid'] = '-'.$config->group;
		return $this->request('photos.makeCover', $param);
	}

	function photos_get($param) {
		if (!@$param['aid']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['gid'] = $config->group;
		else $param['uid'] = $config->user;
		return $this->request('photos.get', $param);
	}

	function photos_add($param) {
		if (!@$param['aid']) return false;
		if (!@$param['file']) return false;
		$param['caption'] = @$param['caption'];
		$param_upload = array(
			'aid' => $param['aid']
		);
		$config = application::get_instance()->config->vk;
		if ($config->group) $param_upload['gid'] = $config->group;
		$res = $this->request('photos.getUploadServer', $param_upload);
		if ($res) {
			$ch = curl_init($res->upload_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('file1' => '@'.$param['file']));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res_upload = json_decode(curl_exec($ch));
			if ($res_upload) {
				$param_photo = array(
					'aid' => $param['aid'],
					'server' => $res_upload->server,
					'photos_list' => $res_upload->photos_list,
					'hash' => $res_upload->hash,
					'caption' => $param['caption']
				);
				if ($config->group) $param_photo['gid'] = $config->group;
				$res_photo = $this->request('photos.save', $param_photo);
				return @$res_photo[0] ? $res_photo[0] : false;
			}
		}
		return false;
	}

	function photos_delete($param) {
		if (!@$param['pid']) return false;
		$config = application::get_instance()->config->vk;
		$param['oid'] = $config->user;
		return $this->request('photos.delete', $param);
	}

	function photos_edit_album($param) {
		if (!@$param['aid']) return false;
		if (!@$param['title']) return false;
		$param['privacy'] = isset($param['privacy']) ? $param['privacy'] : '0';
		$param['comment_privacy'] = isset($param['comment_privacy']) ? $param['comment_privacy'] : '0';
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['oid'] = '-'.$config->group;
		return $this->request('photos.editAlbum', $param);
	}

	function photos_create_album($param) {
		if (!@$param['title']) return false;
		$param['privacy'] = isset($param['privacy']) ? $param['privacy'] : '0';
		$param['comment_privacy'] = isset($param['comment_privacy']) ? $param['comment_privacy'] : '0';
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['gid'] = $config->group;
		return $this->request('photos.createAlbum', $param);
	}

	function request($method, $param = array()) {
		if (time() - $this->last < 4) sleep(4);
		$config = application::get_instance()->config->vk;
		$url = $this->api_url.$method.'?uid='.$config->user.'&access_token='.$config->access_token;
		$url_add = '';
		if ($param) {
			foreach ($param as $k => $v) {
				if ($k && $v) $url_add .= '&'.urlencode($k).'='.urlencode($v);
			}
		}
		$url .= $url_add;
		//echo $url;
		$this->last_response = @file_get_contents($url);
		echo $this->last_response.'***';
		if ($this->last_response) {
			$this->last_response = json_decode($this->last_response);
			if (@$this->last_response->response) return $this->last_response->response;
		}
		$this->last = time();
		return false;
	}

}