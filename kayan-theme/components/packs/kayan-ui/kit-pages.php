<?php
/**
 * تصميم الكِت (19 نوع صفحة) فوق محتوى ووردبريس الموجود.
 * لا يزرع مدناً أو خدمات — يقرأ الجداول/التصنيفات/المقالات الظاهرة في الموقع.
 */

if ( ! function_exists( 'kayan_kit_plain' ) ) {
	function kayan_kit_plain( $html, $len = 0 ) {
		$text = wp_strip_all_tags( (string) $html );
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
		if ( function_exists( 'kayan_i18n_translate_text' ) ) {
			$text = kayan_i18n_translate_text( $text );
		}
		if ( $len > 0 && mb_strlen( $text, 'UTF-8' ) > $len ) {
			$text = mb_substr( $text, 0, $len, 'UTF-8' ) . '…';
		}
		return $text;
	}
}

if ( ! function_exists( 'kayan_kit_phone' ) ) {
	function kayan_kit_phone() {
		return trim( (string) get_option( 'phonenumber' ) );
	}
}

if ( ! function_exists( 'kayan_kit_whatsapp' ) ) {
	function kayan_kit_whatsapp() {
		return trim( (string) get_option( 'whatsapp_number' ) );
	}
}

if ( ! function_exists( 'kayan_kit_lang_urls' ) ) {
	function kayan_kit_lang_urls() {
		$home = home_url( '/' );
		$out  = array(
			'ar'      => $home,
			'en'      => trailingslashit( $home ) . 'en/',
			'current' => 'ar',
		);

		if ( function_exists( 'pll_the_languages' ) ) {
			$langs = pll_the_languages(
				array(
					'raw'           => 1,
					'hide_if_empty' => 0,
					'echo'          => 0,
				)
			);
			if ( is_array( $langs ) && ! empty( $langs ) ) {
				foreach ( $langs as $code => $row ) {
					if ( empty( $row['url'] ) ) {
						continue;
					}
					$out[ $code ] = $row['url'];
					if ( ! empty( $row['current_lang'] ) ) {
						$out['current'] = $code;
					}
				}
				return $out;
			}
		}

		$path  = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH );
		$path  = is_string( $path ) ? $path : '/';
		$is_en = function_exists( 'kayan_i18n_is_english' )
			? kayan_i18n_is_english()
			: ( '/en' === $path || 0 === strpos( $path, '/en/' ) );
		$out['current'] = $is_en ? 'en' : 'ar';
		if ( $is_en ) {
			$rest        = preg_replace( '#^/en(/|$)#', '/', $path );
			$out['en']   = home_url( $path );
			$out['ar']   = home_url( $rest ? $rest : '/' );
		} else {
			$out['ar'] = home_url( $path );
			$out['en'] = home_url( '/en' . ( '/' === $path ? '/' : rtrim( $path, '/' ) . '/' ) );
		}
		return $out;
	}
}

if ( ! function_exists( 'kayan_kit_render_header_lang_switcher' ) ) {
	function kayan_kit_render_header_lang_switcher() {
		$urls = kayan_kit_lang_urls();
		$cur  = isset( $urls['current'] ) ? $urls['current'] : 'ar';
		$en   = ( 'en' === $cur );
		$ar_u = isset( $urls['ar'] ) ? $urls['ar'] : home_url( '/' );
		$en_u = isset( $urls['en'] ) ? $urls['en'] : home_url( '/en/' );

		echo '<div class="rukn-lc kayan-header-lang" dir="' . ( $en ? 'ltr' : 'rtl' ) . '">';
		echo '<button type="button" class="rukn-lc-btn icon-btn lang-btn" aria-haspopup="true" aria-expanded="false" aria-label="' . esc_attr( function_exists( 'kayan_ui' ) ? kayan_ui( 'تبديل اللغة', 'Switch language' ) : __( 'تبديل اللغة', 'yourcolor' ) ) . '">';
		if ( $en ) {
			echo '<span class="rukn-lc-langico en">EN</span>';
		} else {
			echo '<span class="rukn-lc-langico ar">ع</span>';
		}
		echo '</button>';
		echo '<div class="rukn-lc-menu" role="menu">';
		echo '<div class="rukn-lc-h"><i class="fas fa-language"></i><span>' . esc_html( function_exists( 'kayan_ui' ) ? kayan_ui( 'اللغة', 'Language' ) : __( 'اللغة', 'yourcolor' ) ) . '</span></div>';
		echo '<a class="rukn-lc-item' . ( $en ? '' : ' is-on' ) . '" href="' . esc_url( $ar_u ) . '" role="menuitem" data-rukn-lang="ar">';
		echo '<span class="rukn-lc-langico ar">ع</span><span class="rukn-lc-name">' . esc_html( function_exists( 'kayan_ui' ) ? kayan_ui( 'العربية', 'Arabic' ) : __( 'العربية', 'yourcolor' ) ) . '</span><i class="fas fa-check rukn-lc-check"></i></a>';
		echo '<a class="rukn-lc-item' . ( $en ? ' is-on' : '' ) . '" href="' . esc_url( $en_u ) . '" role="menuitem" data-rukn-lang="en">';
		echo '<span class="rukn-lc-langico en">EN</span><span class="rukn-lc-name">English</span><i class="fas fa-check rukn-lc-check"></i></a>';
		echo '</div></div>';

		static $js = false;
		if ( $js ) {
			return;
		}
		$js = true;
		echo '<script>(function(){';
		echo 'function closeAll(){document.querySelectorAll(".kayan-header-lang").forEach(function(n){n.classList.remove("open");var b=n.querySelector(".rukn-lc-btn");if(b)b.setAttribute("aria-expanded","false");});}';
		echo 'document.addEventListener("click",function(e){';
		echo 'var btn=e.target.closest&&e.target.closest(".kayan-header-lang .rukn-lc-btn");';
		echo 'if(btn){e.preventDefault();e.stopPropagation();var root=btn.closest(".kayan-header-lang");var open=root.classList.contains("open");closeAll();if(!open){root.classList.add("open");btn.setAttribute("aria-expanded","true");}return;}';
		echo 'if(!e.target.closest||!e.target.closest(".kayan-header-lang"))closeAll();';
		echo '},true);';
		echo '})();</script>';
	}
}
add_action( 'rukn_v3_lang_switcher', 'kayan_kit_render_header_lang_switcher' );

if ( ! function_exists( 'kayan_kit_terms' ) ) {
	function kayan_kit_terms( $taxonomies, $args = array() ) {
		$taxonomies = (array) $taxonomies;
		$args       = is_array( $args ) ? $args : array();
		foreach ( $taxonomies as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = get_terms( array_merge(
				array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
					'number'     => 40,
				),
				$args
			) );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				if ( function_exists( 'kayan_i18n_translate_text' ) && kayan_i18n_is_english() ) {
					foreach ( $terms as $term ) {
						if ( isset( $term->name ) ) {
							$term->name = kayan_i18n_translate_text( $term->name );
						}
						if ( isset( $term->description ) ) {
							$term->description = kayan_i18n_translate_text( $term->description );
						}
					}
				}
				return $terms;
			}
		}
		return array();
	}
}

if ( ! function_exists( 'kayan_kit_posts' ) ) {
	function kayan_kit_posts( $post_types, $args = array() ) {
		$post_types = (array) $post_types;
		$args       = is_array( $args ) ? $args : array();
		foreach ( $post_types as $pt ) {
			if ( 'post' !== $pt && 'page' !== $pt && ! post_type_exists( $pt ) ) {
				continue;
			}
			$posts = get_posts( array_merge(
				array(
					'post_type'      => $pt,
					'posts_per_page' => 24,
					'post_status'    => 'publish',
				),
				$args
			) );
			if ( ! empty( $posts ) ) {
				if ( function_exists( 'kayan_i18n_translate_text' ) && function_exists( 'kayan_i18n_is_english' ) && kayan_i18n_is_english() ) {
					foreach ( $posts as $post ) {
						if ( isset( $post->post_title ) ) {
							$post->post_title = kayan_i18n_translate_text( $post->post_title );
						}
						if ( isset( $post->post_excerpt ) ) {
							$post->post_excerpt = kayan_i18n_translate_text( $post->post_excerpt );
						}
					}
				}
				return $posts;
			}
		}
		return array();
	}
}

if ( ! function_exists( 'kayan_kit_crumbs' ) ) {
	function kayan_kit_crumbs( $current = '' ) {
		echo '<div class="crumb">';
		echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
		echo ' <i class="fas fa-chevron-left"></i> ';
		if ( $current !== '' ) {
			echo '<span>' . esc_html( $current ) . '</span>';
		} else {
			echo '<span>' . esc_html( wp_get_document_title() ) . '</span>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_kit_hero' ) ) {
	function kayan_kit_hero( $title, $subtitle = '', $current_crumb = '', $extra = array() ) {
		$extra = is_array( $extra ) ? $extra : array();
		echo '<section class="phero compact">';
		echo '<div class="wrap">';
		kayan_kit_crumbs( $current_crumb !== '' ? $current_crumb : wp_strip_all_tags( (string) $title ) );
		echo '<h1>' . wp_kses_post( $title ) . '</h1>';
		$limit    = ! empty( $extra['full_lead'] ) ? 0 : 280;
		$subtitle = kayan_kit_plain( $subtitle, $limit );
		if ( $subtitle !== '' ) {
			echo '<p class="psub">' . esc_html( $subtitle ) . '</p>';
		}
		if ( ! empty( $extra['ctas'] ) ) {
			kayan_kit_cta_buttons();
		}
		if ( ! empty( $extra['chips'] ) && is_array( $extra['chips'] ) ) {
			echo '<div class="hero-proof">';
			foreach ( $extra['chips'] as $chip ) {
				if ( $chip === '' ) {
					continue;
				}
				echo '<span class="chip">' . wp_kses_post( $chip ) . '</span>';
			}
			echo '</div>';
		}
		if ( ! empty( $extra['meta'] ) && is_array( $extra['meta'] ) ) {
			echo '<div class="phero-meta">';
			foreach ( $extra['meta'] as $row ) {
				if ( empty( $row['b'] ) ) {
					continue;
				}
				echo '<div><b>' . esc_html( $row['b'] ) . '</b><small>' . esc_html( isset( $row['s'] ) ? $row['s'] : '' ) . '</small></div>';
			}
			echo '</div>';
		}
		echo '</div>';
		echo '</section>';
	}
}

if ( ! function_exists( 'kayan_kit_city_taxonomy' ) ) {
	function kayan_kit_city_taxonomy() {
		if ( taxonomy_exists( 'city' ) ) {
			return 'city';
		}
		if ( taxonomy_exists( 'cities' ) ) {
			return 'cities';
		}
		return 'city';
	}
}

if ( ! function_exists( 'kayan_kit_cat_taxonomy' ) ) {
	function kayan_kit_cat_taxonomy() {
		if ( taxonomy_exists( 'category' ) ) {
			return 'category';
		}
		if ( taxonomy_exists( 'service_categories' ) ) {
			return 'service_categories';
		}
		return 'category';
	}
}

if ( ! function_exists( 'kayan_kit_term_icon' ) ) {
	function kayan_kit_term_icon( $term, $fallback = 'fas fa-layer-group' ) {
		if ( ! $term ) {
			return '<i class="' . esc_attr( $fallback ) . '"></i>';
		}
		$icon = get_term_meta( $term->term_id, 'icon', true );
		if ( empty( $icon ) ) {
			$icon = get_term_meta( $term->term_id, 'Image-Icon', true );
		}
		if ( ! empty( $icon ) ) {
			return function_exists( 'kayan_icon_html' ) ? kayan_icon_html( $icon ) : $icon;
		}
		return '<i class="' . esc_attr( $fallback ) . '"></i>';
	}
}

if ( ! function_exists( 'kayan_kit_cta_buttons' ) ) {
	function kayan_kit_cta_buttons( $wrap_class = 'hero-ctas' ) {
		$phone = kayan_kit_phone();
		$wa    = kayan_kit_whatsapp();
		$book  = function_exists( 'kayan_kit_booking_button_url' ) ? kayan_kit_booking_button_url( get_the_ID() ) : '';
		if ( ! $phone && ! $wa && ! $book ) {
			return;
		}
		echo '<div class="' . esc_attr( $wrap_class ) . '">';
		if ( $book ) {
			echo '<a class="btn btn-quote" href="' . esc_url( $book ) . '"><i class="fas fa-calendar-check"></i> ' . esc_html( function_exists( 'kayan_ui' ) ? kayan_ui( 'احجز الآن', 'Book now' ) : __( 'احجز الآن', 'yourcolor' ) ) . '</a>';
		}
		if ( $wa ) {
			echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> ' . esc_html( function_exists( 'kayan_ui' ) ? kayan_ui( 'تواصل عبر واتساب', 'Chat on WhatsApp' ) : __( 'تواصل عبر واتساب', 'yourcolor' ) ) . '</a>';
		}
		if ( $phone ) {
			echo '<a class="btn btn-call" href="tel:' . esc_attr( $phone ) . '"><i class="fas fa-phone"></i> ' . esc_html( function_exists( 'kayan_ui' ) ? kayan_ui( 'اتصل الآن', 'Call now' ) : __( 'اتصل الآن', 'yourcolor' ) ) . '</a>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_kit_side_cta' ) ) {
	function kayan_kit_side_cta( $title = '', $text = '' ) {
		$phone = kayan_kit_phone();
		$wa    = kayan_kit_whatsapp();
		$book  = function_exists( 'kayan_kit_booking_button_url' ) ? kayan_kit_booking_button_url( get_the_ID() ) : '';
		if ( ! $phone && ! $wa && ! $book ) {
			return;
		}
		echo '<aside class="side-w cta">';
		echo '<h4>' . esc_html( $title !== '' ? $title : get_bloginfo( 'name' ) ) . '</h4>';
		if ( $text !== '' ) {
			echo '<p>' . esc_html( $text ) . '</p>';
		}
		if ( $book ) {
			echo '<a class="btn btn-quote" href="' . esc_url( $book ) . '" style="width:100%;margin-bottom:10px"><i class="fas fa-calendar-check"></i> ' . esc_html__( 'احجز الآن', 'yourcolor' ) . '</a>';
		}
		if ( $wa ) {
			echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener" style="width:100%;margin-bottom:10px"><i class="fab fa-whatsapp"></i> ' . esc_html__( 'واتساب', 'yourcolor' ) . '</a>';
		}
		if ( $phone ) {
			echo '<a class="btn btn-ghost" href="tel:' . esc_attr( $phone ) . '" style="width:100%"><i class="fas fa-phone"></i> ' . esc_html( $phone ) . '</a>';
		}
		echo '</aside>';
	}
}

if ( ! function_exists( 'kayan_kit_render_svc' ) ) {
	function kayan_kit_render_svc( $item ) {
		$icon  = get_post_meta( $item->ID, 'service_icon', true );
		$price = get_post_meta( $item->ID, 'service_price', true );
		echo '<a class="svc" href="' . esc_url( get_permalink( $item ) ) . '">';
		echo '<div class="svc-ic">' . ( $icon && function_exists( 'kayan_icon_html' ) ? kayan_icon_html( $icon ) : '<i class="fas fa-screwdriver-wrench"></i>' ) . '</div>';
		echo '<h3>' . esc_html( $item->post_title ) . '</h3>';
		echo '<p class="desc">' . esc_html( kayan_kit_plain( $item->post_excerpt ? $item->post_excerpt : $item->post_content, 140 ) ) . '</p>';
		if ( $price ) {
			echo '<span class="ccount">' . esc_html( $price ) . '</span> ';
		}
		echo '<span class="svc-cta">' . esc_html__( 'تفاصيل الخدمة', 'yourcolor' ) . ' <i class="fas fa-arrow-left"></i></span>';
		echo '</a>';
	}
}

if ( ! function_exists( 'kayan_kit_render_catcard' ) ) {
	function kayan_kit_render_catcard( $term ) {
		$link  = get_term_link( $term );
		$count = isset( $term->count ) ? (int) $term->count : 0;
		echo '<a class="catcard" href="' . esc_url( is_wp_error( $link ) ? '#' : $link ) . '">';
		echo '<div class="cic">' . kayan_kit_term_icon( $term, 'fas fa-toolbox' ) . '</div>';
		echo '<h3>' . esc_html( $term->name ) . '</h3>';
		echo '<p>' . esc_html( kayan_kit_plain( $term->description, 90 ) ) . '</p>';
		if ( $count > 0 ) {
			echo '<span class="ccount">' . esc_html( sprintf( _n( '%s عنصر', '%s عناصر', $count, 'yourcolor' ), number_format_i18n( $count ) ) ) . '</span>';
		} else {
			echo '<span class="ccount">' . esc_html__( 'عرض التفاصيل', 'yourcolor' ) . '</span>';
		}
		echo '</a>';
	}
}

if ( ! function_exists( 'kayan_kit_render_citycard' ) ) {
	function kayan_kit_render_citycard( $term ) {
		$link = get_term_link( $term );
		$img  = get_term_meta( $term->term_id, 'image_blog', true );
		if ( empty( $img ) ) {
			$img_id = get_term_meta( $term->term_id, 'image_blog_id', true );
			if ( $img_id ) {
				$img = wp_get_attachment_image_url( $img_id, 'medium_large' );
			}
		}
		$style = $img ? ' style="background-image:url(' . esc_url( $img ) . ');background-size:cover;background-position:center"' : '';
		echo '<a class="citycard" href="' . esc_url( is_wp_error( $link ) ? '#' : $link ) . '"' . $style . '>';
		if ( $term->count ) {
			echo '<span class="ccbadge">' . esc_html( number_format_i18n( $term->count ) ) . '</span>';
		}
		echo '<div class="cc-in"><h3>' . esc_html( $term->name ) . '</h3>';
		$small = kayan_kit_plain( $term->description, 70 );
		$resp  = get_term_meta( $term->term_id, 'city_response_time', true );
		if ( $resp ) {
			$small = $small ? $small . ' · ' . $resp : $resp;
		}
		if ( $small ) {
			echo '<small><i class="fas fa-location-dot"></i> ' . esc_html( $small ) . '</small>';
		}
		echo '</div></a>';
	}
}

if ( ! function_exists( 'kayan_kit_render_bcard' ) ) {
	function kayan_kit_render_bcard( $item, $featured = false ) {
		$cats = get_the_terms( $item->ID, 'category' );
		$cat  = ( is_array( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : '';
		$thumb = get_the_post_thumbnail_url( $item->ID, 'medium_large' );
		echo '<a class="bcard' . ( $featured ? ' featured' : '' ) . '" href="' . esc_url( get_permalink( $item ) ) . '">';
		echo '<div class="bimg"' . ( $thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ');background-size:cover;background-position:center"' : '' ) . '>';
		if ( $cat ) {
			echo '<span class="bcat">' . esc_html( $cat ) . '</span>';
		}
		if ( ! $thumb ) {
			echo '<i class="fas fa-newspaper"></i>';
		}
		echo '</div>';
		echo '<div class="bbody">';
		echo '<h3>' . esc_html( $item->post_title ) . '</h3>';
		echo '<p>' . esc_html( kayan_kit_plain( $item->post_excerpt ? $item->post_excerpt : $item->post_content, 110 ) ) . '</p>';
		echo '<span class="bread">' . esc_html__( 'اقرأ المزيد', 'yourcolor' ) . ' <i class="fas fa-arrow-left"></i></span>';
		echo '</div></a>';
	}
}

if ( ! function_exists( 'kayan_kit_render_faq' ) ) {
	function kayan_kit_render_faq( $question, $answer, $open = false ) {
		echo '<div class="faq-item' . ( $open ? ' faq-open' : '' ) . '">';
		echo '<div class="faq-q">' . esc_html( $question ) . ' <i class="fas fa-chevron-down"></i></div>';
		echo '<div class="faq-a"' . ( $open ? ' style="max-height:400px"' : '' ) . '><p>' . wp_kses_post( $answer ) . '</p></div>';
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_kit_live_dash_services' ) ) {
	function kayan_kit_live_dash_services( $limit = 6 ) {
		$terms = kayan_kit_terms( array( 'category', 'service_categories' ), array( 'number' => $limit ) );
		$out   = array();
		foreach ( $terms as $term ) {
			$icon = get_term_meta( $term->term_id, 'icon', true );
			$link = get_term_link( $term );
			$out[] = array(
				'icon'  => $icon ? $icon : '<i class="fas fa-circle-check"></i>',
				'title' => $term->name,
				'url'   => is_wp_error( $link ) ? '' : $link,
			);
		}
		return $out;
	}
}

if ( ! function_exists( 'kayan_kit_contact_form' ) ) {
	function kayan_kit_contact_form( $submit_label = '' ) {
		$fields = array(
			array( 'title' => __( 'الاسم بالكامل', 'yourcolor' ), 'id' => 'user__name', 'type' => 'Text', 'Require' => 'on' ),
			array( 'title' => __( 'رقم الهاتف', 'yourcolor' ), 'id' => 'phone__number', 'type' => 'Number', 'Require' => 'on' ),
			array( 'title' => __( 'البريد الالكتروني', 'yourcolor' ), 'id' => 'user_mail', 'type' => 'Email', 'Require' => '' ),
			array( 'title' => __( 'تفاصيل الطلب', 'yourcolor' ), 'id' => 'description', 'type' => 'TextArea', 'Require' => 'on' ),
		);
		$encoded = base64_encode( wp_json_encode( $fields ) );
		echo '<form method="POST" action="contact__form" data-form-ajax="true" data-for-action="1" data-fields-arguments="' . esc_attr( $encoded ) . '">';
		echo '<div class="form-grid">';
		foreach ( $fields as $field ) {
			$full = ( 'TextArea' === $field['type'] || 'user_mail' === $field['id'] ) ? ' full' : '';
			echo '<div class="fld' . $full . '" data-field-id="' . esc_attr( $field['id'] ) . '">';
			echo '<label>' . esc_html( $field['title'] ) . '</label>';
			if ( 'TextArea' === $field['type'] ) {
				echo '<textarea name="' . esc_attr( $field['id'] ) . '"></textarea>';
			} elseif ( 'Email' === $field['type'] ) {
				echo '<input type="email" name="' . esc_attr( $field['id'] ) . '" />';
			} elseif ( 'Number' === $field['type'] ) {
				echo '<input type="tel" name="' . esc_attr( $field['id'] ) . '" />';
			} else {
				echo '<input type="text" name="' . esc_attr( $field['id'] ) . '" />';
			}
			echo '</div>';
		}
		echo '</div>';
		echo '<button type="submit" class="btn btn-quote" style="width:100%;margin-top:20px"><i class="fas fa-paper-plane"></i> ' . esc_html( $submit_label !== '' ? $submit_label : __( 'إرسال الرسالة', 'yourcolor' ) ) . '</button>';
		echo '<div class="form-note"><i class="fas fa-lock"></i> ' . esc_html__( 'بياناتك محفوظة ولن تُستخدم إلا للتواصل معك.', 'yourcolor' ) . '</div>';
		echo '</form>';
	}
}

if ( ! function_exists( 'kayan_kit_empty' ) ) {
	function kayan_kit_empty( $message = '' ) {
		echo '<div class="kayan-empty">';
		echo '<i class="fa-regular fa-folder-open"></i>';
		if ( $message !== '' ) {
			echo '<p>' . wp_kses_post( $message ) . '</p>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_kit_stars' ) ) {
	function kayan_kit_stars( $count ) {
		$count = max( 0, min( 5, (int) $count ) );
		$html  = '';
		for ( $i = 1; $i <= 5; $i++ ) {
			$html .= $i <= $count ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_kit_widgets_include' ) ) {
	function kayan_kit_widgets_include( $widgets, $name ) {
		if ( empty( $widgets ) || ! is_array( $widgets ) ) {
			return false;
		}
		$hay = wp_json_encode( $widgets );
		return ( false !== $hay && false !== strpos( $hay, $name ) );
	}
}

if ( ! function_exists( 'kayan_kit_is_skipped_lead_paragraph' ) ) {
	function kayan_kit_is_skipped_lead_paragraph( $inner_html, $plain ) {
		$plain = trim( (string) $plain );
		if ( mb_strlen( $plain, 'UTF-8' ) < 50 ) {
			return true;
		}
		if ( false !== stripos( $plain, '[caption' ) ) {
			return true;
		}
		if ( preg_match( '/<img\b/i', (string) $inner_html ) ) {
			return true;
		}
		if ( preg_match( '/كتب هذا المقال|آخر تحديث|كتب بواسطة|Last updated|Written by/u', $plain ) ) {
			return true;
		}
		return false;
	}
}

if ( ! function_exists( 'kayan_kit_split_article_lead' ) ) {
	function kayan_kit_split_article_lead( $html ) {
		$html = (string) $html;
		$lead = '';
		$rest = $html;
		if ( preg_match_all( '/<p(\s[^>]*)?>(.*?)<\/p>/is', $html, $matches, PREG_OFFSET_CAPTURE ) ) {
			foreach ( $matches[0] as $i => $m ) {
				$attrs = isset( $matches[1][ $i ][0] ) ? $matches[1][ $i ][0] : '';
				$inner = $matches[2][ $i ][0];
				$plain = trim( kayan_kit_plain( $inner ) );
				if ( preg_match( '/wp-caption-text|screen-reader-text/i', $attrs ) ) {
					continue;
				}
				if ( kayan_kit_is_skipped_lead_paragraph( $inner, $plain ) ) {
					continue;
				}
				$lead = $plain;
				$full = $m[0];
				$pos  = $m[1];
				$rest = substr( $html, 0, $pos ) . substr( $html, $pos + strlen( $full ) );
				break;
			}
		}
		$rest = preg_replace( '/^\s*(<section\b[^>]*>\s*)<h1\b[^>]*>.*?<\/h1>/is', '$1', $rest, 1 );
		$rest = preg_replace( '/^\s*<h1\b[^>]*>.*?<\/h1>/is', '', $rest, 1 );
		return array( $lead, $rest );
	}
}

if ( ! function_exists( 'kayan_kit_render_customer_ratings' ) ) {
	function kayan_kit_render_customer_ratings( $post ) {
		if ( ! $post || empty( $post->ID ) ) {
			return;
		}
		if ( ! empty( get_option( 'hide__feedback__rating' ) ) ) {
			return;
		}

		$post_id = (int) $post->ID;
		$avg     = get_post_meta( $post_id, 'TotalRate_v1', true );
		$count   = (int) get_post_meta( $post_id, 'RateUserCount_v1', true );
		$bars    = get_post_meta( $post_id, 'RateUsersData_v1', true );
		$bars    = is_array( $bars ) ? $bars : array();

		if ( $count <= 0 ) {
			$def = get_post_meta( $post_id, 'defualt__rating', true );
			if ( ! is_array( $def ) ) {
				$def = array();
			}
			if ( isset( $def['ratingValue'] ) && is_numeric( $def['ratingValue'] ) ) {
				$avg = $def['ratingValue'];
			}
			$count = 0;
			$bars  = array();
			for ( $i = 1; $i <= 5; $i++ ) {
				$key        = 'ratingUsers_' . $i;
				$bars[ $i ] = ( isset( $def[ $key ] ) && is_numeric( $def[ $key ] ) ) ? (int) $def[ $key ] : 0;
				$count     += $bars[ $i ];
			}
			if ( $count <= 0 ) {
				$bars  = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 1 );
				$count = 1;
				$avg   = ( is_numeric( $avg ) && (float) $avg > 0 ) ? $avg : 5;
			}
		}

		$avg = is_numeric( $avg ) ? round( (float) $avg, 1 ) : 0;
		if ( $avg <= 0 && $count > 0 ) {
			$sum = 0;
			for ( $i = 1; $i <= 5; $i++ ) {
				$sum += $i * ( isset( $bars[ $i ] ) ? (int) $bars[ $i ] : 0 );
			}
			$avg = $count ? round( $sum / $count, 1 ) : 0;
		}
		if ( $avg <= 0 ) {
			$avg = 5;
		}

		$title = function_exists( 'kayan_ui' ) ? kayan_ui( 'تقييمات العملاء', 'Customer reviews' ) : 'تقييمات العملاء';
		$pct_fill = min( 100, round( ( $avg / 5 ) * 100, 2 ) );

		echo '<div class="--rating--widgets--box kayan-customer-ratings">';
		echo '<div class="-sidebar-related-title-section --rating--widgets-title">' . esc_html( $title ) . '</div>';
		echo '<div class="--YC-single-rating-box--">';
		echo '<div class="--rating--widgets--result--box">';
		echo '<div class="--rating--widgets--stars-result">';
		echo '<div class="SB--Stars">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo '<i class="fa-solid fa-star"></i>';
		}
		echo '</div>';
		echo '<div class="Active--Stars" style="--bevalue:' . esc_attr( $pct_fill ) . '%">';
		for ( $i = 1; $i <= 5; $i++ ) {
			echo '<i class="fa-solid fa-star"></i>';
		}
		echo '</div></div>';
		echo '<div class="ratingServise--stars-value -rating-value" data-post-id="' . esc_attr( $post_id ) . '">' . esc_html( $avg ) . '</div>';
		echo '</div>';
		echo '<div class="--rating--widgets--stars-averageList">';
		echo '<div class="-Rate-Average-Items -Js-Rate-AverageItems" data-post-id="' . esc_attr( $post_id ) . '">';
		for ( $s = 5; $s >= 1; $s-- ) {
			$n   = isset( $bars[ $s ] ) ? (int) $bars[ $s ] : 0;
			$pct = $count > 0 ? round( ( $n * 100 ) / $count, 1 ) : 0;
			echo '<div class="-Rate-Average-element">';
			echo '<em>' . esc_html( $s ) . '</em>';
			echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="' . esc_attr( $pct ) . '" style="width:' . esc_attr( $pct ) . '%"></div></div>';
			echo '<span>' . esc_html( $pct ) . '%</span>';
			echo '</div>';
		}
		echo '</div></div></div></div>';
	}
}

if ( ! function_exists( 'kayan_kit_has_plugin_toc' ) ) {
	function kayan_kit_has_plugin_toc( $html ) {
		return (bool) preg_match( '/ez-toc|rank-math-toc|wp-block-rank-math-toc/i', (string) $html );
	}
}

if ( ! function_exists( 'kayan_kit_anchor_headings' ) ) {
	function kayan_kit_anchor_headings( $html ) {
		$i = 0;
		return preg_replace_callback(
			'/<h2(\s[^>]*)?>/i',
			function( $m ) use ( &$i ) {
				$i++;
				$attrs = isset( $m[1] ) ? $m[1] : '';
				if ( preg_match( '/\sid\s*=/', $attrs ) ) {
					return $m[0];
				}
				return '<h2' . $attrs . ' id="kit-h-' . $i . '">';
			},
			(string) $html
		);
	}
}

if ( ! function_exists( 'kayan_kit_page_excerpt' ) ) {
	function kayan_kit_page_excerpt( $post ) {
		if ( ! $post ) {
			return '';
		}
		if ( ! empty( $post->post_excerpt ) ) {
			return kayan_kit_plain( $post->post_excerpt, 280 );
		}
		return kayan_kit_plain( $post->post_content, 280 );
	}
}

/**
 * املأ حقول إعدادات الودجت الفارغة بما يظهر فعلاً في الموقع (تصنيفات، مدن، اسم الموقع).
 * لا يكتب في قاعدة البيانات حتى يحفظ المدير.
 */
if ( ! function_exists( 'kayan_hydrate_widget_meta_for_admin' ) ) {
	function kayan_hydrate_widget_meta_for_admin( $widget_type, $meta ) {
		$meta = is_array( $meta ) ? $meta : array();
		$site = get_bloginfo( 'name' );

		if ( empty( $meta['title'] ) && in_array( $widget_type, array( 'slider_intro_v1' ), true ) ) {
			$meta['title'] = $site;
		}
		if ( 'slider_intro_v1' === $widget_type && empty( $meta['dash_services'] ) ) {
			$meta['dash_services'] = kayan_kit_live_dash_services();
		}

		if ( 'city__widget' === $widget_type ) {
			$terms = kayan_kit_terms( array( kayan_kit_city_taxonomy(), 'city', 'cities' ) );
			if ( empty( $meta['taxonomy_option'] ) && ! empty( $terms ) ) {
				$meta['taxonomy_option'] = wp_list_pluck( $terms, 'term_id' );
			}
			if ( empty( $meta['manual_cities'] ) && ! empty( $terms ) ) {
				$meta['manual_cities'] = array();
				foreach ( $terms as $term ) {
					$link = get_term_link( $term );
					$meta['manual_cities'][] = array(
						'title'    => $term->name,
						'small'    => kayan_kit_plain( $term->description, 80 ),
						'services' => '',
						'url'      => is_wp_error( $link ) ? '' : $link,
						'icon'     => '',
					);
				}
			}
		}

		if ( 'category' === $widget_type ) {
			$terms = kayan_kit_terms( array( 'category', 'service_categories' ) );
			if ( empty( $meta['taxonomy_option'] ) && ! empty( $terms ) ) {
				$meta['taxonomy_option'] = wp_list_pluck( $terms, 'term_id' );
			}
			if ( empty( $meta['manual_cards'] ) && ! empty( $terms ) ) {
				$meta['manual_cards'] = array();
				foreach ( $terms as $term ) {
					$link = get_term_link( $term );
					$meta['manual_cards'][] = array(
						'title'    => $term->name,
						'desc'     => kayan_kit_plain( $term->description, 120 ),
						'features' => '',
						'url'      => is_wp_error( $link ) ? '' : $link,
						'icon'     => '',
					);
				}
			}
		}

		if ( 'rukn_finder' === $widget_type ) {
			if ( empty( $meta['manual_cities'] ) ) {
				$cities = kayan_kit_terms( array( 'city', 'cities' ) );
				if ( ! empty( $cities ) ) {
					$meta['manual_cities'] = array();
					foreach ( $cities as $term ) {
						$meta['manual_cities'][] = array(
							'name' => $term->name,
							'time' => get_term_meta( $term->term_id, 'city_response_time', true ),
						);
					}
				}
			}
			if ( empty( $meta['manual_services'] ) ) {
				$cats = kayan_kit_terms( array( 'category', 'service_categories' ) );
				if ( ! empty( $cats ) ) {
					$lines = array();
					foreach ( $cats as $term ) {
						$lines[] = $term->name;
					}
					$meta['manual_services'] = implode( "\n", $lines );
				}
			}
		}

		if ( 'Faqs__simple2' === $widget_type && empty( $meta['manual_faqs'] ) ) {
			$faqs = kayan_kit_posts( array( 'faqs', 'faq' ), array( 'posts_per_page' => 12 ) );
			if ( ! empty( $faqs ) ) {
				$meta['manual_faqs'] = array();
				foreach ( $faqs as $faq ) {
					$meta['manual_faqs'][] = array(
						'question' => $faq->post_title,
						'answer'   => kayan_kit_plain( $faq->post_content, 400 ),
						'category' => '',
					);
				}
			}
		}

		if ( 'blog_v1' === $widget_type && empty( $meta['title'] ) ) {
			$meta['title'] = __( 'المدونة', 'yourcolor' );
		}

		return $meta;
	}
}

if ( ! function_exists( 'kayan_kit_model_labels' ) ) {
	function kayan_kit_model_labels() {
		return array(
			'about'           => 'من نحن',
			'article-single'  => 'مقال مفرد',
			'blog'            => 'المدونة',
			'booking'         => 'الحجز',
			'categories'      => 'تصنيف خدمة',
			'cities'          => 'المدن',
			'city-single'     => 'مدينة مفردة',
			'contact-us'      => 'تواصل معنا',
			'faqs'            => 'الأسئلة الشائعة',
			'offers'          => 'العروض',
			'price'           => 'الأسعار',
			'privacy'         => 'سياسة الخصوصية',
			'reviews'         => 'التقييمات',
			'service-city'    => 'خدمة × مدينة',
			'service-single'  => 'خدمة مفردة',
			'services'        => 'الخدمات',
			'sitemap'         => 'خريطة الموقع',
			'standard-page'   => 'صفحة عادية',
			'terms'           => 'الشروط والأحكام',
			'works'           => 'معرض الأعمال',
		);
	}
}
