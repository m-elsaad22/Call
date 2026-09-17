(function ($) {
	'use strict';

	function parseColors(dataColors) {
		return (dataColors || '').split(',').map(function (color) {
			return $.trim(color);
		}).filter(Boolean);
	}

	function collectStops($builder) {
		var stops = [];
		$builder.find('.kayan-gradient-stop').each(function () {
			var $stop = $(this);
			var color = $.trim($stop.find('.kayan-gradient-stop-color').val());
			var position = parseFloat($stop.find('.kayan-gradient-stop-position').val());
			if (!color) {
				return;
			}
			if (isNaN(position)) {
				position = 0;
			}
			stops.push({
				color: color,
				position: Math.max(0, Math.min(100, position))
			});
		});
		stops.sort(function (a, b) {
			return a.position - b.position;
		});
		return stops;
	}

	function buildCss($builder) {
		var enabled = $builder.find('input[type="checkbox"][name$="[enabled]"]').is(':checked');
		if (!enabled) {
			return '';
		}

		var type = $builder.find('.kayan-gradient-type-select').val() || 'linear';
		var stops = collectStops($builder);
		if (stops.length < 2) {
			return '';
		}

		var parts = stops.map(function (stop) {
			return stop.color + ' ' + stop.position + '%';
		}).join(', ');

		if (type === 'radial') {
			var shape = $builder.find('.kayan-gradient-radial-shape').val() || 'circle';
			var position = $builder.find('.kayan-gradient-radial-position').val() || 'center';
			return 'radial-gradient(' + shape + ' at ' + position + ', ' + parts + ')';
		}

		var angle = parseInt($builder.find('.kayan-gradient-angle-input').val(), 10);
		if (isNaN(angle)) {
			angle = 135;
		}
		return 'linear-gradient(' + angle + 'deg, ' + parts + ')';
	}

	function refreshPreview($builder) {
		var css = buildCss($builder);
		var fallback = 'linear-gradient(135deg, #a03576 0%, #2563eb 100%)';
		var output = css || fallback;
		$builder.find('.kayan-gradient-preview').css('background', output);
		$builder.find('.kayan-gradient-css-output').text(output);
	}

	function reindexStops($builder) {
		var inputName = $builder.data('input-name');
		$builder.find('.kayan-gradient-stop').each(function (index) {
			var $stop = $(this);
			$stop.attr('data-stop-index', index);
			$stop.find('.kayan-gradient-stop-color').attr('name', inputName + '[stops][' + index + '][color]');
			$stop.find('.kayan-gradient-stop-position').attr('name', inputName + '[stops][' + index + '][position]');
		});
	}

	function addStop($builder, color, position) {
		var inputName = $builder.data('input-name');
		var index = $builder.find('.kayan-gradient-stop').length;
		var stopHtml = ''
			+ '<div class="kayan-gradient-stop" data-stop-index="' + index + '">'
			+ '<span class="kayan-gradient-stop-handle"><i class="fa-solid fa-grip-vertical"></i></span>'
			+ '<input type="text" class="ColorViewer kayan-gradient-stop-color" name="' + inputName + '[stops][' + index + '][color]" value="' + (color || '#ffffff') + '" />'
			+ '<input type="number" min="0" max="100" class="kayan-gradient-stop-position" name="' + inputName + '[stops][' + index + '][position]" value="' + (position != null ? position : 50) + '" />'
			+ '<span class="kayan-gradient-stop-unit">%</span>'
			+ '<button type="button" class="kayan-gradient-remove-stop" title="Remove"><i class="fa-solid fa-trash"></i></button>'
			+ '</div>';
		$builder.find('.kayan-gradient-stops').append(stopHtml);
		if ($.fn.colorpicker) {
			$builder.find('.kayan-gradient-stop:last .ColorViewer').colorpicker();
		}
		refreshPreview($builder);
	}

	function bindBuilder($builder) {
		if (!$builder.length || $builder.data('kayan-gradient-bound')) {
			return;
		}
		$builder.data('kayan-gradient-bound', true);

		$builder.on('change input', '.kayan-gradient-type-select, .kayan-gradient-target-select, .kayan-gradient-radial-shape, .kayan-gradient-radial-position, .kayan-gradient-angle-range, .kayan-gradient-angle-input, .kayan-gradient-stop-color, .kayan-gradient-stop-position, input[type="checkbox"][name$="[enabled]"]', function () {
			if ($(this).hasClass('kayan-gradient-angle-range')) {
				$builder.find('.kayan-gradient-angle-input').val($(this).val());
			}
			if ($(this).hasClass('kayan-gradient-angle-input')) {
				$builder.find('.kayan-gradient-angle-range').val($(this).val());
			}
			if ($(this).hasClass('kayan-gradient-type-select')) {
				var isRadial = $(this).val() === 'radial';
				$builder.find('.kayan-gradient-linear-controls').toggleClass('is-hidden', isRadial);
				$builder.find('.kayan-gradient-radial-controls').toggleClass('is-hidden', !isRadial);
			}
			refreshPreview($builder);
		});

		$builder.on('click', '.kayan-gradient-add-stop', function (event) {
			event.preventDefault();
			addStop($builder, '#ffffff', 50);
		});

		$builder.on('click', '.kayan-gradient-remove-stop', function (event) {
			event.preventDefault();
			if ($builder.find('.kayan-gradient-stop').length <= 2) {
				return;
			}
			$(this).closest('.kayan-gradient-stop').remove();
			reindexStops($builder);
			refreshPreview($builder);
		});

		$builder.on('click', '.kayan-gradient-preset', function (event) {
			event.preventDefault();
			var colors = parseColors($(this).data('colors'));
			if (colors.length < 2) {
				return;
			}
			$builder.find('.kayan-gradient-stops').empty();
			colors.forEach(function (color, index) {
				var position = colors.length === 1 ? 0 : Math.round((index / (colors.length - 1)) * 100);
				addStop($builder, color, position);
			});
			reindexStops($builder);
			refreshPreview($builder);
		});

		if ($.fn.sortable) {
			$builder.find('.kayan-gradient-stops').sortable({
				handle: '.kayan-gradient-stop-handle',
				update: function () {
					reindexStops($builder);
					refreshPreview($builder);
				}
			});
		}

		refreshPreview($builder);
	}

	window.initKayanGradientBuilder = function (context) {
		var $scope = context ? $(context) : $(document);
		$scope.find('.kayan-gradient-builder').each(function () {
			bindBuilder($(this));
		});
	};

	$(function () {
		initKayanGradientBuilder(document);
	});
})(jQuery);
