(function ($) {
	'use strict';

	var presetLayers = {
		subtle: [{ x: 0, y: 1, blur: 3, spread: 0 }],
		medium: [
			{ x: 0, y: 4, blur: 12, spread: 0 },
			{ x: 0, y: 2, blur: 4, spread: -1 }
		],
		deep: [
			{ x: 0, y: 10, blur: 30, spread: -5 },
			{ x: 0, y: 6, blur: 16, spread: -4 }
		],
		floating: [
			{ x: 0, y: 20, blur: 40, spread: -8 },
			{ x: 0, y: 8, blur: 20, spread: -6 }
		]
	};

	function hexToRgba(hex, opacity) {
		hex = (hex || '#0f172a').replace('#', '');
		if (hex.length === 3) {
			hex = hex.split('').map(function (c) { return c + c; }).join('');
		}
		var r = parseInt(hex.substring(0, 2), 16);
		var g = parseInt(hex.substring(2, 4), 16);
		var b = parseInt(hex.substring(4, 6), 16);
		var alpha = Math.max(0, Math.min(100, parseFloat(opacity || 16))) / 100;
		return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
	}

	function scaleLayers(layers, intensity, multiplier) {
		var scale = Math.max(0.5, Math.min(1.5, (parseFloat(intensity || 100) / 100))) * (multiplier || 1);
		return layers.map(function (layer) {
			return {
				x: Math.round(layer.x * scale),
				y: Math.round(layer.y * scale),
				blur: Math.round(layer.blur * scale),
				spread: Math.round(layer.spread * scale)
			};
		});
	}

	function layersToCss(layers, color, opacity) {
		var rgba = hexToRgba(color, opacity);
		return layers.map(function (layer) {
			return layer.x + 'px ' + layer.y + 'px ' + layer.blur + 'px ' + layer.spread + 'px ' + rgba;
		}).join(', ');
	}

	function getLayers($builder) {
		var preset = $builder.find('.kayan-shadow-preset-select').val();
		if (preset !== 'custom') {
			return presetLayers[preset] || presetLayers.medium;
		}

		var layers = [];
		$builder.find('.kayan-shadow-layer').each(function () {
			layers.push({
				x: parseInt($(this).find('.kayan-shadow-layer-x').val(), 10) || 0,
				y: parseInt($(this).find('.kayan-shadow-layer-y').val(), 10) || 0,
				blur: parseInt($(this).find('.kayan-shadow-layer-blur').val(), 10) || 0,
				spread: parseInt($(this).find('.kayan-shadow-layer-spread').val(), 10) || 0
			});
		});
		return layers.length ? layers : presetLayers.medium;
	}

	function buildCss($builder, multiplier) {
		if (!$builder.find('input[type="checkbox"][name$="[enabled]"]').is(':checked')) {
			return '';
		}
		var layers = scaleLayers(getLayers($builder), $builder.find('.kayan-shadow-intensity-input').val(), multiplier);
		return layersToCss(
			layers,
			$builder.find('.kayan-shadow-color').val(),
			$builder.find('.kayan-shadow-opacity').val()
		);
	}

	function refreshPreview($builder) {
		var css = buildCss($builder, 1) || '0 4px 12px rgba(15,23,42,.16)';
		$builder.find('.kayan-shadow-preview-card').css('box-shadow', css);
		$builder.find('.kayan-shadow-css-output').text(css);

		var tokens = {
			sm: layersToCss(scaleLayers(presetLayers.subtle, $builder.find('.kayan-shadow-intensity-input').val(), 1), $builder.find('.kayan-shadow-color').val(), $builder.find('.kayan-shadow-opacity').val()),
			md: layersToCss(scaleLayers(presetLayers.medium, $builder.find('.kayan-shadow-intensity-input').val(), 1), $builder.find('.kayan-shadow-color').val(), $builder.find('.kayan-shadow-opacity').val()),
			lg: layersToCss(scaleLayers(presetLayers.deep, $builder.find('.kayan-shadow-intensity-input').val(), 1), $builder.find('.kayan-shadow-color').val(), $builder.find('.kayan-shadow-opacity').val()),
			xl: layersToCss(scaleLayers(presetLayers.floating, $builder.find('.kayan-shadow-intensity-input').val(), 1), $builder.find('.kayan-shadow-color').val(), $builder.find('.kayan-shadow-opacity').val())
		};

		$.each(tokens, function (key, value) {
			$builder.closest('.kayan-global-shadows-field').find('.kayan-shadow-token[data-token="' + key + '"] em').text(value);
		});
	}

	function reindexLayers($builder) {
		var inputName = $builder.data('input-name');
		$builder.find('.kayan-shadow-layer').each(function (index) {
			var $layer = $(this);
			$layer.attr('data-layer-index', index);
			$layer.find('.kayan-shadow-layer-x').attr('name', inputName + '[layers][' + index + '][x]');
			$layer.find('.kayan-shadow-layer-y').attr('name', inputName + '[layers][' + index + '][y]');
			$layer.find('.kayan-shadow-layer-blur').attr('name', inputName + '[layers][' + index + '][blur]');
			$layer.find('.kayan-shadow-layer-spread').attr('name', inputName + '[layers][' + index + '][spread]');
		});
	}

	function addLayer($builder, layer) {
		var inputName = $builder.data('input-name');
		var index = $builder.find('.kayan-shadow-layer').length;
		layer = layer || { x: 0, y: 4, blur: 12, spread: 0 };
		var html = ''
			+ '<div class="kayan-shadow-layer" data-layer-index="' + index + '">'
			+ '<span class="kayan-shadow-layer-handle"><i class="fa-solid fa-grip-vertical"></i></span>'
			+ '<label>X</label><input type="number" class="kayan-shadow-layer-x" name="' + inputName + '[layers][' + index + '][x]" value="' + layer.x + '" />'
			+ '<label>Y</label><input type="number" class="kayan-shadow-layer-y" name="' + inputName + '[layers][' + index + '][y]" value="' + layer.y + '" />'
			+ '<label>Blur</label><input type="number" min="0" class="kayan-shadow-layer-blur" name="' + inputName + '[layers][' + index + '][blur]" value="' + layer.blur + '" />'
			+ '<label>Spread</label><input type="number" class="kayan-shadow-layer-spread" name="' + inputName + '[layers][' + index + '][spread]" value="' + layer.spread + '" />'
			+ '<button type="button" class="kayan-shadow-remove-layer"><i class="fa-solid fa-trash"></i></button>'
			+ '</div>';
		$builder.find('.kayan-shadow-layers').append(html);
		refreshPreview($builder);
	}

	function bindBuilder($builder) {
		if (!$builder.length || $builder.data('kayan-shadow-bound')) {
			return;
		}
		$builder.data('kayan-shadow-bound', true);

		$builder.on('change input', '.kayan-shadow-preset-select, .kayan-shadow-target-select, .kayan-shadow-color, .kayan-shadow-opacity, .kayan-shadow-intensity-range, .kayan-shadow-intensity-input, .kayan-shadow-layer-x, .kayan-shadow-layer-y, .kayan-shadow-layer-blur, .kayan-shadow-layer-spread, input[type="checkbox"][name$="[enabled]"]', function () {
			if ($(this).hasClass('kayan-shadow-intensity-range')) {
				$builder.find('.kayan-shadow-intensity-input').val($(this).val());
			}
			if ($(this).hasClass('kayan-shadow-intensity-input')) {
				$builder.find('.kayan-shadow-intensity-range').val($(this).val());
			}
			if ($(this).hasClass('kayan-shadow-preset-select')) {
				var isCustom = $(this).val() === 'custom';
				$builder.closest('.kayan-global-shadows').find('.kayan-shadow-layers-wrap').toggleClass('is-hidden', !isCustom);
			}
			refreshPreview($builder);
		});

		$builder.on('click', '.kayan-shadow-add-layer', function (event) {
			event.preventDefault();
			addLayer($builder);
		});

		$builder.on('click', '.kayan-shadow-remove-layer', function (event) {
			event.preventDefault();
			if ($builder.find('.kayan-shadow-layer').length <= 1) {
				return;
			}
			$(this).closest('.kayan-shadow-layer').remove();
			reindexLayers($builder);
			refreshPreview($builder);
		});

		$builder.on('click', '.kayan-shadow-preset-btn', function (event) {
			event.preventDefault();
			var preset = $(this).data('preset');
			$builder.find('.kayan-shadow-preset-select').val(preset).trigger('change');
		});

		if ($.fn.sortable) {
			$builder.find('.kayan-shadow-layers').sortable({
				handle: '.kayan-shadow-layer-handle',
				update: function () {
					reindexLayers($builder);
					refreshPreview($builder);
				}
			});
		}

		refreshPreview($builder);
	}

	window.initKayanGlobalShadows = function (context) {
		var $scope = context ? $(context) : $(document);
		$scope.find('.kayan-global-shadows').each(function () {
			bindBuilder($(this));
		});
	};

	$(function () {
		initKayanGlobalShadows(document);
	});
})(jQuery);
