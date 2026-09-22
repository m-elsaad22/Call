<?php
/**
 * kayan-seo — طبقة SEO الخاصة بـ KAYAN
 *
 * السياسة الافتراضية:
 * - Rank Math Active للتخزين.
 * - واجهة Rank Math معطّلة بينما KAYAN SEO يعمل.
 * - لاستعادة واجهة Rank Math: فعّل kayan_seo_disable (لا تشغّل الاثنين معاً على الواجهة).
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/compatibility.php';
require_once __DIR__ . '/rank-math-bridge.php';

if ( ! function_exists( 'kayan_seo_bootstrap' ) ) {
	function kayan_seo_bootstrap() {
		add_theme_support( 'title-tag' );

		# إذا KAYAN SEO معطّل → لا نطبع عنوان/وصف من القالب (Rank Math يتولى الواجهة)
		if ( kayan_seo_is_disabled() ) {
			return;
		}

		add_filter( 'pre_get_document_title', 'kayan_seo_filter_document_title', 20 );
		add_action( 'wp_head', 'kayan_seo_print_meta_description', 1 );
		add_action( 'wp_head', 'kayan_seo_print_canonical', 1 );
	}
	add_action( 'after_setup_theme', 'kayan_seo_bootstrap', 2 );
}

add_filter( 'get_canonical_url', 'kayan_seo_filter_get_canonical_url', 20, 2 );
add_filter( 'rank_math/frontend/canonical', 'kayan_seo_filter_rank_math_canonical', 20 );
add_filter( 'rank_math/opengraph/facebook/og_url', 'kayan_seo_filter_rank_math_canonical', 20 );

if ( ! function_exists( 'kayan_seo_filter_document_title' ) ) {
	function kayan_seo_filter_document_title( $title ) {
		if ( kayan_seo_is_disabled() ) {
			return $title;
		}
		$rm_title = kayan_seo_get_rank_math_title();
		if ( '' !== $rm_title ) {
			return apply_filters( 'kayan_seo_resolved_title', $rm_title );
		}
		return apply_filters( 'kayan_seo_resolved_title', $title );
	}
}

if ( ! function_exists( 'kayan_drain_filter_seo_title' ) ) {
	function kayan_drain_filter_seo_title( $title ) {
		if ( is_admin() || ! is_singular() || ! function_exists( 'kayan_is_drain_article' ) || ! kayan_is_drain_article() ) {
			return $title;
		}
		$title = (string) $title;
		if ( strpos( $title, '0541673020' ) !== false ) {
			return $title;
		}
		return kayan_drain_build_seo_title();
	}
}
add_filter( 'kayan_seo_resolved_title', 'kayan_drain_filter_seo_title', 30 );

if ( ! function_exists( 'kayan_plumbing_filter_seo_title' ) ) {
	function kayan_plumbing_filter_seo_title( $title ) {
		if ( is_admin() || ! is_singular() || ! function_exists( 'kayan_is_plumbing_article' ) || ! kayan_is_plumbing_article() ) {
			return $title;
		}
		$title = (string) $title;
		if ( strpos( $title, '0567868605' ) !== false ) {
			return $title;
		}
		return kayan_plumbing_build_seo_title();
	}
}
add_filter( 'kayan_seo_resolved_title', 'kayan_plumbing_filter_seo_title', 31 );

if ( ! function_exists( 'kayan_seo_print_meta_description' ) ) {
	function kayan_seo_print_meta_description() {
		if ( is_admin() || kayan_seo_is_disabled() ) {
			return;
		}
		$hide = get_option( 'hide__description_show' );
		if ( ! empty( $hide ) ) {
			return;
		}
		$desc = kayan_seo_get_rank_math_description();
		if ( '' === $desc ) {
			$desc = is_singular() ? wp_strip_all_tags( get_the_excerpt() ) : get_bloginfo( 'description' );
		}
		$desc = apply_filters( 'kayan_seo_resolved_description', $desc );
		if ( '' === $desc ) {
			return;
		}
		echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
	}
}

if ( ! function_exists( 'kayan_seo_filter_get_canonical_url' ) ) {
	function kayan_seo_filter_get_canonical_url( $canonical, $post = null ) {
		unset( $post );
		$fixed = function_exists( 'kayan_seo_canonical_url' ) ? kayan_seo_canonical_url() : $canonical;
		return $fixed ? $fixed : $canonical;
	}
}

if ( ! function_exists( 'kayan_seo_filter_rank_math_canonical' ) ) {
	function kayan_seo_filter_rank_math_canonical( $canonical ) {
		if ( ! is_string( $canonical ) || $canonical === '' ) {
			return $canonical;
		}
		if ( function_exists( 'kayan_i18n_normalize_site_url' ) ) {
			$canonical = kayan_i18n_normalize_site_url( $canonical );
		}
		return $canonical;
	}
}

if ( ! function_exists( 'kayan_seo_print_canonical' ) ) {
	function kayan_seo_print_canonical() {
		if ( is_admin() || is_404() || kayan_seo_is_disabled() ) {
			return;
		}
		$url = kayan_seo_canonical_url();
		if ( $url === '' ) {
			return;
		}
		remove_action( 'wp_head', 'rel_canonical' );
		echo '<link rel="canonical" href="' . esc_url( $url ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_attr( $url ) . '" />' . "\n";
	}
}

if ( ! function_exists( 'kayan_seo_start_head_buffer' ) ) {
	function kayan_seo_start_head_buffer() {
		if ( is_admin() ) {
			return;
		}
		$GLOBALS['kayan_seo_head_buffering'] = true;
		ob_start();
	}
}

if ( ! function_exists( 'kayan_seo_rewrite_head_url_attr' ) ) {
	function kayan_seo_rewrite_head_url_attr( $url ) {
		if ( function_exists( 'kayan_i18n_normalize_site_url' ) ) {
			$url = kayan_i18n_normalize_site_url( $url );
		}
		if ( ! kayan_seo_is_disabled() && function_exists( 'kayan_seo_canonical_url' ) && ! is_404() ) {
			$canonical = kayan_seo_canonical_url();
			if ( is_string( $canonical ) && $canonical !== '' ) {
				return $canonical;
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'kayan_seo_dedupe_link_tags' ) ) {
	function kayan_seo_dedupe_link_tags( $html, $pattern ) {
		$count = 0;
		return preg_replace_callback(
			$pattern,
			function( $m ) use ( &$count ) {
				$count++;
				return ( 1 === $count ) ? $m[0] : '';
			},
			$html
		);
	}
}

if ( ! function_exists( 'kayan_seo_dedupe_hreflang' ) ) {
	function kayan_seo_dedupe_hreflang( $html ) {
		$seen = array();
		return preg_replace_callback(
			'/<link\b[^>]*\bhreflang=["\']([^"\']+)["\'][^>]*>\s*/i',
			function( $m ) use ( &$seen ) {
				if ( ! preg_match( '/rel=["\']alternate["\']/i', $m[0] ) ) {
					return $m[0];
				}
				$key = strtolower( $m[1] );
				if ( isset( $seen[ $key ] ) ) {
					return '';
				}
				$seen[ $key ] = true;
				return $m[0];
			},
			$html
		);
	}
}

if ( ! function_exists( 'kayan_seo_end_head_buffer' ) ) {
	function kayan_seo_end_head_buffer() {
		if ( empty( $GLOBALS['kayan_seo_head_buffering'] ) ) {
			return;
		}
		$GLOBALS['kayan_seo_head_buffering'] = false;
		$html = ob_get_clean();
		if ( ! is_string( $html ) || $html === '' ) {
			return;
		}

		$html = preg_replace_callback(
			'/(<link\b[^>]*rel=["\']canonical["\'][^>]*href=["\'])([^"\']+)(["\'])/i',
			function( $m ) {
				return $m[1] . esc_url( kayan_seo_rewrite_head_url_attr( $m[2] ) ) . $m[3];
			},
			$html
		);
		$html = preg_replace_callback(
			'/(<link\b[^>]*href=["\'])([^"\']+)(["\'][^>]*rel=["\']canonical["\'])/i',
			function( $m ) {
				return $m[1] . esc_url( kayan_seo_rewrite_head_url_attr( $m[2] ) ) . $m[3];
			},
			$html
		);
		$html = preg_replace_callback(
			'/(<meta\b[^>]*property=["\']og:url["\'][^>]*content=["\'])([^"\']+)(["\'])/i',
			function( $m ) {
				return $m[1] . esc_attr( kayan_seo_rewrite_head_url_attr( $m[2] ) ) . $m[3];
			},
			$html
		);
		$html = preg_replace_callback(
			'/(<meta\b[^>]*content=["\'])([^"\']+)(["\'][^>]*property=["\']og:url["\'])/i',
			function( $m ) {
				return $m[1] . esc_attr( kayan_seo_rewrite_head_url_attr( $m[2] ) ) . $m[3];
			},
			$html
		);

		$html = kayan_seo_dedupe_link_tags( $html, '/<link\b[^>]*rel=["\']canonical["\'][^>]*>\s*/i' );
		$html = kayan_seo_dedupe_link_tags( $html, '/<meta\b[^>]*property=["\']og:url["\'][^>]*>\s*/i' );
		if ( function_exists( 'kayan_i18n_p2_dedupe_hreflang' ) ) {
			$html = kayan_i18n_p2_dedupe_hreflang( $html );
		} else {
			$html = kayan_seo_dedupe_hreflang( $html );
		}

		echo $html;
	}
}

add_action( 'BeforeWPHead', 'kayan_seo_start_head_buffer', 0 );
add_action( 'AfterWPHead', 'kayan_seo_end_head_buffer', 99 );
