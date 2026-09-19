(function ($) {
	function removeContentCallButtons() {
		if (window.kayanShowCallButtons) {
			return;
		}
		$('.order-services-phonenumber, .-callbutton--post-card, a[href^="tel:"].post-card-buttons').remove();
		$('.YC-wigdht-contact-minibox .phonenumber').remove();
		$('.-company-contact-minibox .phonenumber').remove();
		$('.-taxonomy--contact- a[href^="tel:"]').closest('.-taxonimes-').remove();
		$('.-header-call-').remove();
		$('a[href^="tel:"].order-services-button').remove();
	}

	function removeFloatingCallButton() {
		if (window.kayanShowFloatingCallButton !== false) {
			return;
		}
		$('.--YourColor--phone-button').remove();
	}

	function removeCallButtons() {
		removeContentCallButtons();
		removeFloatingCallButton();
	}

	$(document).ready(removeCallButtons);
	$(window).on('scroll', function () {
		setTimeout(removeCallButtons, 50);
	});
	function fillRatingBars() {
		$('[data-progressload]').each(function () {
			var el = $(this);
			var pct = el.data('progressload');
			if (pct === undefined || pct === '') {
				return;
			}
			el.css('width', pct + '%');
		});
	}

	function hideVisitorDates() {
		$('.chip:has(.fa-calendar), .chip:has(.fa-calendar-days)').remove();
		$('.bmeta, .post-date, time.entry-date, time.published, time.updated, .posted-on, .rank-math-breadcrumb time').remove();
		$('.side-w a.rel small, .side-w .rel small').each(function () {
			var t = $(this).text() || '';
			if (/يناير|فبراير|مارس|أبريل|ابريل|مايو|يونيو|يوليو|أغسطس|اغسطس|سبتمبر|أكتوبر|اكتوبر|نوفمبر|ديسمبر|\b20\d{2}\b/.test(t) && !/دقيق/.test(t)) {
				$(this).remove();
			}
		});
	}

	$(document).ready(function () {
		fillRatingBars();
		hideVisitorDates();
	});
	$(document).ajaxComplete(function () {
		fillRatingBars();
		hideVisitorDates();
	});
	$(document).on('click', '.faq-q', function () {
		var item = $(this).closest('.faq-item');
		var list = item.parent();
		var open = item.hasClass('faq-open');
		list.find('.faq-item.faq-open').removeClass('faq-open').find('.faq-a').css('max-height', '');
		if (!open) {
			var ans = item.addClass('faq-open').find('.faq-a');
			if (ans.length) {
				ans.css('max-height', ans[0].scrollHeight + 'px');
			}
		}
	});
})(jQuery);
