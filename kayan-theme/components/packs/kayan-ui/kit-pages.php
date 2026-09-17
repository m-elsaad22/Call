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
		$subtitle = kayan_kit_plain( $subtitle, 280 );
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
		if ( ! $phone && ! $wa ) {
			return;
		}
		echo '<div class="' . esc_attr( $wrap_class ) . '">';
		if ( $wa ) {
			echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> ' . esc_html__( 'تواصل عبر واتساب', 'yourcolor' ) . '</a>';
		}
		if ( $phone ) {
			echo '<a class="btn btn-call" href="tel:' . esc_attr( $phone ) . '"><i class="fas fa-phone"></i> ' . esc_html__( 'اتصل الآن', 'yourcolor' ) . '</a>';
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_kit_side_cta' ) ) {
	function kayan_kit_side_cta( $title = '', $text = '' ) {
		$phone = kayan_kit_phone();
		$wa    = kayan_kit_whatsapp();
		if ( ! $phone && ! $wa ) {
			return;
		}
		echo '<aside class="side-w cta">';
		echo '<h4>' . esc_html( $title !== '' ? $title : get_bloginfo( 'name' ) ) . '</h4>';
		if ( $text !== '' ) {
			echo '<p>' . esc_html( $text ) . '</p>';
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
		echo '<div class="bmeta"><span><i class="fas fa-calendar"></i> ' . esc_html( get_the_date( '', $item ) ) . '</span></div>';
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
