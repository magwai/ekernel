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
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['gid'] = $config->group;
		return $this->request('photos.getAlbums', $param);
	}

	function video_get_albums($param) {
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['owner_id'] = '-'.$config->group;
		return $this->request('video.getAlbums', $param);
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
			'album_id' => $param['aid']
		);
		$config = application::get_instance()->config->vk;
		if ($config->group) $param_upload['group_id'] = $config->group;
		$res = $this->request('photos.getUploadServer', $param_upload);
		if ($res) {
			$ch = curl_init($res->upload_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('file1' => '@'.$param['file']));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res_upload = json_decode(curl_exec($ch));
			if ($res_upload) {
				$param_photo = array(
					'album_id' => $param['aid'],
					'server' => $res_upload->server,
					'photos_list' => $res_upload->photos_list,
					'hash' => $res_upload->hash,
					'caption' => $param['caption']
				);
				if ($config->group) $param_photo['group_id'] = $config->group;
				$res_photo = $this->request('photos.save', $param_photo);
				return @$res_photo[0] ? $res_photo[0] : false;
			}
		}
		return false;
	}

	function video_save($param) {
		if (!@$param['album_id']) return false;
		if (!@$param['file'] && !@$param['link']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['group_id'] = $config->group;
		$res = $this->request('video.save', $param);
		if ($res) {
			$ch = curl_init($res->upload_url);
			if (@$param['file']) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('video_file' => '@'.$param['file']));
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res_upload = json_decode(curl_exec($ch));
			if ($res_upload) {
				return $res;
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

	function video_add_album($param) {
		if (!@$param['title']) return false;
		$config = application::get_instance()->config->vk;
		if ($config->group) $param['group_id'] = $config->group;
		return $this->request('video.addAlbum', $param);
	}

	function request($method, $param = array()) {
		if (time() - $this->last < 4) sleep(4);
		$config = application::get_instance()->config->vk;
		$post = array(
			'v' => 5.17,
			'uid' => $config->user,
			'access_token' => $config->access_token
		);
		$url = $this->api_url.$method;
		if ($param) {
			foreach ($param as $k => $v) {
				if ($k && $v) $post[$k] = $v;
			}
		}
		//echo $url;print_r($post);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$this->last_response = curl_exec($ch);
		echo $this->last_response.'***';
		if ($this->last_response) {
			$this->last_response = json_decode($this->last_response);
			if (@$this->last_response->response) return $this->last_response->response;
		}
		$this->last = time();
		return false;
	}

}