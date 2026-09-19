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
		$('.phero .chip, .hero-proof .chip').each(function () {
			var t = $(this).text() || '';
			if (/يناير|فبراير|مارس|أبريل|ابريل|مايو|يونيو|يوليو|أغسطس|اغسطس|سبتمبر|أكتوبر|اكتوبر|نوفمبر|ديسمبر|\b20\d{2}\b/.test(t)) {
				$(this).remove();
			}
		});
		$('.bmeta, .post-date, time.entry-date, time.published, time.updated, .posted-on, .rank-math-breadcrumb time').remove();
		$('.side-w a.rel small, .side-w .rel small').each(function () {
			var t = $(this).text() || '';
			if (/يناير|فبراير|مارس|أبريل|ابريل|مايو|يونيو|يوليو|أغسطس|اغسطس|سبتمبر|أكتوبر|اكتوبر|نوفمبر|ديسمبر|\b20\d{2}\b/.test(t) && !/دقيق/.test(t)) {
				$(this).remove();
			}
		});
		$('#kayanArticleRate, .kayan-article-rate').remove();
	}

	function isSkippedLeadParagraph(el, text) {
		if (!text || text.length < 50) {
			return true;
		}
		if (el.closest('.wp-caption, figure, .article-featured')) {
			return true;
		}
		if (/\[caption|كتب هذا المقال|آخر تحديث|كتب بواسطة|Last updated|Written by/.test(text)) {
			return true;
		}
		if (el.querySelector('img') && text.length < 120) {
			return true;
		}
		return false;
	}

	function moveLeadToHeroOnce() {
		if (window.__kayanLeadOnce) {
			return;
		}
		var hero = document.querySelector('.phero .psub');
		var body = document.querySelector('.article-body .prose + .prose') || document.querySelector('.article-body .prose');
		if (!body) {
			return;
		}
		var ps = body.querySelectorAll('p');
		var lead = null;
		for (var i = 0; i < ps.length; i++) {
			var t = (ps[i].innerText || '').replace(/\s+/g, ' ').trim();
			if (!isSkippedLeadParagraph(ps[i], t)) {
				lead = ps[i];
				break;
			}
		}
		if (!lead) {
			return;
		}
		window.__kayanLeadOnce = true;
		var text = (lead.innerText || '').replace(/\s+/g, ' ').trim();
		if (hero) {
			hero.textContent = text;
		}
		if (lead.parentNode) {
			lead.parentNode.removeChild(lead);
		}
		var h1 = body.querySelector('h1');
		var heroH = document.querySelector('.phero h1');
		if (h1 && heroH) {
			var a = (h1.innerText || '').replace(/\s+/g, '');
			var b = (heroH.innerText || '').replace(/\s+/g, '');
			if (a && b && (a === b || a.indexOf(b.slice(0, 18)) === 0 || b.indexOf(a.slice(0, 18)) === 0)) {
				h1.remove();
			}
		}
	}

	function markSquareFeaturedOnly() {
		var first = document.querySelector('.article-body > .prose:first-child');
		var next = document.querySelector('.article-body > .prose + .prose');
		if (!first || !next || first.classList.contains('article-featured')) {
			return;
		}
		if (first.querySelector('img') && !first.querySelector('p, h1, h2, h3, ul, ol')) {
			first.classList.add('article-featured');
		}
	}

	$(document).ready(function () {
		markSquareFeaturedOnly();
		moveLeadToHeroOnce();
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
