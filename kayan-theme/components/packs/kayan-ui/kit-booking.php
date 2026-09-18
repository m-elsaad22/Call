<?php
/**
 * ربط زر الحجز بجدول الأسعار + سياق الخدمة/المدينة لصفحة الحجز.
 */

if ( ! function_exists( 'kayan_kit_has_price_table' ) ) {
	function kayan_kit_has_price_table( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
		if ( ! $post_id ) {
			return false;
		}
		if ( get_post_meta( $post_id, 'hide_price_list__section', true ) ) {
			return false;
		}
		$data = get_post_meta( $post_id, 'post__price_list__data', true );
		if ( ! is_array( $data ) || empty( $data['price_list__items'] ) || ! is_array( $data['price_list__items'] ) ) {
			return false;
		}
		foreach ( $data['price_list__items'] as $row ) {
			if ( is_array( $row ) && ! empty( $row['title'] ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'kayan_kit_price_packages' ) ) {
	function kayan_kit_price_packages( $post_id ) {
		$data = get_post_meta( $post_id, 'post__price_list__data', true );
		if ( ! is_array( $data ) || empty( $data['price_list__items'] ) ) {
			return array();
		}
		$out = array();
		foreach ( $data['price_list__items'] as $row ) {
			if ( ! is_array( $row ) || empty( $row['title'] ) ) {
				continue;
			}
			$out[] = array(
				'title' => $row['title'],
				'value' => isset( $row['value'] ) ? $row['value'] : '',
			);
		}
		return $out;
	}
}

if ( ! function_exists( 'kayan_kit_booking_page_id' ) ) {
	function kayan_kit_booking_page_id() {
		static $found = null;
		if ( null !== $found ) {
			return $found;
		}

		$opt = (int) get_option( 'kayan_kit_booking_page_id' );
		if ( $opt && 'publish' === get_post_status( $opt ) ) {
			$found = $opt;
			return $found;
		}

		$pages = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'posts_per_page'   => 250,
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);
		foreach ( $pages as $page ) {
			$tpl = get_post_meta( $page->ID, 'template', true );
			if ( is_array( $tpl ) && ! empty( $tpl['SelectedModel'] ) && 'booking' === $tpl['SelectedModel'] ) {
				$found = (int) $page->ID;
				return $found;
			}
		}

		$slugs = array( 'booking', 'book', 'reservation', 'حجز', 'الحجز', 'طلب-خدمة', 'talab-khidma', 'book-now' );
		foreach ( $slugs as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page && 'publish' === $page->post_status ) {
				$found = (int) $page->ID;
				return $found;
			}
		}

		$found = 0;
		return $found;
	}
}

if ( ! function_exists( 'kayan_kit_match_term_in_text' ) ) {
	function kayan_kit_match_term_in_text( $text, $taxonomies ) {
		$text = (string) $text;
		if ( '' === $text ) {
			return null;
		}
		foreach ( (array) $taxonomies as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = get_terms(
				array(
					'taxonomy'   => $tax,
					'hide_empty' => false,
					'number'     => 80,
				)
			);
			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}
			usort(
				$terms,
				function ( $a, $b ) {
					return mb_strlen( $b->name, 'UTF-8' ) - mb_strlen( $a->name, 'UTF-8' );
				}
			);
			foreach ( $terms as $term ) {
				$name = trim( (string) $term->name );
				if ( mb_strlen( $name, 'UTF-8' ) < 2 ) {
					continue;
				}
				if ( false !== mb_stripos( $text, $name, 0, 'UTF-8' ) ) {
					return $term;
				}
			}
		}
		return null;
	}
}

if ( ! function_exists( 'kayan_kit_strip_city_from_title' ) ) {
	function kayan_kit_strip_city_from_title( $title, $city ) {
		$original = trim( (string) $title );
		$title    = $original;
		$city     = trim( (string) $city );
		if ( '' === $title || '' === $city ) {
			return $title;
		}
		$quoted = preg_quote( $city, '/' );
		$title  = preg_replace( '/\s*(في|بـ|ب)\s*' . $quoted . '\s*$/u', '', $title );
		$title  = preg_replace( '/\s+' . $quoted . '\s*$/u', '', $title );
		$title  = trim( $title, " \t\n\r\0\x0B-–—|،," );
		return $title !== '' ? $title : $original;
	}
}

if ( ! function_exists( 'kayan_kit_context_from_post' ) ) {
	function kayan_kit_context_from_post( $post_id ) {
		$ctx = array(
			'from_id'      => 0,
			'from'         => null,
			'title'        => '',
			'url'          => '',
			'service'      => '',
			'service_id'   => 0,
			'city'         => '',
			'city_id'      => 0,
			'package'      => '',
			'packages'     => array(),
			'category_ids' => array(),
			'excerpt'      => '',
			'icon'         => '',
		);

		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return $ctx;
		}
		$from = get_post( $post_id );
		if ( ! $from || 'publish' !== $from->post_status ) {
			return $ctx;
		}

		$ctx['from_id']  = $post_id;
		$ctx['from']     = $from;
		$ctx['title']    = $from->post_title;
		$ctx['url']      = get_permalink( $from );
		$ctx['excerpt']  = function_exists( 'kayan_kit_page_excerpt' ) ? kayan_kit_page_excerpt( $from ) : '';
		$ctx['packages'] = kayan_kit_price_packages( $post_id );
		$ctx['icon']     = get_post_meta( $post_id, 'service_icon', true );

		if ( 'services' === $from->post_type ) {
			$ctx['service_id'] = $post_id;
			$ctx['service']    = $from->post_title;
		}

		$cat_id = (int) get_post_meta( $post_id, 'kit_page_category', true );
		if ( $cat_id ) {
			$term = get_term( $cat_id );
			if ( $term && ! is_wp_error( $term ) ) {
				$ctx['category_ids'][] = (int) $term->term_id;
				if ( '' === $ctx['service'] ) {
					$ctx['service'] = $term->name;
				}
			}
		}

		foreach ( array( 'category', 'service_categories' ) as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$cats = get_the_terms( $post_id, $tax );
			if ( ! is_array( $cats ) ) {
				continue;
			}
			foreach ( $cats as $term ) {
				if ( (int) $term->term_id === 1 && 'category' === $tax ) {
					continue;
				}
				$ctx['category_ids'][] = (int) $term->term_id;
				if ( '' === $ctx['service'] ) {
					$ctx['service'] = $term->name;
				}
			}
		}

		$city_id  = (int) get_post_meta( $post_id, 'kit_page_city', true );
		$city_tax = function_exists( 'kayan_kit_city_taxonomy' ) ? kayan_kit_city_taxonomy() : 'city';
		if ( $city_id ) {
			$term = get_term( $city_id );
			if ( $term && ! is_wp_error( $term ) ) {
				$ctx['city_id'] = (int) $term->term_id;
				$ctx['city']    = $term->name;
			}
		}
		if ( '' === $ctx['city'] ) {
			foreach ( array( $city_tax, 'city', 'cities' ) as $tax ) {
				if ( ! taxonomy_exists( $tax ) ) {
					continue;
				}
				$city_terms = get_the_terms( $post_id, $tax );
				if ( ! is_array( $city_terms ) || empty( $city_terms ) ) {
					continue;
				}
				$ctx['city_id'] = (int) $city_terms[0]->term_id;
				$ctx['city']    = $city_terms[0]->name;
				break;
			}
		}

		$haystack = $from->post_title . ' ' . $ctx['excerpt'];
		if ( '' === $ctx['city'] ) {
			$matched = kayan_kit_match_term_in_text( $haystack, array( $city_tax, 'city', 'cities' ) );
			if ( $matched ) {
				$ctx['city_id'] = (int) $matched->term_id;
				$ctx['city']    = $matched->name;
			}
		}
		if ( '' === $ctx['service'] ) {
			$matched = kayan_kit_match_term_in_text( $haystack, array( 'category', 'service_categories' ) );
			if ( $matched && (int) $matched->term_id !== 1 ) {
				$ctx['category_ids'][] = (int) $matched->term_id;
				$ctx['service']        = $matched->name;
			}
		}

		if ( empty( $ctx['service_id'] ) ) {
			if ( post_type_exists( 'services' ) ) {
				$needle = $ctx['service'] ? $ctx['service'] : $from->post_title;
				$svcs   = get_posts(
					array(
						'post_type'      => 'services',
						'post_status'    => 'publish',
						'posts_per_page' => 40,
						'no_found_rows'  => true,
					)
				);
				foreach ( $svcs as $svc ) {
					if ( 0 === strcasecmp( $svc->post_title, $needle ) || false !== mb_stripos( $needle, $svc->post_title, 0, 'UTF-8' ) || false !== mb_stripos( $svc->post_title, $needle, 0, 'UTF-8' ) ) {
						$ctx['service_id'] = (int) $svc->ID;
						if ( '' === $ctx['service'] ) {
							$ctx['service'] = $svc->post_title;
						}
						break;
					}
				}
			}
		}

		if ( '' === $ctx['service'] ) {
			$cleaned = kayan_kit_strip_city_from_title( $from->post_title, $ctx['city'] );
			$ctx['service'] = $cleaned !== '' ? $cleaned : $from->post_title;
		} elseif ( $ctx['city'] && $ctx['service'] === $from->post_title ) {
			$cleaned = kayan_kit_strip_city_from_title( $ctx['service'], $ctx['city'] );
			if ( $cleaned !== '' ) {
				$ctx['service'] = $cleaned;
			}
		}

		$ctx['category_ids'] = array_values( array_unique( array_filter( $ctx['category_ids'] ) ) );
		return $ctx;
	}
}

if ( ! function_exists( 'kayan_kit_booking_url' ) ) {
	function kayan_kit_booking_url( $from_id = 0, $extra = array() ) {
		$page_id = kayan_kit_booking_page_id();
		if ( ! $page_id ) {
			return '';
		}
		$args = array();
		if ( $from_id ) {
			$args['from'] = (int) $from_id;
			$preview      = kayan_kit_context_from_post( $from_id );
			if ( ! empty( $preview['service'] ) ) {
				$args['service'] = $preview['service'];
			}
			if ( ! empty( $preview['city'] ) ) {
				$args['city'] = $preview['city'];
			}
		}
		if ( is_array( $extra ) ) {
			foreach ( $extra as $k => $v ) {
				if ( '' === $v || null === $v ) {
					continue;
				}
				$args[ $k ] = $v;
			}
		}
		return add_query_arg( $args, get_permalink( $page_id ) );
	}
}

if ( ! function_exists( 'kayan_kit_booking_button_url' ) ) {
	function kayan_kit_booking_button_url( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
		if ( ! $post_id || ! kayan_kit_has_price_table( $post_id ) ) {
			return '';
		}
		if ( $post_id === kayan_kit_booking_page_id() ) {
			return '';
		}
		return kayan_kit_booking_url( $post_id );
	}
}

if ( ! function_exists( 'kayan_kit_booking_context' ) ) {
	function kayan_kit_booking_context() {
		$from_id = isset( $_GET['from'] ) ? absint( wp_unslash( $_GET['from'] ) ) : 0;
		$ctx     = $from_id ? kayan_kit_context_from_post( $from_id ) : array(
			'from_id'      => 0,
			'from'         => null,
			'title'        => '',
			'url'          => '',
			'service'      => '',
			'service_id'   => 0,
			'city'         => '',
			'city_id'      => 0,
			'package'      => '',
			'packages'     => array(),
			'category_ids' => array(),
			'excerpt'      => '',
			'icon'         => '',
		);

		if ( isset( $_GET['service'] ) && '' !== trim( (string) wp_unslash( $_GET['service'] ) ) ) {
			$ctx['service'] = sanitize_text_field( wp_unslash( $_GET['service'] ) );
		}
		if ( isset( $_GET['city'] ) && '' !== trim( (string) wp_unslash( $_GET['city'] ) ) ) {
			$ctx['city'] = sanitize_text_field( wp_unslash( $_GET['city'] ) );
		}
		if ( isset( $_GET['package'] ) && '' !== trim( (string) wp_unslash( $_GET['package'] ) ) ) {
			$ctx['package'] = sanitize_text_field( wp_unslash( $_GET['package'] ) );
		}

		$ctx['questions'] = kayan_kit_booking_questions( $ctx );
		return $ctx;
	}
}

if ( ! function_exists( 'kayan_kit_booking_questions' ) ) {
	function kayan_kit_booking_questions( $ctx ) {
		$fields = array();
		if ( ! empty( $ctx['service_id'] ) && class_exists( 'Kayan_Booking' ) ) {
			$fields = Kayan_Booking::get_service_fields( $ctx['service_id'] );
		}
		if ( empty( $fields ) && ! empty( $ctx['category_ids'] ) && class_exists( 'Kayan_Booking' ) ) {
			foreach ( $ctx['category_ids'] as $cid ) {
				$services = Kayan_Booking::get_linked_services( $cid );
				foreach ( $services as $svc ) {
					$try = Kayan_Booking::get_service_fields( $svc['id'] );
					if ( ! empty( $try ) ) {
						$fields = $try;
						break 2;
					}
				}
			}
		}
		if ( ! empty( $fields ) ) {
			$ids = array();
			foreach ( $fields as $field ) {
				if ( ! empty( $field['id'] ) ) {
					$ids[] = $field['id'];
				}
			}
			if ( ! in_array( 'visit_date', $ids, true ) && ! in_array( 'preferred_date', $ids, true ) ) {
				$fields[] = array(
					'id'      => 'visit_date',
					'title'   => __( 'التاريخ المفضل للزيارة', 'yourcolor' ),
					'type'    => 'Date',
					'require' => false,
					'options' => array(),
				);
			}
			if ( ! in_array( 'preferred_time', $ids, true ) && ! in_array( 'visit_time', $ids, true ) ) {
				$fields[] = array(
					'id'      => 'preferred_time',
					'title'   => __( 'الوقت المفضل', 'yourcolor' ),
					'type'    => 'Select',
					'require' => false,
					'options' => array( __( 'صباحاً (9–12)', 'yourcolor' ), __( 'ظهراً (12–4)', 'yourcolor' ), __( 'مساءً (4–8)', 'yourcolor' ), __( 'عاجل / طوارئ', 'yourcolor' ), __( 'مرن', 'yourcolor' ) ),
				);
			}
			return $fields;
		}

		$svc = ! empty( $ctx['service'] ) ? $ctx['service'] : __( 'الخدمة', 'yourcolor' );
		return array(
			array(
				'id'      => 'property_type',
				'title'   => sprintf( __( 'نوع العقار المطلوب لـ %s', 'yourcolor' ), $svc ),
				'type'    => 'Radio',
				'require' => true,
				'options' => array( __( 'شقة', 'yourcolor' ), __( 'فيلا', 'yourcolor' ), __( 'مكتب / تجاري', 'yourcolor' ), __( 'أخرى', 'yourcolor' ) ),
			),
			array(
				'id'      => 'visit_urgency',
				'title'   => sprintf( __( 'طبيعة طلب %s', 'yourcolor' ), $svc ),
				'type'    => 'Radio',
				'require' => true,
				'options' => array( __( 'معاينة مجدولة', 'yourcolor' ), __( 'طلب عاجل', 'yourcolor' ) ),
			),
			array(
				'id'      => 'visit_date',
				'title'   => __( 'التاريخ المفضل للزيارة', 'yourcolor' ),
				'type'    => 'Date',
				'require' => false,
				'options' => array(),
			),
			array(
				'id'      => 'preferred_time',
				'title'   => __( 'الوقت المفضل للزيارة', 'yourcolor' ),
				'type'    => 'Select',
				'require' => false,
				'options' => array( __( 'صباحاً (9–12)', 'yourcolor' ), __( 'ظهراً (12–4)', 'yourcolor' ), __( 'مساءً (4–8)', 'yourcolor' ), __( 'عاجل / طوارئ', 'yourcolor' ), __( 'مرن', 'yourcolor' ) ),
			),
		);
	}
}

if ( ! function_exists( 'kayan_kit_render_booking_field' ) ) {
	function kayan_kit_render_booking_field( $field ) {
		$id = isset( $field['id'] ) ? sanitize_key( $field['id'] ) : '';
		if ( '' === $id ) {
			return;
		}
		$name     = 'kit_q_' . $id;
		$title    = isset( $field['title'] ) ? $field['title'] : '';
		$type     = isset( $field['type'] ) ? $field['type'] : 'Text';
		$req      = ! empty( $field['require'] ) || ! empty( $field['Require'] );
		$opts     = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
		$disc     = isset( $field['disc'] ) ? $field['disc'] : '';
		$req_attr = $req ? ' required' : '';

		if ( 'File' === $type ) {
			return;
		}

		echo '<div class="kbook-q" data-field-id="' . esc_attr( $name ) . '">';
		echo '<div class="kbook-qlabel">' . esc_html( $title );
		if ( $req ) {
			echo ' <i>*</i>';
		}
		echo '</div>';
		if ( $disc ) {
			echo '<p class="kbook-qhint">' . esc_html( $disc ) . '</p>';
		}

		if ( 'Radio' === $type && $opts ) {
			echo '<div class="kbook-pills">';
			foreach ( $opts as $i => $opt ) {
				$oid = $name . '_' . $i;
				echo '<label class="kbook-pill"><input type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $oid ) . '" value="' . esc_attr( $opt ) . '"' . $req_attr . ' /><span>' . esc_html( $opt ) . '</span></label>';
			}
			echo '</div>';
		} elseif ( 'CheckBox' === $type && $opts ) {
			echo '<div class="kbook-pills">';
			foreach ( $opts as $i => $opt ) {
				echo '<label class="kbook-pill"><input type="checkbox" name="' . esc_attr( $name ) . '[]" value="' . esc_attr( $opt ) . '" /><span>' . esc_html( $opt ) . '</span></label>';
			}
			echo '</div>';
		} elseif ( 'Select' === $type && $opts ) {
			echo '<div class="sel"><i class="fas fa-chevron-down"></i>';
			echo '<select name="' . esc_attr( $name ) . '"' . $req_attr . '>';
			echo '<option value="">' . esc_html__( 'اختر', 'yourcolor' ) . '</option>';
			foreach ( $opts as $opt ) {
				echo '<option value="' . esc_attr( $opt ) . '">' . esc_html( $opt ) . '</option>';
			}
			echo '</select></div>';
		} elseif ( 'TextArea' === $type ) {
			echo '<textarea name="' . esc_attr( $name ) . '"' . $req_attr . '></textarea>';
		} elseif ( 'Number' === $type ) {
			echo '<input type="number" name="' . esc_attr( $name ) . '"' . $req_attr . ' />';
		} elseif ( 'Date' === $type ) {
			echo '<input type="date" name="' . esc_attr( $name ) . '"' . $req_attr . ' />';
		} elseif ( 'SwitchBox' === $type ) {
			echo '<label class="kbook-pill"><input type="checkbox" name="' . esc_attr( $name ) . '" value="1" /><span>' . esc_html__( 'نعم', 'yourcolor' ) . '</span></label>';
		} else {
			echo '<input type="text" name="' . esc_attr( $name ) . '"' . $req_attr . ' />';
		}
		echo '</div>';
	}
}
