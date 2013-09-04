<?php

class k_paginator {
	public $source = null;
	public $list = array();
	public $data = array();
	public $perpage = 0;
	public $page = 1;
	public $records = 0;
	public $pages = 0;
	public $first = 1;
	public $last = 1;
	public $prev = 1;
	public $next = 1;
	public $style = 'all';
	public $style_param = array();

	public function __construct($source) {
		if ($source) $this->source = $source;
	}

	public function query() {
		if ($this->source instanceOf database_select) {
			$source_count = clone $this->source;
			$source_count->reset('limit');
			$source_count->parts['join'][0]['cols'] = 'COUNT(*)';
			if (count($source_count->parts['join']) > 1) for ($i = 1; $i < count($source_count->parts['join']); $i++) $source_count->parts['join'][$i]['cols'] = '';
			unset($source_count->parts['order']);
			unset($source_count->parts['group']);
			$this->records = $source_count->adapter->fetch_one($source_count);
			$source_list = clone $this->source;
			$source_list->limit($this->perpage, ($this->page - 1) * $this->perpage);
			$this->data = $source_list->adapter->fetch_all($source_list);
		}
		else {
			$this->records = count($this->source);
			$this->data = $this->records > $this->perpage ? array_slice($this->source, $this->page * $this->perpage, $this->perpage) : $this->source;
		}
		$this->pages = ceil($this->records / $this->perpage);
		$this->first = 1;
		$this->last = $this->pages;
		if ($this->last < 1) $this->last = 1;
		if ($this->page * $this->perpage < $this->records) $this->next = $this->page + 1;
		else $this->next = 0;
		$this->prev = $this->page - 1;
		if ($this->prev < 0) $this->prev = 0;
		$this->list = array();
		if ($this->pages) {
			if ($this->style == 'sliding') {
				$sliding_cnt = @$this->style_param['count'] ? $this->style_param['count'] : 3;
				$cnt_first_orig = $this->page > ($sliding_cnt + 1) ? $this->page - ($sliding_cnt + 1) : 0;
				$cnt_first = $cnt_first_orig > $sliding_cnt ? $sliding_cnt : $cnt_first_orig;
				for ($i = 0; $i < $cnt_first; $i++) $this->list[] = $i + 1;
				if ($cnt_first_orig == ($sliding_cnt + 1)) $this->list[] = ($sliding_cnt + 1);
				else if ($cnt_first_orig > ($sliding_cnt + 1)) $this->list[] = '...';

				$cnt_prev = $this->page - 1;
				if ($cnt_prev > 3) $cnt_prev = $sliding_cnt;
				for ($i = 0; $i < $cnt_prev; $i++) $this->list[] = $this->page - $cnt_prev + $i;
				$this->list[] = $this->page;
				$cnt_next = $this->pages - $this->page;
				if ($cnt_next > 3) $cnt_next = $sliding_cnt;
				for ($i = 0; $i < $cnt_next; $i++) $this->list[] = $this->page + ($i + 1);

				$cnt_last_orig = $this->pages - $this->page - $sliding_cnt;
				$cnt_last = $cnt_last_orig > $sliding_cnt ? $sliding_cnt : $cnt_last_orig;
				if ($cnt_last_orig == ($sliding_cnt + 1)) $this->list[] = $this->pages - $sliding_cnt;
				else if ($cnt_last_orig > ($sliding_cnt + 1)) $this->list[] = '...';
				for ($i = 0; $i < $cnt_last; $i++) $this->list[] = $this->pages - $cnt_last + $i + 1;
			}
			else for ($i = 0; $i < $this->pages; $i++) $this->list[] = $i + 1;

		}
	}
}