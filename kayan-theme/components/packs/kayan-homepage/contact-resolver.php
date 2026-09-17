<?
/**
 * Per-post / per-page phone & WhatsApp resolution for homepage v3.
 * Matches legacy chain: post_meta → theme options (via kayan_homepage_get_*_raw).
 */

if ( ! function_exists( 'kayan_hp_resolve_post_id' ) ) {
	/**
	 * @param int|null $post_id Explicit post ID or null for current query.
	 * @return int
	 */
	function kayan_hp_resolve_post_id( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_queried_object_id();
		}
		return (int) $post_id;
	}
}

if ( ! function_exists( 'kayan_hp_resolve_phone' ) ) {
	/**
	 * @param int|null $post_id Post/page ID; null uses get_queried_object_id().
	 * @return string
	 */
	function kayan_hp_resolve_phone( $post_id = null ) {
		if ( function_exists( 'kayan_i18n_resolve_phone' ) ) {
			return kayan_i18n_resolve_phone( $post_id );
		}

		$post_id = kayan_hp_resolve_post_id( $post_id );
		if ( $post_id > 0 ) {
			$meta = trim( (string) get_post_meta( $post_id, 'phone_number', true ) );
			if ( $meta !== '' ) {
				return $meta;
			}
		}
		return kayan_homepage_get_phone_raw();
	}
}

if ( ! function_exists( 'kayan_hp_resolve_whatsapp' ) ) {
	/**
	 * @param int|null $post_id Post/page ID; null uses get_queried_object_id().
	 * @return string
	 */
	function kayan_hp_resolve_whatsapp( $post_id = null ) {
		if ( function_exists( 'kayan_i18n_resolve_whatsapp' ) ) {
			return kayan_i18n_resolve_whatsapp( $post_id );
		}

		$post_id = kayan_hp_resolve_post_id( $post_id );
		if ( $post_id > 0 ) {
			$meta = trim( (string) get_post_meta( $post_id, 'whatsapp_number', true ) );
			if ( $meta !== '' ) {
				return $meta;
			}
		}
		return kayan_homepage_get_whatsapp_raw();
	}
}

if ( ! function_exists( 'kayan_hp_resolve_tel_url' ) ) {
	/**
	 * @param int|null $post_id Post/page ID; null uses get_queried_object_id().
	 * @return string
	 */
	function kayan_hp_resolve_tel_url( $post_id = null ) {
		$phone  = kayan_hp_resolve_phone( $post_id );
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( $digits === '' ) {
			return '#';
		}
		return 'tel:+' . ltrim( $digits, '+' );
	}
}

if ( ! function_exists( 'kayan_hp_resolve_whatsapp_url' ) ) {
	/**
	 * @param int|null $post_id Post/page ID; null uses get_queried_object_id().
	 * @return string
	 */
	function kayan_hp_resolve_whatsapp_url( $post_id = null ) {
		$number = kayan_hp_resolve_whatsapp( $post_id );
		if ( function_exists( 'kayan_wa_build_url' ) ) {
			$url = kayan_wa_build_url( $number );
			if ( $url !== '' ) {
				return $url;
			}
		}
		$digits = preg_replace( '/\D+/', '', (string) $number );
		if ( $digits === '' ) {
			return '#';
		}
		return 'https://wa.me/' . $digits;
	}
}
