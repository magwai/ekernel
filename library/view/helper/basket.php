<?php

class k_view_helper_basket extends view_helper {
	protected $_uid = null;
	protected $_model_item = null;
	protected $_model_order = null;
	protected $_model_order_item = null;
	protected $_model_delivery = null;
	protected $_field_order_item_id = 'itemid';

	public function basket() {
		return $this;
	}

	function __construct() {
		if ($this->_model_item === null) $this->_model_item = new model_catalogitem;
		if ($this->_model_order === null) $this->_model_order = new model_order;
		if ($this->_model_order_item === null) $this->_model_order_item = new model_orderitem;
		if ($this->_model_delivery === null && class_exists('model_delivery')) $this->_model_delivery = new model_delivery;
	}

	function basket_id($create = false) {
		$id = 0;
		$uid = (int)$this->view->user('id');
		$sid = session::get_id();
		$id = $uid ? (int)$this->_model_order->fetch_one('id', array(
			'author' => $uid,
			'finished' => 0,
			'active' => 1
		), array('date' => 'desc')) : 0;
		if (!$id) {
			$id = (int)$this->_model_order->fetch_one('id', array(
				'sid' => $sid,
				'finished' => 0,
				'active' => 1
			), array('date' => 'desc'));
			if ($id && $uid) {
				$this->_model_order->update(array(
					'author' => $uid
				), array(
					'id' => $id
				));
				$this->_model_order->update(array(
					'active' => 0
				), array(
					'`author` = ?' => $uid,
					'`finished` = 0',
					'`active` = 1',
					'`id` != ?' => $id
				));
			}
		}
		if (!$id && $create) {
			$d = array(
				'author' => $uid,
				'sid' => $sid
			);
			if (method_exists($this, 'order_default')) {
				$dd = $this->order_default();
				if ($dd) $d = array_merge($d, $dd);
			}
			$id = $this->_model_order->insert($d);
			if ($id) {
				if (method_exists($this, 'on_create')) {
					$this->on_create($id);
				}
			}
		}
		return $id;
	}

	function add($id, $quant = 1, $ext = array()) {
		$oid = $this->basket_id(true);
		$item = $this->_model_item->fetch_order_card($id);
		if (!$item || !$item->price || !$quant) return false;
		$ex = $this->_model_order_item->fetch_row(array(
			'parentid' => $oid,
			$this->_field_order_item_id => $id
		));
		return $this->_set($id, $ex ? $ex->quant + $quant : $quant, $ext);
	}

	function set($id, $quant, $ext = array()) {
		return $this->_set($id, $quant, $ext);
	}

	function _set($id, $quant, $ext = array()) {
		$oid = $this->basket_id(true);
		$item = $this->_model_item->fetch_order_card($id);
		if (!$item || !$item->price || !$quant) return false;
		$ex = $this->_model_order_item->fetch_row(array(
			'parentid' => $oid,
			$this->_field_order_item_id => $id
		));
		if ($ex) {
			$d = array_merge(array(
				'quant' => $quant
			), $ext);
			$ok = $this->_model_order_item->update($d, array(
				'id' => $ex->id
			));
		}
		else {
			$d = array(
				'parentid' => $oid,
				'quant' => $quant,
				$this->_field_order_item_id => $id,
				'price' => $item->price,
				'orderid' => (int)$this->_model_order_item->fetch_max('orderid') + 1
			);
			if (method_exists($this, 'order_item_default')) {
				$dd = $this->order_item_default();
				if ($dd) $d = array_merge($d, $dd);
			}
			$d = array_merge($d, $ext);
			$ok = $this->_model_order_item->insert($d);
		}
		$this->on_change();
		return $ok;
	}

	function get_percent($v, $total = 1) {
		if (strpos($v, '%') !== false) $v = substr($v, 0, -1) * $total / 100;
		return is_numeric($v)
			? (is_float($v) ? (float)$v : (int)$v)
			: (string)$v;
	}

	function delivery() {
		$delivery = 0;
		if ($this->_model_delivery) {
			$card = $this->card();
			if ($card) {
				$delivery_price = (float)$this->_model_delivery->fetch_one('price', array(
					'id' => $card->delivery
				));
				if ($delivery_price) $delivery = $this->get_percent($delivery_price, $this->price_clean());
			}
		}
		$this->on_delivery($delivery);
		return $delivery;
	}

	function delete($id) {
		$oid = $this->basket_id();
		if (!$oid) return false;
		$ok = $this->_model_order_item->delete(array(
			'parentid' => $oid,
			$this->_field_order_item_id => $id
		));
		$this->on_change();
		return $ok;
	}

	function quant($id = null) {
		$oid = $this->basket_id();
		$select = new database_select();
		$select	->from(array(
					'i' => $this->_model_order_item->name
				), array(
					'quant' => '(SUM(i.quant))'
				))
				->join(array(
					'o' => $this->_model_order->name
				), 'o.id = i.parentid', '')
				->where('o.id = ?', $oid)
				->group('o.id');
		if ($id != null) $select->where('i.'.$this->_field_order_item_id.' = ?', $id);
		return (int)$this->_model_order_item->adapter->fetch_one($select);
	}

	function fetch_list() {
		$oid = $this->basket_id();
		$list = $this->_model_order_item->fetch_all(array(
			'parentid' => $oid
		), 'orderid');
		$ret = array();
		if ($list) {
			foreach ($list as $el) {
				$item = $this->_model_item->fetch_order_card($el->{$this->_field_order_item_id});
				$d = new data(array_merge($el->to_array(), $item->to_array()));
				$ret[] = $d;
			}
		}
		return $ret;
	}

	function price_clean($id = null) {
		$oid = $this->basket_id();
		$select = new database_select();
		$select	->from(array(
					'i' => $this->_model_order_item->name
				), array(
					'price' => '(SUM(i.price * i.quant))'
				))
				->join(array(
					'o' => $this->_model_order->name
				), 'o.id = i.parentid', '')
				->where('o.id = ?', $oid)
				->where('o.finished = ?', 0)
				->where('o.active = ?', 1)
				->group('o.id');
		if ($id != null) $select->where('i.'.$this->_field_order_item_id.' = ?', $id);
		return (int)$this->_model_order_item->adapter->fetch_one($select);
	}

	function price($id = null) {
		$card = $this->card();
		$price = $this->price_clean($id);
		$price += (float)$card->price_delivery;
		$this->on_price($price);
		return $price;
	}

	function save($data) {
		$ok = $this->save_clean($data);
		$this->on_change();
		return $ok;
	}

	function save_clean($data) {
		$oid = $this->basket_id();
		if (!$oid) return false;
		$ok = $this->_model_order->update($data, array(
			'id' => $oid
		));
		return $ok ? $oid : false;
	}

	function finish($data = array()) {
		$data['finished'] = 1;
		$data['date'] = date('Y-m-d H:i:s');
		$oid = $this->save($data);
		return $oid;
	}

	function card() {
		$oid = $this->basket_id();
		$res = $this->_model_order->fetch_row(array(
			'id' => (int)$oid
		));
		return $res ? new data($res) : null;
	}

	function finished_card($oid) {
		$res = $this->_model_order->fetch_row(array(
			'id' => (int)$oid,
			'finished' => 1
		));
		return $res ? new data($res) : null;
	}

	function finished_save($oid, $data) {
		$ok = $this->_model_order->update($data, array(
			'id' => (int)$oid,
			'finished' => 1
		));
		return $ok ? $oid : false;
	}

	function fetch_finished_list($oid) {
		$list = $this->_model_order_item->fetch_all(array(
			'parentid' => $oid
		), 'orderid');
		$ret = array();
		if ($list) {
			foreach ($list as $el) {
				$item = $this->_model_item->fetch_order_card($el->{$this->_field_order_item_id});
				$d = new data(array_merge($el->to_array(), $item->to_array()));
				$ret[] = $d;
			}
		}
		return $ret;
	}

	function finished_price_clean($oid = null, $id = null) {
		$select = new database_select();
		$select	->from(array(
					'i' => $this->_model_order_item->name
				), array(
					'price' => '(SUM(i.price * i.quant))'
				))
				->join(array(
					'o' => $this->_model_order->name
				), 'o.id = i.parentid', '')
				->where('o.id = ?', $oid)
				->where('o.finished = ?', 1)
				->where('o.active = ?', 1)
				->group('o.id');
		if ($id != null) $select->where('i.'.$this->_field_order_item_id.' = ?', $id);
		return (int)$this->_model_order_item->adapter->fetch_one($select);
	}

	function finished_price($oid = null, $id = null) {
		$price = $this->finished_price_clean($oid, $id);
		return $price;
	}
	
	function finished_quant($oid = null, $id = null) {
		$select = new database_select();
		$select	->from(array(
					'i' => $this->_model_order_item->name
				), array(
					'quant' => '(SUM(i.quant))'
				))
				->join(array(
					'o' => $this->_model_order->name
				), 'o.id = i.parentid', '')
				->where('o.id = ?', $oid)
				->group('o.id');
		if ($id != null) $select->where('i.'.$this->_field_order_item_id.' = ?', $id);
		return (int)$this->_model_order_item->adapter->fetch_one($select);
	}

	function on_price(&$price) { }

	function on_delivery(&$delivery) { }

	function on_change() {
		$this->save_clean(array(
			'price_delivery' => $this->delivery()
		));
	}
}