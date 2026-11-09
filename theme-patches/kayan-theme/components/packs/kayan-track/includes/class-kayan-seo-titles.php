<?php
if ( ! class_exists( 'Kayan_Seo_Titles' ) ) {

	class Kayan_Seo_Titles {

		const TITLE_KEY  = 'rank_math_title';
		const BACKUP_KEY = 'kayan_track_seo_title_backup';

		public static function rank_math_active() {
			return defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
		}

		public static function get_title( $post_id ) {
			$title = get_post_meta( (int) $post_id, self::TITLE_KEY, true );
			return is_string( $title ) ? $title : '';
		}

		public static function list_posts( $args ) {
			$page     = max( 1, (int) ( isset( $args['page'] ) ? $args['page'] : 1 ) );
			$per_page = min( 2000, max( 5, (int) ( isset( $args['per_page'] ) ? $args['per_page'] : 20 ) ) );
			$search   = sanitize_text_field( isset( $args['search'] ) ? $args['search'] : '' );
			$type     = sanitize_key( isset( $args['post_type'] ) ? $args['post_type'] : '' );

			$types = $type ? $type : get_post_types( array( 'public' => true ) );
			if ( is_array( $types ) ) {
				$types = array_values( array_diff( $types, array( 'attachment' ) ) );
			}

			$query_args = array(
				'post_type'              => $types,
				'post_status'            => array( 'publish', 'draft' ),
				'posts_per_page'         => $per_page,
				'paged'                  => $page,
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'no_found_rows'          => false,
				'update_post_meta_cache' => true,
			);
			if ( $search !== '' ) {
				$query_args['s'] = $search;
			}

			$query = new WP_Query( $query_args );
			$posts = array();
			foreach ( $query->posts as $post ) {
				$posts[] = self::post_row( $post );
			}

			$types = array();
			foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $pt ) {
				$types[] = array(
					'name'  => $pt->name,
					'label' => $pt->label,
				);
			}

			return array(
				'posts'             => $posts,
				'total'             => (int) $query->found_posts,
				'total_pages'       => max( 1, (int) $query->max_num_pages ),
				'current_page'      => $page,
				'per_page'          => $per_page,
				'rank_math_active'  => self::rank_math_active() ? 1 : 0,
				'post_types'        => $types,
			);
		}

		public static function set_title( $post_id, $title ) {
			if ( ! self::rank_math_active() ) {
				return new WP_Error( 'rank_math_inactive', 'إضافة Rank Math غير مفعلة.' );
			}
			$post_id = (int) $post_id;
			if ( ! get_post( $post_id ) ) {
				return new WP_Error( 'missing', 'المقالة غير موجودة.' );
			}
			$old = self::get_title( $post_id );
			if ( $old !== '' && ! get_post_meta( $post_id, self::BACKUP_KEY, true ) ) {
				update_post_meta( $post_id, self::BACKUP_KEY, $old );
			}
			update_post_meta( $post_id, self::TITLE_KEY, sanitize_text_field( $title ) );
			return true;
		}

		public static function clear_title( $post_id ) {
			$post_id = (int) $post_id;
			delete_post_meta( $post_id, self::TITLE_KEY );
			delete_post_meta( $post_id, self::BACKUP_KEY );
			return true;
		}

		public static function rollback_title( $post_id ) {
			$post_id = (int) $post_id;
			$backup  = get_post_meta( $post_id, self::BACKUP_KEY, true );
			if ( $backup === '' || $backup === false ) {
				return new WP_Error( 'no_backup', 'لا توجد نسخة احتياطية لهذا العنوان.' );
			}
			update_post_meta( $post_id, self::TITLE_KEY, $backup );
			delete_post_meta( $post_id, self::BACKUP_KEY );
			return true;
		}

		public static function preview_bulk( $post_ids, $template ) {
			$out = array();
			foreach ( $post_ids as $post_id ) {
				$post = get_post( (int) $post_id );
				if ( ! $post ) {
					continue;
				}
				$resolved = self::resolve_template( $template, (int) $post_id );
				$out[]    = array(
					'post_id'     => (int) $post_id,
					'post_title'  => $post->post_title,
					'current_seo' => self::get_title( (int) $post_id ),
					'new_seo'     => $resolved,
					'warnings'    => self::validate( $resolved ),
				);
			}
			return $out;
		}

		public static function apply_bulk( $post_ids, $template ) {
			$ok = 0;
			$fail = 0;
			foreach ( $post_ids as $post_id ) {
				$resolved = self::resolve_template( $template, (int) $post_id );
				$result   = self::set_title( (int) $post_id, $resolved );
				if ( is_wp_error( $result ) ) {
					$fail++;
				} else {
					$ok++;
				}
			}
			return array(
				'success' => $ok,
				'failed'  => $fail,
			);
		}

		public static function resolve_template( $template, $post_id ) {
			$post      = get_post( $post_id );
			$site_name = get_bloginfo( 'name' );
			$city      = self::extract_meta_or_term( $post_id, array( 'city', 'المدينة', '_city', 'location' ), 'city' );
			$service   = self::extract_meta_or_term( $post_id, array( 'service', 'الخدمة', '_service' ), 'service' );
			$category  = self::extract_meta_or_term( $post_id, array(), 'category' );
			$map       = array(
				'{post_title}' => $post ? $post->post_title : '',
				'{city}'       => $city,
				'{service}'    => $service,
				'{category}'   => $category,
				'{site_name}'  => $site_name,
			);
			return str_replace( array_keys( $map ), array_values( $map ), $template );
		}

		private static function extract_meta_or_term( $post_id, $meta_keys, $type ) {
			foreach ( $meta_keys as $key ) {
				$val = get_post_meta( $post_id, $key, true );
				if ( $val ) {
					return sanitize_text_field( $val );
				}
			}
			$map = array(
				'city'     => array( 'city', 'cities', 'location', 'المدن' ),
				'service'  => array( 'service', 'services', 'الخدمات' ),
				'category' => array( 'category', 'service_category' ),
			);
			$taxes = array();
			if ( isset( $map[ $type ] ) ) {
				foreach ( $map[ $type ] as $tax ) {
					if ( taxonomy_exists( $tax ) ) {
						$taxes[] = $tax;
					}
				}
			}
			if ( ! $taxes ) {
				return '';
			}
			$terms = wp_get_object_terms( $post_id, $taxes, array( 'fields' => 'names', 'number' => 1 ) );
			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				return '';
			}
			return sanitize_text_field( $terms[0] );
		}

		private static function validate( $title ) {
			$warnings = array();
			if ( trim( $title ) === '' ) {
				$warnings[] = 'العنوان فارغ.';
			}
			if ( function_exists( 'mb_strlen' ) && mb_strlen( $title ) > 70 ) {
				$warnings[] = 'العنوان أطول من 70 حرفاً.';
			}
			if ( preg_match( '/\{[^}]+\}/', $title ) ) {
				$warnings[] = 'يحتوي العنوان على متغير لم يُحلّ.';
			}
			return $warnings;
		}

		private static function post_row( $post ) {
			$seo = self::get_title( $post->ID );
			return array(
				'post_id'     => (int) $post->ID,
				'title'       => $post->post_title,
				'seo_title'   => $seo,
				'has_backup'  => get_post_meta( $post->ID, self::BACKUP_KEY, true ) ? 1 : 0,
				'edit_url'    => get_edit_post_link( $post->ID, 'raw' ),
				'post_url'    => get_permalink( $post->ID ),
				'post_type'   => $post->post_type,
			);
		}
	}
}
