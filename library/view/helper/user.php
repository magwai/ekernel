<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_view_helper_user extends view_helper  {
	private $default_model_user = 'user';
	private $default_model_usersoc = 'user';
	private $default_model_role = 'role';
	private $default_model_rule = 'rule';
	private $default_model_resource = 'resource';
	private $default_model_role2role = 'role2role';
	private $default_model_rule2role = 'rule2role';
	private $default_model_rule2resource = 'rule2resource';

	public $model_user = null;
	public $model_role = null;
	public $model_rule = null;
	public $model_resource = null;
	public $model_role2role = null;
	public $model_rule2role = null;
	public $model_rule2resource = null;
	public $salt = '';
	private $_acl = null;
	protected $_key = 'user';
	protected $_data = null;
	protected $_inited = false;

	public function init() {
		if ($this->_inited) return;
		$this->_inited = true;

		$config = application::get_instance()->config->user;
		$config->model = array();
		$config->model->user = isset($config->model->user) ? $config->model->user : $this->default_model_user;
		$this->model_user = 'model_'.$config->model->user;
		if (class_exists($this->model_user)) {
			$this->model_user = new $this->model_user();
			$this->_key = 'user_'.$this->model_user->name;
		}
		$this->model_usersoc = 'model_'.(isset($config->model->usersoc) ? $config->model->usersoc : $this->default_model_usersoc);
		$this->model_usersoc = class_exists($this->model_usersoc) ? new $this->model_usersoc() : null;
		if ($config->acl) {
			$this->model_role = 'model_'.(isset($config->model->role) ? $config->model->role : $this->default_model_role);
			$this->model_role = class_exists($this->model_role) ? new $this->model_role() : null;
			$this->model_rule = 'model_'.(isset($config->model->rule) ? $config->model->rule : $this->default_model_rule);
			$this->model_rule = class_exists($this->model_rule) ? new $this->model_rule() : null;
			$this->model_resource = 'model_'.(isset($config->model->resource) ? $config->model->resource : $this->default_model_resource);
			$this->model_resource = class_exists($this->model_resource) ? new $this->model_resource() : null;
			$this->model_role2role = 'model_'.(isset($config->model->role2role) ? $config->model->role2role : $this->default_model_role2role);
			$this->model_role2role = class_exists($this->model_role2role) ? new $this->model_role2role() : null;
			$this->model_rule2role = 'model_'.(isset($config->model->rule2role) ? $config->model->rule2role : $this->default_model_rule2role);
			$this->model_rule2role = class_exists($this->model_rule2role) ? new $this->model_rule2role() : null;
			$this->model_rule2resource = 'model_'.(isset($config->model->rule2resource) ? $config->model->rule2resource : $this->default_model_rule2resource);
			$this->model_rule2resource = class_exists($this->model_rule2resource) ? new $this->model_rule2resource() : null;
			$this->init_acl();
		}
		$this->salt = $config->salt;
		$this->login_auto();
	}

	public function is_allowed_by_key($resource_key) {
		$resource = (int)$this->model_resource->fetch_one('id', array(
			'key' => $resource_key
		));
		return $this->is_allowed($resource);
	}

	public function is_allowed_by_role_key($role_key) {
		$role = (int)$this->model_role->fetch_one('id', array(
			'key' => $role_key
		));
		return $role == $this->_data->role;
	}

	public function is_allowed($resource) {
		$role = @(int)$this->_data->role;
		if ($role && $this->_acl->is_allowed($role, $resource)) return true;
		return false;
	}

	public function init_acl() {
		if (!$this->model_role || !$this->model_rule || !$this->model_resource || !$this->model_role2role || !$this->model_rule2role || !$this->model_rule2resource) return false;
		$this->_acl = new acl;

		// Добавляем роли
		$roles = $this->model_role->fetch_col('id');
		if (!$roles) return false;
		foreach ($roles as $el) {
			$select = new database_select();
			$select	->from(array(
						'i' => $this->model_role->name
					), array(
						'i.id'
					))
					->join(array(
						'r' => $this->model_role2role->name
					), 'i.id = r.role', '')
					->group('i.id')
					->where('r.parentid = ?', $el);
			$parents = $this->model_role->adapter->fetch_col($select);
			$this->_acl->add_role($el, $parents);
		}

		// Добавляем ресурсы
		$select = new database_select();
		$select	->from(array(
					'i' => $this->model_resource->name
				), array(
					'i.id'
				))
				->join_left(array(
					'r' => $this->model_resource->name
				), 'i.parentid = r.id', array(
					'parentid' => 'r.id'
				))
				->group('i.id');
		$resources = $this->model_resource->fetch_all($select);
		if (!$resources) return false;
		foreach ($resources as $el) {
			$this->_acl->add_resource($el->id, $el->parentid);
		}

		// Добавляем правила
		$rules = $this->model_rule->fetch_all(null, 'orderid');
		if (!$rules) return false;

		foreach ($rules as $el) {
			$select_1 = new database_select();
			$select_1	->from(array(
						'i' => $this->model_role->name
					), array(
						'i.id'
					))
					->join(array(
						'r' => $this->model_rule2role->name
					), 'i.id = r.role', '')
					->group('i.id')
					->where('r.parentid = ?', $el->id);
			$roles_refer = $this->model_role->adapter->fetch_col($select_1);

			$select_2 = new database_select();
			$select_2	->from(array(
						'i' => $this->model_resource->name
					), array(
						'i.id'
					))
					->join(array(
						'r' => $this->model_rule2resource->name
					), 'i.id = r.resource', '')
					->group('i.id')
					->where('r.parentid = ?', $el->id);
			$resources_refer = $this->model_resource->adapter->fetch_col($select_2);

			$this->_acl->{$el->is_allow ? 'allow' : 'deny'}($roles_refer, $resources_refer);
		}
	}

	public function fetch_profile($id) {
		$data = $this->model_user->fetch_card_by_id($id);
		if ($data) {
			$data = new entity_user($data);
			$data->view = $this->view;
		}
		return $data;
	}


	function login_auto() {
		$session_uid = session::get($this->_key);
		if ($session_uid) $this->login($session_uid);
		if (!$this->_data) {
			$cookie_uid = isset($_COOKIE[$this->_key]) ? $_COOKIE[$this->_key] : null;
			if ($cookie_uid) {
				$id = $this->model_user->fetch_id_by_hash($this->salt, $cookie_uid);
				if ($id) $this->login($id);
			}
		}
	}

	function login($login, $password = null, $remember = false) {
		if ($password === null) {
			$data = $this->fetch_profile($login);
			if ($data) {
				session::set($this->_key, $login);
				$this->_data = $data;
				if ($this->model_user->name != $this->model_usersoc->name) {
					$soc_data = $this->model_usersoc->fetch_row(array(
						'author' => $login,
						'active' => 1
					));
					if ($soc_data) {
						unset($soc_data['id']);unset($soc_data['author']);unset($soc_data['active']);
						$this->_data->set($soc_data);
					}
				}
				if ($remember) setcookie(
					$this->_key,
					sha1($this->_data->profile.$this->_data->login.$this->_data->password.$this->salt),
					time() + 86400 * 30,
					'/'
				);
				return true;
			}
		}
		else {
			$id = $this->model_user->login($login, $password, $this->salt);
			if ($id) {
				$ok = $this->login($id, null, $remember);
				if ($ok && $this->model_user->name != $this->model_usersoc->name) {
					$this->model_usersoc->update(array(
						'active' => 0
					), array(
						'author' => $this->_data->id
					));
				}
				return $ok;
			}
		}
		return false;
	}

	function logout() {
		if (session::get($this->_key)) {
			session::remove($this->_key);
			if (isset($_COOKIE[$this->_key])) {
				unset($_COOKIE[$this->_key]);
				setcookie(
					$this->_key,
					'',
					0,
					'/'
				);
			}
			$this->_data = null;
			return true;
		}
		return false;
	}

	function register($data) {
		if (isset($data['password'])) $data['password'] = $this->password_hash((string)$data['password']);
		$meta = $this->model_user->metadata();
		if ($data) foreach ($data as $k => $v) if (!array_key_exists($k, $meta)) unset($data[$k]);
		return $this->model_user->insert($data);
	}

	function usersoc_register($data) {
		$meta = $this->model_usersoc->metadata();
		if ($data) foreach ($data as $k => $v) if (!array_key_exists($k, $meta)) unset($data[$k]);
		return $this->model_usersoc->usersoc_register($data);
	}

	function update($data, $id = null) {
		if ($id === null && isset($this->_data['id'])) $id = $this->_data['id'];
		$same = $id == $this->_data['id'];
		if (isset($data['password'])) $data['password'] = $this->password_hash((string)$data['password']);
		$meta = $this->model_user->metadata();
		if ($data) foreach ($data as $k => $v) if (!array_key_exists($k, $meta)) unset($data[$k]);
		$res = $this->model_user->update($data, array(
			'id' => (int)$id
		));
		if ($res && $same) {
			$this->_data = $this->fetch_profile($id);
		}
		return $res;
	}

	function ulogin_parse($data) {
		$ret = json_decode($data, true);
		if ($ret && !@$ret['error']) return $ret;
		return array();
	}

	function ulogin_override($data) {
		return $data;
	}

	function ulogin($token) {
		$ok = false;
		$res = @file_get_contents('http://ulogin.ru/token.php?token='.$token);
		$res_decoded = $this->ulogin_parse($res);
		if ($res_decoded && isset($res_decoded['profile'])) {
			$ex_soc = $this->model_usersoc->fetch_card_by_profile((string)@$res_decoded['profile']);
			$ok = $ex_soc ? @(int)$ex_soc[$this->model_usersoc->name == $this->model_user->name ? 'id' : 'author'] : false;
			if (!$ok) {
				$d = $this->ulogin_override($res_decoded);
				$ex = $this->model_user->fetch_card_by_login(@(string)$d['login']);
				if ($ex) $ok = $ex->id;
				else $ok = $this->register($d);
				if ($ok) {
					$d[$this->model_usersoc->name == $this->model_user->name ? 'id' : 'author'] = $ok;
					if (!$ex) $d['confirmed'] = 1;
					$ok = $this->usersoc_register($d);
					if ($ok) $ex_soc = $this->model_usersoc->fetch_card_by_profile((string)@$res_decoded['profile']);
				}
			}
			if ($ok) {
				$ok = $this->login($ok, null, true);
				if ($ok && $ex_soc && $this->model_user->name != $this->model_usersoc->name) {
					$this->model_usersoc->update(array(
						'active' => 0
					), array(
						'author' => $this->_data->id
					));
					$this->model_usersoc->update(array(
						'active' => 1
					), array(
						'id' => $ex_soc['id']
					));
				}
			}
		}
		return $ok;
	}

	public function user($p = null) {
		$this->init();
		if ($p === true) return $this->_data;
		else if ($p !== null) return @$this->_data->$p;
    	return $this;
    }

	public function password_hash($password) {
		return sha1($password.(string)$this->salt);
	}
}