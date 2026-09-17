<?
/**
 * الصفحة الرئيسية — تصميم 2026 (محتوى افتراضي قابل للتعديل من إعدادات القالب).
 */

if ( ! function_exists( 'kayan_homepage_uses_new_design' ) ) {
	function kayan_homepage_uses_new_design() {
		return true;
	}
}

if ( ! function_exists( 'kayan_homepage_get_company_name' ) ) {
	/**
	 * اسم الشركة من إعدادات القالب (sitename) — يعمل على أي موقع.
	 */
	function kayan_homepage_get_company_name() {
		if ( function_exists( 'kayan_seo_get_site_name' ) ) {
			$name = kayan_seo_get_site_name();
			if ( $name !== '' ) {
				return $name;
			}
		}

		$name = yc_get_option( 'sitename' );
		if ( ! empty( $name ) ) {
			return is_string( $name ) ? trim( $name ) : $name;
		}

		$name = get_bloginfo( 'name' );
		return is_string( $name ) ? trim( $name ) : '';
	}
}

if ( ! function_exists( 'kayan_homepage_get_brand_parts' ) ) {
	function kayan_homepage_get_brand_parts() {
		$first  = trim( (string) yc_get_option( 'kayan_homepage_brand_first' ) );
		$second = trim( (string) yc_get_option( 'kayan_homepage_brand_second' ) );

		if ( $first !== '' || $second !== '' ) {
			return array( $first, $second );
		}

		$name  = kayan_homepage_get_company_name();
		$parts = preg_split( '/\s+/u', trim( $name ), 2 );

		if ( is_array( $parts ) && count( $parts ) === 2 ) {
			return array( $parts[0], $parts[1] );
		}

		return array( $name, '' );
	}
}

if ( ! function_exists( 'kayan_homepage_get_context_post_id' ) ) {
	/**
	 * Post/page ID for homepage + contact/UI token context.
	 */
	function kayan_homepage_get_context_post_id() {
		if ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
			return (int) get_option( 'page_on_front' );
		}
		$id = (int) get_queried_object_id();
		return $id > 0 ? $id : 0;
	}
}

if ( ! function_exists( 'kayan_homepage_ui_string' ) ) {
	/**
	 * Localized UI label with Arabic fallback when kayan-i18n is inactive.
	 */
	function kayan_homepage_ui_string( $key, $fallback_ar ) {
		if ( function_exists( 'kayan_i18n_t' ) ) {
			$value = kayan_i18n_t( $key, $fallback_ar );
			if ( $value !== '' ) {
				return $value;
			}
		}
		return $fallback_ar;
	}
}

if ( ! function_exists( 'kayan_homepage_resolve_ui_tokens' ) ) {
	/**
	 * UI + contact tokens for body.html.php (locale switcher, buttons, tel/wa URLs).
	 *
	 * @param int|null $post_id Null → get_queried_object_id() (or static front page on home).
	 * @return array<string, string>
	 */
	function kayan_homepage_resolve_ui_tokens( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = kayan_homepage_get_context_post_id();
			if ( $post_id <= 0 ) {
				$post_id = (int) get_queried_object_id();
			}
		}
		$post_id = (int) $post_id;

		$switcher_html = '';
		if ( function_exists( 'kayan_i18n_get_switcher_html' ) ) {
			$switcher_html = kayan_i18n_get_switcher_html( array( 'instance_suffix' => 'Home' ) );
		}

		$whatsapp_url = '#';
		if ( function_exists( 'kayan_hp_resolve_whatsapp_url' ) ) {
			$whatsapp_url = kayan_hp_resolve_whatsapp_url( $post_id > 0 ? $post_id : null );
		}

		$tel_url = '#';
		if ( function_exists( 'kayan_hp_resolve_tel_url' ) ) {
			$tel_url = kayan_hp_resolve_tel_url( $post_id > 0 ? $post_id : null );
		}

		return array(
			'locale_switcher_html' => $switcher_html,
			'whatsapp_url'         => esc_url( $whatsapp_url ),
			'tel_url'              => esc_url( $tel_url ),
			'ui_btn_whatsapp'      => esc_html( kayan_homepage_ui_string( 'btn_whatsapp', 'واتساب' ) ),
			'ui_btn_whatsapp_full' => esc_html( kayan_homepage_ui_string( 'btn_whatsapp_full', 'تواصل واتساب' ) ),
			'ui_btn_call'          => esc_html( kayan_homepage_ui_string( 'btn_call', 'اتصل بنا' ) ),
			'ui_btn_quote'         => esc_html( kayan_homepage_ui_string( 'btn_quote', 'احصل على عرض' ) ),
			'ui_menu_label'        => esc_html( kayan_homepage_ui_string( 'menu_open', 'القائمة' ) ),
			'ui_close_label'       => esc_html( kayan_homepage_ui_string( 'menu_close', 'إغلاق' ) ),
		);
	}
}

if ( ! function_exists( 'kayan_homepage_get_phone_raw' ) ) {
	function kayan_homepage_get_phone_raw() {
		$phone = yc_get_option( 'phonenumber' );
		if ( empty( $phone ) ) {
			$phone = yc_get_option( 'contact_number' );
		}
		if ( empty( $phone ) ) {
			$phone = get_option( 'phonenumber' );
		}
		return trim( (string) $phone );
	}
}

if ( ! function_exists( 'kayan_homepage_get_whatsapp_raw' ) ) {
	function kayan_homepage_get_whatsapp_raw() {
		$number = yc_get_option( 'whatsapp_number' );
		if ( empty( $number ) ) {
			$number = get_option( 'whatsapp_number' );
		}
		if ( empty( $number ) ) {
			$number = kayan_homepage_get_phone_raw();
		}
		return trim( (string) $number );
	}
}

if ( ! function_exists( 'kayan_homepage_build_fab_html' ) ) {
	/**
	 * Fixed WhatsApp FAB (#fab) — revealed after scroll via kayan-home.js.
	 *
	 * @param int|null $post_id Post/page ID; null uses homepage context.
	 * @return string
	 */
	function kayan_homepage_build_fab_html( $post_id = null ) {
		if ( null === $post_id ) {
			$post_id = kayan_homepage_get_context_post_id();
			if ( $post_id <= 0 ) {
				$post_id = (int) get_queried_object_id();
			}
		}
		$post_id = (int) $post_id;

		$show = true;
		if ( function_exists( 'kayan_ui_show_whatsapp_button' ) ) {
			$show = (bool) kayan_ui_show_whatsapp_button( $post_id );
		}
		if ( ! $show ) {
			return '';
		}

		$wa_url = function_exists( 'kayan_hp_resolve_whatsapp_url' )
			? kayan_hp_resolve_whatsapp_url( $post_id > 0 ? $post_id : null )
			: '';
		if ( $wa_url === '' || $wa_url === '#' ) {
			return '';
		}

		$aria_ar = 'تواصل عبر واتساب';
		$aria    = kayan_homepage_ui_string( 'fab_whatsapp_aria', $aria_ar );

		return '<a href="' . esc_url( $wa_url ) . '" id="fab" class="fab" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $aria ) . '"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
	}
}

if ( ! function_exists( 'kayan_homepage_format_phone_display' ) ) {
	function kayan_homepage_format_phone_display( $phone = null ) {
		if ( null === $phone ) {
			$phone = kayan_hp_resolve_phone();
		}
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( strlen( $digits ) >= 11 && strpos( $digits, '971' ) === 0 ) {
			$local = substr( $digits, 3 );
			if ( strlen( $local ) >= 9 ) {
				return '+971 ' . substr( $local, 0, 2 ) . ' ' . substr( $local, 2, 3 ) . ' ' . substr( $local, 5 );
			}
		}
		return trim( (string) $phone );
	}
}

if ( ! function_exists( 'kayan_homepage_get_tel_url' ) ) {
	function kayan_homepage_get_tel_url( $phone = null ) {
		if ( null === $phone ) {
			$phone = kayan_homepage_get_phone_raw();
		}
		$digits = preg_replace( '/\D+/', '', (string) $phone );
		if ( $digits === '' ) {
			return '#';
		}
		return 'tel:+' . ltrim( $digits, '+' );
	}
}

if ( ! function_exists( 'kayan_homepage_get_whatsapp_url' ) ) {
	function kayan_homepage_get_whatsapp_url( $number = null ) {
		if ( null === $number ) {
			$number = kayan_homepage_get_whatsapp_raw();
		}
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

if ( ! function_exists( 'kayan_homepage_get_post_url' ) ) {
	function kayan_homepage_get_post_url( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return '#';
		}

		if (
			function_exists( 'kayan_i18n_build_url' )
			&& function_exists( 'kayan_i18n_get_country' )
			&& function_exists( 'kayan_i18n_get_lang' )
			&& $post->post_name !== ''
		) {
			return kayan_i18n_build_url( kayan_i18n_get_country(), kayan_i18n_get_lang(), $post->post_name );
		}

		$url = get_permalink( $post );
		return $url ? $url : '#';
	}
}

if ( ! function_exists( 'kayan_homepage_resolve_url_token' ) ) {
	function kayan_homepage_resolve_url_token( $url, $fallback = '#' ) {
		$url = trim( (string) $url );
		if ( $url === '' ) {
			$url = $fallback;
		}

		$home = function_exists( 'kayan_i18n_home_url' ) ? kayan_i18n_home_url() : home_url( '/' );
		$tokens = array(
			'{{home_url}}'     => $home,
			'{{contact_url}}'  => trailingslashit( $home ) . '#contact',
			'{{whatsapp_url}}' => function_exists( 'kayan_hp_resolve_whatsapp_url' ) ? kayan_hp_resolve_whatsapp_url() : kayan_homepage_get_whatsapp_url(),
			'{{tel_url}}'      => function_exists( 'kayan_hp_resolve_tel_url' ) ? kayan_hp_resolve_tel_url() : kayan_homepage_get_tel_url(),
		);

		$url = str_replace( array_keys( $tokens ), array_values( $tokens ), $url );
		$url = preg_replace_callback(
			'/\{\{(?:post|page)_url:(\d+)\}\}/',
			function ( $matches ) {
				return kayan_homepage_get_post_url( (int) $matches[1] );
			},
			$url
		);

		return kayan_homepage_expand_inline_tokens( $url );
	}
}

if ( ! function_exists( 'kayan_homepage_expand_inline_tokens' ) ) {
	function kayan_homepage_expand_inline_tokens( $text ) {
		$company = kayan_homepage_get_company_name();
		$text    = str_replace( '{{company_name}}', $company, $text );
		$text    = str_replace( '{{year}}', gmdate( 'Y' ), $text );

		if ( function_exists( 'kayan_i18n_get_lang' ) ) {
			$lang = kayan_i18n_get_lang();
			$text = str_replace( '{{country_name}}', kayan_i18n_country_label( null, $lang ), $text );
			$text = str_replace( '{{in_country}}', kayan_i18n_country_in_phrase( null, $lang ), $text );
			$text = str_replace( '{{all_regions}}', kayan_i18n_country_regions( null, $lang ), $text );
		}

		return $text;
	}
}

if ( ! function_exists( 'kayan_homepage_is_english' ) ) {
	function kayan_homepage_is_english() {
		return function_exists( 'kayan_i18n_is_english' ) && kayan_i18n_is_english();
	}
}

if ( ! function_exists( 'kayan_homepage_pick_locale_text' ) ) {
	function kayan_homepage_pick_locale_text( $key_ar, $default_ar, $key_en, $default_en ) {
		if ( kayan_homepage_is_english() ) {
			return kayan_homepage_get_option_text( $key_en, $default_en );
		}
		return kayan_homepage_get_option_text( $key_ar, $default_ar );
	}
}

if ( ! function_exists( 'kayan_homepage_get_address' ) ) {
	function kayan_homepage_get_address() {
		$address = yc_get_option( 'company__adress' );
		if ( empty( $address ) && function_exists( 'kayan_i18n_country_address' ) ) {
			$address = kayan_i18n_country_address();
		}
		if ( empty( $address ) ) {
			$address = 'دبي، الإمارات العربية المتحدة';
		}
		return trim( (string) $address );
	}
}

if ( ! function_exists( 'kayan_homepage_get_option_text' ) ) {
	function kayan_homepage_get_option_text( $key, $default ) {
		$value = yc_get_option( $key );
		if ( $value === '' || $value === null ) {
			$alt_key = null;
			if ( strpos( $key, 'kayan_homepage_' ) === 0 ) {
				$alt_key = 'kayan_hp_' . substr( $key, 15 );
			} elseif ( strpos( $key, 'kayan_hp_' ) === 0 ) {
				$alt_key = 'kayan_homepage_' . substr( $key, 9 );
			}
			if ( $alt_key !== null ) {
				$alt = yc_get_option( $alt_key );
				if ( $alt !== '' && $alt !== null ) {
					$value = $alt;
				}
			}
		}
		if ( $value === '' || $value === null ) {
			$value = $default;
		}
		return trim( (string) $value );
	}
}

if ( ! function_exists( 'kayan_homepage_get_option_html' ) ) {
	function kayan_homepage_get_option_html( $key, $default ) {
		$value = kayan_homepage_get_option_text( $key, $default );
		$value = kayan_homepage_expand_inline_tokens( $value );
		return wp_kses_post( $value );
	}
}

if ( ! function_exists( 'kayan_homepage_build_social_links_html' ) ) {
	function kayan_homepage_build_social_links_html() {
		$networks = array(
			'instagram' => array( 'icon' => 'fab fa-instagram', 'label' => 'Instagram' ),
			'facebook'  => array( 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook' ),
			'youtube'   => array( 'icon' => 'fab fa-youtube', 'label' => 'YouTube' ),
			'twitter'   => array( 'icon' => 'fab fa-x-twitter', 'label' => 'X' ),
			'linkedin'  => array( 'icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn' ),
			'threads'   => array( 'icon' => 'fab fa-threads', 'label' => 'Threads' ),
			'telegram'  => array( 'icon' => 'fab fa-telegram', 'label' => 'Telegram' ),
		);

		$html = '';
		foreach ( $networks as $id => $data ) {
			$url = trim( (string) yc_get_option( $id ) );
			if ( $url === '' ) {
				continue;
			}
			$html .= '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $data['label'] ) . '" rel="noopener noreferrer" target="_blank"><i class="' . esc_attr( $data['icon'] ) . '"></i></a>';
		}

		$wa_url = kayan_hp_resolve_whatsapp_url();
		if ( $wa_url !== '#' ) {
			$html .= '<a href="' . esc_url( $wa_url ) . '" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>';
		}

		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_blog_posts_html' ) ) {
	function kayan_homepage_build_blog_posts_html() {
		$limit = (int) kayan_homepage_get_option_text( 'kayan_homepage_blog_count', '3' );
		if ( $limit < 1 ) {
			$limit = 3;
		}
		if ( $limit > 6 ) {
			$limit = 6;
		}

		$posts = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		if ( empty( $posts ) ) {
			return '';
		}

		$gradients = array(
			'linear-gradient(135deg,var(--blue),var(--navy2))',
			'linear-gradient(135deg,var(--turq),var(--aqua))',
			'linear-gradient(135deg,var(--gold),#ffce6b)',
			'linear-gradient(135deg,var(--success),var(--turq))',
		);
		$icons     = array( 'fas fa-droplet', 'fas fa-layer-group', 'fas fa-snowflake', 'fas fa-wrench' );

		$html = '';
		$i    = 0;
		foreach ( $posts as $post ) {
			$gradient = $gradients[ $i % count( $gradients ) ];
			$icon     = $icons[ $i % count( $icons ) ];
			$thumb    = get_the_post_thumbnail_url( $post, 'medium_large' );
			$cats     = get_the_category( $post->ID );
			$cat_name = ! empty( $cats ) ? $cats[0]->name : 'مقال';
			$style    = $thumb ? 'background-image:url(' . esc_url( $thumb ) . ');background-size:cover;background-position:center' : 'background:' . $gradient;

			$html .= '<article class="post rv">';
			$html .= '<div class="post-img" style="' . esc_attr( $style ) . '">';
			if ( ! $thumb ) {
				$html .= '<i class="' . esc_attr( $icon ) . '"></i>';
			}
			$html .= '</div><div class="post-body">';
			$html .= '<span class="post-cat">' . esc_html( $cat_name ) . '</span>';
			$html .= '<span class="post-date">' . esc_html( get_the_date( 'j F Y', $post ) ) . '</span>';
			$html .= '<h3>' . esc_html( get_the_title( $post ) ) . '</h3>';
			$html .= '<p>' . esc_html( wp_trim_words( get_the_excerpt( $post ), 18, '…' ) ) . '</p>';
			$html .= '<a class="read" href="' . esc_url( kayan_homepage_get_post_url( $post ) ) . '">' . esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'read_article' ) : 'اقرأ المقال' ) . ' <i class="fas fa-arrow-left"></i></a>';
			$html .= '</div></article>';
			$i++;
		}

		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_get_tokens' ) ) {
	function kayan_homepage_get_tokens() {
		list( $brand_first, $brand_second ) = kayan_homepage_get_brand_parts();
		$company_name                       = kayan_homepage_get_company_name();
		$context_post_id = kayan_homepage_get_context_post_id();
		$phone_raw       = kayan_hp_resolve_phone( $context_post_id > 0 ? $context_post_id : null );
		$phone_display                      = kayan_homepage_format_phone_display( $phone_raw );
		$year                               = gmdate( 'Y' );

		$hero_title_default_ar     = '{{company_name}} — منصة <em>الخدمات المنزلية المتكاملة</em> الأولى في {{country_name}}';
		$hero_title_default_en     = '{{company_name}} — The Leading <em>Integrated Home Services</em> Platform in {{country_name}}';
		$hero_subtitle_default_ar  = 'من عزل الأسطح وكشف التسربات إلى صيانة التكييف والتنظيف الاحترافي — فريق معتمد، أجهزة حديثة، وضمان مكتوب يصل إلى 10 سنوات.';
		$hero_subtitle_default_en  = 'From roof insulation and leak detection to AC maintenance and deep cleaning — certified teams, modern equipment, and a written warranty up to 10 years.';
		$dashboard_title_default_ar = 'لوحة خدمات {{company_name}}';
		$dashboard_title_default_en = '{{company_name}} Services Dashboard';
		$why_heading_default_ar    = 'لماذا يختار الآلاف <span>{{company_name}}؟</span>';
		$why_heading_default_en    = 'Why do thousands choose <span>{{company_name}}?</span>';
		$compare_heading_default_ar = 'لماذا يختار العملاء <span>{{company_name}}؟</span>';
		$compare_heading_default_en = 'Why customers choose <span>{{company_name}}?</span>';
		$areas_intro_default_ar    = 'أينما كنت في {{in_country}}، فريق {{company_name}} قريب منك وجاهز للخدمة.';
		$areas_intro_default_en    = 'Wherever you are in {{in_country}}, the {{company_name}} team is nearby and ready to help.';
		$footer_tagline_default_ar = 'منصة الخدمات المنزلية المتكاملة الأولى في {{country_name}} — جودة احترافية وضمان مكتوب وثقة 15,000+ عميل.';
		$footer_tagline_default_en = 'A leading integrated home services platform in {{country_name}} — professional quality, written warranty, and 15,000+ happy clients.';
		$copyright_default_ar      = '© {{year}} {{company_name}} للخدمات المنزلية. جميع الحقوق محفوظة.';
		$copyright_default_en      = '© {{year}} {{company_name}} Home Services. All rights reserved.';

		$hero_title     = kayan_homepage_pick_locale_text( 'kayan_hp_hero_title', $hero_title_default_ar, 'kayan_hp_hero_title_en', $hero_title_default_en );
		$hero_subtitle  = kayan_homepage_pick_locale_text( 'kayan_hp_hero_subtitle', $hero_subtitle_default_ar, 'kayan_hp_hero_subtitle_en', $hero_subtitle_default_en );
		$hero_highlight = trim( (string) yc_get_option( 'kayan_hp_hero_title_highlight' ) );
		if ( $hero_highlight !== '' && strpos( $hero_title, '<em>' ) === false ) {
			$hero_title = preg_replace(
				'/' . preg_quote( $hero_highlight, '/' ) . '/u',
				'<em>' . $hero_highlight . '</em>',
				$hero_title,
				1
			);
		}
		$dashboard_title = kayan_homepage_pick_locale_text( 'kayan_homepage_dashboard_title', $dashboard_title_default_ar, 'kayan_homepage_dashboard_title_en', $dashboard_title_default_en );
		$why_heading    = kayan_homepage_pick_locale_text( 'kayan_homepage_why_heading', $why_heading_default_ar, 'kayan_homepage_why_heading_en', $why_heading_default_en );
		$compare_heading = kayan_homepage_pick_locale_text( 'kayan_homepage_compare_heading', $compare_heading_default_ar, 'kayan_homepage_compare_heading_en', $compare_heading_default_en );
		$areas_intro    = kayan_homepage_pick_locale_text( 'kayan_homepage_areas_intro', $areas_intro_default_ar, 'kayan_homepage_areas_intro_en', $areas_intro_default_en );
		$footer_tagline = kayan_homepage_pick_locale_text( 'kayan_homepage_footer_tagline', $footer_tagline_default_ar, 'kayan_homepage_footer_tagline_en', $footer_tagline_default_en );
		$copyright      = kayan_homepage_pick_locale_text( 'kayan_homepage_copyright', $copyright_default_ar, 'kayan_homepage_copyright_en', $copyright_default_en );

		$context_post_id  = kayan_homepage_get_context_post_id();
		$ui_tokens        = kayan_homepage_resolve_ui_tokens( $context_post_id > 0 ? $context_post_id : null );
		$header_logo_html = function_exists( 'kayan_homepage_build_logo_html' ) ? kayan_homepage_build_logo_html( 'header', 'logo' ) : '';
		$header_nav_html  = function_exists( 'kayan_homepage_build_nav_links_html' ) ? kayan_homepage_build_nav_links_html() : '';
		$footer_html      = function_exists( 'kayan_homepage_build_footer_html' ) ? kayan_homepage_build_footer_html() : '';
		$floating_buttons = function_exists( 'kayan_homepage_build_floating_buttons_html' ) ? kayan_homepage_build_floating_buttons_html() : '';
		$services_grid    = function_exists( 'kayan_homepage_build_services_grid_html' ) ? kayan_homepage_build_services_grid_html() : '';
		$cities_grid      = function_exists( 'kayan_homepage_build_cities_grid_html' ) ? kayan_homepage_build_cities_grid_html() : '';
		$services_head    = function_exists( 'kayan_homepage_build_section_head_html' )
			? kayan_homepage_build_section_head_html( 'services', 'خدماتنا', 'خدماتنا المنزلية <span>المتكاملة</span>', 'حلول احترافية شاملة تغطي كل احتياجات منزلك أو منشأتك بأعلى معايير الجودة والضمان.', 'Our services', 'Integrated <span>home services</span>', 'Professional solutions for your home or business.' )
			: '';

		$area_cards       = function_exists( 'kayan_homepage_build_area_cards_html' ) ? kayan_homepage_build_area_cards_html() : '';
		$areas_head       = function_exists( 'kayan_homepage_build_section_head_html' )
			? kayan_homepage_build_section_head_html( 'areas', 'مناطق الخدمة', 'خدماتنا في جميع <span>{{all_regions}}</span>', $areas_intro_default_ar, 'Service areas', 'We serve <span>{{all_regions}}</span>', $areas_intro_default_en )
			: '';
		$projects_head    = function_exists( 'kayan_homepage_build_section_head_html' )
			? kayan_homepage_build_section_head_html( 'projects', 'أعمالنا', 'مشاريعنا <span>المنجزة</span>', 'نماذج من مشاريعنا المنشورة في الموقع.', 'Our work', 'Completed <span>projects</span>', 'Published portfolio from your site.' )
			: '';
		$blog_head        = function_exists( 'kayan_homepage_build_section_head_html' )
			? kayan_homepage_build_section_head_html( 'blog', 'المدونة', 'مقالات ونصائح <span>مفيدة</span>', 'أحدث المقالات المنشورة في موقعك.', 'Blog', 'Helpful <span>articles</span>', 'Latest posts from your site.' )
			: '';
		$projects_grid    = function_exists( 'kayan_homepage_build_projects_grid_html' ) ? kayan_homepage_build_projects_grid_html() : '';
		$mobile_nav       = function_exists( 'kayan_homepage_build_mobile_nav_html' ) ? kayan_homepage_build_mobile_nav_html() : '';
		$hero_proof       = function_exists( 'kayan_homepage_build_hero_proof_html' ) ? kayan_homepage_build_hero_proof_html() : '';
		$hero_dashboard   = function_exists( 'kayan_homepage_build_hero_dashboard_html' ) ? kayan_homepage_build_hero_dashboard_html() : '';
		$trustbar         = function_exists( 'kayan_homepage_build_trustbar_html' ) ? kayan_homepage_build_trustbar_html() : '';
		$atb              = function_exists( 'kayan_homepage_build_atb_html' ) ? kayan_homepage_build_atb_html() : '';
		$finder           = function_exists( 'kayan_homepage_build_finder_html' ) ? kayan_homepage_build_finder_html() : '';
		$why_body         = function_exists( 'kayan_homepage_build_why_body_html' ) ? kayan_homepage_build_why_body_html() : '';
		$team_grid        = function_exists( 'kayan_homepage_build_team_grid_html' ) ? kayan_homepage_build_team_grid_html() : '';
		$team_head        = function_exists( 'kayan_homepage_build_section_head_html' )
			? kayan_homepage_build_section_head_html( 'team', 'فريقنا', 'خبراؤنا في <span>خدمتكم</span>', 'فريق ' . $company_name . ' المعتمد.', 'Our team', 'Experts at <span>your service</span>', 'The certified ' . $company_name . ' team.' )
			: '';
		$stats_section    = function_exists( 'kayan_homepage_build_stats_section_html' ) ? kayan_homepage_build_stats_section_html() : '';
		$compare_body     = function_exists( 'kayan_homepage_build_compare_body_html' ) ? kayan_homepage_build_compare_body_html() : '';
		$ba_html          = function_exists( 'kayan_homepage_build_ba_html' ) ? kayan_homepage_build_ba_html() : '';
		$reviews_html     = function_exists( 'kayan_homepage_build_reviews_html' ) ? kayan_homepage_build_reviews_html() : '';
		$faq_html         = function_exists( 'kayan_homepage_build_faq_html' ) ? kayan_homepage_build_faq_html() : '';
		$pricing_html     = function_exists( 'kayan_homepage_build_pricing_html' ) ? kayan_homepage_build_pricing_html() : '';
		$cta_html         = function_exists( 'kayan_homepage_build_cta_html' ) ? kayan_homepage_build_cta_html() : '';

		$tokens = array_merge(
			array(
			'header_logo_html'       => $header_logo_html,
			'header_nav_html'        => $header_nav_html,
			'header_mobile_nav_html' => $mobile_nav,
			'hero_proof_html'        => $hero_proof,
			'hero_dashboard_html'    => $hero_dashboard,
			'trustbar_html'          => $trustbar,
			'atb_html'               => $atb,
			'finder_html'            => $finder,
			'why_body_html'          => $why_body,
			'team_head_html'         => $team_head,
			'team_grid_html'         => $team_grid,
			'stats_section_html'     => $stats_section,
			'compare_body_html'      => $compare_body,
			'ba_html'                => $ba_html,
			'reviews_html'           => $reviews_html,
			'faq_html'               => $faq_html,
			'pricing_html'           => $pricing_html,
			'cta_html'               => $cta_html,
			'footer_html'            => $footer_html,
			'floating_buttons_html'  => $floating_buttons,
			'fab_html'               => kayan_homepage_build_fab_html(),
			'services_grid_html'   => $services_grid,
			'cities_grid_html'     => $cities_grid,
			'services_head_html'   => $services_head,
			'areas_head_html'      => $areas_head,
			'area_cards_html'      => $area_cards,
			'projects_head_html'   => $projects_head,
			'projects_grid_html'   => $projects_grid,
			'blog_head_html'       => $blog_head,
			'brand_first'          => esc_html( $brand_first ),
			'brand_second'         => esc_html( $brand_second ),
			'company_name'         => esc_html( $company_name ),
			'country_name'         => esc_html( function_exists( 'kayan_i18n_country_label' ) ? kayan_i18n_country_label() : 'الإمارات' ),
			'all_regions'          => esc_html( function_exists( 'kayan_i18n_country_regions' ) ? kayan_i18n_country_regions() : 'جميع الإمارات' ),
			'phone_display'        => esc_html( $phone_display ),
			'address'              => esc_html( kayan_homepage_get_address() ),
			'hero_title_html'      => wp_kses_post( kayan_homepage_expand_inline_tokens( $hero_title ) ),
			'hero_subtitle'        => esc_html( kayan_homepage_expand_inline_tokens( $hero_subtitle ) ),
			'dashboard_title'      => esc_html( kayan_homepage_expand_inline_tokens( $dashboard_title ) ),
			'why_heading_html'     => wp_kses_post( kayan_homepage_expand_inline_tokens( $why_heading ) ),
			'compare_heading_html' => wp_kses_post( kayan_homepage_expand_inline_tokens( $compare_heading ) ),
			'areas_intro'          => esc_html( kayan_homepage_expand_inline_tokens( $areas_intro ) ),
			'footer_tagline'       => esc_html( kayan_homepage_expand_inline_tokens( $footer_tagline ) ),
			'copyright'            => esc_html( kayan_homepage_expand_inline_tokens( $copyright ) ),
			'social_links_html'    => kayan_homepage_build_social_links_html(),
			'blog_posts_html'      => kayan_homepage_build_blog_posts_html(),
			'ui_btn_service'       => esc_html( kayan_homepage_ui_string( 'btn_service', 'طلب خدمة' ) ),
			'ui_btn_call_short'    => esc_html( kayan_homepage_ui_string( 'btn_call_short', 'اتصال' ) ),
			'ui_nav_services'      => esc_html( kayan_homepage_ui_string( 'nav_services', 'الخدمات' ) ),
			'ui_nav_cities'        => esc_html( kayan_homepage_ui_string( 'nav_cities', 'المدن' ) ),
			'ui_nav_projects'      => esc_html( kayan_homepage_ui_string( 'nav_projects', 'المشاريع' ) ),
			'ui_nav_blog'          => esc_html( kayan_homepage_ui_string( 'nav_blog', 'المدونة' ) ),
			'ui_nav_about'         => esc_html( kayan_homepage_ui_string( 'nav_about', 'من نحن' ) ),
			'ui_nav_faq'           => esc_html( kayan_homepage_ui_string( 'nav_faq', 'الأسئلة الشائعة' ) ),
			),
			$ui_tokens
		);

		return apply_filters( 'kayan_homepage_tokens', $tokens );
	}
}

if ( ! function_exists( 'kayan_home_body_classes' ) ) {
	function kayan_home_body_classes() {
		$classes = array( 'kayan-homepage-v3', 'kayan-homepage-dynamic' );
		if ( kayan_homepage_is_english() ) {
			$classes[] = 'kayan-homepage-en';
		}
		if ( function_exists( 'kayan_ui_show_call_button' ) && ! kayan_ui_show_call_button() ) {
			$classes[] = 'kayan-no-content-call';
		}
		if ( function_exists( 'kayan_ui_show_floating_call_button' ) && ! kayan_ui_show_floating_call_button( 0, false ) ) {
			$classes[] = 'kayan-no-floating-call';
		}
		return implode( ' ', $classes );
	}
}

if ( ! function_exists( 'kayan_homepage_v3_filter_html' ) ) {
	function kayan_homepage_v3_filter_html( $html ) {
		$tokens = kayan_homepage_get_tokens();
		foreach ( $tokens as $key => $value ) {
			$html = str_replace( '{{' . $key . '}}', $value, $html );
		}
		if ( function_exists( 'kayan_hp_apply_section_visibility' ) ) {
			$html = kayan_hp_apply_section_visibility( $html );
		}
		if ( function_exists( 'kayan_hp_auto_hide_empty_sections' ) ) {
			$html = kayan_hp_auto_hide_empty_sections( $html );
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_inner_hero' ) ) {
	/**
	 * Shared inner-page hero — kit .phero chrome, WordPress title/content.
	 *
	 * @param string   $title    Hero heading.
	 * @param string   $subtitle Optional subtext (tags stripped).
	 * @param string   $bg_image Attachment ID, URL, or file array.
	 * @param int|null $post_id  Reserved for context (alt text / future use).
	 */
	function kayan_homepage_inner_hero( $title, $subtitle = '', $bg_image = '', $post_id = null ) {
		unset( $post_id );
		if ( function_exists( 'kayan_kit_render_phero' ) ) {
			kayan_kit_render_phero(
				array(
					'title'     => $title,
					'subtitle'  => $subtitle,
					'image_url' => $bg_image,
					'compact'   => true,
				)
			);
			return;
		}

		$title     = trim( (string) $title );
		$subtitle  = trim( wp_strip_all_tags( (string) $subtitle ) );
		$image_url = '';

		if ( ! empty( $bg_image ) ) {
			if ( is_numeric( $bg_image ) ) {
				$image_url = wp_get_attachment_image_url( (int) $bg_image, 'full' );
			} elseif ( is_array( $bg_image ) ) {
				if ( ! empty( $bg_image['url'] ) ) {
					$image_url = (string) $bg_image['url'];
				} elseif ( ! empty( $bg_image['id'] ) ) {
					$image_url = wp_get_attachment_image_url( (int) $bg_image['id'], 'full' );
				}
			} else {
				$image_url = trim( (string) $bg_image );
			}
		}

		$hero_class = 'phero compact kayan-inner-hero';
		if ( $image_url === '' ) {
			$hero_class .= ' kayan-inner-hero--gradient';
		}

		echo '<section class="' . esc_attr( $hero_class ) . '">';
		if ( $image_url !== '' ) {
			echo '<div class="kayan-inner-hero__media" aria-hidden="true">';
			echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" loading="eager" decoding="async" />';
			echo '</div>';
			echo '<div class="kayan-inner-hero__overlay" aria-hidden="true"></div>';
		}
		echo '<div class="wrap">';
		if ( $title !== '' ) {
			echo '<h1>' . esc_html( $title ) . '</h1>';
		}
		if ( $subtitle !== '' ) {
			echo '<p class="psub">' . esc_html( $subtitle ) . '</p>';
		}
		echo '</div>';
		echo '</section>';
	}
}
