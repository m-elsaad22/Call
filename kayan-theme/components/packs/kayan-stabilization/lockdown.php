<?
/**
 * Phase 1.9 — Production lockdown: KAYAN Theme is the only source of frontend behavior.
 */

if ( ! function_exists( 'kayan_lockdown_is_active' ) ) {
	/**
	 * Production lockdown is ON by default (v2027.3.9+).
	 * Disable via theme option kayan_lockdown_disable = 1 (staging only).
	 */
	function kayan_lockdown_is_active() {
		if ( ! empty( yc_get_option( 'kayan_lockdown_disable' ) ) ) {
			return false;
		}
		return true;
	}
}

if ( ! function_exists( 'kayan_lockdown_filter_header_injection' ) ) {
	function kayan_lockdown_filter_header_injection( $code ) {
		if ( ! kayan_lockdown_is_active() || is_admin() ) {
			return $code;
		}
		if ( ! empty( yc_get_option( 'kayan_lockdown_allow_header_injection' ) ) ) {
			return $code;
		}
		return '';
	}
}

if ( ! function_exists( 'kayan_lockdown_sanitize_frontend_html' ) ) {
	function kayan_lockdown_sanitize_frontend_html( $html ) {
		if ( ! kayan_lockdown_is_active() || is_admin() ) {
			return $html;
		}

		$patterns = array(
			// Legacy inline trackers (Code Snippets).
			'#<script[^>]*id=["\']kayan-tracking-js["\'][^>]*>.*?</script>#is',
			'#<script>\s*\(function\(\)\s*\{\s*var A=.*?rukn_track_click.*?</script>#is',
			'#<script>\s*\(function\(\)\s*\{\s*var S=sessionStorage\.getItem\([\'"]_rsa_sid[\'"]\).*?</script>#is',
			'#<script[^>]*>[\s\S]*?rukn_track_click[\s\S]*?</script>#is',
			'#<script[^>]*>[\s\S]*?rukn_track_pv[\s\S]*?</script>#is',
			'#<script[^>]*>[\s\S]*?kt_track_click[\s\S]*?</script>#is',
		);

		if ( function_exists( 'kayan_seo_controls_frontend' ) && kayan_seo_controls_frontend() ) {
			$patterns = array_merge(
				$patterns,
				array(
					'#<script[^>]*class=["\']rank-math-schema["\'][^>]*>.*?</script>#is',
					'#<script[^>]*class=["\']yoast-schema-graph["\'][^>]*>.*?</script>#is',
					'#<script type="application/ld\+json">\s*\{"@context":"https://schema\.org","@type":"Organization"[^<]*"sameAs":\[\][^<]*\}\s*</script>#is',
				)
			);
		}

		foreach ( $patterns as $pattern ) {
			$html = preg_replace( $pattern, '', $html );
		}

		return $html;
	}
}

if ( ! function_exists( 'kayan_lockdown_start_output_buffer' ) ) {
	function kayan_lockdown_start_output_buffer() {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( kayan_lockdown_is_active() ) {
			ob_start( 'kayan_lockdown_sanitize_frontend_html' );
		}
	}
}
add_action( 'template_redirect', 'kayan_lockdown_start_output_buffer', -1 );

if ( ! function_exists( 'kayan_lockdown_force_single_authority' ) ) {
	function kayan_lockdown_force_single_authority() {
		if ( ! kayan_lockdown_is_active() ) {
			return;
		}

		if ( function_exists( 'kayan_seo_disable_rank_math_frontend' ) ) {
			kayan_seo_disable_rank_math_frontend();
		}
		if ( function_exists( 'kayan_seo_disable_third_party_seo_output' ) ) {
			kayan_seo_disable_third_party_seo_output();
		}
	}
}
add_action( 'plugins_loaded', 'kayan_lockdown_force_single_authority', PHP_INT_MAX );
add_action( 'init', 'kayan_lockdown_force_single_authority', PHP_INT_MAX );
add_action( 'wp', 'kayan_lockdown_force_single_authority', PHP_INT_MAX );

if ( ! function_exists( 'kayan_lockdown_get_external_dependency_registry' ) ) {
	/**
	 * Ops checklist — sources that must NOT run on production alongside KAYAN Theme.
	 */
	function kayan_lockdown_get_external_dependency_registry() {
		return array(
			array(
				'name'     => 'KAYAN legacy tracker (kt_*)',
				'type'     => 'code_snippet',
				'path'     => 'WP Admin → Snippets → kayan-tracking-js (or similar)',
				'injects'  => 'Inline script: kt_register_visit, kt_track_click, admin-ajax handlers',
				'why'      => 'Pre-KAYAN Track conversion tracking',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => 'rukn_track_* tracker',
				'type'     => 'code_snippet',
				'path'     => 'WP Admin → Snippets → rukn_track (inline)',
				'injects'  => 'rukn_track_click, rukn_track_pv via admin-ajax',
				'why'      => 'Custom RSA pageview/click tracking',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => '_rsa_sid session pageview',
				'type'     => 'code_snippet',
				'path'     => 'WP Admin → Snippets → _rsa_sid',
				'injects'  => 'sessionStorage pageview beacon',
				'why'      => 'Legacy session analytics',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => 'Organization JSON-LD snippet',
				'type'     => 'code_snippet',
				'path'     => 'Snippet with sameAs:[] Organization block',
				'injects'  => 'Duplicate Organization schema in <head>',
				'why'      => 'Manual schema before KAYAN SEO',
				'decision' => 'REMOVE',
			),
			array(
				'name'     => 'Rank Math SEO',
				'type'     => 'plugin',
				'path'     => 'wp-content/plugins/seo-by-rank-math/',
				'injects'  => 'Meta, canonical, OG, rank-math-schema JSON-LD via wp_head',
				'why'      => 'SEO plugin — keep for meta storage only; frontend disabled by KAYAN SEO',
				'decision' => 'KEEP (data) / DISABLE frontend output',
			),
			array(
				'name'     => 'LiteSpeed Cache',
				'type'     => 'plugin',
				'path'     => 'wp-content/plugins/litespeed-cache/',
				'injects'  => 'HTML cache, lazy-load script, speculationrules prefetch',
				'why'      => 'Performance — purge after theme deploy',
				'decision' => 'KEEP',
			),
			array(
				'name'     => 'header___codes theme option',
				'type'     => 'theme_option',
				'path'     => 'Theme options → header codes / HeadCode',
				'injects'  => 'Raw HTML/JS in <head> before wp_head',
				'why'      => 'Legacy custom head injection',
				'decision' => 'REMOVE when lockdown active (blocked in theme)',
			),
			array(
				'name'     => 'Child theme',
				'type'     => 'theme',
				'path'     => 'wp-content/themes/*-child/',
				'injects'  => 'functions.php overrides, duplicate enqueues',
				'why'      => 'Should not exist — KAYAN is parent only',
				'decision' => 'REMOVE if present',
			),
			array(
				'name'     => 'Mu-plugins',
				'type'     => 'mu-plugin',
				'path'     => 'wp-content/mu-plugins/',
				'injects'  => 'Unknown — audit server filesystem',
				'why'      => 'Must verify manually on server',
				'decision' => 'AUDIT on server',
			),
			array(
				'name'     => 'Insert Headers / WPCode / Code Snippets Pro',
				'type'     => 'plugin',
				'path'     => 'wp-content/plugins/wpcode* or insert-headers-and-footers',
				'injects'  => 'Arbitrary head/footer scripts',
				'why'      => 'Overlaps KAYAN Track + SEO',
				'decision' => 'REMOVE tracking/SEO snippets; deactivate if empty',
			),
		);
	}
}
