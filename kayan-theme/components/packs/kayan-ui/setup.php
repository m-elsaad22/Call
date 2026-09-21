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
		'2027.1.4.27',
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

if ( ! function_exists( 'kayan_drain_sync_seo_titles' ) ) {
	function kayan_drain_sync_seo_titles() {
		if ( ! function_exists( 'kayan_is_drain_article' ) || ! function_exists( 'kayan_drain_build_seo_title' ) ) {
			return 0;
		}
		global $wpdb;
		$ids = $wpdb->get_col(
			"SELECT ID FROM {$wpdb->posts} WHERE post_status='publish' AND post_type IN ('post','page') AND (
				post_title LIKE '%تسليك%' OR post_name LIKE '%sewer%' OR post_name LIKE '%sewage%' OR post_name LIKE '%drainage%'
			)"
		);
		$n = 0;
		foreach ( $ids as $id ) {
			$post = get_post( (int) $id );
			if ( ! $post || ! kayan_is_drain_article( $post ) ) {
				continue;
			}
			$next = kayan_drain_build_seo_title( $post );
			$cur  = (string) get_post_meta( $post->ID, 'rank_math_title', true );
			if ( strpos( $cur, '0541673020' ) !== false ) {
				continue;
			}
			update_post_meta( $post->ID, 'rank_math_title', $next );
			$n++;
		}
		return $n;
	}
}

add_action( 'init', function () {
	if ( is_admin() || empty( $_GET['kayan_drain_seo'] ) ) {
		return;
	}
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$n = kayan_drain_sync_seo_titles();
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo 'drain seo titles updated ' . (int) $n;
	exit;
}, 2 );

add_action( 'wp', function () {
	if ( is_admin() || ! is_singular() || ! function_exists( 'kayan_is_drain_article' ) ) {
		return;
	}
	$post = get_queried_object();
	if ( ! ( $post instanceof WP_Post ) || ! kayan_is_drain_article( $post ) ) {
		return;
	}
	$cur  = (string) get_post_meta( $post->ID, 'rank_math_title', true );
	if ( strpos( $cur, '0541673020' ) !== false ) {
		return;
	}
	update_post_meta( $post->ID, 'rank_math_title', kayan_drain_build_seo_title( $post ) );
}, 20 );
