<?php

class k_acl {
	public $role = array();
	public $resource = array();
	public $allow_role = array();
	public $deny_role = array();
	

	public function add_role($role, $parents = array()) {
		if (!isset($this->role[$role])) $this->role[$role] = array(
			'parents' => array(),
			'children' => array()
		);
		if ($parents) {
			foreach ($parents as $el) {
				if (!isset($this->role[$el])) $this->role[$el] = array(
					'parents' => array(),
					'children' => array()
				);
				if (!in_array($role, $this->role[$el]['children'])) $this->role[$el]['children'][] = $role;
				if (!in_array($el, $this->role[$role]['parents'])) $this->role[$role]['parents'][] = $el;
			}
		}
	}

	public function add_resource($resource, $parent = null) {
		if (!isset($this->resource[$resource])) $this->resource[$resource] = array(
			'parent' => $parent,
			'children' => array()
		);
		if ($parent) {
			if (!isset($this->resource[$parent])) $this->resource[$parent] = array(
				'parent' => null,
				'children' => array()
			);
			if (!in_array($parent, $this->resource[$parent]['children'])) $this->resource[$parent]['children'][] = $parent;
		}
	}

	public function allow($roles = null, $resources = null) {
		if ($roles == null) $roles = array_keys($this->role);
		if (!is_array($roles)) $roles = array($roles);
		if ($resources == null) $resources = array_keys($this->resource);
		if (!is_array($resources)) $resources = array($resources);
		foreach ($roles as $role) {
			foreach ($resources as $resource) {
				if (!isset($this->allow_role[$role])) $this->allow_role[$role] = array();
				$this->allow_role[$role][] = $resource;
			}
		}
	}

	public function deny($roles, $resources) {
		if (!is_array($roles)) $roles = array($roles);
		if (!is_array($resources)) $resources = array($resources);
		foreach ($roles as $role) {
			foreach ($resources as $resource) {
				if (!isset($this->deny_role[$role])) $this->deny_role[$role] = array();
				$this->deny_role[$role][] = $resource;
			}
		}
	}

	public function is_allowed($role, $resource) {
		$checked_roles = array();
		$roles = $this->inner_role($role, $checked_roles);
		if (!in_array($role, $checked_roles)) $roles[] = $role;
	
		$checked_resources = array();
		$resources = $this->inner_resource($resource, $checked_resources);
		if (!in_array($resource, $checked_resources)) $resources[] = $resource;

		$allowed = false;
		foreach ($roles as $el_role) {
			foreach ($resources as $el_resource) {
				if (isset($this->allow_role[$el_role]) && in_array($el_resource, $this->allow_role[$el_role])) {
					$allowed = true;
					break;
				}
			}
		}

		if ($allowed) foreach ($roles as $el_role) {
			foreach ($resources as $el_resource) {
				if (isset($this->deny_role[$el_role]) && in_array($el_resource, $this->deny_role[$el_role])) {
					$allowed = false;
					break;
				}
			}
		}

		return $allowed;
	}

	public function inner_role($role, &$checked = array()) {
		$ret = array();
		if (!in_array($role, $checked)) {
			$checked[] = $role;
			if (isset($this->role[$role])) {
				if ($this->role[$role]['parents']) {
					foreach ($this->role[$role]['parents'] as $parent) {
						$inner = $this->inner_role($parent, $checked);
						if ($inner) $ret += $inner;
						$ret[] = $parent;
					}
				}
				$ret[] = $role;
			}
		}
		return $ret;
	}

	public function inner_resource($resource, &$checked = array()) {
		$ret = array();
		if (!in_array($resource, $checked)) {
			$checked[] = $resource;
			if (isset($this->resource[$resource])) {
				if ($this->resource[$resource]['parent']) {
					$inner = $this->inner_resource($this->resource[$resource]['parent'], $checked);
					if ($inner) $ret += $inner;
				}
				$ret[] = $resource;
			}
		}
		return $ret;
	}
}