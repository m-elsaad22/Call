<?php 
require_once __DIR__ . '/countries.php';
require_once __DIR__ . '/strings.php';
require_once __DIR__ . '/content-map.php';

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

if ( ! function_exists( 'kayan_i18n_detect_country_from_path' ) ) {
	function kayan_i18n_detect_country_from_path( $path = null ) {
		$path      = null === $path ? kayan_i18n_get_request_path() : $path;
		$countries = kayan_i18n_get_countries();
		$matches   = array();

		foreach ( $countries as $code => $data ) {
			$prefix = isset( $data['path'] ) ? trim( (string) $data['path'], '/' ) : '';
			if ( $prefix === '' ) {
				continue;
			}
			if ( $path === '/' . $prefix || strpos( $path, '/' . $prefix . '/' ) === 0 ) {
				$matches[ strlen( $prefix ) ] = $code;
			}
		}

		if ( empty( $matches ) ) {
			$default = yc_get_option( 'kayan_i18n_default_country' );
			if ( ! empty( $default ) && isset( $countries[ $default ] ) ) {
				return $default;
			}
			return 'ae';
		}

		ksort( $matches );
		return end( $matches );
	}
}

if ( ! function_exists( 'kayan_i18n_get_country' ) ) {
	function kayan_i18n_get_country() {
		$countries = kayan_i18n_get_countries();
		$country   = get_query_var( 'kayan_country' );
		if ( ! empty( $country ) && isset( $countries[ $country ] ) ) {
			return $country;
		}
		return kayan_i18n_detect_country_from_path();
	}
}

if ( ! function_exists( 'kayan_i18n_get_country_data' ) ) {
	function kayan_i18n_get_country_data( $country = null ) {
		if ( null === $country ) {
			$country = kayan_i18n_get_country();
		}
		$countries = kayan_i18n_get_countries();
		return isset( $countries[ $country ] ) ? $countries[ $country ] : $countries['ae'];
	}
}

if ( ! function_exists( 'kayan_i18n_get_country_path' ) ) {
	function kayan_i18n_get_country_path( $country = null ) {
		$data = kayan_i18n_get_country_data( $country );
		return isset( $data['path'] ) ? (string) $data['path'] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_detect_lang_from_path' ) ) {
	function kayan_i18n_detect_lang_from_path( $path = null ) {
		$path    = null === $path ? kayan_i18n_get_request_path() : $path;
		$country = kayan_i18n_detect_country_from_path( $path );
		$base    = kayan_i18n_get_country_path( $country );
		$rest    = $path;

		if ( $base !== '' ) {
			$rest = substr( $path, strlen( $base ) );
			if ( $rest === false || $rest === '' ) {
				$rest = '/';
			}
		}

		if ( $rest === '/en' || $rest === '/en/' || strpos( $rest, '/en/' ) === 0 ) {
			return 'en';
		}
		if ( strpos( $path, '/en' ) === 0 && $base === '' ) {
			return 'en';
		}
		return 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_get_lang' ) ) {
	function kayan_i18n_get_lang() {
		if ( function_exists( 'pll_current_language' ) ) {
			$pll = pll_current_language();
			if ( is_string( $pll ) && $pll !== '' ) {
				return ( 0 === strcasecmp( substr( $pll, 0, 2 ), 'en' ) ) ? 'en' : 'ar';
			}
		}
		$lang = get_query_var( 'kayan_lang' );
		if ( 'en' === $lang ) {
			return 'en';
		}
		return kayan_i18n_detect_lang_from_path();
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
		$base        = trailingslashit( home_url() );
		$country_path = kayan_i18n_get_country_path( $country );
		$slug        = ( $slug && $slug !== '/' ) ? '/' . trim( (string) $slug, '/' ) : '';

		if ( 'en' === $lang ) {
			return user_trailingslashit( $base . trim( $country_path . '/en' . $slug, '/' ) );
		}
		if ( $slug === '' || $slug === '/' ) {
			return user_trailingslashit( $base . trim( $country_path, '/' ) ?: '' );
		}
		return user_trailingslashit( $base . trim( $country_path . $slug, '/' ) );
	}
}

if ( ! function_exists( 'kayan_i18n_get_post_en_meta' ) ) {
	function kayan_i18n_get_post_en_meta( $post_id, $key ) {
		$value = get_post_meta( $post_id, 'kayan_en_' . $key, true );
		return ! empty( $value ) ? trim( wp_strip_all_tags( (string) $value ) ) : '';
	}
}

if ( ! function_exists( 'kayan_i18n_get_localized_url' ) ) {
	function kayan_i18n_get_localized_url( $lang = 'ar', $post_id = 0 ) {
		$country = kayan_i18n_get_country();
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}

		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$slug = $post->post_name;
				return kayan_i18n_build_url( $country, $lang, $slug );
			}
		}

		if ( is_front_page() || is_home() ) {
			return kayan_i18n_build_url( $country, $lang, '/' );
		}

		if ( function_exists( 'kayan_seo_get_current_url' ) ) {
			$url  = kayan_seo_get_current_url();
			$path = wp_parse_url( $url, PHP_URL_PATH );
			$path = is_string( $path ) ? $path : '/';
			$base = kayan_i18n_get_country_path( $country );
			if ( $base !== '' && strpos( $path, $base ) === 0 ) {
				$path = substr( $path, strlen( $base ) );
			}
			if ( 'en' === $lang ) {
				$path = preg_replace( '#^/en/?#', '/en/', $path );
				if ( strpos( $path, '/en' ) !== 0 ) {
					$path = '/en' . ( $path === '/' ? '' : $path );
				}
			} else {
				$path = preg_replace( '#^/en/?#', '/', $path );
			}
			return user_trailingslashit( home_url( trim( $base . $path, '/' ) ) );
		}

		return kayan_i18n_build_url( $country, $lang, '/' );
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

if ( ! function_exists( 'kayan_i18n_render_hreflang' ) ) {
	function kayan_i18n_render_hreflang() {
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}
		$ar_url = kayan_i18n_get_localized_url( 'ar' );
		$en_url = kayan_i18n_get_localized_url( 'en' );
		if ( empty( $ar_url ) || empty( $en_url ) || $ar_url === $en_url ) {
			return;
		}
		echo '<link rel="alternate" hreflang="ar" href="' . esc_url( $ar_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="en" href="' . esc_url( $en_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $ar_url ) . '" />' . "\n";
	}
}

if ( ! function_exists( 'kayan_i18n_filter_language_attributes' ) ) {
	function kayan_i18n_filter_language_attributes( $output ) {
		return kayan_i18n_get_html_attrs();
	}
}
