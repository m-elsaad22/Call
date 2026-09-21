<?php 
if ( ! function_exists( 'kayan_ui_show_call_button' ) ) {
	/**
	 * أزرار الاتصال داخل المحتوى (بطاقة، ودجات، هيدر، بوب أب) — يتحكم بها kayan_show_call_buttons فقط.
	 */
	function kayan_ui_show_call_button() {
		return ! empty( yc_get_option( 'kayan_show_call_buttons' ) );
	}
}

if ( ! function_exists( 'kayan_ui_show_floating_call_button' ) ) {
	/**
	 * الزر العائم فقط — مستقل عن kayan_show_call_buttons.
	 *
	 * @param int  $post_id           معرّف المقال/الصفحة (0 خارج singular).
	 * @param bool $chat_mode_active  وضع نافذة واتساب يستبدل الأزرار العائمة.
	 */
	function kayan_ui_show_floating_call_button( $post_id = 0, $chat_mode_active = false ) {
		if ( $chat_mode_active ) {
			return false;
		}

		if ( ! empty( yc_get_option( 'hide__floating__call' ) ) ) {
			return false;
		}

		$post_id = (int) $post_id;
		if ( $post_id > 0 ) {
			if ( ! empty( get_post_meta( $post_id, 'hide__floating__call', true ) ) ) {
				return false;
			}

			$hide_categories = yc_get_option( 'hide__floating__call__categories' );
			if ( ! empty( $hide_categories ) && is_array( $hide_categories ) ) {
				$post_cats = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
				if ( ! empty( array_intersect( $post_cats, array_map( 'intval', $hide_categories ) ) ) ) {
					return false;
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'kayan_wa_get_site_name' ) ) {
	function kayan_wa_get_site_name() {
		if ( function_exists( 'kayan_seo_get_site_name' ) ) {
			return kayan_seo_get_site_name();
		}
		$name = yc_get_option( 'sitename' );
		if ( empty( $name ) ) {
			$name = get_bloginfo( 'name' );
		}
		return trim( wp_strip_all_tags( (string) $name ) );
	}
}

if ( ! function_exists( 'kayan_wa_get_page_title' ) ) {
	function kayan_wa_get_page_title( $post_id = 0 ) {
		if ( $post_id ) {
			return trim( wp_strip_all_tags( get_the_title( $post_id ) ) );
		}
		if ( is_singular() ) {
			return trim( wp_strip_all_tags( get_the_title() ) );
		}
		if ( is_category() || is_tax() || is_tag() ) {
			$obj = get_queried_object();
			return isset( $obj->name ) ? trim( wp_strip_all_tags( $obj->name ) ) : '';
		}
		if ( is_search() ) {
			return 'نتائج البحث: ' . trim( get_search_query() );
		}
		return '';
	}
}

if ( ! function_exists( 'kayan_wa_default_message' ) ) {
	function kayan_wa_default_message( $page_title = null ) {
		if ( null === $page_title ) {
			$page_title = kayan_wa_get_page_title();
		}
		$page_title = trim( (string) $page_title );
		$site_name  = kayan_wa_get_site_name();
		if ( $page_title !== '' && $site_name !== '' ) {
			return 'مرحباً! ' . $page_title . '، ' . $site_name;
		}
		if ( $site_name !== '' ) {
			return 'مرحباً! ' . $site_name;
		}
		return 'مرحباً!';
	}
}

if ( ! function_exists( 'kayan_wa_resolve_title' ) ) {
	function kayan_wa_resolve_title( $custom = '' ) {
		$custom = trim( (string) $custom );
		if ( $custom === '' ) {
			return kayan_wa_get_site_name();
		}
		$replacements = array(
			'{site}'      => kayan_wa_get_site_name(),
			'{site_name}' => kayan_wa_get_site_name(),
		);
		return str_replace( array_keys( $replacements ), array_values( $replacements ), $custom );
	}
}

if ( ! function_exists( 'kayan_wa_resolve_message' ) ) {
	function kayan_wa_resolve_message( $custom = '', $page_title = null ) {
		$custom = trim( (string) $custom );
		if ( $custom === '' ) {
			return kayan_wa_default_message( $page_title );
		}
		if ( null === $page_title ) {
			$page_title = kayan_wa_get_page_title();
		}
		$replacements = array(
			'{title}'     => $page_title,
			'{page}'      => $page_title,
			'{site}'      => kayan_wa_get_site_name(),
			'{site_name}' => kayan_wa_get_site_name(),
		);
		return str_replace( array_keys( $replacements ), array_values( $replacements ), $custom );
	}
}

if ( ! function_exists( 'kayan_wa_sanitize_number' ) ) {
	function kayan_wa_sanitize_number( $number ) {
		return preg_replace( '/\D+/', '', (string) $number );
	}
}

if ( ! function_exists( 'kayan_wa_build_url' ) ) {
	function kayan_wa_build_url( $number, $message = null, $page_title = null ) {
		$digits = kayan_wa_sanitize_number( $number );
		if ( $digits === '' ) {
			return '';
		}
		if ( null === $message ) {
			$message = kayan_wa_default_message( $page_title );
		} else {
			$message = kayan_wa_resolve_message( $message, $page_title );
		}
		return 'https://wa.me/' . $digits . '?text=' . rawurlencode( $message );
	}
}

if ( ! defined( 'KAYAN_DRAIN_CALL' ) ) {
	define( 'KAYAN_DRAIN_CALL', '+971526965446' );
}
if ( ! defined( 'KAYAN_DRAIN_WA' ) ) {
	define( 'KAYAN_DRAIN_WA', '971526965446' );
}
if ( ! defined( 'KAYAN_DRAIN_SEO_NUM' ) ) {
	define( 'KAYAN_DRAIN_SEO_NUM', '0526965446' );
}

if ( ! function_exists( 'kayan_is_drain_article' ) ) {
	function kayan_is_drain_article( $post = null ) {
		if ( $post instanceof WP_Post ) {
			$title = (string) $post->post_title;
			$slug  = (string) $post->post_name;
		} else {
			$id = (int) $post;
			if ( $id <= 0 && is_singular() ) {
				$id = (int) get_queried_object_id();
			}
			if ( $id <= 0 ) {
				return false;
			}
			$title = (string) get_the_title( $id );
			$slug  = (string) get_post_field( 'post_name', $id );
		}
		$hay = $title . ' ' . $slug;
		return (bool) preg_match(
			'/تسليك\s*(?:ال)?(?:مجار[يى]|بالوع)|sewerage|sewer-wiring|sewer-plumbing|sewer_plumbing|drainage-compan|(?:^|-)sewage(?:-|$)/iu',
			$hay
		);
	}
}

if ( ! function_exists( 'kayan_drain_compact_title_base' ) ) {
	function kayan_drain_compact_title_base( $base ) {
		$base = trim( wp_strip_all_tags( (string) $base ) );
		$base = preg_replace( '/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', '', $base );
		$base = str_replace( array( '📞', '📢', '💧' ), '', $base );
		$base = preg_replace( '/الإعلان للإيجار/u', '', $base );
		$base = preg_replace( '/\s*[|–—]\s*تسليك فوري.*$/u', '', $base );
		$base = preg_replace( '/\+?971\s?\d{8,9}|0\d{9}|01\d{8,10}/', '', $base );
		$base = preg_replace( '/\s+/u', ' ', $base );
		$base = trim( $base, " \t\n\r\0\x0B-|–—" );
		if ( $base === '' ) {
			$base = 'شركة تسليك مجاري';
		}
		return $base;
	}
}

if ( ! function_exists( 'kayan_drain_build_seo_title' ) ) {
	function kayan_drain_build_seo_title( $post = null ) {
		if ( ! ( $post instanceof WP_Post ) ) {
			$id   = $post ? (int) $post : (int) get_queried_object_id();
			$post = $id ? get_post( $id ) : null;
		}
		$base = kayan_drain_compact_title_base( $post ? $post->post_title : '' );
		$num  = KAYAN_DRAIN_SEO_NUM;
		$len  = function_exists( 'mb_strlen' ) ? 'mb_strlen' : 'strlen';
		$sub  = function_exists( 'mb_substr' ) ? 'mb_substr' : 'substr';
		# Google ~600px ≈ 50–56 glyphs with Arabic + digits. Keep the number intact.
		$max_base = 40;
		if ( $len( $base ) > $max_base ) {
			$base = trim( $sub( $base, 0, $max_base ) );
		}
		if ( strpos( $base, $num ) === false ) {
			$base .= ' ' . $num;
		}
		return $base;
	}
}

if ( ! function_exists( 'kayan_drain_seo_title_is_current' ) ) {
	function kayan_drain_seo_title_is_current( $title ) {
		$title = (string) $title;
		if ( strpos( $title, KAYAN_DRAIN_SEO_NUM ) === false ) {
			return false;
		}
		if ( strpos( $title, '0541673020' ) !== false || strpos( $title, 'تسليك فوري' ) !== false ) {
			return false;
		}
		$len = function_exists( 'mb_strlen' ) ? mb_strlen( $title ) : strlen( $title );
		return $len <= 56;
	}
}
