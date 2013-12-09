(function(jQuery) {
	jQuery.fn.extend({
		point: function(opt) {
			var $this = this;
			this.opt = $.extend({
				mark_size: 5,
				mark_color: '#ff0000'
			}, opt);
			this.after('<div class="e-form-point"><img src="' + $this.opt.url + '" alt="" /></div>');
			this.frame = $this.next('.e-form-point');
			this.frame.css({
				position: 'relative'
			});
			this.img = this.frame.find('img');
			this.img.css({
				width: 'auto!important',
				height: 'auto!important',
				minWidth: '0px!important',
				minHeight: '0px!important',
				maxWidth: '10000px!important',
				maxHeight: '10000px!important'
			});
			this.img.click(function(e) {
				var t = $(this);
				var off = t.offset();
				var x = e.pageX - off.left;
				var y = e.pageY - off.top;
				$this.mark_set(x, y);
				return false;
			});
			var val = $this.val();
			if (val && val.length) {
				var p = val.split(';');
				for (var k in p) {
					var pp = p[k].split(',');
					if (pp.length === 2) $this.mark_set(pp[0], pp[1]);
				}
			}
		},
		mark_set: function(x, y) {
			var $this = this;
			this.frame.find('.e-form-point-mark').remove();
			this.frame.append('<a href="javascript:;" class="e-form-point-mark"></a>');
			var mark = this.frame.find('.e-form-point-mark:last');
			mark.css({
				position: 'absolute',
				left: x - Math.floor(this.opt.mark_size / 2),
				top: y - Math.floor(this.opt.mark_size / 2),
				width: this.opt.mark_size,
				height: this.opt.mark_size,
				backgroundColor: this.opt.mark_color,
				borderRadius: Math.floor(this.opt.mark_size / 2)
			});
			$this.input_update();
			mark.click(function() {
				$this.mark_remove($(this));
				$this.input_update();
				return false;
			});
		},
		mark_remove: function(mark) {
			mark.remove();
		},
		input_update: function() {
			var val = [];
			this.frame.find('.e-form-point-mark').each(function() {
				var pos = $(this).position();
				val.push(pos.left + ',' + pos.top);
			});
			this.val(val.join(';'));
		}
	});
})(jQuery);