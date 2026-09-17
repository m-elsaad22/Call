(function ($) {
	'use strict';

	function reindexSections($builder) {
		var inputName = $builder.data('input-name');
		$builder.find('.kayan-homepage-section-item').each(function (index) {
			var $item = $(this);
			$item.attr('data-section-index', index);
			$item.find('.kayan-homepage-section-id').attr('name', inputName + '[sections][' + index + '][section_id]');
			$item.find('.kayan-homepage-section-type').attr('name', inputName + '[sections][' + index + '][type]');
			$item.find('.kayan-homepage-section-visible-input').attr('name', inputName + '[sections][' + index + '][visible]');
			$item.find('.kayan-homepage-section-widget-key').attr('name', inputName + '[sections][' + index + '][widget_key]');
			$item.find('.kayan-homepage-section-widget-post').attr('name', inputName + '[sections][' + index + '][widget_post__id]');
			$item.find('.kayan-homepage-section-widget-id').attr('name', inputName + '[sections][' + index + '][widget_id]');
		});
		$builder.find('.kayan-homepage-sections-count').text($builder.find('.kayan-homepage-section-item').length);
	}

	function bindBuilder($builder) {
		if (!$builder.length || $builder.data('kayan-sections-bound')) {
			return;
		}
		$builder.data('kayan-sections-bound', true);

		if ($.fn.sortable) {
			$builder.find('.kayan-homepage-sections-list').sortable({
				handle: '.kayan-homepage-section-handle',
				placeholder: 'kayan-homepage-section-placeholder',
				update: function () {
					reindexSections($builder);
				}
			});
		}

		reindexSections($builder);
	}

	window.initKayanHomepageSectionsOrder = function (context) {
		var $scope = context ? $(context) : $(document);
		$scope.find('.kayan-homepage-sections-order').each(function () {
			bindBuilder($(this));
		});
	};

	$(function () {
		initKayanHomepageSectionsOrder(document);
	});
})(jQuery);
