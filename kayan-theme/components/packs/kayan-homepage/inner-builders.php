<?
/**
 * Inner page layout markup — uses kayan-inner.css + kayan-home design tokens.
 */

if ( ! function_exists( 'kayan_homepage_uses_inner_layout' ) ) {
	function kayan_homepage_uses_inner_layout() {
		return function_exists( 'kayan_homepage_inner_page_request' ) && kayan_homepage_inner_page_request();
	}
}

if ( ! function_exists( 'kayan_homepage_render_inner_hero' ) ) {
	/**
	 * @param array $args title, subtitle, image_url, image_alt, category_term, total_rate.
	 */
	function kayan_homepage_render_inner_hero( $args = array() ) {
		if ( function_exists( 'kayan_kit_render_phero' ) ) {
			kayan_kit_render_phero( $args );
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'title'          => '',
				'subtitle'       => '',
				'image_url'      => '',
				'image_alt'      => '',
				'category_term'  => null,
				'total_rate'     => 0,
			)
		);

		$title     = trim( (string) $args['title'] );
		$subtitle  = trim( (string) $args['subtitle'] );
		$image_url = trim( (string) $args['image_url'] );
		$image_alt = trim( (string) $args['image_alt'] );
		if ( $image_alt === '' ) {
			$image_alt = $title;
		}

		echo '<section class="phero compact kayan-inner-hero">';
		echo '<div class="wrap">';
		if ( $title !== '' ) {
			echo '<h1>' . esc_html( $title ) . '</h1>';
		}
		if ( $subtitle !== '' ) {
			echo '<p class="psub">' . esc_html( $subtitle ) . '</p>';
		}
		echo '</div></section>';
	}
}

if ( ! function_exists( 'kayan_homepage_render_inner_breadcrumb' ) ) {
	function kayan_homepage_render_inner_breadcrumb() {
		// Crumb is rendered inside .phero by kayan_kit_render_phero().
		// Keep this as a no-op so existing templates do not print a second trail.
	}
}

if ( ! function_exists( 'kayan_homepage_render_contact_box' ) ) {
	/**
	 * @param int|null   $post_id             Post/page ID for contact resolver.
	 * @param string|null $phonenumber_fallback Fallback when resolver is unavailable.
	 * @param string|null $whatsapp_fallback    Fallback when resolver is unavailable.
	 */
	function kayan_homepage_render_contact_box( $post_id = null, $phonenumber_fallback = null, $whatsapp_fallback = null ) {
		$post_id = (int) $post_id;
		$show_call = ! function_exists( 'kayan_ui_show_call_button' ) || kayan_ui_show_call_button();

		if ( function_exists( 'kayan_hp_resolve_phone' ) ) {
			$phone = kayan_hp_resolve_phone( $post_id > 0 ? $post_id : null );
		} else {
			$phone = trim( (string) $phonenumber_fallback );
		}

		if ( function_exists( 'kayan_hp_resolve_tel_url' ) ) {
			$tel_url = kayan_hp_resolve_tel_url( $post_id > 0 ? $post_id : null );
		} else {
			$digits  = preg_replace( '/\D+/', '', (string) $phone );
			$tel_url = $digits !== '' ? 'tel:+' . ltrim( $digits, '+' ) : '#';
		}

		if ( function_exists( 'kayan_hp_resolve_whatsapp_url' ) ) {
			$wa_url = kayan_hp_resolve_whatsapp_url( $post_id > 0 ? $post_id : null );
		} else {
			$wa_raw = trim( (string) $whatsapp_fallback );
			if ( function_exists( 'kayan_wa_build_url' ) && $wa_raw !== '' ) {
				$wa_url = kayan_wa_build_url( $wa_raw, null, $post_id > 0 ? get_the_title( $post_id ) : '' );
			} else {
				$digits = preg_replace( '/\D+/', '', $wa_raw );
				$wa_url = $digits !== '' ? 'https://wa.me/' . $digits : '#';
			}
		}

		if ( function_exists( 'kayan_kit_render_cta_widget' ) ) {
			kayan_kit_render_cta_widget( $post_id );
			return;
		}

		$btn_call = function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_call', 'اتصل' ) : 'اتصل';
		$btn_wa   = function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_whatsapp_full', 'واتساب' ) : 'واتساب';

		echo '<div class="side-w cta kayan-contact-box">';
		echo '<h4 class="kayan-contact-box__title">';
		echo esc_html( function_exists( 'kayan_homepage_ui_string' ) ? kayan_homepage_ui_string( 'btn_service', 'تواصل معنا' ) : 'تواصل معنا' );
		echo '</h4>';
		echo '<p class="kayan-contact-box__text">';
		echo esc_html( function_exists( 'kayan_homepage_get_company_name' ) ? kayan_homepage_get_company_name() : get_bloginfo( 'name' ) );
		echo '</p>';
		echo '<div class="kayan-contact-box__actions">';

		if ( $wa_url !== '#' ) {
			echo '<a href="' . esc_url( $wa_url ) . '" class="btn btn-wa" target="_blank" rel="noopener noreferrer">';
			echo '<i class="fab fa-whatsapp"></i> ' . esc_html( $btn_wa );
			echo '</a>';
		}

		if ( $show_call && $tel_url !== '#' && trim( (string) $phone ) !== '' ) {
			echo '<a href="' . esc_url( $tel_url ) . '" class="btn btn-call">';
			echo '<i class="fas fa-phone"></i> ' . esc_html( $btn_call );
			echo '</a>';
		}

		echo '</div>';
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_homepage_render_sidebar_related_services' ) ) {
	/**
	 * @param int   $post_id        Current post ID.
	 * @param array $category_ids   Category term IDs.
	 * @param int   $limit          Max related posts.
	 */
	function kayan_homepage_render_sidebar_related_services( $post_id, $category_ids = array(), $limit = 6 ) {
		$post_id      = (int) $post_id;
		$category_ids = array_filter( array_map( 'intval', (array) $category_ids ) );
		if ( empty( $category_ids ) ) {
			return;
		}

		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => max( 1, (int) $limit ),
				'post__not_in'   => array( $post_id ),
				'orderby'        => 'rand',
				'no_found_rows'  => true,
				'tax_query'      => array(
					array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => $category_ids,
					),
				),
			)
		);

		if ( empty( $posts ) ) {
			return;
		}

		echo '<div class="side-w kayan-inner-sidebar__related">';
		echo '<h4>خدمات ذات صلة</h4>';
		foreach ( $posts as $related_post ) {
			$url   = get_permalink( $related_post );
			$thumb = get_the_post_thumbnail_url( $related_post, 'thumbnail' );
			echo '<a class="rel" href="' . esc_url( $url ) . '">';
			echo '<span class="rth">';
			if ( $thumb ) {
				echo '<img src="' . esc_url( $thumb ) . '" alt="" width="60" height="60" loading="lazy" />';
			} else {
				echo '<i class="fas fa-screwdriver-wrench"></i>';
			}
			echo '</span><span><b>' . esc_html( get_the_title( $related_post ) ) . '</b></span></a>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_homepage_render_inner_header' ) ) {
	function kayan_homepage_render_inner_header( $args = array() ) {
		kayan_homepage_render_inner_hero( $args );
	}
}

if ( ! function_exists( 'kayan_homepage_render_inner_layout_open' ) ) {
	/**
	 * Opens kit article layout. Set has_sidebar false for full-width.
	 */
	function kayan_homepage_render_inner_layout_open( $has_sidebar = true ) {
		if ( function_exists( 'kayan_kit_open_article_layout' ) ) {
			kayan_kit_open_article_layout( $has_sidebar );
			return;
		}
		$layout_class = $has_sidebar ? 'article-layout' : 'article-layout kayan-inner-layout--no-sidebar';
		echo '<section class="sec kayan-inner-body"><div class="wrap">';
		echo '<div class="' . esc_attr( $layout_class ) . '">';
		echo '<div class="article-body prose">';
	}
}

if ( ! function_exists( 'kayan_homepage_render_inner_sidebar_open' ) ) {
	function kayan_homepage_render_inner_sidebar_open() {
		if ( function_exists( 'kayan_kit_open_sidebar' ) ) {
			kayan_kit_open_sidebar();
			return;
		}
		echo '</div><aside>';
	}
}

if ( ! function_exists( 'kayan_homepage_render_inner_layout_close' ) ) {
	function kayan_homepage_render_inner_layout_close( $has_sidebar = true ) {
		if ( function_exists( 'kayan_kit_close_article_layout' ) ) {
			kayan_kit_close_article_layout( $has_sidebar );
			return;
		}
		if ( $has_sidebar ) {
			echo '</aside></div>';
		} else {
			echo '</div>';
		}
		echo '</div></section>';
	}
}
