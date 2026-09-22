<?php
/**
 * Portable language/hreflang boot. Loaded from functions.php so incomplete
 * country copies and stale OPcache of helpers.php still get a single source
 * of html lang + hreflang for THIS WordPress only.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'kayan_i18n_boot_rank_math_present' ) ) {
	function kayan_i18n_boot_rank_math_present() {
		return defined( 'RANK_MATH_VERSION' )
			|| class_exists( 'RankMath' )
			|| function_exists( 'rank_math' );
	}
}

if ( ! function_exists( 'kayan_i18n_boot_other_hreflang' ) ) {
	function kayan_i18n_boot_other_hreflang() {
		if ( function_exists( 'pll_languages_list' ) ) {
			$list = pll_languages_list();
			if ( is_array( $list ) && count( $list ) >= 2 ) {
				return true;
			}
		}
		if ( function_exists( 'has_action' ) && ( has_action( 'rank_math/head' ) || has_action( 'rank_math/opengraph/facebook' ) ) ) {
			return true;
		}
		$rm = kayan_i18n_boot_rank_math_present();
		if ( $rm && ( ! function_exists( 'kayan_seo_is_enabled' ) || ! kayan_seo_is_enabled() ) ) {
			return true;
		}
		if ( function_exists( 'kayan_seo_is_disabled' ) && kayan_seo_is_disabled() && $rm ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'kayan_i18n_render_hreflang_single_source' ) ) {
	function kayan_i18n_render_hreflang_single_source() {
		if ( kayan_i18n_boot_other_hreflang() ) {
			return;
		}
		if ( function_exists( 'kayan_i18n_render_hreflang' ) ) {
			kayan_i18n_render_hreflang();
		}
	}
}

remove_action( 'wp_head', 'kayan_i18n_render_hreflang', 2 );
add_action( 'wp_head', 'kayan_i18n_render_hreflang_single_source', 2 );

if ( ! function_exists( 'kayan_i18n_boot_rewrite_html_lang' ) ) {
	function kayan_i18n_boot_rewrite_html_lang( $html ) {
		if ( ! is_string( $html ) || $html === '' ) {
			return $html;
		}
		if ( ! function_exists( 'kayan_i18n_get_html_attrs' ) ) {
			return $html;
		}
		return preg_replace( '/<html\b[^>]*>/i', '<html ' . kayan_i18n_get_html_attrs() . '>', $html, 1 );
	}
}

if ( ! function_exists( 'kayan_i18n_boot_start_html_lang_buffer' ) ) {
	function kayan_i18n_boot_start_html_lang_buffer() {
		if ( is_admin() || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) {
			return;
		}
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) $_SERVER['REQUEST_URI'] : '';
		if ( false !== strpos( $uri, '/wp-json/' ) || false !== strpos( $uri, '/wp-admin/' ) ) {
			return;
		}
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return;
		}
		if ( ! empty( $GLOBALS['kayan_i18n_html_buffering'] ) ) {
			return;
		}
		$GLOBALS['kayan_i18n_html_buffering'] = true;
		ob_start( 'kayan_i18n_boot_rewrite_html_lang' );
	}
}
add_action( 'init', 'kayan_i18n_boot_start_html_lang_buffer', 0 );
add_action( 'template_redirect', 'kayan_i18n_boot_start_html_lang_buffer', 0 );
add_action( 'wp', 'kayan_i18n_boot_start_html_lang_buffer', 0 );
