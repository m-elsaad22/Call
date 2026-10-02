<?php
/**
 * Article service POPUP (شعار بوبا).
 *
 * The theme JS (SingularPopOver) only runs when [data-scroll-popover] exists
 * and the visitor has scrolled 800px. That trigger was never printed, so the
 * popup never appeared. This pack prints it on singular posts/pages and also
 * opens the popup a few seconds after entry.
 *
 * WhatsApp only — phonenumber is never included, so the Call button is not built.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'kayan_article_popover_digits' ) ) {
	function kayan_article_popover_digits( $raw ) {
		return preg_replace( '/[^0-9]/', '', (string) $raw );
	}
}

if ( ! function_exists( 'kayan_article_popover_option_group' ) ) {
	function kayan_article_popover_option_group() {
		$group = function_exists( 'yc_get_option' ) ? yc_get_option( 'post__popover__data' ) : get_option( 'post__popover__data' );
		if ( ! is_array( $group ) || empty( $group ) ) {
			$group = get_option( 'site_1_post__popover__data' );
		}
		return is_array( $group ) ? $group : array();
	}
}

if ( ! function_exists( 'kayan_article_popover_payload' ) ) {
	function kayan_article_popover_payload( $post_id ) {
		$defaults = kayan_article_popover_option_group();
		$local    = $post_id ? get_post_meta( $post_id, 'post__popover__data', true ) : array();
		if ( ! is_array( $local ) ) {
			$local = array();
		}

		$title = '';
		if ( ! empty( $local['popover_call_title'] ) ) {
			$title = $local['popover_call_title'];
		} elseif ( ! empty( $defaults['popover_call_title'] ) ) {
			$title = $defaults['popover_call_title'];
		} else {
			$title = 'احجز خدمتك الآن عن طريق واتساب!';
		}

		$content = '';
		if ( ! empty( $local['popover_call_content'] ) ) {
			$content = $local['popover_call_content'];
		} elseif ( ! empty( $defaults['popover_call_content'] ) ) {
			$content = $defaults['popover_call_content'];
		} else {
			$content = 'وأحصل على خصم 30%';
		}

		$icon = '';
		foreach ( array( $local['popover_call_icon'] ?? '', $defaults['popover_call_icon'] ?? '' ) as $candidate ) {
			$candidate = trim( (string) $candidate );
			if ( $candidate !== '' && stripos( $candidate, '<img' ) !== false ) {
				$icon = $candidate;
				break;
			}
			if ( $icon === '' && $candidate !== '' ) {
				$icon = $candidate;
			}
		}
		if ( $icon === '' ) {
			$icon = '<img src="https://rukn-eltatawer.com/wp-content/uploads/icon/w0.png" width="280" alt="">';
		}

		$wa = '';
		if ( class_exists( 'Rukn_Contact_System' ) && method_exists( 'Rukn_Contact_System', 'resolve' ) ) {
			$resolved = Rukn_Contact_System::resolve( $post_id );
			if ( ! empty( $resolved['wa_show'] ) && ! empty( $resolved['wa_number'] ) ) {
				$wa = $resolved['wa_number'];
			}
		}
		if ( $wa === '' && $post_id ) {
			$wa = get_post_meta( $post_id, 'whatsapp_number', true );
		}
		if ( $wa === '' ) {
			$wa = get_option( 'whatsapp_number' );
		}
		if ( $wa === '' ) {
			$wa = '971586634710';
		}

		$icon_allowed = array(
			'img' => array(
				'src'    => true,
				'width'  => true,
				'height' => true,
				'alt'    => true,
				'class'  => true,
			),
			'i'   => array(
				'class' => true,
			),
		);

		return array(
			'popover_call_title'   => wp_strip_all_tags( $title ),
			'popover_call_content' => wp_strip_all_tags( $content ),
			'popover_call_icon'    => wp_kses( $icon, $icon_allowed ),
			'whatsapp_number'      => kayan_article_popover_digits( $wa ),
		);
	}
}

if ( ! function_exists( 'kayan_article_popover_should_show' ) ) {
	function kayan_article_popover_should_show( $post_id ) {
		if ( ! is_singular( array( 'post', 'page' ) ) ) {
			return false;
		}
		if ( function_exists( 'yc_get_option' ) && ! empty( yc_get_option( 'hide__post__popover' ) ) ) {
			return false;
		}
		if ( $post_id && ! empty( get_post_meta( $post_id, 'hide__single__popover', true ) ) ) {
			return false;
		}
		$payload = kayan_article_popover_payload( $post_id );
		return $payload['whatsapp_number'] !== '';
	}
}

if ( ! function_exists( 'kayan_article_popover_render' ) ) {
	function kayan_article_popover_render() {
		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}
		$post_id = get_queried_object_id();
		if ( ! kayan_article_popover_should_show( $post_id ) ) {
			return;
		}

		$payload = kayan_article_popover_payload( $post_id );
		$encoded = base64_encode( wp_json_encode( $payload, JSON_UNESCAPED_UNICODE ) );

		echo '<div class="kayan-article-popover-trigger" hidden data-scroll-popover="' . esc_attr( $encoded ) . '" data-kayan-wa-popover="1"></div>';

		$wa_href = 'https://wa.me/' . $payload['whatsapp_number'];
		if ( class_exists( 'Rukn_Contact_System' ) && method_exists( 'Rukn_Contact_System', 'resolve' ) ) {
			$resolved = Rukn_Contact_System::resolve( $post_id );
			if ( ! empty( $resolved['wa_message'] ) ) {
				$wa_href .= '?text=' . rawurlencode( $resolved['wa_message'] );
			}
		}

		$cfg = array(
			'delay'   => 3500,
			'title'   => $payload['popover_call_title'],
			'content' => $payload['popover_call_content'],
			'icon'    => $payload['popover_call_icon'],
			'wa'      => $wa_href,
			'label'   => 'الواتساب',
		);
		?>
<style id="kayan-article-popover-css">
.-order-services--single--popoover a.order-services-phonenumber{display:none!important}
.-order-services--single--popoover .order-services--icon img{max-width:100%;height:auto;display:block}
.-order-services--single--popoover .popup-boxnumber{justify-content:center}
.-order-services--single--popoover .popup-boxnumber > a.order-services-whatsapp{flex:1;max-width:100%;margin-inline-end:0}
</style>
<script id="kayan-article-popover-js">
(function(){
	var cfg = <?php echo wp_json_encode( $cfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?>;
	var shown = false;
	function markTrigger(){
		var nodes = document.querySelectorAll('[data-scroll-popover]');
		for (var i=0;i<nodes.length;i++){
			nodes[i].setAttribute('data-is-loaded','true');
			if (window.jQuery) { window.jQuery(nodes[i]).data('is-loaded','true'); }
		}
	}
	function closePop(el){
		var box = el && el.closest ? el.closest('.-order-services--single--popoover') : document.querySelector('.-order-services--single--popoover');
		if (box && box.parentNode) { box.parentNode.removeChild(box); }
	}
	function showPop(){
		if (shown) { return; }
		if (document.querySelector('.-order-services--single--popoover')) { shown = true; markTrigger(); return; }
		shown = true;
		markTrigger();
		var wrap = document.createElement('div');
		wrap.className = '-order-services--single--popoover';
		wrap.setAttribute('data-kayan-wa-only','1');
		wrap.innerHTML =
			'<div class="order-services--overlay" data-button="closse--order-services"></div>' +
			'<div class="order-services--body">' +
				'<div class="order-services--closse" data-button="closse--order-services" role="button" aria-label="إغلاق"><i class="fa-solid fa-xmark"></i></div>' +
				'<div class="order-services--icon">' + (cfg.icon || '') + '</div>' +
				'<div class="order-services--info-context">' +
					(cfg.title ? '<h2>' + cfg.title + '</h2>' : '') +
					(cfg.content ? '<p>' + cfg.content + '</p>' : '') +
					'<div class="popup-boxnumber">' +
						'<a target="_blank" rel="noopener" class="order-services-button order-services-whatsapp -BTN--hoverable" href="' + cfg.wa + '">' +
							'<i class="fa-brands fa-whatsapp"></i><span>   ' + cfg.label + '</span>' +
						'</a>' +
					'</div>' +
				'</div>' +
			'</div>';
		document.body.appendChild(wrap);
		wrap.addEventListener('click', function(e){
			var t = e.target;
			if (t && t.closest && t.closest('[data-button="closse--order-services"]')) {
				e.preventDefault();
				closePop(t);
			}
		});
	}
	function boot(){
		setTimeout(showPop, cfg.delay || 3500);
		window.addEventListener('scroll', function(){
			if (!shown && (window.scrollY || document.documentElement.scrollTop) > 120) {
				showPop();
			}
		}, {passive:true});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
</script>
		<?php
	}
}

add_action( 'wp_footer', 'kayan_article_popover_render', 40 );
