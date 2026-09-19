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
		'2027.1.4.21',
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
