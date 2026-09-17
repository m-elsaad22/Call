<?
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/schema-extensions.php';
require_once __DIR__ . '/rank-math-bridge.php';
require_once __DIR__ . '/frontend.php';
require_once __DIR__ . '/compatibility.php';
require_once __DIR__ . '/routes.php';
require_once __DIR__ . '/dashboard.php';
require_once __DIR__ . '/schema-output.php';

add_action( 'wp_head', 'kayan_seo_output_schema_graph', 2 );

function kayan_seo_send_404_status() {
	if ( is_404() ) {
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'wp', 'kayan_seo_send_404_status', 1 );

function kayan_seo_register_core_sitemaps() {
	if ( ! kayan_seo_is_enabled() ) {
		return;
	}
	add_filter( 'wp_sitemaps_enabled', '__return_true' );
}
add_action( 'init', 'kayan_seo_register_core_sitemaps', 5 );
