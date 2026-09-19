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

	function wrapContentTables() {
		$('.article-body table, .prose table, .wp-block-table > table').each(function () {
			var $t = $(this);
			if ($t.closest('.kayan-table-wrap').length) {
				return;
			}
			$t.wrap('<div class="kayan-table-wrap"></div>');
		});
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

	function overlayListingCards() {
		$('.bcard, article.post.rv').each(function () {
			var card = this;
			if (card.querySelector('.bov')) {
				return;
			}
			var img = card.querySelector('.bimg, .post-img');
			var body = card.querySelector('.bbody, .post-body');
			if (!img || !body) {
				return;
			}
			body.classList.add('bov');
			img.appendChild(body);
			var bg = img.style && img.style.backgroundImage;
			if (bg && !img.querySelector('img')) {
				var m = bg.match(/url\(["']?(.*?)["']?\)/);
				if (m && m[1]) {
					var image = document.createElement('img');
					image.src = m[1];
					image.alt = '';
					img.insertBefore(image, img.firstChild);
					img.style.backgroundImage = '';
				}
			}
		});
	}

	function fillRelatedThumbs() {
		document.querySelectorAll('.side-w .rel').forEach(function (a) {
			if (a.querySelector('.rth img')) {
				return;
			}
			var href = a.getAttribute('href') || '';
			var slug = href.replace(/\/$/, '').split('/').pop();
			if (!slug) {
				return;
			}
			fetch('/wp-json/wp/v2/posts?slug=' + encodeURIComponent(slug) + '&_embed=wp:featuredmedia')
				.then(function (r) { return r.json(); })
				.then(function (items) {
					if (!items || !items[0]) {
						return;
					}
					var media = items[0]._embedded && items[0]._embedded['wp:featuredmedia'] && items[0]._embedded['wp:featuredmedia'][0];
					var url = media && (media.source_url || (media.media_details && media.media_details.sizes && media.media_details.sizes.medium && media.media_details.sizes.medium.source_url));
					if (!url) {
						return;
					}
					var th = a.querySelector('.rth');
					if (!th) {
						return;
					}
					th.innerHTML = '<img src="' + url + '" alt="">';
				})
				.catch(function () {});
		});
	}

	function setHeroSeoExcerpt() {
		var hero = document.querySelector('.phero.compact .psub') || document.querySelector('.phero .psub');
		if (!hero || !document.querySelector('.article-body')) {
			return;
		}
		var meta = document.querySelector('meta[name="description"]');
		var desc = meta ? (meta.getAttribute('content') || '').replace(/\s+/g, ' ').trim() : '';
		if (desc.length >= 40) {
			hero.textContent = desc;
		}
	}

	$(document).ready(function () {
		markSquareFeaturedOnly();
		setHeroSeoExcerpt();
		overlayListingCards();
		fillRelatedThumbs();
		wrapContentTables();
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
