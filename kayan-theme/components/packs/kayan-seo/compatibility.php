<?
/**
 * KAYAN SEO — third-party output suppression (Rank Math, Yoast, legacy theme schema).
 * Ensures a single head + JSON-LD source when KAYAN SEO controls the frontend.
 */

if ( ! function_exists( 'kayan_seo_disable_rank_math_frontend' ) ) {
	function kayan_seo_disable_rank_math_frontend() {
		if ( ! kayan_seo_controls_frontend() || ! function_exists( 'kayan_seo_rank_math_active' ) || ! kayan_seo_rank_math_active() ) {
			return;
		}

		add_filter( 'rank_math/frontend/disable', '__return_true', PHP_INT_MAX );
		add_filter( 'rank_math/json_ld', '__return_false', PHP_INT_MAX );
		add_filter( 'rank_math/opengraph/enable', '__return_false', PHP_INT_MAX );
		add_filter( 'rank_math/opengraph/facebook', '__return_false', PHP_INT_MAX );
		add_filter( 'rank_math/opengraph/twitter', '__return_false', PHP_INT_MAX );
		add_filter( 'rank_math/remove_credit_notice', '__return_true', PHP_INT_MAX );

		remove_all_actions( 'rank_math/head' );
		remove_all_actions( 'rank_math/opengraph/facebook' );
		remove_all_actions( 'rank_math/opengraph/twitter' );
		remove_all_actions( 'rank_math/json_ld' );

		if ( class_exists( 'RankMath\\Frontend\\Head' ) && method_exists( 'RankMath\\Frontend\\Head', 'get' ) ) {
			$head = RankMath\Frontend\Head::get();
			if ( $head ) {
				remove_action( 'wp_head', array( $head, 'head' ), 1 );
			}
		}
	}
}
add_action( 'plugins_loaded', 'kayan_seo_disable_rank_math_frontend', 0 );
add_action( 'init', 'kayan_seo_disable_rank_math_frontend', 0 );
add_action( 'wp', 'kayan_seo_disable_rank_math_frontend', 0 );

if ( ! function_exists( 'kayan_seo_disable_legacy_schema_head' ) ) {
	function kayan_seo_disable_legacy_schema_head() {
		if ( ! function_exists( 'kayan_seo_uses_modern_schema' ) || ! kayan_seo_uses_modern_schema() ) {
			return;
		}
		global $YourColor__Schema_instance;
		if ( $YourColor__Schema_instance instanceof YourColor__Schema ) {
			remove_action( 'wp_head', array( $YourColor__Schema_instance, 'insert__schema' ) );
		}
	}
}
add_action( 'wp_loaded', 'kayan_seo_disable_legacy_schema_head', 20 );
