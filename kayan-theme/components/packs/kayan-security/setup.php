<?php
/**
 * kayan-security — Phase 1 hardening (CSRF/capability/REST/readme/option writes)
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'kayan_security_sensitive_options' ) ) {
	function kayan_security_sensitive_options() {
		return array( 'header___codes', 'open_css' );
	}
}

if ( ! function_exists( 'kayan_security_can_write_sensitive_option' ) ) {
	function kayan_security_can_write_sensitive_option() {
		return is_user_logged_in() && current_user_can( 'manage_options' );
	}
}

if ( ! function_exists( 'kayan_security_guard_sensitive_option' ) ) {
	function kayan_security_guard_sensitive_option( $value, $old_value ) {
		if ( kayan_security_can_write_sensitive_option() ) {
			return $value;
		}
		return $old_value;
	}
}

foreach ( kayan_security_sensitive_options() as $kayan_opt ) {
	add_filter( 'pre_update_option_' . $kayan_opt, 'kayan_security_guard_sensitive_option', 1, 2 );
}

if ( ! function_exists( 'kayan_security_restrict_users_rest' ) ) {
	function kayan_security_restrict_users_rest( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}
		foreach ( array_keys( (array) $endpoints ) as $route ) {
			if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}
}
add_filter( 'rest_endpoints', 'kayan_security_restrict_users_rest', 20 );

if ( ! function_exists( 'kayan_security_block_readme_html' ) ) {
	function kayan_security_block_readme_html() {
		if ( is_admin() ) {
			return;
		}
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$path = wp_parse_url( $uri, PHP_URL_PATH );
		if ( is_string( $path ) && preg_match( '#/readme\.html$#i', $path ) ) {
			status_header( 404 );
			nocache_headers();
			exit;
		}
	}
}
add_action( 'init', 'kayan_security_block_readme_html', 0 );
