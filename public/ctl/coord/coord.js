(function(jQuery) {
	jQuery.fn.extend({
		coord: function(opt) {
			var $this = this;
			this.opt = $.extend({
				id: 'map_' + this.attr('name'),
				width: '100%',
				height: '400px',
				map_type: 'yandex',
				center: [48.712688, 44.513394],
				zoom: 8
			}, opt);
			this.after('<div class="e-form-map" id="' + this.opt.id + '"></div>');
			this.map = null;
			this.map_object = $this.next('.e-form-map');
			this.map_object.css({
				width: this.opt.width,
				height: this.opt.height
			});
			this.marker = null;
			ymaps.ready(function () {
				var val = $this.val();
				this.map = new ymaps.Map(this.opt.id, {
					center: val && val.length ? val.split(',') : this.opt.center,
					zoom: this.opt.zoom
				});
				this.map.controls
					.add('zoomControl')
					.add('typeSelector');
				this.map.events.add('click', function (e) {
					this.mark_remove();
					this.mark_set(e.get('coordPosition'));
					this.input_update();
				}.bind(this));
				this.map.cursors.push('crosshair');
				if (val && val.length) {
					this.mark_set(val.split(','));
				}
			}.bind(this));
		},
		mark_set: function(geo) {
			this.marker = new ymaps.Placemark(geo);
			this.marker.events.add('click', function() {
				this.mark_remove();
			}.bind(this));
			this.map.geoObjects.add(this.marker);
		},
		mark_remove: function() {
			if (this.marker) {
				this.map.geoObjects.remove(this.marker);
				this.marker = null;
				this.input_update();
			}
		},
		input_update: function() {
			this.val(this.marker ? this.marker.geometry.getCoordinates().join(',') : '');
		}
	});
})(jQuery);