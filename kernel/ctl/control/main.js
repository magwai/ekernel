c = {
	url: '/',
	last_index: null
};

c.init = function(d) {
	c = $.extend(c, d);

	$('.c-buttons .c-button').click(function() {
		return c.table_do_action($(this));
	});

	$('.c-table .link').click(function() {
		var tr = $(this).parents('tr:first');
		tr.click();
		var table = tr.parents('table:first');
		if (table.hasClass('c-table-tree')) {
			return c.table_do_action('tree');
		}
		else {
			var o = $('.c-button-top .c-default');
			if (o.length) return c.table_do_action(o);
		}
	});

	$('.c-table tbody tr').click(function() {
		var input = $(this).find('.c-table-cb input');
		$('.table .c-table-cb input').not(input).prop('checked', false);
		input.prop('checked', true);
		c.table_checkbox(input, false);
	});

	$('.c-table tbody .c-table-cb').click(function(event) {
		event.stopPropagation();
		$(this).find('input').click();
		return true;
	});

	$('.c-table tbody .c-table-cb input').click(function(event) {
		event.stopPropagation();
		c.table_checkbox($(this), event.shiftKey);
		return true;
	});

	$('.c-table thead .c-table-cb input').click(function(event) {
		$('.table tbody .c-table-cb input').prop('checked', $(this).prop('checked'));
		c.table_checkbox_highlight(null);
	});

	var fancy = $('.c-fancy');
	if (fancy.length) fancy.fancybox();

	c.notify_get();
};

c.table_checkbox = function(o, is_shift) {
	var list = $('.c-table tbody .c-table-cb input');
	var cur_index = list.index(o);
	if (is_shift) {
		var start = null;
		var finish = null;
		if (cur_index > c.last_index) {
			start = c.last_index;
			finish = cur_index;
		}
		else {
			start = cur_index;
			finish = c.last_index;
		}
		for (var i2 = start; i2 <= finish; i2++) {
			list.eq(i2).prop('checked', true);
		}
	}
	c.last_index = cur_index;
	if (!o.prop('checked')) $('.c-table thead .c-table-cb input').prop('checked', false);
	c.table_checkbox_highlight(o);
}

c.table_checkbox_highlight = function(o) {
	if ((o && o.prop('checked')) || !$('.c-table tbody .info').find('.c-table-cb input').prop('checked')) $('.c-table tbody tr').removeClass('info');
	if (o && o.prop('checked')) o.parents('tr:first').addClass('info');
	if (!$('.c-table tbody .info').length) {
		var list = $('.c-table tbody .c-table-cb input:checked');
		if (list.length) c.table_checkbox_highlight(list.eq(0));
	}
}

c.table_gen_url = function(url, key_id) {
	var ids = [];
	var id = null;
	$('.c-table tbody .c-table-cb input:checked').each(function() {
		var el = $(this).parents('tr:first').data('id');
		if ($(this).parents('tr:first').hasClass('info')) id = el;
		ids.push(el);
	});
	if (ids.length == 1) ids = [];
	return url + (id ? '/' + key_id + '/' + id : '') + (ids.length ? '/ids/' + ids.join(',') : '');
};

c.table_do_action = function(o) {
	var key_id = 'id';
	if (o == 'tree') key_id = 'oid';
	else if (o.hasClass('c-confirm') && !window.confirm(o.text() + '?')) return false;
	window.location = c.table_gen_url(o == 'tree' ? c.url_current : o.attr('href'), key_id);
	return false;
};

c.notify_get = function() {
	$.ajax({
		url: c.url + '/cnotify/read',
		dataType: 'json',
		type: 'get',
		success: function(d) {
			c.info(d);
		}
	});
};

c.notify_mark = function(id) {
	$.ajax({
		url: c.url + '/cnotify/mark',
		dataType: 'json',
		type: 'post',
		data: {
			id: id
		}
	});
};

c.info = function(d) {
	if (!d || !d.length) return;
	for (var i = 0; i < d.length; i++) {
		var opt = {
			text: d[i].title,
			timeout: 4000,
			type: d[i].style,
			dismissQueue: true
		};
		opt.callback = {
			onShow: function() {
				c.notify_mark(d[i].id);
			}
		};
		noty(opt);
	}
};

c.filter_change = function(o, e) {
	var add = '';
	var go = false;
	if (o.tagName == 'SELECT') {
		add = o.value;
		go = true;
	}
	else if (e.keyCode == 13) {
		var val = encodeURIComponent(o.value);
		var parent = $(o).parents('.c-table-filter-range');
		if (parent.length) {
			var inp = [parent.find('input:first'), parent.find('input:last')];
			var vals = [inp[0].val(), inp[1].val()];
			if (!vals[0]) vals[0] = String(inp[0].data('default'));
			if (!vals[1]) vals[1] = String(inp[1].data('default'));
			if (vals[0] != inp[0].data('default') || vals[1] != inp[1].data('default')) add = vals.join(',');
		}
		else if (val.length) add = val;
		go = true;
	}
	if (go) {
		var r = new RegExp('\\/search\\_' + $(o).data('field') + '\\/.*?(\\/|$)', 'gi');
		window.location = (c.url_current.replace(r, '/').replace(/^(.*)\/$/gi, '$1') + (add.length ? ('/search_' + $(o).data('field') + '/' + add) : '')).replace(/\/index$/gi, '');
	}
};