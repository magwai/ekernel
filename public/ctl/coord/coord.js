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
			var val = this.val();
			if (this.opt.map_type === 'yandex') {
				ymaps.ready(function () {
					this.map = new ymaps.Map(this.opt.id, {
						center: val && val.length ? val.split(',') : this.opt.center,
						zoom: this.opt.zoom
					});
					this.map.events.add('click', function (e) {
						this.mark_remove();
						this.mark_set(e.get('coords'));
						this.input_update();
					}.bind(this));
					this.map.cursors.push('crosshair');
					if (val && val.length) {
						this.mark_set(val.split(','));
					}
				}.bind(this));
			}
			else if (this.opt.map_type === 'google') {
				google.maps.event.addDomListener(window, 'load', function() {
					var val_arr = val && val.length ? val.split(',') : this.opt.center;
					this.map = new google.maps.Map(document.getElementById(this.opt.id), {
						draggableCursor: 'crosshair',
						zoom: this.opt.zoom,
						center: new google.maps.LatLng(val_arr[0], val_arr[1])
					});
					google.maps.event.addListener(this.map, 'click', function(e) {
						this.mark_remove();
						this.mark_set([e.latLng.lat(), e.latLng.lng()]);
						this.input_update();
					}.bind(this));
					if (val && val.length) {
						this.mark_set(val.split(','));
					}
				}.bind(this));
			}
		},
		mark_set: function(geo) {
			if (this.opt.map_type === 'yandex') {
				this.marker = new ymaps.Placemark(geo);
				this.marker.events.add('click', function() {
					this.mark_remove();
				}.bind(this));
				this.map.geoObjects.add(this.marker);
			}
			else if (this.opt.map_type === 'google') {
				this.marker = new google.maps.Marker({
					position: new google.maps.LatLng(geo[0], geo[1]),
					map: this.map
				});
				google.maps.event.addListener(this.marker, 'click', function(e) {
					this.mark_remove();
				}.bind(this));
			}
		},
		mark_remove: function() {
			if (this.marker) {
				if (this.opt.map_type === 'yandex') {
					this.map.geoObjects.remove(this.marker);
				}
				else if (this.opt.map_type === 'google') {
					this.marker.setMap(null);
				}
				this.marker = null;
				this.input_update();
			}
		},
		input_update: function() {
			var val = '';
			if (this.marker) {
				if (this.opt.map_type === 'yandex') {
					val = this.marker.geometry.getCoordinates().join(',');
				}
				else if (this.opt.map_type === 'google') {
					val = this.marker.getPosition();
					val = [val.lat(), val.lng()].join(',');
				}
			}
			this.val(val);
		}
	});
})(jQuery);