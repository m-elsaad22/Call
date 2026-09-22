<?php
# Optional pack files: independent WP copies may not ship the full i18n set.
# Missing files must not fatal the theme (REST, head, or front).
foreach ( array( 'countries.php', 'strings.php', 'content-map.php' ) as $kayan_i18n_dep ) {
	$kayan_i18n_dep_path = __DIR__ . '/' . $kayan_i18n_dep;
	if ( is_readable( $kayan_i18n_dep_path ) ) {
		require_once $kayan_i18n_dep_path;
	}
}

if ( ! function_exists( 'kayan_i18n_get_countries' ) ) {
	function kayan_i18n_get_countries() {
		return array();
	}
}

if ( ! function_exists( 'kayan_i18n_is_enabled' ) ) {
	function kayan_i18n_is_enabled() {
		return empty( yc_get_option( 'kayan_i18n_disable' ) );
	}
}

if ( ! function_exists( 'kayan_i18n_get_request_path' ) ) {
	function kayan_i18n_get_request_path() {
		$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
		return is_string( $path ) ? $path : '/';
	}
}

/**
 * Path of THIS WordPress install from home_url(), e.g. '' or '/xx'.
 * Independent subdirectory sites must not treat other prefixes as same-WP routes.
 */
if ( ! function_exists( 'kayan_i18n_site_home_path' ) ) {
	function kayan_i18n_site_home_path() {
		$path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		if ( ! is_string( $path ) || $path === '' || $path === '/' ) {
			return '';
		}
		return '/' . trim( $path, '/' );
	}
}

if ( ! function_exists( 'kayan_i18n_request_path_relative_to_home' ) ) {
	function kayan_i18n_request_path_relative_to_home( $path = null ) {
		$path = null === $path ? kayan_i18n_get_request_path() : $path;
		if ( ! is_string( $path ) || $path === '' ) {
			$path = '/';
		}
		$home = kayan_i18n_site_home_path();
		if ( $home !== '' ) {
			if ( $path === $home || $path === $home . '/' ) {
				return '/';
			}
			if ( strpos( $path, $home . '/' ) === 0 ) {
				$rel = substr( $path, strlen( $home ) );
				return ( $rel === '' || $rel === false ) ? '/' : $rel;
			}
		}
		return $path[0] === '/' ? $path : '/' . $path;
	}
}

if ( ! function_exists( 'kayan_i18n_normalize_site_url' ) ) {
	function kayan_i18n_normalize_site_url( $url ) {
		if ( ! is_string( $url ) || $url === '' ) {
			return $url;
		}
		$home_path = kayan_i18n_site_home_path();
		if ( $home_path === '' ) {
			return $url;
		}
		$parts = wp_parse_url( $url );
		if ( ! is_array( $parts ) || empty( $parts['path'] ) ) {
			return $url;
		}
		$path   = $parts['path'];
		$double = $home_path . $home_path;
		if ( $path === $double || $path === $double . '/' || strpos( $path, $double . '/' ) === 0 ) {
			$path = $home_path . substr( $path, strlen( $double ) );
			if ( $path === '' ) {
				$path = '/';
			}
			$parts['path'] = $path;
			$scheme        = isset( $parts['scheme'] ) ? $parts['scheme'] . '://' : '';
			$host          = isset( $parts['host'] ) ? $parts['host'] : '';
			$port          = isset( $parts['port'] ) ? ':' . $parts['port'] : '';
			$query         = isset( $parts['query'] ) ? '?' . $parts['query'] : '';
			$fragment      = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';
			return $scheme . $host . $port . $path . $query . $fragment;
		}
		return $url;
	}
}

if ( ! function_exists( 'kayan_i18n_fallback_country_code' ) ) {
	function kayan_i18n_fallback_country_code() {
		$countries = kayan_i18n_get_countries();
		$opt       = function_exists( 'yc_get_option' ) ? yc_get_option( 'kayan_i18n_default_country' ) : '';
		if ( ! empty( $opt ) && isset( $countries[ $opt ] ) ) {
			return $opt;
		}
		foreach ( $countries as $code => $data ) {
			$prefix = isset( $data['path'] ) ? trim( (string) $data['path'], '/' ) : '';
			if ( $prefix === '' ) {
				return $code;
			}
		}
		$keys = array_keys( $countries );
		return $keys ? (string) $keys[0] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_detect_country_from_home' ) ) {
	function kayan_i18n_detect_country_from_home() {
		$home_path = kayan_i18n_site_home_path();
		$countries = kayan_i18n_get_countries();
		foreach ( $countries as $code => $data ) {
			$prefix = isset( $data['path'] ) ? trim( (string) $data['path'], '/' ) : '';
			$prefix = $prefix === '' ? '' : '/' . $prefix;
			if ( $home_path === $prefix ) {
				return $code;
			}
		}
		return kayan_i18n_fallback_country_code();
	}
}

if ( ! function_exists( 'kayan_i18n_detect_country_from_path' ) ) {
	function kayan_i18n_detect_country_from_path( $path = null ) {
		unset( $path );
		return kayan_i18n_detect_country_from_home();
	}
}

if ( ! function_exists( 'kayan_i18n_get_country' ) ) {
	function kayan_i18n_get_country() {
		return kayan_i18n_detect_country_from_home();
	}
}

if ( ! function_exists( 'kayan_i18n_get_country_data' ) ) {
	function kayan_i18n_get_country_data( $country = null ) {
		$countries = kayan_i18n_get_countries();
		if ( null === $country ) {
			$country = kayan_i18n_get_country();
		}
		if ( isset( $countries[ $country ] ) ) {
			return $countries[ $country ];
		}
		$fallback = kayan_i18n_fallback_country_code();
		return isset( $countries[ $fallback ] ) ? $countries[ $fallback ] : array( 'path' => '' );
	}
}

if ( ! function_exists( 'kayan_i18n_get_country_path' ) ) {
	function kayan_i18n_get_country_path( $country = null ) {
		# Other installs are separate WordPress sites. Never prefix home_url() again.
		unset( $country );
		return '';
	}
}

if ( ! function_exists( 'kayan_i18n_detect_lang_from_path' ) ) {
	function kayan_i18n_detect_lang_from_path( $path = null ) {
		$rest = kayan_i18n_request_path_relative_to_home( $path );
		if ( $rest === '/en' || $rest === '/en/' || strpos( $rest, '/en/' ) === 0 ) {
			return 'en';
		}
		return 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_get_lang' ) ) {
	function kayan_i18n_get_lang() {
		$lang = get_query_var( 'kayan_lang' );
		if ( 'en' === $lang ) {
			return 'en';
		}
		if ( 'en' === kayan_i18n_detect_lang_from_path() ) {
			return 'en';
		}
		if ( function_exists( 'pll_current_language' ) ) {
			$pll = pll_current_language();
			if ( is_string( $pll ) && $pll !== '' ) {
				return ( 0 === strcasecmp( substr( $pll, 0, 2 ), 'en' ) ) ? 'en' : 'ar';
			}
		}
		return 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_skip_localize_key' ) ) {
	function kayan_i18n_skip_localize_key( $key ) {
		if ( ! is_string( $key ) ) {
			return false;
		}
		$k = strtolower( $key );
		$skip = array(
			'url', 'href', 'icon', 'color', 'class', 'css', 'widget_id', 'widget_post',
			'attrstyle', 'textareacolor', 'selectedmodel', 'embed', 'image', 'src',
			'phonenumber', 'whatsapp_number', 'color_edits', 'nonce', 'token',
		);
		foreach ( $skip as $needle ) {
			if ( false !== strpos( $k, $needle ) ) {
				return true;
			}
		}
		return (bool) preg_match( '/(_url|_id|_icon|_class|_css|_color|_switch)$/', $k );
	}
}

if ( ! function_exists( 'kayan_i18n_sorted_pairs' ) ) {
	function kayan_i18n_sorted_pairs() {
		static $sorted = null;
		if ( null !== $sorted ) {
			return $sorted;
		}
		$map = function_exists( 'kayan_i18n_content_pairs' ) ? kayan_i18n_content_pairs() : array();
		uksort(
			$map,
			function( $a, $b ) {
				$la = function_exists( 'mb_strlen' ) ? mb_strlen( $a, 'UTF-8' ) : strlen( $a );
				$lb = function_exists( 'mb_strlen' ) ? mb_strlen( $b, 'UTF-8' ) : strlen( $b );
				return $lb - $la;
			}
		);
		$sorted = $map;
		return $sorted;
	}
}

if ( ! function_exists( 'kayan_i18n_apply_pairs' ) ) {
	function kayan_i18n_apply_pairs( $text ) {
		if ( ! is_string( $text ) || $text === '' ) {
			return $text;
		}
		$out = $text;
		foreach ( kayan_i18n_sorted_pairs() as $ar => $en ) {
			if ( $ar !== '' && false !== strpos( $out, $ar ) ) {
				$out = str_replace( $ar, $en, $out );
			}
		}
		return $out;
	}
}

if ( ! function_exists( 'kayan_i18n_translate_text' ) ) {
	function kayan_i18n_translate_text( $text ) {
		if ( ! is_string( $text ) || $text === '' ) {
			return $text;
		}
		if ( ! kayan_i18n_is_english() ) {
			return $text;
		}
		if ( ! preg_match( '/\p{Arabic}/u', $text ) ) {
			return $text;
		}
		if ( preg_match( '/^(https?:|tel:|mailto:|#|\/)/i', $text ) ) {
			return $text;
		}

		$plain = trim( wp_strip_all_tags( str_replace( array( '{%', '%}' ), '', $text ) ) );
		$plain_len = function_exists( 'mb_strlen' ) ? mb_strlen( $plain, 'UTF-8' ) : strlen( $plain );
		if ( false !== strpos( $plain, 'للإيجار' ) && $plain_len < 180 ) {
			return $text;
		}

		$map = kayan_i18n_sorted_pairs();
		if ( isset( $map[ $plain ] ) ) {
			if ( false !== strpos( $text, '{%' ) || $plain === trim( $text ) ) {
				return $map[ $plain ];
			}
		}

		$out = kayan_i18n_apply_pairs( $text );
		if ( $out !== $text ) {
			return $out;
		}
		if ( function_exists( 'pll__' ) ) {
			$pll = pll__( $plain );
			if ( is_string( $pll ) && $pll !== '' && $pll !== $plain ) {
				return $pll;
			}
		}
		return $out;
	}
}

if ( ! function_exists( 'kayan_i18n_translate_html' ) ) {
	function kayan_i18n_translate_html( $html ) {
		if ( ! is_string( $html ) || $html === '' || ! kayan_i18n_is_english() ) {
			return $html;
		}
		if ( ! preg_match( '/\p{Arabic}/u', $html ) ) {
			return $html;
		}

		$store = array();
		$html  = preg_replace_callback(
			'/<(script|style|textarea|code|pre)(\b[^>]*)>.*?<\/\1>/is',
			function( $m ) use ( &$store ) {
				$key           = '___K18N' . count( $store ) . '___';
				$store[ $key ] = $m[0];
				return $key;
			},
			$html
		);
		$html = preg_replace_callback(
			'/\s(?:href|src|srcset|action|poster|cite|formaction|data-[a-z0-9_-]+)=(?:\'[^\']*\'|"[^"]*")/i',
			function( $m ) use ( &$store ) {
				$key           = '___K18N' . count( $store ) . '___';
				$store[ $key ] = $m[0];
				return $key;
			},
			$html
		);

		$html = kayan_i18n_apply_pairs( $html );

		if ( ! empty( $store ) ) {
			$html = strtr( $html, $store );
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_i18n_localize_tree' ) ) {
	function kayan_i18n_localize_tree( $value ) {
		if ( ! kayan_i18n_is_english() ) {
			return $value;
		}
		if ( is_array( $value ) ) {
			$out = array();
			foreach ( $value as $key => $item ) {
				if ( kayan_i18n_skip_localize_key( $key ) ) {
					$out[ $key ] = $item;
					continue;
				}
				$out[ $key ] = kayan_i18n_localize_tree( $item );
			}
			foreach ( $out as $key => $item ) {
				if ( is_string( $key ) && substr( $key, -3 ) !== '_en' && isset( $out[ $key . '_en' ] ) && $out[ $key . '_en' ] !== '' && null !== $out[ $key . '_en' ] ) {
					$out[ $key ] = $out[ $key . '_en' ];
				}
			}
			return $out;
		}
		if ( is_string( $value ) ) {
			return kayan_i18n_translate_text( $value );
		}
		return $value;
	}
}

if ( ! function_exists( 'kayan_ui' ) ) {
	function kayan_ui( $ar, $en = '' ) {
		if ( function_exists( 'kayan_i18n_is_english' ) && kayan_i18n_is_english() ) {
			if ( $en !== '' ) {
				return $en;
			}
			return kayan_i18n_translate_text( $ar );
		}
		return $ar;
	}
}

if ( ! function_exists( 'kayan_i18n_is_english' ) ) {
	function kayan_i18n_is_english() {
		return 'en' === kayan_i18n_get_lang();
	}
}

if ( ! function_exists( 'kayan_i18n_get_html_attrs' ) ) {
	function kayan_i18n_get_html_attrs() {
		if ( kayan_i18n_is_english() ) {
			return 'lang="en" dir="ltr"';
		}
		return 'lang="ar" dir="rtl"';
	}
}

if ( ! function_exists( 'kayan_i18n_country_label' ) ) {
	function kayan_i18n_country_label( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'label_en' : 'label_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_country_in_phrase' ) ) {
	function kayan_i18n_country_in_phrase( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'in_en' : 'in_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_country_regions' ) ) {
	function kayan_i18n_country_regions( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'regions_en' : 'regions_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_country_address' ) ) {
	function kayan_i18n_country_address( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'address_en' : 'address_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_get_switcher_config' ) ) {
	function kayan_i18n_get_switcher_config() {
		$country_paths = array();
		$flags         = array();
		foreach ( kayan_i18n_get_countries() as $code => $data ) {
			$country_paths[ $code ] = isset( $data['path'] ) ? (string) $data['path'] : '';
			$flags[ $code ]         = isset( $data['flag'] ) ? (string) $data['flag'] : '🌐';
		}

		$base_domain = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		if ( empty( $base_domain ) ) {
			$base_domain = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		}

		return array(
			'baseDomain'   => $base_domain,
			'countryPaths' => $country_paths,
			'flags'        => $flags,
			'storageKey'   => 'kayan_geo_pref',
		);
	}
}

if ( ! function_exists( 'kayan_i18n_build_url' ) ) {
	function kayan_i18n_build_url( $country, $lang, $slug = '/' ) {
		unset( $country );
		$slug = ( $slug && $slug !== '/' ) ? '/' . trim( (string) $slug, '/' ) : '';
		if ( 'en' === $lang ) {
			return user_trailingslashit( home_url( 'en' . $slug ) );
		}
		if ( $slug === '' || $slug === '/' ) {
			return user_trailingslashit( home_url( '/' ) );
		}
		return user_trailingslashit( home_url( ltrim( $slug, '/' ) ) );
	}
}

if ( ! function_exists( 'kayan_i18n_get_post_en_meta' ) ) {
	function kayan_i18n_get_post_en_meta( $post_id, $key ) {
		$value = get_post_meta( $post_id, 'kayan_en_' . $key, true );
		return ! empty( $value ) ? trim( wp_strip_all_tags( (string) $value ) ) : '';
	}
}

if ( ! function_exists( 'kayan_i18n_pll_has_lang' ) ) {
	function kayan_i18n_pll_has_lang( $lang ) {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return false;
		}
		$list = pll_languages_list();
		if ( ! is_array( $list ) ) {
			return false;
		}
		$want = strtolower( substr( (string) $lang, 0, 2 ) );
		foreach ( $list as $item ) {
			if ( $want === strtolower( substr( (string) $item, 0, 2 ) ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'kayan_i18n_get_localized_url' ) ) {
	function kayan_i18n_get_localized_url( $lang = 'ar', $post_id = 0 ) {
		$lang = ( 'en' === $lang ) ? 'en' : 'ar';
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}

		if ( $post_id && function_exists( 'pll_get_post' ) && kayan_i18n_pll_has_lang( $lang ) ) {
			$translated = pll_get_post( $post_id, $lang );
			if ( $translated ) {
				return kayan_i18n_normalize_site_url( get_permalink( $translated ) );
			}
		}

		if ( ( is_front_page() || is_home() ) && function_exists( 'pll_home_url' ) && kayan_i18n_pll_has_lang( $lang ) ) {
			$pll_home = pll_home_url( $lang );
			if ( is_string( $pll_home ) && $pll_home !== '' ) {
				return kayan_i18n_normalize_site_url( $pll_home );
			}
		}

		$rel = kayan_i18n_request_path_relative_to_home();
		$rel = preg_replace( '#^/en(/|$)#', '/', $rel );
		if ( ! is_string( $rel ) || $rel === '' ) {
			$rel = '/';
		}

		if ( $post_id && ! is_front_page() && ! is_home() ) {
			$post = get_post( $post_id );
			if ( $post && ! empty( $post->post_name ) ) {
				return kayan_i18n_build_url( kayan_i18n_get_country(), $lang, $post->post_name );
			}
		}

		if ( 'en' === $lang ) {
			if ( $rel === '/' ) {
				return user_trailingslashit( home_url( 'en' ) );
			}
			return user_trailingslashit( home_url( 'en/' . ltrim( $rel, '/' ) ) );
		}

		return user_trailingslashit( home_url( $rel === '/' ? '/' : $rel ) );
	}
}

if ( ! function_exists( 'kayan_i18n_filter_seo_title' ) ) {
	function kayan_i18n_filter_seo_title( $title ) {
		if ( ! kayan_i18n_is_english() ) {
			return $title;
		}
		if ( is_singular() ) {
			$en = kayan_i18n_get_post_en_meta( get_queried_object_id(), 'title' );
			if ( $en ) {
				return $en;
			}
		}
		return kayan_i18n_translate_text( $title );
	}
}

if ( ! function_exists( 'kayan_i18n_filter_seo_description' ) ) {
	function kayan_i18n_filter_seo_description( $description ) {
		if ( ! kayan_i18n_is_english() ) {
			return $description;
		}
		if ( is_singular() ) {
			$en = kayan_i18n_get_post_en_meta( get_queried_object_id(), 'description' );
			if ( $en ) {
				return $en;
			}
		}
		return kayan_i18n_translate_text( $description );
	}
}

if ( ! function_exists( 'kayan_i18n_get_schema_language' ) ) {
	function kayan_i18n_get_schema_language() {
		return kayan_i18n_is_english() ? 'en' : 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_default_lang' ) ) {
	function kayan_i18n_default_lang() {
		if ( function_exists( 'pll_default_language' ) ) {
			$d = pll_default_language();
			if ( is_string( $d ) && $d !== '' ) {
				return ( 0 === strcasecmp( substr( $d, 0, 2 ), 'en' ) ) ? 'en' : 'ar';
			}
		}
		return 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_rank_math_plugin_present' ) ) {
	function kayan_i18n_rank_math_plugin_present() {
		return defined( 'RANK_MATH_VERSION' )
			|| class_exists( 'RankMath' )
			|| function_exists( 'rank_math' );
	}
}

if ( ! function_exists( 'kayan_i18n_other_hreflang_active' ) ) {
	function kayan_i18n_other_hreflang_active() {
		if ( function_exists( 'pll_languages_list' ) ) {
			$list = pll_languages_list();
			if ( is_array( $list ) && count( $list ) >= 2 ) {
				return true;
			}
		}
		if ( ! function_exists( 'kayan_seo_is_enabled' ) || ! kayan_seo_is_enabled() ) {
			return true;
		}
		if ( function_exists( 'kayan_i18n_rank_math_plugin_present' ) && kayan_i18n_rank_math_plugin_present() ) {
			return true;
		}
		if ( function_exists( 'has_action' ) && ( has_action( 'rank_math/head' ) || has_action( 'rank_math/opengraph/facebook' ) ) ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'kayan_i18n_url_is_this_site' ) ) {
	function kayan_i18n_url_is_this_site( $url ) {
		if ( ! is_string( $url ) || $url === '' ) {
			return false;
		}
		$home = wp_parse_url( home_url( '/' ) );
		$got  = wp_parse_url( $url );
		if ( ! is_array( $home ) || ! is_array( $got ) ) {
			return false;
		}
		$home_host = isset( $home['host'] ) ? strtolower( $home['host'] ) : '';
		$got_host  = isset( $got['host'] ) ? strtolower( $got['host'] ) : '';
		if ( strpos( $home_host, 'www.' ) === 0 ) {
			$home_host = substr( $home_host, 4 );
		}
		if ( strpos( $got_host, 'www.' ) === 0 ) {
			$got_host = substr( $got_host, 4 );
		}
		if ( $home_host === '' || $got_host === '' || $home_host !== $got_host ) {
			return false;
		}
		$home_path = isset( $home['path'] ) ? rtrim( $home['path'], '/' ) : '';
		$got_path  = isset( $got['path'] ) ? (string) $got['path'] : '';
		if ( $home_path === '' ) {
			return true;
		}
		return ( $got_path === $home_path || $got_path === $home_path . '/' || strpos( $got_path, $home_path . '/' ) === 0 );
	}
}

if ( ! function_exists( 'kayan_i18n_site_has_lang' ) ) {
	function kayan_i18n_site_has_lang( $lang ) {
		$lang = ( 'en' === $lang ) ? 'en' : 'ar';
		if ( function_exists( 'pll_languages_list' ) ) {
			$list = pll_languages_list();
			if ( is_array( $list ) && ! empty( $list ) ) {
				return kayan_i18n_pll_has_lang( $lang );
			}
		}
		# Theme language routes live in this same WordPress only.
		return 'ar' === $lang || kayan_i18n_is_enabled();
	}
}

if ( ! function_exists( 'kayan_i18n_render_hreflang' ) ) {
	function kayan_i18n_render_hreflang() {
		if ( is_admin() || ! kayan_i18n_is_enabled() ) {
			return;
		}
		if ( kayan_i18n_other_hreflang_active() ) {
			return;
		}
		if ( ! kayan_i18n_site_has_lang( 'ar' ) || ! kayan_i18n_site_has_lang( 'en' ) ) {
			return;
		}
		$ar_url = kayan_i18n_normalize_site_url( kayan_i18n_get_localized_url( 'ar' ) );
		$en_url = kayan_i18n_normalize_site_url( kayan_i18n_get_localized_url( 'en' ) );
		if ( empty( $ar_url ) || empty( $en_url ) || $ar_url === $en_url ) {
			return;
		}
		if ( ! kayan_i18n_url_is_this_site( $ar_url ) || ! kayan_i18n_url_is_this_site( $en_url ) ) {
			return;
		}
		$default = kayan_i18n_default_lang();
		$x_url   = ( 'en' === $default ) ? $en_url : $ar_url;
		echo '<link rel="alternate" hreflang="ar" href="' . esc_url( $ar_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="en" href="' . esc_url( $en_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $x_url ) . '" />' . "\n";
	}
}

if ( ! function_exists( 'kayan_i18n_filter_language_attributes' ) ) {
	function kayan_i18n_filter_language_attributes( $output ) {
		unset( $output );
		return kayan_i18n_get_html_attrs();
	}
}

if ( ! function_exists( 'kayan_i18n_rewrite_html_lang' ) ) {
	function kayan_i18n_rewrite_html_lang( $html ) {
		if ( ! is_string( $html ) || $html === '' ) {
			return $html;
		}
		$attrs = kayan_i18n_get_html_attrs();
		return preg_replace( '/<html\b[^>]*>/i', '<html ' . $attrs . '>', $html, 1 );
	}
}

if ( ! function_exists( 'kayan_i18n_start_html_lang_buffer' ) ) {
	function kayan_i18n_start_html_lang_buffer() {
		if ( is_admin() || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) {
			return;
		}
		if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) ) {
			return;
		}
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}
		if ( ! empty( $GLOBALS['kayan_i18n_html_buffering'] ) ) {
			return;
		}
		$GLOBALS['kayan_i18n_html_buffering'] = true;
		ob_start( 'kayan_i18n_rewrite_html_lang' );
	}
}
