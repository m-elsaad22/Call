<?php
/**
 * Portable head guard. Loaded from #header/part.php so it still runs when
 * OPcache is holding an older kayan-i18n/setup.php. One hreflang/canonical
 * source for THIS WordPress only (home_url, no hardcoded hosts).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'kayan_i18n_p2_suppress_other_hreflang' ) ) {
	function kayan_i18n_p2_suppress_other_hreflang() {
		$skip = false;
		if ( function_exists( 'pll_languages_list' ) ) {
			$list = pll_languages_list();
			if ( is_array( $list ) && count( $list ) >= 2 ) {
				$skip = true;
			}
		}
		if ( ! function_exists( 'kayan_seo_is_enabled' ) || ! kayan_seo_is_enabled() ) {
			$skip = true;
		}
		if ( ! $skip ) {
			return;
		}
		foreach ( array( 1, 2, 3, 10 ) as $pri ) {
			remove_action( 'wp_head', 'kayan_i18n_render_hreflang', $pri );
			remove_action( 'wp_head', 'kayan_i18n_render_hreflang_single_source', $pri );
		}
	}
}

if ( ! function_exists( 'kayan_i18n_p2_collapse_home_path' ) ) {
	function kayan_i18n_p2_collapse_home_path( $url ) {
		if ( ! is_string( $url ) || $url === '' ) {
			return $url;
		}
		if ( function_exists( 'kayan_i18n_normalize_site_url' ) ) {
			$url = kayan_i18n_normalize_site_url( $url );
		}
		if ( ! function_exists( 'home_url' ) || ! function_exists( 'wp_parse_url' ) ) {
			return $url;
		}
		$parts = wp_parse_url( $url );
		$home  = wp_parse_url( home_url( '/' ) );
		if ( ! is_array( $parts ) || ! is_array( $home ) || empty( $home['path'] ) || empty( $parts['path'] ) ) {
			return $url;
		}
		$home_path = '/' . trim( (string) $home['path'], '/' );
		if ( $home_path === '/' ) {
			return $url;
		}
		$path   = (string) $parts['path'];
		$double = $home_path . $home_path;
		if ( $path !== $double && $path !== $double . '/' && strpos( $path, $double . '/' ) !== 0 ) {
			return $url;
		}
		$path = $home_path . substr( $path, strlen( $double ) );
		if ( $path === '' ) {
			$path = '/';
		}
		$scheme   = isset( $parts['scheme'] ) ? $parts['scheme'] . '://' : '';
		$host     = isset( $parts['host'] ) ? $parts['host'] : '';
		$port     = isset( $parts['port'] ) ? ':' . $parts['port'] : '';
		$query    = isset( $parts['query'] ) ? '?' . $parts['query'] : '';
		$fragment = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';
		return $scheme . $host . $port . $path . $query . $fragment;
	}
}

if ( ! function_exists( 'kayan_i18n_p2_rewrite_head_urls' ) ) {
	function kayan_i18n_p2_rewrite_head_urls( $html ) {
		$canon = '';
		if ( function_exists( 'kayan_seo_is_enabled' ) && kayan_seo_is_enabled() && function_exists( 'kayan_seo_canonical_url' ) && ! ( function_exists( 'is_404' ) && is_404() ) ) {
			$canon = kayan_seo_canonical_url();
		}
		$html = preg_replace_callback(
			'/(<link\b[^>]*rel=["\']canonical["\'][^>]*href=["\'])([^"\']+)(["\'])/i',
			function( $m ) use ( $canon ) {
				$url = $canon !== '' ? $canon : kayan_i18n_p2_collapse_home_path( $m[2] );
				return $m[1] . ( function_exists( 'esc_url' ) ? esc_url( $url ) : $url ) . $m[3];
			},
			$html
		);
		$html = preg_replace_callback(
			'/(<link\b[^>]*href=["\'])([^"\']+)(["\'][^>]*rel=["\']canonical["\'])/i',
			function( $m ) use ( $canon ) {
				$url = $canon !== '' ? $canon : kayan_i18n_p2_collapse_home_path( $m[2] );
				return $m[1] . ( function_exists( 'esc_url' ) ? esc_url( $url ) : $url ) . $m[3];
			},
			$html
		);
		$html = preg_replace_callback(
			'/(<meta\b[^>]*property=["\']og:url["\'][^>]*content=["\'])([^"\']+)(["\'])/i',
			function( $m ) use ( $canon ) {
				$url = $canon !== '' ? $canon : kayan_i18n_p2_collapse_home_path( $m[2] );
				return $m[1] . ( function_exists( 'esc_attr' ) ? esc_attr( $url ) : $url ) . $m[3];
			},
			$html
		);
		$html = preg_replace_callback(
			'/(<meta\b[^>]*content=["\'])([^"\']+)(["\'][^>]*property=["\']og:url["\'])/i',
			function( $m ) use ( $canon ) {
				$url = $canon !== '' ? $canon : kayan_i18n_p2_collapse_home_path( $m[2] );
				return $m[1] . ( function_exists( 'esc_attr' ) ? esc_attr( $url ) : $url ) . $m[3];
			},
			$html
		);
		if ( $canon !== '' && ! preg_match( '/rel=["\']canonical["\']/i', $html ) ) {
			$html = '<link rel="canonical" href="' . ( function_exists( 'esc_url' ) ? esc_url( $canon ) : $canon ) . '" />' . "\n" . $html;
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_i18n_p2_dedupe_pattern' ) ) {
	function kayan_i18n_p2_dedupe_pattern( $html, $pattern ) {
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

if ( ! function_exists( 'kayan_i18n_p2_dedupe_hreflang' ) ) {
	function kayan_i18n_p2_dedupe_hreflang( $html ) {
		if ( ! preg_match_all( '/<link\b[^>]*\bhreflang=["\'][^"\']+["\'][^>]*>\s*/i', $html, $all ) ) {
			return $html;
		}
		$href_first     = false;
		$hreflang_first = false;
		foreach ( $all[0] as $tag ) {
			if ( ! preg_match( '/rel=["\']alternate["\']/i', $tag ) ) {
				continue;
			}
			$href_pos = stripos( $tag, 'href=' );
			$hf_pos   = stripos( $tag, 'hreflang=' );
			if ( false === $href_pos || false === $hf_pos ) {
				continue;
			}
			if ( $href_pos < $hf_pos ) {
				$href_first = true;
			} else {
				$hreflang_first = true;
			}
		}
		$drop_theme = $href_first && $hreflang_first;
		$seen       = array();
		return preg_replace_callback(
			'/<link\b[^>]*\bhreflang=["\']([^"\']+)["\'][^>]*>\s*/i',
			function( $m ) use ( &$seen, $drop_theme ) {
				if ( ! preg_match( '/rel=["\']alternate["\']/i', $m[0] ) ) {
					return $m[0];
				}
				if ( $drop_theme ) {
					$href_pos = stripos( $m[0], 'href=' );
					$hf_pos   = stripos( $m[0], 'hreflang=' );
					if ( false !== $href_pos && false !== $hf_pos && $hf_pos < $href_pos ) {
						return '';
					}
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

if ( ! function_exists( 'kayan_i18n_p2_filter_wp_head' ) ) {
	function kayan_i18n_p2_filter_wp_head( $html ) {
		if ( ! is_string( $html ) || $html === '' ) {
			return $html;
		}
		$html = kayan_i18n_p2_rewrite_head_urls( $html );
		$html = kayan_i18n_p2_dedupe_pattern( $html, '/<link\b[^>]*rel=["\']canonical["\'][^>]*>\s*/i' );
		$html = kayan_i18n_p2_dedupe_pattern( $html, '/<meta\b[^>]*property=["\']og:url["\'][^>]*>\s*/i' );
		$html = kayan_i18n_p2_dedupe_hreflang( $html );
		return $html;
	}
}

# One-shot: REST/init still run when the HTML homepage is a LiteSpeed hit.
if ( ! function_exists( 'kayan_i18n_p2_purge_stale_head_once' ) ) {
	function kayan_i18n_p2_purge_stale_head_once() {
		if ( get_option( 'kayan_p2_head_cache' ) === '1.0.2' ) {
			return;
		}
		update_option( 'kayan_p2_head_cache', '1.0.2', false );
		if ( ! headers_sent() ) {
			header( 'X-LiteSpeed-Purge: *' );
		}
		if ( function_exists( 'do_action' ) ) {
			do_action( 'litespeed_purge_all' );
		}
		if ( function_exists( 'litespeed_purge_all' ) ) {
			litespeed_purge_all();
		}
	}
}
if ( function_exists( 'add_action' ) ) {
	add_action( 'init', 'kayan_i18n_p2_purge_stale_head_once', 1 );
	add_action( 'rest_api_init', 'kayan_i18n_p2_purge_stale_head_once', 1 );
}
