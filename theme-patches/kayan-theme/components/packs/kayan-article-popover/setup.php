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
		if ( stripos( $icon, '<img' ) !== false ) {
			if ( stripos( $icon, 'skip-lazy' ) === false ) {
				$icon = preg_replace( '/<img\b/i', '<img class="skip-lazy" data-no-lazy="1" data-skip-lazy="1"', $icon, 1 );
			}
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
				'src'            => true,
				'width'          => true,
				'height'         => true,
				'alt'            => true,
				'class'          => true,
				'data-no-lazy'   => true,
				'data-skip-lazy' => true,
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

		$wa_href = 'https://wa.me/' . $payload['whatsapp_number'];
		if ( class_exists( 'Rukn_Contact_System' ) && method_exists( 'Rukn_Contact_System', 'resolve' ) ) {
			$resolved = Rukn_Contact_System::resolve( $post_id );
			if ( ! empty( $resolved['wa_message'] ) ) {
				$wa_href .= '?text=' . rawurlencode( $resolved['wa_message'] );
			}
		}

		echo '<div class="kayan-article-popover-trigger" hidden data-scroll-popover="' . esc_attr( $encoded ) . '" data-is-loaded="true" data-kayan-wa-popover="1"></div>';
		?>
<style id="kayan-article-popover-css">
@keyframes kayanWaPopShow{to{opacity:1;visibility:visible;pointer-events:auto}}
.kayan-wa-pop{
	position:fixed!important;inset:0!important;width:100%!important;height:100%!important;
	z-index:2147483000!important;background:#000000e6;opacity:0;visibility:hidden;pointer-events:none;
	animation:kayanWaPopShow .25s ease 2.8s forwards;
}
.kayan-wa-pop.is-open{opacity:1!important;visibility:visible!important;pointer-events:auto!important;animation:none}
.kayan-wa-pop.is-closed{display:none!important;animation:none!important}
.kayan-wa-pop .order-services--overlay{position:absolute;inset:0}
.kayan-wa-pop .order-services--body{
	position:absolute!important;top:50%!important;left:50%!important;right:auto!important;
	transform:translate(-50%,-50%)!important;width:min(400px,calc(100vw - 32px))!important;
	background:#fff!important;border-radius:35px!important;padding:40px!important;
	display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;
	box-shadow:0 24px 60px rgba(0,0,0,.28);z-index:2;
}
.kayan-wa-pop .order-services--closse{
	position:absolute;left:28px;top:28px;font-size:28px;color:#6b7280;cursor:pointer;
	width:36px;height:36px;display:flex;align-items:center;justify-content:center;z-index:3;
}
.kayan-wa-pop .order-services--icon{
	width:100px;height:100px;border-radius:50%;background:#fff;box-shadow:0 10px 28px rgba(0,0,0,.12);
	display:flex;align-items:center;justify-content:center;overflow:hidden;margin:20px 0 28px;padding:12px;
}
.kayan-wa-pop .order-services--icon img{max-width:100%;height:auto;display:block}
.kayan-wa-pop .order-services--info-context{display:flex;flex-direction:column;align-items:center;width:100%;text-align:center}
.kayan-wa-pop .order-services--info-context h2{font-size:26px;line-height:1.4;margin:0 0 12px;color:#0d1728}
.kayan-wa-pop .order-services--info-context p{font-size:17px;line-height:1.6;margin:0;color:#5b6573}
.kayan-wa-pop .popup-boxnumber{display:flex;align-items:center;justify-content:center;width:100%;margin-top:32px}
.kayan-wa-pop .popup-boxnumber > a.order-services-whatsapp{
	flex:1;max-width:100%;margin:0;display:flex;align-items:center;justify-content:center;gap:8px;
	background:#25D366;border:2px solid #25D366;color:#fff;border-radius:12px;padding:15px 18px;font-weight:700;text-decoration:none
}
.kayan-wa-pop .popup-boxnumber > a.order-services-whatsapp:hover{background:transparent;color:#25D366}
.kayan-wa-pop a.order-services-phonenumber{display:none!important}
</style>
<div class="-order-services--single--popoover kayan-wa-pop" data-kayan-wa-only="1" role="dialog" aria-modal="true">
	<div class="order-services--overlay" data-button="closse--order-services"></div>
	<div class="order-services--body">
		<div class="order-services--closse" data-button="closse--order-services" role="button" tabindex="0" aria-label="إغلاق"><i class="fa-solid fa-xmark"></i></div>
		<div class="order-services--icon"><?php echo $payload['popover_call_icon']; ?></div>
		<div class="order-services--info-context">
			<?php if ( $payload['popover_call_title'] !== '' ) : ?>
				<h2><?php echo esc_html( $payload['popover_call_title'] ); ?></h2>
			<?php endif; ?>
			<?php if ( $payload['popover_call_content'] !== '' ) : ?>
				<p><?php echo esc_html( $payload['popover_call_content'] ); ?></p>
			<?php endif; ?>
			<div class="popup-boxnumber">
				<a target="_blank" rel="noopener noreferrer" class="order-services-button order-services-whatsapp -BTN--hoverable" href="<?php echo esc_url( $wa_href ); ?>">
					<i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
					<span>الواتساب</span>
				</a>
			</div>
		</div>
	</div>
</div>
<script id="kayan-article-popover-js">
(function(){
	var box = document.querySelector('.kayan-wa-pop');
	if (!box) { return; }
	function openPop(){ box.classList.add('is-open'); }
	function closePop(){ box.classList.add('is-closed'); box.classList.remove('is-open'); }
	setTimeout(openPop, 2800);
	box.addEventListener('click', function(e){
		var t = e.target;
		if (t && t.closest && t.closest('[data-button="closse--order-services"]')) {
			e.preventDefault();
			closePop();
		}
	});
	window.addEventListener('scroll', function(){
		if (!box.classList.contains('is-closed') && (window.scrollY || document.documentElement.scrollTop) > 80) {
			openPop();
		}
	}, {passive:true});
})();
</script>
		<?php
	}
}

add_action( 'wp_footer', 'kayan_article_popover_render', 99 );
