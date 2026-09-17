<?
/**
 * Per-country phone & WhatsApp theme options + resolution chain.
 */

if ( ! function_exists( 'kayan_i18n_get_supported_country_codes' ) ) {
	function kayan_i18n_get_supported_country_codes() {
		return array( 'ae', 'sa', 'kw', 'qa', 'bh', 'om' );
	}
}

if ( ! function_exists( 'kayan_i18n_get_current_country_code' ) ) {
	/**
	 * First URL path segment if it matches a supported country code.
	 *
	 * @return string|null ae|sa|kw|qa|bh|om or null.
	 */
	function kayan_i18n_get_current_country_code() {
		$path = function_exists( 'kayan_i18n_get_request_path' )
			? kayan_i18n_get_request_path()
			: wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );

		$path = is_string( $path ) ? trim( $path, '/' ) : '';
		if ( $path === '' ) {
			return null;
		}

		$segments = explode( '/', $path );
		$first    = isset( $segments[0] ) ? strtolower( $segments[0] ) : '';

		if ( $first !== '' && in_array( $first, kayan_i18n_get_supported_country_codes(), true ) ) {
			return $first;
		}

		return null;
	}
}

if ( ! function_exists( 'kayan_i18n_resolve_country_contact_code' ) ) {
	/**
	 * Country code for per-country contact options (URL segment or configured country).
	 *
	 * @return string|null
	 */
	function kayan_i18n_resolve_country_contact_code() {
		$code = kayan_i18n_get_current_country_code();
		if ( $code !== null ) {
			return $code;
		}

		$path = function_exists( 'kayan_i18n_get_request_path' )
			? kayan_i18n_get_request_path()
			: wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
		$path = is_string( $path ) ? trim( $path, '/' ) : '';
		if ( $path === '' ) {
			return 'ae';
		}

		if ( function_exists( 'kayan_i18n_get_country' ) ) {
			$from_config = kayan_i18n_get_country();
			if ( in_array( $from_config, kayan_i18n_get_supported_country_codes(), true ) ) {
				return $from_config;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'kayan_i18n_resolve_phone' ) ) {
	/**
	 * Post meta → per-country option → global theme phone.
	 *
	 * @param int|null $post_id Post/page ID; null uses get_queried_object_id().
	 * @return string
	 */
	function kayan_i18n_resolve_phone( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_queried_object_id();
		}
		$post_id = (int) $post_id;

		if ( $post_id > 0 ) {
			$meta = trim( (string) get_post_meta( $post_id, 'phone_number', true ) );
			if ( $meta !== '' ) {
				return $meta;
			}
		}

		$code = kayan_i18n_resolve_country_contact_code();
		if ( $code !== null ) {
			$country_phone = trim( (string) yc_get_option( 'kayan_country_' . $code . '_phone' ) );
			if ( $country_phone !== '' ) {
				return $country_phone;
			}
		}

		if ( function_exists( 'kayan_homepage_get_phone_raw' ) ) {
			return kayan_homepage_get_phone_raw();
		}

		$phone = trim( (string) yc_get_option( 'phonenumber' ) );
		if ( $phone === '' ) {
			$phone = trim( (string) yc_get_option( 'contact_number' ) );
		}
		return $phone;
	}
}

if ( ! function_exists( 'kayan_i18n_resolve_whatsapp' ) ) {
	/**
	 * Post meta → per-country option → global theme WhatsApp.
	 *
	 * @param int|null $post_id Post/page ID; null uses get_queried_object_id().
	 * @return string
	 */
	function kayan_i18n_resolve_whatsapp( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = get_queried_object_id();
		}
		$post_id = (int) $post_id;

		if ( $post_id > 0 ) {
			$meta = trim( (string) get_post_meta( $post_id, 'whatsapp_number', true ) );
			if ( $meta !== '' ) {
				return $meta;
			}
		}

		$code = kayan_i18n_resolve_country_contact_code();
		if ( $code !== null ) {
			$country_whatsapp = trim( (string) yc_get_option( 'kayan_country_' . $code . '_whatsapp' ) );
			if ( $country_whatsapp !== '' ) {
				return $country_whatsapp;
			}
		}

		if ( function_exists( 'kayan_homepage_get_whatsapp_raw' ) ) {
			return kayan_homepage_get_whatsapp_raw();
		}

		return trim( (string) yc_get_option( 'whatsapp_number' ) );
	}
}

if ( ! function_exists( 'kayan_i18n_register_country_contact_options' ) ) {
	function kayan_i18n_register_country_contact_options() {
		global $YC__CFM__global_setup_fields;

		if ( ! isset( $YC__CFM__global_setup_fields['ThemeOptions'] ) ) {
			$YC__CFM__global_setup_fields['ThemeOptions'] = array();
		}

		$fields = array(
			array(
				'type'  => 'Title',
				'title' => 'أرقام الاتصال حسب الدولة',
				'disc'  => 'تُستخدم عند زيارة مسار الدولة (مثل /sa/ أو /kw/). إن تُركت فارغة يُستخدم رقم إعدادات الاتصال العامة.',
			),
		);

		$countries = function_exists( 'kayan_i18n_get_countries' ) ? kayan_i18n_get_countries() : array();
		$supported = kayan_i18n_get_supported_country_codes();

		foreach ( $supported as $code ) {
			$name = $code;
			if ( isset( $countries[ $code ]['label_ar'] ) ) {
				$name = $countries[ $code ]['label_ar'];
			}

			$fields[] = array(
				'type'  => 'Title',
				'title' => $name,
			);
			$fields[] = array(
				'title' => 'رقم الاتصال (' . $name . ')',
				'type'  => 'Text',
				'id'    => 'kayan_country_' . $code . '_phone',
			);
			$fields[] = array(
				'title' => 'رقم واتساب (' . $name . ')',
				'type'  => 'Text',
				'id'    => 'kayan_country_' . $code . '_whatsapp',
			);
		}

		$YC__CFM__global_setup_fields['ThemeOptions']['kayan_i18n_country_contact'] = array(
			'title'    => ' اتصال حسب الدولة ',
			'en_title' => 'Country contact numbers',
			'icon'     => '<i class="fa-solid fa-phone-volume"></i>',
			'number'   => 49,
			'id'       => 'kayan_i18n_country_contact',
			'disc'     => 'أرقام هاتف وواتساب لكل دولة في مسارات kayan-i18n. الإمارات على الجذر / تستخدم خيارات ae عند ضبطها.',
			'fields'   => $fields,
		);
	}
}

add_action( 'YC__CFM__global_setup_fields', 'kayan_i18n_register_country_contact_options', 10 );
