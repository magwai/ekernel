(function(jQuery) {
	jQuery.fn.extend({
		crop: function(opt) {
			var $this = this;
			this.opt = $.extend({
				control_selector: '.e-form-el-control',
				link_selector: '.fileinfo a'
			}, opt);
			this.target = $('input[name="' + this.opt.target + '"]');
			if (!this.target.length) return false;
			this.control = this.target.parents(this.opt.control_selector);
			if (!this.control.length) return false;
			this.link = this.control.find(this.opt.link_selector);
			if (!this.link.length) return false;
			this.link.html('размеры превью');
			var jo = typeof this.opt.jcrop == 'object' ? opt.jcrop : {};
			this.link.fancybox({
				afterShow: function() {
					var img = $('.fancybox-image');
					var i = new Image();
					i.onload = function() {
						var aspect = img.width() / this.width;
						if ($this.length && $this.val()) {
							var cr_val = $this.val().split(',');
							if (cr_val.length == 4) {
								jo.setSelect = [];
								for (var c = 0; c < cr_val.length; c++) jo.setSelect.push(Math.floor(cr_val[c] * aspect));
							}
							//else jo.setSelect = null;
						}
						//else jo.setSelect = null;
						jo.onChange = function(c) {
							$this.val(Math.floor(c.x / aspect) + ',' + Math.floor(c.y / aspect) + ',' + Math.floor(c.x2 / aspect) + ',' + Math.floor(c.y2 / aspect));
						};
						jo.onRelease = function() {
							$this.val('');
						};
						img.Jcrop(jo);
					};
					i.src = img.attr('src');
				}
			});
		}
	});
})(jQuery);