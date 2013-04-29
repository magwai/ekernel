<?php

class k_view_helper_xlist extends view_helper {
	public function xlist($data = array()) {
		if (@$data['fetch']) {
			if (!isset($data['fetch']['data'])) $data['fetch']['data'] = array();
			$data['fetch']['model'] = @$data['fetch']['model']
				?	(is_object($data['fetch']['model'])
						?	$data['fetch']['model']
						:	(class_exists($data['fetch']['model'])
								?	$data['fetch']['model']
								:	'model_'.@$data['fetch']['model']
							)
					)
				:	'';
			$data['fetch']['entity'] = @$data['fetch']['entity']
				?	(class_exists($data['fetch']['entity'])
						?	$data['fetch']['entity']
						:	'entity_'.@$data['fetch']['entity']
					)
				:	($data['fetch']['model']
						?	'entity_'.strtolower(str_ireplace('model_', '', is_object($data['fetch']['model']) ? get_class($data['fetch']['model']) : $data['fetch']['model']))
						:	'none'
					);
			$data['fetch']['param'] = isset($data['fetch']['param'])
				?	(is_array($data['fetch']['param'])
						? $data['fetch']['param']
						: array($data['fetch']['param'])
					)
				:	array();
			if (!isset($data['fetch']['method'])) $data['fetch']['method'] = 'list';
			if ($data['fetch']['model'] && !method_exists($data['fetch']['model'], $data['fetch']['method'])) $data['fetch']['method'] = 'fetch_'.$data['fetch']['method'];
		}

		if (!isset($data['callback'])) $data['callback'] = array();
		if (!isset($data['callback']['empty'])) $data['callback']['empty'] = null;

		if (!@$data['view']) $data['view'] = array();
		if (!isset($data['view']['script'])) $data['view']['script'] = strtolower(str_ireplace('model_', '', $data['fetch']['model'])).'/'.strtolower(str_ireplace('fetch_', '', $data['fetch']['method']));
		if (stripos($data['view']['script'], '/') === false) $data['view']['script'] .= '/index';
		if (!isset($data['view']['param'])) $data['view']['param'] = array();
		if (!isset($data['view']['empty'])) $data['view']['empty'] = true;

		if (@$data['pager']) {
			$data['pager'] = is_array($data['pager']) ? $data['pager'] : array();
			if (!isset($data['pager']['style'])) $data['pager']['style'] = 'all';
			if (!isset($data['pager']['active'])) $data['pager']['active'] = true;
			if (!isset($data['pager']['url'])) $data['pager']['url'] = '';
			if (!isset($data['pager']['page'])) $data['pager']['page'] = @$this->view->page ? $this->view->page : 1;
			if (!isset($data['pager']['perpage'])) $data['pager']['perpage'] = @$this->view->perpage ? $this->view->perpage : 10;
			if (!@$data['pager']['script']) $data['pager']['script'] = 'pager';
			if (stripos($data['pager']['script'], '/') === false) $data['pager']['script'] .= '/index';
			if (!isset($data['pager']['param'])) $data['pager']['param'] = array();
		}

		$class = !$data['fetch']['data'] && $data['fetch']['model']
			? is_object($data['fetch']['model']) ? $data['fetch']['model'] : new $data['fetch']['model']()
			: '';

		$list = $data['fetch']['data']
			?	$data['fetch']['data']
			:	($class
					?	call_user_func_array(
							array(
								$class,
								$data['fetch']['method']
							),
							$data['fetch']['param']
						)
					:	array()
				);

		if (@$data['pager']) {
			$paginator = new paginator($list);
			$paginator->perpage = $data['pager']['perpage'];
			$paginator->page = $data['pager']['page'];
			$paginator->style = $data['pager']['style'];
			$paginator->query();
			$list = $paginator->data;
			$param = array_merge($data['pager']['param'], array('url' => $data['pager']['url']), array(
				'records' => $paginator->records,
				'pages' => $paginator->pages,
				'first' => $paginator->first,
				'last' => $paginator->last,
				'prev' => $paginator->prev,
				'next' => $paginator->next
			));
			$this->view->pager = $this->view->xlist(array(
				'fetch' => array(
					'data' => $paginator->list
				),
				'view' => array(
					'script' => $data['pager']['script'],
					'param' => $param
				)
			));
		}
		else if ($list instanceOf database_select) $list = $class->entity_all($class->adapter->fetch_all($list));

		if (count($list)) {
			if ($data['fetch']['entity'] != 'none') foreach ($list as &$el) { 
				$entity = class_exists($data['fetch']['entity']) ? $data['fetch']['entity'] : 'entity';
				$el = new $entity($el);
				$el->view = $this->view;
			}
		}

		$ret = '';
		if ($list || $data['view']['empty']) {
			$this->view->data = null;
			$this->view->data = $list;
			if (@$data['view']['param']) foreach ($data['view']['param'] as $k => $v) $this->view->$k = $v;
			$ret = $this->view->render($data['view']['script']);
			if (@$data['view']['param']) foreach ($data['view']['param'] as $k => $v) unset($this->view->$k);
			unset($this->view->data);
		}

		if (!$list && $data['callback']['empty']) {
			$f = $data['callback']['empty'];
			$f($this);
		}

		return $ret;
	}
}