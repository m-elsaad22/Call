<?php 
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/kit-pages.php';
require_once __DIR__ . '/kit-booking.php';

function kayan_ui_enqueue_fixes() {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_script(
		'kayan-ui-fixes',
		get_template_directory_uri() . '/components/packs/kayan-ui/kayan-ui-fixes.js',
		array( 'jquery', 'yourcolor-init' ),
		'2027.1.4.20',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kayan_ui_enqueue_fixes', 20 );

if ( ! function_exists( 'kayan_ui_blank_visitor_date' ) ) {
	function kayan_ui_blank_visitor_date( $value ) {
		if ( is_admin() ) {
			return $value;
		}
		return '';
	}
}
add_filter( 'the_date', 'kayan_ui_blank_visitor_date', 99 );
add_filter( 'the_time', 'kayan_ui_blank_visitor_date', 99 );

if ( ! function_exists( 'kayan_ui_article_rating_fallback' ) ) {
	function kayan_ui_article_rating_fallback() {
		if ( is_admin() || ! is_singular( 'post' ) || ! empty( $GLOBALS['kayan_article_rate_rendered'] ) ) {
			return;
		}
		if ( ! function_exists( 'kayan_kit_render_article_rating' ) ) {
			return;
		}
		$post = get_queried_object();
		if ( ! $post || empty( $post->ID ) ) {
			return;
		}
		ob_start();
		kayan_kit_render_article_rating( $post );
		$html = ob_get_clean();
		if ( $html === '' ) {
			return;
		}
		echo '<script id="kayan-article-rate-fallback">(function(){';
		echo 'function place(){if(document.getElementById("kayanArticleRate"))return;var t=document.querySelector(".author-box")||document.querySelector(".article-body")||document.querySelector(".prose");if(t)t.insertAdjacentHTML("afterend",' . wp_json_encode( $html ) . ');}';
		echo 'if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",place);else place();';
		echo '})();</script>';
	}
}
add_action( 'wp_footer', 'kayan_ui_article_rating_fallback', 4 );
