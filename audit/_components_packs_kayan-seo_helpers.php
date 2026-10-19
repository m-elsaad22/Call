<?php
/**
 * kayan-seo / helpers.php
 *
 * KAYAN SEO يعمل طالما الخيار kayan_seo_disable فارغ.
 * إذا كان غير فارغ (مثلاً 1 أو on) → يتوقف KAYAN SEO وتعود واجهة Rank Math.
 */

if ( ! function_exists( 'kayan_seo_is_disabled' ) ) {
	/**
	 * هل تم تعطيل KAYAN SEO؟
	 */
	function kayan_seo_is_disabled() {
		$val = function_exists( 'yc_get_option' )
			? yc_get_option( 'kayan_seo_disable', '' )
			: get_option( 'kayan_seo_disable', '' );
		return ! empty( $val );
	}
}

if ( ! function_exists( 'kayan_seo_is_enabled' ) ) {
	/**
	 * هل KAYAN SEO يعمل؟ (الوضع الافتراضي: نعم)
	 */
	function kayan_seo_is_enabled() {
		return ! kayan_seo_is_disabled();
	}
}

if ( ! function_exists( 'kayan_seo_get_current_url' ) ) {
	function kayan_seo_get_current_url() {
		if ( function_exists( 'kayan_i18n_request_path_relative_to_home' ) ) {
			$rel = kayan_i18n_request_path_relative_to_home();
		} else {
			$path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH );
			$rel  = is_string( $path ) && $path !== '' ? $path : '/';
		}
		if ( $rel === '/' || $rel === '' ) {
			$url = home_url( '/' );
		} else {
			$url = home_url( $rel );
		}
		$url = user_trailingslashit( $url );
		if ( function_exists( 'kayan_i18n_normalize_site_url' ) ) {
			$url = kayan_i18n_normalize_site_url( $url );
		}
		return $url;
	}
}

if ( ! function_exists( 'kayan_seo_canonical_url' ) ) {
	function kayan_seo_canonical_url() {
		if ( is_front_page() || is_home() ) {
			if ( function_exists( 'kayan_i18n_is_english' ) && kayan_i18n_is_english() ) {
				$url = user_trailingslashit( home_url( 'en' ) );
			} else {
				$url = user_trailingslashit( home_url( '/' ) );
			}
		} else {
			$url = kayan_seo_get_current_url();
		}
		if ( function_exists( 'kayan_i18n_normalize_site_url' ) ) {
			$url = kayan_i18n_normalize_site_url( $url );
		}
		return $url;
	}
}
