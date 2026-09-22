<?php 
require_once __DIR__ . '/helpers.php';
if ( is_readable( __DIR__ . '/switcher.php' ) ) {
	require_once __DIR__ . '/switcher.php';
}

if ( ! function_exists( 'kayan_i18n_register_query_var' ) ) {
	function kayan_i18n_register_query_var( $vars ) {
		$vars[] = 'kayan_lang';
		$vars[] = 'kayan_country';
		return $vars;
	}
}
add_filter( 'query_vars', 'kayan_i18n_register_query_var' );

if ( ! function_exists( 'kayan_i18n_register_rewrites' ) ) {
	function kayan_i18n_register_rewrites() {
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}

		# Language only, relative to THIS site's home_url(). Other country
		# prefixes belong to separate WordPress installs — do not rewrite them.
		add_rewrite_rule( '^en/?$', 'index.php?kayan_lang=en', 'top' );
		add_rewrite_rule( '^en/([^/]+)/?$', 'index.php?kayan_lang=en&name=$matches[1]', 'top' );
		add_rewrite_rule( '^en/([^/]+)/page/([0-9]+)/?$', 'index.php?kayan_lang=en&name=$matches[1]&paged=$matches[2]', 'top' );
	}
}
add_action( 'init', 'kayan_i18n_register_rewrites', 5 );

if ( ! function_exists( 'kayan_i18n_flush_rewrites_once' ) ) {
	function kayan_i18n_flush_rewrites_once() {
		if ( get_option( 'kayan_i18n_rewrite_version' ) === '1.0.6' ) {
			return;
		}
		# Persist first so a heavy flush cannot 500-loop on the front/REST.
		update_option( 'kayan_i18n_rewrite_version', '1.0.6', false );
		if ( is_admin() && ! wp_doing_ajax() ) {
			flush_rewrite_rules( false );
		}
	}
}
add_action( 'init', 'kayan_i18n_flush_rewrites_once', 99 );

if ( ! function_exists( 'kayan_i18n_seed_lang_query_var' ) ) {
	function kayan_i18n_seed_lang_query_var( $wp ) {
		if ( is_admin() || ! kayan_i18n_is_enabled() ) {
			return;
		}
		if ( isset( $wp->query_vars['kayan_lang'] ) && 'en' === $wp->query_vars['kayan_lang'] ) {
			return;
		}
		if ( 'en' !== kayan_i18n_detect_lang_from_path() ) {
			return;
		}
		$wp->query_vars['kayan_lang'] = 'en';
		$rel = kayan_i18n_request_path_relative_to_home();
		if ( $rel === '/en' || $rel === '/en/' ) {
			unset( $wp->query_vars['name'], $wp->query_vars['pagename'], $wp->query_vars['page'] );
		}
	}
}
add_action( 'parse_request', 'kayan_i18n_seed_lang_query_var', 1 );

if ( ! function_exists( 'kayan_i18n_resolve_localized_request' ) ) {
	function kayan_i18n_resolve_localized_request( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$lang = get_query_var( 'kayan_lang' );
		if ( 'en' !== $lang && function_exists( 'kayan_i18n_detect_lang_from_path' ) && 'en' === kayan_i18n_detect_lang_from_path() ) {
			$lang = 'en';
			$query->set( 'kayan_lang', 'en' );
		}
		if ( 'en' !== $lang ) {
			return;
		}

		$rel = function_exists( 'kayan_i18n_request_path_relative_to_home' )
			? kayan_i18n_request_path_relative_to_home()
			: '/';
		if ( $rel === '/en' || $rel === '/en/' ) {
			$query->set( 'name', '' );
			$query->set( 'pagename', '' );
			$query->is_home       = true;
			$query->is_front_page = true;
			$query->is_page       = false;
			$query->is_single     = false;
			$query->is_singular   = false;
			$query->is_404        = false;
			return;
		}

		$name = get_query_var( 'name' );
		if ( empty( $name ) ) {
			$query->is_home       = true;
			$query->is_front_page = true;
			return;
		}

		$query->set( 'post_type', 'post' );
		$query->set( 'name', $name );
	}
}
add_action( 'pre_get_posts', 'kayan_i18n_resolve_localized_request', 1 );

if ( ! function_exists( 'kayan_i18n_enqueue_assets' ) ) {
	function kayan_i18n_enqueue_assets() {
		if ( is_admin() ) {
			return;
		}
		$css = get_template_directory_uri() . '/components/packs/kayan-i18n/assets/kayan-locale.css';
		wp_enqueue_style( 'kayan-locale', $css, array(), '1.4.29' );
	}
}
add_action( 'wp_enqueue_scripts', 'kayan_i18n_enqueue_assets', 6 );

if ( ! function_exists( 'kayan_i18n_filter_gettext' ) ) {
	function kayan_i18n_filter_gettext( $translated, $text, $domain ) {
		if ( is_admin() || 'yourcolor' !== $domain ) {
			return $translated;
		}
		if ( ! function_exists( 'kayan_i18n_is_english' ) || ! kayan_i18n_is_english() ) {
			return $translated;
		}
		return kayan_i18n_translate_text( $text );
	}
}
add_filter( 'gettext', 'kayan_i18n_filter_gettext', 20, 3 );

if ( ! function_exists( 'kayan_i18n_filter_the_title' ) ) {
	function kayan_i18n_filter_the_title( $title ) {
		if ( is_admin() ) {
			return $title;
		}
		return function_exists( 'kayan_i18n_translate_text' ) ? kayan_i18n_translate_text( $title ) : $title;
	}
}
add_filter( 'the_title', 'kayan_i18n_filter_the_title', 20 );

if ( ! function_exists( 'kayan_i18n_filter_nav_items' ) ) {
	function kayan_i18n_filter_nav_items( $items ) {
		if ( is_admin() || ! is_array( $items ) || ! function_exists( 'kayan_i18n_is_english' ) || ! kayan_i18n_is_english() ) {
			return $items;
		}
		foreach ( $items as $item ) {
			if ( isset( $item->title ) ) {
				$item->title = kayan_i18n_translate_text( $item->title );
			}
		}
		return $items;
	}
}
add_filter( 'wp_get_nav_menu_items', 'kayan_i18n_filter_nav_items', 20 );

if ( ! function_exists( 'kayan_i18n_filter_get_term' ) ) {
	function kayan_i18n_filter_get_term( $term ) {
		if ( is_admin() || is_wp_error( $term ) || ! is_object( $term ) ) {
			return $term;
		}
		if ( ! function_exists( 'kayan_i18n_is_english' ) || ! kayan_i18n_is_english() ) {
			return $term;
		}
		if ( isset( $term->name ) ) {
			$term->name = kayan_i18n_translate_text( $term->name );
		}
		if ( isset( $term->description ) ) {
			$term->description = kayan_i18n_translate_text( $term->description );
		}
		return $term;
	}
}
add_filter( 'get_term', 'kayan_i18n_filter_get_term', 20 );

add_filter( 'kayan_seo_resolved_title', 'kayan_i18n_filter_seo_title', 10, 1 );
add_filter( 'kayan_seo_resolved_description', 'kayan_i18n_filter_seo_description', 10, 1 );
add_filter( 'language_attributes', 'kayan_i18n_filter_language_attributes', 99 );
add_action( 'wp_head', 'kayan_i18n_render_hreflang', 2 );
