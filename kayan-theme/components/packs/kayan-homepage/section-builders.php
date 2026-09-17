<?
/**
 * بناء أقسام الصفحة الرئيسية من إعدادات القالب وبيانات الموقع.
 */

if ( ! function_exists( 'kayan_hp_section_disabled' ) ) {
	function kayan_hp_section_disabled( $section ) {
		return ! empty( yc_get_option( 'kayan_hp_' . $section . '_disable' ) );
	}
}

if ( ! function_exists( 'kayan_hp_apply_section_visibility' ) ) {
	function kayan_hp_apply_section_visibility( $html ) {
		return preg_replace_callback(
			'/<!--\s*kayan-section:([a-z0-9_-]+)\s*-->(.*?)<!--\s*\/kayan-section(?::\1)?\s*-->/s',
			function ( $matches ) {
				$section = $matches[1];
				if ( kayan_hp_section_disabled( $section ) ) {
					return '';
				}
				return $matches[2];
			},
			$html
		);
	}
}

if ( ! function_exists( 'kayan_hp_get_section_text' ) ) {
	function kayan_hp_get_section_text( $section, $field, $default_ar, $default_en = '' ) {
		$key_ar = 'kayan_hp_' . $section . '_' . $field;
		$key_en = $key_ar . '_en';
		if ( $section === 'areas' && $field === 'subtitle' ) {
			$key_ar = 'kayan_homepage_areas_intro';
			$key_en = 'kayan_homepage_areas_intro_en';
		}
		if ( $section === 'blog' && $field === 'subtitle' ) {
			$key_ar = 'kayan_hp_blog_subtitle';
			$key_en = 'kayan_hp_blog_subtitle_en';
		}
		if ( function_exists( 'kayan_homepage_pick_locale_text' ) ) {
			return kayan_homepage_pick_locale_text( $key_ar, $default_ar, $key_en, $default_en !== '' ? $default_en : $default_ar );
		}
		return kayan_homepage_get_option_text( $key_ar, $default_ar );
	}
}

if ( ! function_exists( 'kayan_homepage_get_logo_data' ) ) {
	function kayan_homepage_get_logo_data( $context = 'header' ) {
		if ( $context === 'footer' ) {
			$data = yc_get_option( 'footer__logo' );
			if ( is_array( $data ) && ! empty( $data['logo__mode'] ) ) {
				return $data;
			}
		}
		$data = yc_get_option( 'logo__data' );
		return is_array( $data ) ? $data : array();
	}
}

if ( ! function_exists( 'kayan_homepage_build_logo_html' ) ) {
	/**
	 * شعار من إعدادات الهيدر/الفوتر (logo__data / footer__logo).
	 */
	function kayan_homepage_build_logo_html( $context = 'header', $class = 'logo' ) {
		$logo     = kayan_homepage_get_logo_data( $context );
		$home_url = function_exists( 'kayan_i18n_home_url' ) ? kayan_i18n_home_url() : home_url( '/' );
		$alt      = kayan_homepage_get_company_name();

		if ( isset( $logo['logo__mode'] ) && $logo['logo__mode'] === 'Image' && isset( $logo['Image']['image_logo_id'] ) ) {
			$alt = ! empty( $logo['Image']['header__alt'] ) ? $logo['Image']['header__alt'] : $alt;
			$img = '';
			if ( function_exists( 'YC_get_attachment' ) ) {
				$img = (string) YC_get_attachment(
					array(
						'alt'  => $alt,
						'id'   => $logo['Image']['image_logo_id'],
						'size' => $context === 'footer' ? 'footer_sizelogo' : 'logo__size',
					)
				);
			}
			if ( $img !== '' ) {
				return '<a href="' . esc_url( $home_url ) . '" class="' . esc_attr( $class ) . '" title="' . esc_attr( $alt ) . '">' . $img . '</a>';
			}
		}

		if ( isset( $logo['logo__mode'] ) && $logo['logo__mode'] === 'Text' && ! empty( $logo['Text']['logo_Text'] ) ) {
			$text = $logo['Text']['logo_Text'];
			$alt  = ! empty( $logo['Text']['header__alt'] ) ? $logo['Text']['header__alt'] : $alt;
			if ( strpos( $text, '{%' ) !== false && strpos( $text, '%}' ) !== false ) {
				$color = ! empty( $logo['Text']['secondary_color'] ) ? $logo['Text']['secondary_color'] : 'var(--turq)';
				$text  = '<span class="first-logo-word">' . esc_html( preg_replace( '/\{%.*?\%\}/', '', $text ) ) . '</span>';
				preg_match( '/\{%(.+?)%\}/', $logo['Text']['logo_Text'], $m );
				$highlight = isset( $m[1] ) ? $m[1] : '';
				if ( $highlight !== '' ) {
					$text = preg_replace( '/\{%.*?\%\}/', '', $logo['Text']['logo_Text'] );
					$parts = preg_split( '/\{%|\%\}/', $logo['Text']['logo_Text'] );
					if ( count( $parts ) >= 3 ) {
						$text = esc_html( trim( $parts[0] ) ) . ' <b style="color:' . esc_attr( $color ) . '">' . esc_html( trim( $parts[1] ) ) . '</b>' . esc_html( trim( $parts[2] ) );
					}
				}
			} else {
				list( $first, $second ) = kayan_homepage_get_brand_parts();
				$text = esc_html( $first ) . ' <b>' . esc_html( $second ) . '</b>';
			}
			return '<a href="' . esc_url( $home_url ) . '" class="' . esc_attr( $class ) . '" title="' . esc_attr( $alt ) . '"><span class="mark"><i class="fas fa-shield-halved"></i></span>' . $text . '</a>';
		}

		list( $first, $second ) = kayan_homepage_get_brand_parts();
		return '<a href="' . esc_url( $home_url ) . '" class="' . esc_attr( $class ) . '"><span class="mark"><i class="fas fa-shield-halved"></i></span>' . esc_html( $first ) . ' <b>' . esc_html( $second ) . '</b></a>';
	}
}

if ( ! function_exists( 'kayan_homepage_build_nav_links_html' ) ) {
	function kayan_homepage_build_nav_links_html( $menu_location = 'main-menu', $css_class = 'menu' ) {
		$locations = get_nav_menu_locations();
		$html      = '';
		$items     = array();

		if ( isset( $locations[ $menu_location ] ) ) {
			$items = wp_get_nav_menu_items( $locations[ $menu_location ] );
		}

		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( (int) $item->menu_item_parent !== 0 ) {
					continue;
				}
				$html .= '<a href="' . esc_url( kayan_homepage_resolve_url_token( $item->url ) ) . '">' . esc_html( $item->title ) . '</a>';
			}
			return $html;
		}

		$home = function_exists( 'kayan_i18n_home_url' ) ? kayan_i18n_home_url() : home_url( '/' );
		$fallback = array(
			array( trailingslashit( $home ) . '#services', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_services' ) : 'الخدمات' ),
			array( trailingslashit( $home ) . '#areas', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_cities' ) : 'المدن' ),
			array( trailingslashit( $home ) . '#projects', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_projects' ) : 'المشاريع' ),
			array( trailingslashit( $home ) . '#blog', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_blog' ) : 'المدونة' ),
			array( trailingslashit( $home ) . '#why', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_about' ) : 'من نحن' ),
		);
		foreach ( $fallback as $link ) {
			$html .= '<a href="' . esc_attr( $link[0] ) . '">' . esc_html( $link[1] ) . '</a>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_mobile_nav_html' ) ) {
	function kayan_homepage_build_mobile_nav_html() {
		$locations = get_nav_menu_locations();
		$html      = '';
		$items     = array();

		if ( isset( $locations['main-menu'] ) ) {
			$items = wp_get_nav_menu_items( $locations['main-menu'] );
		}

		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( (int) $item->menu_item_parent !== 0 ) {
					continue;
				}
				$html .= '<a href="' . esc_url( kayan_homepage_resolve_url_token( $item->url ) ) . '" onclick="toggleMob(false)">' . esc_html( $item->title ) . '</a>';
			}
		} else {
			$home = function_exists( 'kayan_i18n_home_url' ) ? kayan_i18n_home_url() : home_url( '/' );
			$fallback = array(
				array( trailingslashit( $home ) . '#services', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_services' ) : 'الخدمات' ),
				array( trailingslashit( $home ) . '#areas', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_cities' ) : 'المدن' ),
				array( trailingslashit( $home ) . '#projects', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_projects' ) : 'المشاريع' ),
				array( trailingslashit( $home ) . '#blog', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_blog' ) : 'المدونة' ),
				array( trailingslashit( $home ) . '#why', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_about' ) : 'من نحن' ),
				array( trailingslashit( $home ) . '#faq', function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_faq' ) : 'الأسئلة الشائعة' ),
			);
			foreach ( $fallback as $link ) {
				$html .= '<a href="' . esc_attr( $link[0] ) . '" onclick="toggleMob(false)">' . esc_html( $link[1] ) . '</a>';
			}
		}

		$html .= '<a href="' . esc_url( kayan_hp_resolve_whatsapp_url() ) . '" class="btn btn-wa" onclick="toggleMob(false)"><i class="fab fa-whatsapp"></i> ' . esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_whatsapp_full' ) : 'واتساب' ) . '</a>';
		if ( kayan_hp_resolve_phone() !== '' && ( ! function_exists( 'kayan_ui_show_call_button' ) || kayan_ui_show_call_button() ) ) {
			$html .= '<a href="' . esc_url( kayan_hp_resolve_tel_url() ) . '" class="btn btn-call" onclick="toggleMob(false)"><i class="fas fa-phone"></i> ' . esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_call' ) : 'اتصل' ) . '</a>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_footer_social_html' ) ) {
	function kayan_homepage_build_footer_social_html() {
		$hide = yc_get_option( 'social_footer' );
		if ( ! empty( $hide ) ) {
			return kayan_homepage_build_social_links_html();
		}

		$list = yc_get_option( 'social_footer_list' );
		$list = is_array( $list ) ? $list : array();
		$icons = array(
			'facebook'  => 'fab fa-facebook-f',
			'twitter'   => 'fab fa-x-twitter',
			'telegram'  => 'fab fa-telegram',
			'youtube'   => 'fab fa-youtube',
			'linkedin'  => 'fab fa-linkedin-in',
			'instagram' => 'fab fa-instagram',
		);
		$html = '';
		foreach ( $list as $key ) {
			$url = trim( (string) yc_get_option( $key ) );
			if ( $url === '' || ! isset( $icons[ $key ] ) ) {
				continue;
			}
			$html .= '<a href="' . esc_url( $url ) . '" rel="noopener noreferrer" target="_blank" aria-label="' . esc_attr( $key ) . '"><i class="' . esc_attr( $icons[ $key ] ) . '"></i></a>';
		}
		if ( $html === '' ) {
			return kayan_homepage_build_social_links_html();
		}
		$wa = kayan_hp_resolve_whatsapp_url();
		if ( $wa !== '#' ) {
			$html .= '<a href="' . esc_url( $wa ) . '" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_menu_column_html' ) ) {
	function kayan_homepage_build_menu_column_html( $menu_id, $title, $fallback_links = array() ) {
		$html = '<div class="fcol"><h4>' . esc_html( $title ) . '</h4><ul>';
		$items = array();
		if ( ! empty( $menu_id ) ) {
			$items = wp_get_nav_menu_items( $menu_id );
		}
		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				if ( (int) $item->menu_item_parent !== 0 ) {
					continue;
				}
				$html .= '<li><a href="' . esc_url( kayan_homepage_resolve_url_token( $item->url ) ) . '"><i class="fas fa-chevron-left"></i> ' . esc_html( $item->title ) . '</a></li>';
			}
		} elseif ( ! empty( $fallback_links ) ) {
			foreach ( $fallback_links as $link ) {
				$html .= '<li><a href="' . esc_url( kayan_homepage_resolve_url_token( $link['url'] ) ) . '"><i class="fas fa-chevron-left"></i> ' . esc_html( $link['label'] ) . '</a></li>';
			}
		}
		$html .= '</ul></div>';
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_map_html' ) ) {
	function kayan_homepage_build_map_html() {
		if ( ! empty( yc_get_option( 'hide_footer__map' ) ) ) {
			return '';
		}
		$code  = trim( (string) yc_get_option( 'company__map_code' ) );
		$title = trim( (string) yc_get_option( 'company__map_title' ) );
		if ( $code === '' ) {
			return '';
		}
		if ( $title !== '' ) {
			$code = str_replace( 'src=', 'title="' . esc_attr( $title ) . '" loading="lazy" src=', $code );
		} else {
			$code = str_replace( 'src=', 'loading="lazy" src=', $code );
		}
		$allowed = array(
			'iframe' => array(
				'src'             => true,
				'width'           => true,
				'height'          => true,
				'style'           => true,
				'frameborder'     => true,
				'allowfullscreen' => true,
				'loading'         => true,
				'title'           => true,
				'referrerpolicy'  => true,
			),
		);
		return '<div class="fmap rv"><div class="fmap-inner">' . wp_kses( $code, $allowed ) . '</div></div>';
	}
}

if ( ! function_exists( 'kayan_homepage_get_copyright_text' ) ) {
	function kayan_homepage_get_copyright_text() {
		$text = trim( (string) yc_get_option( 'Copyrights' ) );
		if ( $text === '' ) {
			$text = trim( (string) yc_get_option( 'kayan_hp_footer_copyright' ) );
		}
		if ( $text === '' ) {
			$text = trim( (string) yc_get_option( 'kayan_homepage_copyright' ) );
		}
		if ( $text === '' ) {
			return kayan_homepage_expand_inline_tokens(
				kayan_hp_get_section_text(
					'footer',
					'copyright',
					'© {{year}} {{company_name}} للخدمات المنزلية. جميع الحقوق محفوظة.',
					'© {{year}} {{company_name}} Home Services. All rights reserved.'
				)
			);
		}
		if ( strpos( $text, '{%YEAR%}' ) !== false ) {
			$text = str_replace( '{%YEAR%}', gmdate( 'Y' ), $text );
		}
		return $text;
	}
}

if ( ! function_exists( 'kayan_homepage_build_footer_html' ) ) {
	function kayan_homepage_build_footer_html() {
		$tagline = kayan_homepage_expand_inline_tokens(
			kayan_hp_get_section_text(
				'footer',
				'tagline',
				trim( (string) yc_get_option( 'footer__content' ) ) !== '' ? (string) yc_get_option( 'footer__content' ) : '{{company_name}} — منصة الخدمات المنزلية في {{country_name}}.',
				''
			)
		);
		if ( trim( (string) yc_get_option( 'footer__content' ) ) !== '' && empty( yc_get_option( 'kayan_hp_footer_tagline' ) ) && empty( yc_get_option( 'kayan_hp_footer_tagline_en' ) ) ) {
			$tagline = wp_kses_post( yc_get_option( 'footer__content' ) );
		}

		$address_url = trim( (string) yc_get_option( 'footer__company__adress_url' ) );
		if ( $address_url === '' ) {
			$address_url = '#';
		}

		$logo_inner = kayan_homepage_build_logo_html( 'footer', 'flogo' );
		if ( strpos( $logo_inner, 'class="flogo"' ) === false && strpos( $logo_inner, "class='flogo'" ) === false ) {
			$logo_inner = str_replace( 'class="logo"', 'class="flogo"', $logo_inner );
		}

		$menu1_title = kayan_hp_get_section_text( 'footer', 'menu1_title', (string) yc_get_option( 'footer__title_first_menu' ), 'Services' );
		if ( trim( $menu1_title ) === '' ) {
			$menu1_title = function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_services' ) : 'الخدمات';
		}
		$menu2_title = kayan_hp_get_section_text( 'footer', 'menu2_title', (string) yc_get_option( 'footer__title_second_menu' ), 'Quick links' );
		if ( trim( $menu2_title ) === '' ) {
			$menu2_title = function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_cities' ) : 'المدن';
		}

		$col1 = '';
		if ( empty( yc_get_option( 'hide_footer__first_menu' ) ) ) {
			$col1 = kayan_homepage_build_menu_column_html(
				yc_get_option( 'footer__first_menu' ),
				$menu1_title,
				kayan_homepage_get_services_footer_links()
			);
		}
		$col2 = '';
		if ( empty( yc_get_option( 'hide_footer__second_menu' ) ) ) {
			$col2 = kayan_homepage_build_menu_column_html(
				yc_get_option( 'footer__second_menu' ),
				$menu2_title,
				kayan_homepage_get_cities_footer_links()
			);
		}

		$quick_title = kayan_hp_get_section_text( 'footer', 'menu3_title', 'روابط سريعة', 'Quick links' );
		$home        = function_exists( 'kayan_i18n_home_url' ) ? kayan_i18n_home_url() : home_url( '/' );
		$col3        = kayan_homepage_build_menu_column_html(
			0,
			$quick_title,
			array(
				array( 'url' => $home, 'label' => function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_services', 'الرئيسية' ) : 'الرئيسية' ),
				array( 'url' => $home . '#why', 'label' => function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_about' ) : 'من نحن' ),
				array( 'url' => get_permalink( get_option( 'page_for_posts' ) ) ?: $home . '#blog', 'label' => function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_blog' ) : 'المدونة' ),
				array( 'url' => $home . '#contact', 'label' => 'اتصل بنا' ),
				array( 'url' => $home . '#faq', 'label' => function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'nav_faq' ) : 'الأسئلة الشائعة' ),
			)
		);

		$map = kayan_homepage_build_map_html();

		ob_start();
		?>
<footer>
  <div class="wrap">
    <div class="fgrid<?php echo $map !== '' ? ' fgrid-has-map' : ''; ?>">
      <div class="fcol fcol-brand">
        <?php echo $logo_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <p><?php echo wp_kses_post( $tagline ); ?></p>
        <div class="fcontact">
          <?php if ( kayan_hp_resolve_phone() !== '' && ( ! function_exists( 'kayan_ui_show_call_button' ) || kayan_ui_show_call_button() ) ) : ?>
          <a href="<?php echo esc_url( kayan_hp_resolve_tel_url() ); ?>"><i class="fas fa-phone"></i> <?php echo esc_html( kayan_homepage_format_phone_display( kayan_hp_resolve_phone() ) ); ?></a>
          <?php endif; ?>
          <a href="<?php echo esc_url( kayan_hp_resolve_whatsapp_url() ); ?>"><i class="fab fa-whatsapp"></i> <?php echo esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_whatsapp_full' ) : 'واتساب' ); ?></a>
          <a href="<?php echo esc_url( $address_url ); ?>"><i class="fas fa-location-dot"></i> <?php echo esc_html( kayan_homepage_get_address() ); ?></a>
          <?php
			$mail = trim( (string) yc_get_option( 'company__mail' ) );
			if ( $mail !== '' ) :
				$mail_url = trim( (string) yc_get_option( 'footer__company__mail_url' ) );
				?>
          <a href="<?php echo esc_url( $mail_url !== '' ? $mail_url : 'mailto:' . $mail ); ?>"><i class="fas fa-envelope"></i> <?php echo esc_html( $mail ); ?></a>
          <?php endif; ?>
        </div>
        <div class="fsocial"><?php echo kayan_homepage_build_footer_social_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
      </div>
      <?php echo $col1; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      <?php echo $col2; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      <?php echo $col3; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      <?php if ( $map !== '' ) : ?>
      <div class="fcol fcol-map"><?php echo $map; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
      <?php endif; ?>
    </div>
    <div class="fbottom"><?php echo esc_html( kayan_homepage_get_copyright_text() ); ?></div>
  </div>
</footer>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'kayan_homepage_get_services_footer_links' ) ) {
	function kayan_homepage_get_services_footer_links() {
		$links = array();
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		foreach ( $posts as $post ) {
			$url = kayan_homepage_get_post_url( $post );
			$links[] = array( 'url' => $url, 'label' => get_the_title( $post ) );
		}
		return $links;
	}
}

if ( ! function_exists( 'kayan_homepage_get_cities_footer_links' ) ) {
	function kayan_homepage_get_cities_footer_links() {
		$links = array();
		$terms = get_terms(
			array(
				'taxonomy'   => 'city',
				'hide_empty' => false,
				'number'     => 8,
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return $links;
		}
		foreach ( $terms as $term ) {
			$url = get_term_link( $term );
			if ( is_wp_error( $url ) ) {
				continue;
			}
			$links[] = array( 'url' => $url, 'label' => $term->name );
		}
		return $links;
	}
}

if ( ! function_exists( 'kayan_homepage_build_services_grid_html' ) ) {
	function kayan_homepage_build_services_grid_html() {
		$custom = yc_get_option( 'kayan_hp_services_cards' );
		if ( is_array( $custom ) && ! empty( $custom ) ) {
			$html = '';
			foreach ( $custom as $card ) {
				if ( ! is_array( $card ) ) {
					continue;
				}
				$title = isset( $card['title'] ) ? trim( (string) $card['title'] ) : '';
				if ( $title === '' ) {
					continue;
				}
				$icon  = ! empty( $card['icon'] ) ? $card['icon'] : 'fas fa-wrench';
				$desc  = isset( $card['desc'] ) ? $card['desc'] : '';
				$url   = kayan_homepage_resolve_url_token( ! empty( $card['url'] ) ? $card['url'] : '#contact' );
				$bullets = isset( $card['bullets'] ) ? preg_split( '/\r\n|\r|\n/', (string) $card['bullets'] ) : array();
				$html .= '<div class="svc rv"><div class="svc-ic"><i class="' . esc_attr( $icon ) . '"></i></div>';
				$html .= '<h3>' . esc_html( $title ) . '</h3><p class="desc">' . esc_html( $desc ) . '</p><ul>';
				foreach ( $bullets as $line ) {
					$line = trim( $line );
					if ( $line === '' ) {
						continue;
					}
					$html .= '<li><i class="fas fa-check"></i> ' . esc_html( $line ) . '</li>';
				}
				$html .= '</ul><a href="' . esc_url( $url ) . '" class="svc-cta">' . esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_service' ) : 'طلب الخدمة' ) . ' <i class="fas fa-arrow-left"></i></a></div>';
			}
			if ( $html !== '' ) {
				return $html;
			}
		}

		$limit = (int) kayan_homepage_get_option_text( 'kayan_hp_services_count', '6' );
		if ( $limit < 1 ) {
			$limit = 6;
		}
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		if ( empty( $posts ) ) {
			return '';
		}

		$icons = array( 'fas fa-droplet', 'fas fa-layer-group', 'fas fa-snowflake', 'fas fa-spray-can-sparkles', 'fas fa-wrench', 'fas fa-bug-slash' );
		$html  = '';
		$i     = 0;
		foreach ( $posts as $post ) {
			$icon = $icons[ $i % count( $icons ) ];
			$url  = kayan_homepage_get_post_url( $post );
			$html .= '<div class="svc rv"><div class="svc-ic"><i class="' . esc_attr( $icon ) . '"></i></div>';
			$html .= '<h3>' . esc_html( get_the_title( $post ) ) . '</h3>';
			$html .= '<p class="desc">' . esc_html( wp_trim_words( get_the_excerpt( $post ), 14, '…' ) ) . '</p>';
			$html .= '<a href="' . esc_url( $url ) . '" class="svc-cta">' . esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_service' ) : 'طلب الخدمة' ) . ' <i class="fas fa-arrow-left"></i></a></div>';
			$i++;
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_cities_grid_html' ) ) {
	function kayan_homepage_build_cities_grid_html() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'city',
				'hide_empty' => false,
				'number'     => 12,
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}
		$html = '';
		foreach ( $terms as $term ) {
			$url = get_term_link( $term );
			if ( is_wp_error( $url ) ) {
				continue;
			}
			$count = (int) $term->count;
			$html .= '<a class="city rv" href="' . esc_url( $url ) . '"><i class="fas fa-location-dot"></i><span>' . esc_html( $term->name ) . '</span>';
			if ( $count > 0 ) {
				$html .= '<small>' . (int) $count . ' خدمة</small>';
			}
			$html .= '</a>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_area_cards_html' ) ) {
	function kayan_homepage_build_area_cards_html() {
		$terms = get_terms(
			array(
				'taxonomy'   => 'city',
				'hide_empty' => false,
				'number'     => 8,
			)
		);
		$html = '';
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$count = max( 1, (int) $term->count );
				$url   = get_term_link( $term );
				if ( is_wp_error( $url ) ) {
					$url = '#areas';
				}
				$html .= '<a class="acard rv" href="' . esc_url( $url ) . '"><div class="ah"><i class="fas fa-city"></i><div><b>' . esc_html( $term->name ) . '</b><small>' . (int) $count . ' خدمة متوفرة</small></div></div></a>';
			}
		}
		$cta = kayan_hp_get_section_text( 'areas', 'cta_text', 'لم تجد مدينتك؟', 'City not listed?' );
		$html .= '<a class="acard rv" style="display:grid;place-items:center;text-align:center;background:var(--grad);color:#fff;border:none" href="#contact"><div><i class="fas fa-headset" style="font-size:26px;margin-bottom:8px"></i><b style="color:#fff">' . esc_html( $cta ) . '</b><small style="color:rgba(255,255,255,.8)">' . esc_html( kayan_hp_get_section_text( 'areas', 'cta_sub', 'تواصل معنا الآن', 'Contact us now' ) ) . '</small></div></a>';
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_section_head_html' ) ) {
	function kayan_homepage_build_section_head_html( $section, $default_tag, $default_title, $default_sub, $title_en = '', $sub_en = '' ) {
		$tag        = esc_html( kayan_hp_get_section_text( $section, 'tag', $default_tag, $default_tag ) );
		$title      = kayan_hp_get_section_text( $section, 'title', $default_title, $title_en !== '' ? $title_en : $default_title );
		$sub        = esc_html( kayan_hp_get_section_text( $section, 'subtitle', $default_sub, $sub_en !== '' ? $sub_en : $default_sub ) );
		$title_html = wp_kses_post( kayan_homepage_expand_inline_tokens( $title ) );
		return '<div class="shead rv"><span class="tag">' . $tag . '</span><h2>' . $title_html . '</h2><p>' . $sub . '</p></div>';
	}
}

if ( ! function_exists( 'kayan_homepage_build_projects_grid_html' ) ) {
	function kayan_homepage_build_projects_grid_html() {
		$limit = (int) kayan_homepage_get_option_text( 'kayan_hp_projects_count', '6' );
		if ( $limit < 1 ) {
			$limit = 6;
		}
		if ( $limit > 12 ) {
			$limit = 12;
		}

		$posts = get_posts(
			array(
				'post_type'      => 'works',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);

		if ( empty( $posts ) ) {
			return '';
		}

		$gradients = array(
			'linear-gradient(135deg,var(--turq),var(--blue))',
			'linear-gradient(135deg,var(--blue),var(--navy2))',
			'linear-gradient(135deg,var(--aqua),var(--turq))',
			'linear-gradient(135deg,var(--success),var(--turq))',
			'linear-gradient(135deg,var(--gold),#ffce6b)',
			'linear-gradient(135deg,var(--navy2),var(--blue))',
		);
		$icons = array( 'fas fa-layer-group', 'fas fa-droplet', 'fas fa-snowflake', 'fas fa-spray-can-sparkles', 'fas fa-water', 'fas fa-bug-slash' );
		$html  = '';
		$i     = 0;

		foreach ( $posts as $post ) {
			$thumb    = get_the_post_thumbnail_url( $post, 'medium_large' );
			$gradient = $gradients[ $i % count( $gradients ) ];
			$icon     = $icons[ $i % count( $icons ) ];
			$style    = $thumb ? 'background-image:url(' . esc_url( $thumb ) . ');background-size:cover;background-position:center' : 'background:' . $gradient;
			$url      = kayan_homepage_get_post_url( $post );
			$cats     = get_the_terms( $post->ID, 'category' );
			$cat_name = ( ! is_wp_error( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : 'مشروع';
			$city     = '';
			$city_terms = get_the_terms( $post->ID, 'city' );
			if ( ! is_wp_error( $city_terms ) && ! empty( $city_terms ) ) {
				$city = $city_terms[0]->name;
			}

			$html .= '<a class="proj rv" href="' . esc_url( $url ) . '" data-cat="all">';
			$html .= '<div class="proj-img" style="' . esc_attr( $style ) . '">';
			if ( ! $thumb ) {
				$html .= '<i class="' . esc_attr( $icon ) . '"></i>';
			}
			$html .= '<span class="cat">' . esc_html( $cat_name ) . '</span></div>';
			$html .= '<div class="proj-body"><h3>' . esc_html( get_the_title( $post ) ) . '</h3>';
			$html .= '<div class="proj-meta">';
			if ( $city !== '' ) {
				$html .= '<span><i class="fas fa-location-dot"></i> ' . esc_html( $city ) . '</span>';
			}
			$html .= '<span><i class="fas fa-clock"></i> ' . esc_html( get_the_date( 'j M Y', $post ) ) . '</span>';
			$html .= '</div>';
			$excerpt = wp_trim_words( get_the_excerpt( $post ), 12, '…' );
			if ( $excerpt !== '' ) {
				$html .= '<div class="proj-res"><i class="fas fa-circle-check"></i> ' . esc_html( $excerpt ) . '</div>';
			}
			$html .= '</div></a>';
			$i++;
		}

		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_floating_buttons_html' ) ) {
	/**
	 * الأزرار العائمة الصغيرة (نفس markup القالب القديم .btn-fixed-bh).
	 */
	function kayan_homepage_build_floating_buttons_html() {
		$phonenumber        = kayan_hp_resolve_phone();
		$whatsapp_number    = kayan_hp_resolve_whatsapp();
		$show_floating_call = function_exists( 'kayan_ui_show_floating_call_button' )
			? kayan_ui_show_floating_call_button( 0, false )
			: empty( yc_get_option( 'hide__floating__call' ) );
		$show_floating_whatsapp = function_exists( 'kayan_ui_show_whatsapp_button' )
			? kayan_ui_show_whatsapp_button( 0, false )
			: ( $whatsapp_number !== '' );
		if ( function_exists( 'kayan_homepage_v3_active_request' ) && kayan_homepage_v3_active_request() ) {
			$show_floating_whatsapp = false;
		}

		$html = '<div class="btn-fixed-bh">';

		if ( $show_floating_call && $phonenumber !== '' ) {
			$html .= '<div class="--yourcolor--button--phones --YourColor--phone-button">';
			$html .= '<a href="' . esc_url( kayan_hp_resolve_tel_url() ) . '" aria-label="اتصل بنا" data-call="Phone" data-tooltip="اتصل بنا" data-position="top">';
			$html .= '<div class="footer-header"><i class="fa-solid fa-phone"></i></div>';
			$html .= '</a></div>';
		}

		if ( $show_floating_whatsapp && $whatsapp_number !== '' ) {
			$wa_url = kayan_hp_resolve_whatsapp_url();
			$html  .= '<div class="--yourcolor--button--phones --YourColor--whatsapp-button">';
			$html  .= '<a href="' . esc_url( $wa_url ) . '" target="_blank" rel="noopener noreferrer" aria-label="الواتساب" data-call="whatsapp" data-tooltip="تواصل عبر واتساب" data-position="top">';
			$html  .= '<div class="footer-header"><i class="fa-brands fa-whatsapp"></i></div>';
			$html  .= '</a></div>';
		}

		$html .= '</div>';

		return $html;
	}
}
