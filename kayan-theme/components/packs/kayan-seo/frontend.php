<?
/**
 * KAYAN SEO — unified frontend output (single source of truth).
 * Title, meta, canonical, OG, Twitter, verification — all via wp_head.
 */

if ( ! function_exists( 'kayan_seo_controls_frontend' ) ) {
	function kayan_seo_controls_frontend() {
		return function_exists( 'kayan_seo_is_enabled' ) && kayan_seo_is_enabled();
	}
}

if ( ! function_exists( 'kayan_seo_get_document_title' ) ) {
	function kayan_seo_get_document_title() {
		$title = kayan_seo_get_title();
		if ( is_front_page() || is_home() ) {
			return $title;
		}
		$sep   = apply_filters( 'kayan_seo_title_separator', ' | ' );
		$suffix = kayan_seo_get_site_name();
		if ( $suffix && strpos( $title, $suffix ) === false ) {
			return $title . $sep . $suffix;
		}
		return $title;
	}
}

if ( ! function_exists( 'kayan_seo_get_og_type' ) ) {
	function kayan_seo_get_og_type() {
		if ( is_singular( 'post' ) ) {
			return 'article';
		}
		if ( is_singular( 'page' ) ) {
			return 'website';
		}
		if ( is_author() ) {
			return 'profile';
		}
		return 'website';
	}
}

if ( ! function_exists( 'kayan_seo_filter_document_title' ) ) {
	function kayan_seo_filter_document_title( $title ) {
		if ( ! kayan_seo_controls_frontend() ) {
			return $title;
		}
		return kayan_seo_get_document_title();
	}
}

if ( ! function_exists( 'kayan_seo_output_document_title_tag' ) ) {
	function kayan_seo_output_document_title_tag() {
		if ( ! kayan_seo_controls_frontend() ) {
			return;
		}
		echo '<title>' . esc_html( kayan_seo_get_document_title() ) . '</title>' . "\n";
	}
}

if ( ! function_exists( 'kayan_seo_disable_third_party_seo_output' ) ) {
	function kayan_seo_disable_third_party_seo_output() {
		if ( ! kayan_seo_controls_frontend() ) {
			return;
		}

		// Rank Math — keep meta storage, disable frontend head output.
		if ( function_exists( 'kayan_seo_rank_math_active' ) && kayan_seo_rank_math_active() ) {
			add_filter( 'rank_math/frontend/disable', '__return_true' );
			add_action(
				'plugins_loaded',
				function () {
					remove_all_actions( 'rank_math/head' );
					remove_all_actions( 'rank_math/opengraph/facebook' );
					remove_all_actions( 'rank_math/opengraph/twitter' );
					remove_all_actions( 'rank_math/json_ld' );
				},
				20
			);
		}

		// Yoast SEO — if active, disable frontend presenters.
		if ( defined( 'WPSEO_VERSION' ) ) {
			add_filter( 'wpseo_frontend_presenter_classes', '__return_empty_array' );
			add_filter( 'wpseo_json_ld_output', '__return_false' );
		}
	}
}
add_action( 'plugins_loaded', 'kayan_seo_disable_third_party_seo_output', 1 );

if ( ! function_exists( 'kayan_seo_register_frontend_hooks' ) ) {
	function kayan_seo_register_frontend_hooks() {
		if ( ! kayan_seo_controls_frontend() ) {
			return;
		}

		remove_action( 'wp_head', '_wp_render_title_tag', 1 );
		add_action( 'wp_head', 'kayan_seo_output_document_title_tag', 0 );
		add_action( 'wp_head', 'kayan_seo_render_verification_meta', 1 );
		add_action( 'wp_head', 'kayan_seo_render_head_meta', 2 );

		add_filter( 'pre_get_document_title', 'kayan_seo_filter_document_title', 99 );
		add_filter( 'document_title_parts', 'kayan_seo_filter_document_title_parts', 99 );
	}
}
add_action( 'after_setup_theme', 'kayan_seo_register_frontend_hooks', 20 );

if ( ! function_exists( 'kayan_seo_filter_document_title_parts' ) ) {
	function kayan_seo_filter_document_title_parts( $parts ) {
		if ( ! kayan_seo_controls_frontend() ) {
			return $parts;
		}
		$parts['title'] = kayan_seo_get_title();
		$parts['site']  = '';
		return $parts;
	}
}

if ( ! function_exists( 'kayan_seo_theme_seo_delegates_to_kayan' ) ) {
	function kayan_seo_theme_seo_delegates_to_kayan() {
		return kayan_seo_controls_frontend();
	}
}
