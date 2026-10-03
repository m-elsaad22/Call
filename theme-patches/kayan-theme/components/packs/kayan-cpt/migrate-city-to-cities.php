<?php
defined( 'ABSPATH' ) || exit;

/**
 * One-time copy of legacy taxonomy `city` terms, meta, and object
 * relationships onto `cities`. Does not delete the old taxonomy rows.
 */
if ( ! function_exists( 'kayan_city_to_cities_migrate' ) ) {
	function kayan_city_to_cities_migrate() {
		if ( get_option( 'kayan_city_to_cities_migrated' ) === '1' ) {
			return;
		}
		if ( ! taxonomy_exists( 'cities' ) ) {
			return;
		}

		global $wpdb;

		$allowed_types = array( 'post', 'services', 'reviews', 'faqs', 'pricing', 'portfolio', 'before_after' );

		$old_terms = $wpdb->get_results(
			"SELECT t.term_id, t.name, t.slug, tt.term_taxonomy_id, tt.description, tt.parent
			FROM {$wpdb->terms} t
			INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
			WHERE tt.taxonomy = 'city'
			ORDER BY tt.parent ASC, t.term_id ASC"
		);

		if ( ! is_array( $old_terms ) ) {
			$old_terms = array();
		}

		$map = array();

		foreach ( $old_terms as $old ) {
			$old_id = (int) $old->term_id;
			$dest   = get_term_by( 'slug', $old->slug, 'cities' );
			if ( ! $dest || is_wp_error( $dest ) ) {
				$dest = get_term_by( 'name', $old->name, 'cities' );
			}

			$parent = 0;
			if ( (int) $old->parent > 0 && isset( $map[ (int) $old->parent ] ) ) {
				$parent = (int) $map[ (int) $old->parent ];
			}

			if ( $dest && ! is_wp_error( $dest ) ) {
				$dest_id = (int) $dest->term_id;
				$update  = array();
				if ( $dest->description === '' && $old->description !== '' ) {
					$update['description'] = $old->description;
				}
				if ( $parent && (int) $dest->parent === 0 ) {
					$update['parent'] = $parent;
				}
				if ( $update ) {
					wp_update_term( $dest_id, 'cities', $update );
				}
			} else {
				$inserted = wp_insert_term(
					$old->name,
					'cities',
					array(
						'slug'        => $old->slug,
						'description' => $old->description,
						'parent'      => $parent,
					)
				);
				if ( is_wp_error( $inserted ) ) {
					$inserted = wp_insert_term(
						$old->name,
						'cities',
						array(
							'description' => $old->description,
							'parent'      => $parent,
						)
					);
				}
				if ( is_wp_error( $inserted ) ) {
					continue;
				}
				$dest_id = (int) $inserted['term_id'];
			}

			$map[ $old_id ] = $dest_id;

			$metas = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT meta_key, meta_value FROM {$wpdb->termmeta} WHERE term_id = %d",
					$old_id
				)
			);
			if ( is_array( $metas ) ) {
				foreach ( $metas as $meta ) {
					if ( $meta->meta_key === '' ) {
						continue;
					}
					if ( metadata_exists( 'term', $dest_id, $meta->meta_key ) ) {
						continue;
					}
					add_term_meta( $dest_id, $meta->meta_key, maybe_unserialize( $meta->meta_value ) );
				}
			}

			$object_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d",
					(int) $old->term_taxonomy_id
				)
			);
			if ( ! is_array( $object_ids ) ) {
				continue;
			}
			foreach ( $object_ids as $object_id ) {
				$object_id = (int) $object_id;
				$post_type = get_post_type( $object_id );
				if ( ! in_array( $post_type, $allowed_types, true ) ) {
					continue;
				}
				wp_set_object_terms( $object_id, array( $dest_id ), 'cities', true );
			}
		}

		$country_rows = $wpdb->get_results(
			"SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key = 'country'"
		);
		if ( is_array( $country_rows ) ) {
			foreach ( $country_rows as $row ) {
				$val = maybe_unserialize( $row->meta_value );
				if ( ! is_array( $val ) ) {
					continue;
				}
				$changed = false;
				$new     = array();
				foreach ( $val as $key => $id ) {
					$mapped = isset( $map[ (int) $id ] ) ? (string) $map[ (int) $id ] : $id;
					if ( (string) $mapped !== (string) $id ) {
						$changed = true;
					}
					$new[ $key ] = $mapped;
				}
				if ( $changed ) {
					update_term_meta( (int) $row->term_id, 'country', $new );
				}
			}
		}

		$list = get_option( 'country__mapItems_list' );
		if ( is_array( $list ) ) {
			$new_list = array();
			$changed  = false;
			foreach ( $list as $key => $value ) {
				$src = (int) ( is_numeric( $key ) ? $key : $value );
				if ( isset( $map[ $src ] ) ) {
					$dest           = (string) $map[ $src ];
					$new_list[ $dest ] = $dest;
					$changed        = true;
				} else {
					$new_list[ $key ] = $value;
				}
			}
			if ( $changed ) {
				update_option( 'country__mapItems_list', $new_list, false );
			}
		}

		$page_rows = $wpdb->get_results(
			"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'kit_page_city' AND meta_value <> ''"
		);
		if ( is_array( $page_rows ) ) {
			foreach ( $page_rows as $row ) {
				$old_id = (int) $row->meta_value;
				if ( isset( $map[ $old_id ] ) ) {
					update_post_meta( (int) $row->post_id, 'kit_page_city', (string) $map[ $old_id ] );
				}
			}
		}

		update_option( 'kayan_city_to_cities_map', $map, false );
		update_option( 'kayan_city_to_cities_migrated', '1', false );

		if ( get_option( 'kayan_city_cities_rewrites_flushed' ) !== '1' ) {
			flush_rewrite_rules( false );
			update_option( 'kayan_city_cities_rewrites_flushed', '1', false );
		}
	}
}
add_action( 'init', 'kayan_city_to_cities_migrate', 20 );
