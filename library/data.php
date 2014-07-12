<?php
/**
 * ekernel
 *
 * Copyright (c) 2012 Magwai Ltd. <info@magwai.ru>, http://magwai.ru
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 */

class k_data implements Countable, Iterator, ArrayAccess {
    protected $_data = array();
    protected $_index;
    protected $_count;
    protected $_key;
    protected $_skipNextIteration;

	public function __construct() {
		$a = func_get_args();
        if ($a) foreach ($a as $e) $this->set($e);
    }

    function set($k, $v = null) {
		if (is_array($k) || $k instanceof data) {
			if ($k) foreach ($k as $_k => $_v) {
				if (isset($this->_data[$_k]) && $this->_data[$_k] instanceof data && (is_array($_v) || $_v instanceof data)) $this->_data[$_k]->set($_v);
				else $this->set($_k, $_v);
			}
		}
		else {
			if (is_array($v)) {
				$v = new data($v);
				$v->_key = $k;
			}
			if ($v instanceof data) $v->_key = $k;
			$this->_data[$k] = $v;
			$this->_count = count($this->_data);
		}
		return $this;
	}

	function get($k) {
		if (isset($this->_data[$k])) {
			$ret = $this->_data[$k];
		}
		else $ret = null;
		return $ret;
	}

	function __get($k) {
		return $this->get($k);
	}

	function __set($k, $v = null) {
		$this->set(array($k => $v));
	}

	public function __clone() {
      $array = array();
      foreach ($this->_data as $key => $value) {
          if ($value instanceof data) {
              $array[$key] = clone $value;
          } else {
              $array[$key] = $value;
          }
      }
      $this->_data = $array;
    }

	public function __isset($name) {
        return isset($this->_data[$name]);
    }

    public function __unset($name) {
		unset($this->_data[$name]);
		$this->_count = count($this->_data);
		$this->_skipNextIteration = true;
    }

    public function count() {
        return $this->_count;
    }

    public function current() {
        $this->_skipNextIteration = false;
        return current($this->_data);
    }

    public function key() {
        return key($this->_data);
    }

    public function next() {
        if ($this->_skipNextIteration) {
            $this->_skipNextIteration = false;
            return;
        }
        next($this->_data);
        $this->_index++;
    }

    public function rewind() {
        $this->_skipNextIteration = false;
        reset($this->_data);
        $this->_index = 0;
    }

    public function valid() {
        return $this->_index < $this->_count;
    }

    public function offsetExists($k) {
    	return isset($this->_data[$k]);
    }

	public function offsetGet($k) {
		return $this->get($k);
	}

	public function offsetSet($k, $v) {
		if ($k === null) {
			$k = 0;
			if ($this->_data) foreach ($this->_data as $_k => $_v) if ($k <= $_k) $k = $_k + 1;
		}
		$this->set(array($k => $v));
	}

	public function offsetUnset($k) {
		unset($this->_data[$k]);
		$this->_count = count($this->_data);
		$this->_skipNextIteration = true;
	}

	public function to_array() {
		return $this->_data;
	}
}


