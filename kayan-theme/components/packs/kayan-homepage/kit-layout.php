<?
/**
 * Kit layout helpers — visual chrome from the attached HTML kit,
 * content always from WordPress (posts, terms, ThemeOptions, widgets).
 */

if ( ! function_exists( 'kayan_kit_excerpt' ) ) {
	function kayan_kit_excerpt( $text, $length = 160 ) {
		$text = trim( wp_strip_all_tags( (string) $text ) );
		if ( $text === '' ) {
			return '';
		}
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $text, 'UTF-8' ) > $length ) {
			return mb_substr( $text, 0, $length, 'UTF-8' ) . '…';
		}
		if ( strlen( $text ) > $length ) {
			return substr( $text, 0, $length ) . '…';
		}
		return $text;
	}
}

if ( ! function_exists( 'kayan_kit_page_styles' ) ) {
	function kayan_kit_page_styles( $extra = array() ) {
		$styles = array(
			'kayan-home'  => 'kayan-home.css',
			'kayan-inner' => 'kayan-inner.css',
			'shortcodes'  => 'shortcodes.css',
		);
		return array_merge( $styles, is_array( $extra ) ? $extra : array() );
	}
}

if ( ! function_exists( 'kayan_kit_resolve_image_url' ) ) {
	function kayan_kit_resolve_image_url( $bg_image ) {
		if ( empty( $bg_image ) ) {
			return '';
		}
		if ( is_numeric( $bg_image ) ) {
			$url = wp_get_attachment_image_url( (int) $bg_image, 'full' );
			return $url ? $url : '';
		}
		if ( is_array( $bg_image ) ) {
			if ( ! empty( $bg_image['url'] ) ) {
				return (string) $bg_image['url'];
			}
			if ( ! empty( $bg_image['id'] ) ) {
				$url = wp_get_attachment_image_url( (int) $bg_image['id'], 'full' );
				return $url ? $url : '';
			}
			return '';
		}
		return trim( (string) $bg_image );
	}
}

if ( ! function_exists( 'kayan_kit_render_crumb' ) ) {
	function kayan_kit_render_crumb() {
		echo '<nav class="crumb" aria-label="breadcrumb">';
		if ( function_exists( 'Breadcrumb' ) ) {
			ob_start();
			Breadcrumb();
			$html = ob_get_clean();
			if ( $html !== '' ) {
				$html = str_replace( 'class="BreadcrumbsFilters"', 'class="BreadcrumbsFilters crumb-trail"', $html );
				echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<a href="' . esc_url( home_url( '/' ) ) . '">الرئيسية</a>';
			}
		} else {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '">الرئيسية</a>';
		}
		echo '</nav>';
	}
}

if ( ! function_exists( 'kayan_kit_render_phero' ) ) {
	/**
	 * Inner-page hero matching the attached kit (.phero).
	 *
	 * @param array $args title, subtitle, image_url, compact, meta_html, show_crumb, category_term, total_rate.
	 */
	function kayan_kit_render_phero( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'         => '',
				'subtitle'      => '',
				'image_url'     => '',
				'image_alt'     => '',
				'compact'       => true,
				'meta_html'     => '',
				'show_crumb'    => true,
				'category_term' => null,
				'total_rate'    => 0,
			)
		);

		$title     = trim( (string) $args['title'] );
		$subtitle  = kayan_kit_excerpt( $args['subtitle'], 220 );
		$image_url = kayan_kit_resolve_image_url( $args['image_url'] );
		$image_alt = trim( (string) $args['image_alt'] );
		if ( $image_alt === '' ) {
			$image_alt = $title;
		}

		$classes = array( 'phero', 'kayan-inner-hero' );
		if ( ! empty( $args['compact'] ) ) {
			$classes[] = 'compact';
		}
		if ( $image_url === '' ) {
			$classes[] = 'kayan-inner-hero--gradient';
		}

		echo '<section class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		if ( $image_url !== '' ) {
			echo '<div class="kayan-inner-hero__media" aria-hidden="true">';
			echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '" loading="eager" decoding="async" />';
			echo '</div>';
			echo '<div class="kayan-inner-hero__overlay" aria-hidden="true"></div>';
		}
		echo '<div class="wrap">';
		if ( ! empty( $args['show_crumb'] ) ) {
			kayan_kit_render_crumb();
		}
		if ( $title !== '' ) {
			echo '<h1>' . esc_html( $title ) . '</h1>';
		}
		if ( $subtitle !== '' ) {
			echo '<p class="psub">' . esc_html( $subtitle ) . '</p>';
		}

		$meta = trim( (string) $args['meta_html'] );
		$has_term = is_object( $args['category_term'] ) && ! empty( $args['category_term']->name );
		$rate     = (float) $args['total_rate'];
		if ( $meta !== '' || $has_term || $rate > 0 ) {
			echo '<div class="phero-meta">';
			if ( $has_term ) {
				$term      = $args['category_term'];
				$term_link = isset( $term->term_link ) ? $term->term_link : get_term_link( $term );
				$term_link = is_wp_error( $term_link ) ? '#' : $term_link;
				echo '<div><b><a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a></b><small>التصنيف</small></div>';
			}
			if ( $rate > 0 ) {
				echo '<div><b>' . esc_html( number_format_i18n( $rate, 1 ) ) . '</b><small>التقييم</small></div>';
			}
			if ( $meta !== '' ) {
				echo $meta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</div>';
		}
		echo '</div>';
		echo '</section>';
	}
}

if ( ! function_exists( 'kayan_kit_open_section' ) ) {
	function kayan_kit_open_section( $extra_class = '' ) {
		$class = trim( 'sec kayan-inner-body ' . $extra_class );
		echo '<section class="' . esc_attr( $class ) . '">';
		echo '<div class="wrap">';
	}
}

if ( ! function_exists( 'kayan_kit_close_section' ) ) {
	function kayan_kit_close_section() {
		echo '</div></section>';
	}
}

if ( ! function_exists( 'kayan_kit_open_article_layout' ) ) {
	function kayan_kit_open_article_layout( $has_sidebar = true ) {
		kayan_kit_open_section();
		if ( $has_sidebar ) {
			echo '<div class="article-layout">';
			echo '<div class="article-body prose">';
		}
	}
}

if ( ! function_exists( 'kayan_kit_open_sidebar' ) ) {
	function kayan_kit_open_sidebar() {
		echo '</div>';
		echo '<aside>';
	}
}

if ( ! function_exists( 'kayan_kit_close_article_layout' ) ) {
	function kayan_kit_close_article_layout( $has_sidebar = true ) {
		if ( $has_sidebar ) {
			echo '</aside></div>';
		}
		kayan_kit_close_section();
	}
}

if ( ! function_exists( 'kayan_kit_render_cta_widget' ) ) {
	function kayan_kit_render_cta_widget( $post_id = null ) {
		$post_id   = (int) $post_id;
		$show_call = ! function_exists( 'kayan_ui_show_call_button' ) || kayan_ui_show_call_button();
		$phone     = function_exists( 'kayan_hp_resolve_phone' ) ? kayan_hp_resolve_phone( $post_id > 0 ? $post_id : null ) : '';
		$tel_url   = function_exists( 'kayan_hp_resolve_tel_url' ) ? kayan_hp_resolve_tel_url( $post_id > 0 ? $post_id : null ) : '#';
		$wa_url    = function_exists( 'kayan_hp_resolve_whatsapp_url' ) ? kayan_hp_resolve_whatsapp_url( $post_id > 0 ? $post_id : null ) : '#';
		$company   = function_exists( 'kayan_homepage_get_company_name' ) ? kayan_homepage_get_company_name() : get_bloginfo( 'name' );
		$btn_call  = function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_call', 'اتصل' ) : 'اتصل';
		$btn_wa    = function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_whatsapp_full', 'واتساب' ) : 'واتساب';

		echo '<div class="side-w cta">';
		echo '<h4>' . esc_html( function_exists( 'kayan_homepage_ui_string' ) ? kayan_homepage_ui_string( 'btn_service', 'تواصل معنا' ) : 'تواصل معنا' ) . '</h4>';
		echo '<p>' . esc_html( $company ) . '</p>';
		if ( $wa_url !== '#' && $wa_url !== '' ) {
			echo '<a href="' . esc_url( $wa_url ) . '" class="btn btn-wa" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> ' . esc_html( $btn_wa ) . '</a>';
		}
		if ( $show_call && $tel_url !== '#' && trim( (string) $phone ) !== '' ) {
			echo '<a href="' . esc_url( $tel_url ) . '" class="btn btn-call" style="margin-top:10px"><i class="fas fa-phone"></i> ' . esc_html( $btn_call ) . '</a>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_kit_term_icon_class' ) ) {
	function kayan_kit_term_icon_class( $term, $fallback = 'fas fa-location-dot' ) {
		$icon = '';
		if ( is_object( $term ) && isset( $term->term_id ) ) {
			$icon = (string) get_term_meta( $term->term_id, 'icon', true );
		}
		$icon = trim( wp_strip_all_tags( $icon ) );
		if ( $icon === '' ) {
			return $fallback;
		}
		if ( strpos( $icon, 'fa-' ) === false ) {
			return $fallback;
		}
		return $icon;
	}
}

if ( ! function_exists( 'kayan_kit_page_url_by_slugs' ) ) {
	function kayan_kit_page_url_by_slugs( $slugs, $fallback = '' ) {
		foreach ( (array) $slugs as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page instanceof WP_Post ) {
				return get_permalink( $page );
			}
		}
		return $fallback !== '' ? $fallback : home_url( '/' );
	}
}

if ( ! function_exists( 'kayan_kit_contact_info_card' ) ) {
	function kayan_kit_contact_info_card( $post_id = null ) {
		$post_id = (int) $post_id;
		$phone   = function_exists( 'kayan_hp_resolve_phone' ) ? kayan_hp_resolve_phone( $post_id > 0 ? $post_id : null ) : (string) yc_get_option( 'phonenumber' );
		$tel_url = function_exists( 'kayan_hp_resolve_tel_url' ) ? kayan_hp_resolve_tel_url( $post_id > 0 ? $post_id : null ) : 'tel:' . preg_replace( '/\D+/', '', (string) $phone );
		$wa_url  = function_exists( 'kayan_hp_resolve_whatsapp_url' ) ? kayan_hp_resolve_whatsapp_url( $post_id > 0 ? $post_id : null ) : '#';
		$address = function_exists( 'kayan_homepage_get_address' ) ? kayan_homepage_get_address() : (string) yc_get_option( 'company__adress' );
		$mail    = trim( (string) yc_get_option( 'company__mail' ) );
		$company = function_exists( 'kayan_homepage_get_company_name' ) ? kayan_homepage_get_company_name() : get_bloginfo( 'name' );
		$show_call = ! function_exists( 'kayan_ui_show_call_button' ) || kayan_ui_show_call_button();

		echo '<div class="cinfo-card"><div class="inner">';
		echo '<h3 style="color:#fff;margin-bottom:22px">' . esc_html( $company ) . '</h3>';
		if ( $show_call && trim( (string) $phone ) !== '' ) {
			echo '<div class="cinfo-item"><i class="fas fa-phone"></i><div><b>' . esc_html( function_exists( 'kayan_homepage_format_phone_display' ) ? kayan_homepage_format_phone_display( $phone ) : $phone ) . '</b><small>الهاتف</small></div></div>';
		}
		if ( $wa_url !== '#' && $wa_url !== '' ) {
			echo '<div class="cinfo-item"><i class="fab fa-whatsapp"></i><div><b><a href="' . esc_url( $wa_url ) . '" style="color:#fff" target="_blank" rel="noopener noreferrer">واتساب</a></b><small>تواصل مباشر</small></div></div>';
		}
		if ( $address !== '' ) {
			echo '<div class="cinfo-item"><i class="fas fa-location-dot"></i><div><b>' . esc_html( $address ) . '</b><small>العنوان</small></div></div>';
		}
		if ( $mail !== '' ) {
			echo '<div class="cinfo-item"><i class="fas fa-envelope"></i><div><b><a href="mailto:' . esc_attr( $mail ) . '" style="color:#fff">' . esc_html( $mail ) . '</a></b><small>البريد</small></div></div>';
		}
		echo '</div></div>';
	}
}
