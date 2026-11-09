<?php
if ( ! class_exists( 'Kayan_Number_Purge' ) ) {

	/**
	 * Remove a phone number from post content and meta, including full WhatsApp URLs.
	 * Leaves prices, dates, and non-phone digits untouched.
	 */
	class Kayan_Number_Purge {

		public static function purge( $raw ) {
			if ( function_exists( 'set_time_limit' ) ) {
				@set_time_limit( 120 );
			}
			$variants = self::variants( $raw );
			if ( ! $variants ) {
				return array( 'posts' => 0, 'options' => 0, 'terms' => 0 );
			}

			$ids     = self::matching_post_ids( $variants );
			$changed = 0;
			foreach ( $ids as $post_id ) {
				if ( self::purge_post( (int) $post_id, $variants ) ) {
					$changed++;
				}
			}

			return array(
				'posts'   => $changed,
				'options' => self::purge_options( $variants ),
				'terms'   => self::purge_termmeta( $variants ),
			);
		}

		private static function variants( $raw ) {
			$base = Kayan_Number_Sync::variants( $raw );
			$e164 = Kayan_Number_Sync::canonical( $raw );
			if ( $e164 ) {
				$base[] = '+' . $e164;
				$base[] = '00' . $e164;
				$base[] = 'wa.me/' . $e164;
				$base[] = 'wa.me/+' . $e164;
			}
			$base[] = (string) $raw;
			$out    = array();
			foreach ( $base as $item ) {
				$item = trim( (string) $item );
				if ( $item !== '' ) {
					$out[] = $item;
				}
			}
			usort(
				$out,
				function ( $a, $b ) {
					return strlen( $b ) - strlen( $a );
				}
			);
			return array_values( array_unique( $out ) );
		}

		private static function matching_post_ids( $variants ) {
			global $wpdb;
			$likes = array();
			$args  = array();
			foreach ( array_slice( $variants, 0, 12 ) as $v ) {
				if ( strlen( $v ) < 8 ) {
					continue;
				}
				$likes[] = 'post_content LIKE %s';
				$args[]  = '%' . $wpdb->esc_like( $v ) . '%';
			}
			if ( ! $likes ) {
				return array();
			}
			$sql = "SELECT ID FROM {$wpdb->posts}
				WHERE post_status IN ('publish','draft','private','pending','future')
				AND post_type NOT IN ('revision','nav_menu_item','attachment')
				AND (" . implode( ' OR ', $likes ) . ')
				LIMIT 2000';
			$ids = $wpdb->get_col( Kayan_Number_Sync::prepare( $sql, $args ) );
			if ( ! is_array( $ids ) ) {
				$ids = array();
			}

			$meta_likes = array();
			$meta_args  = array();
			foreach ( array_slice( $variants, 0, 8 ) as $v ) {
				if ( strlen( $v ) < 8 ) {
					continue;
				}
				$meta_likes[] = 'meta_value LIKE %s';
				$meta_args[]  = '%' . $wpdb->esc_like( $v ) . '%';
			}
			if ( $meta_likes ) {
				$meta_ids = $wpdb->get_col(
					Kayan_Number_Sync::prepare(
						"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE " . implode( ' OR ', $meta_likes ) . ' LIMIT 2000',
						$meta_args
					)
				);
				if ( is_array( $meta_ids ) ) {
					$ids = array_merge( $ids, $meta_ids );
				}
			}

			return array_values( array_unique( array_map( 'intval', $ids ) ) );
		}

		private static function purge_options( $variants ) {
			$changed = 0;
			$keys    = array( 'phonenumber', 'whatsapp_number', 'contact_number' );
			foreach ( $keys as $key ) {
				if ( function_exists( 'yc_get_option' ) && function_exists( 'yc_update_option' ) ) {
					$val = yc_get_option( $key, '' );
					if ( is_string( $val ) && $val !== '' ) {
						$new = self::strip_text( $val, $variants );
						if ( $new !== $val ) {
							yc_update_option( $key, $new );
							$changed++;
						}
					}
				}
				$val = get_option( $key, '' );
				if ( is_string( $val ) && $val !== '' ) {
					$new = self::strip_text( $val, $variants );
					if ( $new !== $val ) {
						update_option( $key, $new );
						$changed++;
					}
				}
			}
			return $changed;
		}

		private static function purge_termmeta( $variants ) {
			global $wpdb;
			if ( empty( $wpdb->termmeta ) ) {
				return 0;
			}
			$likes = array();
			$args  = array();
			foreach ( array_slice( $variants, 0, 8 ) as $v ) {
				if ( strlen( $v ) < 8 ) {
					continue;
				}
				$likes[] = 'meta_value LIKE %s';
				$args[]  = '%' . $wpdb->esc_like( $v ) . '%';
			}
			if ( ! $likes ) {
				return 0;
			}
			$rows = $wpdb->get_results(
				Kayan_Number_Sync::prepare(
					"SELECT meta_id, meta_value FROM {$wpdb->termmeta} WHERE " . implode( ' OR ', $likes ) . ' LIMIT 500',
					$args
				),
				ARRAY_A
			);
			$changed = 0;
			if ( is_array( $rows ) ) {
				foreach ( $rows as $row ) {
					$updated = self::strip_meta_value( $row['meta_value'], $variants );
					if ( $updated !== $row['meta_value'] ) {
						$wpdb->update(
							$wpdb->termmeta,
							array( 'meta_value' => $updated ),
							array( 'meta_id' => (int) $row['meta_id'] )
						);
						$changed++;
					}
				}
			}
			return $changed;
		}

		private static function purge_post( $post_id, $variants ) {
			global $wpdb;
			$post    = get_post( $post_id );
			$changed = false;
			if ( $post ) {
				$new = self::strip_text( $post->post_content, $variants );
				if ( $new !== $post->post_content ) {
					wp_update_post(
						array(
							'ID'           => $post_id,
							'post_content' => $new,
						)
					);
					$changed = true;
				}
			}

			$keys = array(
				'whatsapp_number',
				'whatsapp',
				'rukn_wa_number',
				'phone_number',
				'phone',
				'phonenumber',
				'rukn_call_number',
				'contact_number',
				'memo-meta-phone',
				'post__call_section__data',
				'YourColor_Service',
				'yourcolor__faqs',
				'faq',
				'post__card__data',
				'post__price_list__data',
				'post__service_request__data',
				'post__work_steps__data',
				'_aie_schema',
			);
			$in   = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
			$rows = $wpdb->get_results(
				Kayan_Number_Sync::prepare(
					"SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key IN ({$in})",
					array_merge( array( $post_id ), $keys )
				),
				ARRAY_A
			);
			if ( is_array( $rows ) ) {
				foreach ( $rows as $row ) {
					$updated = self::strip_meta_value( $row['meta_value'], $variants );
					if ( $updated !== $row['meta_value'] ) {
						$wpdb->update(
							$wpdb->postmeta,
							array( 'meta_value' => $updated ),
							array( 'meta_id' => (int) $row['meta_id'] )
						);
						$changed = true;
					}
				}
			}

			if ( $changed ) {
				clean_post_cache( $post_id );
			}
			return $changed;
		}

		private static function strip_meta_value( $raw, $variants ) {
			if ( ! is_string( $raw ) || $raw === '' ) {
				return $raw;
			}
			if ( is_serialized( $raw ) ) {
				$un = @unserialize( $raw );
				if ( false === $un && $raw !== 'b:0;' ) {
					return $raw;
				}
				return serialize( self::strip_value( $un, $variants ) );
			}
			$trim = ltrim( $raw );
			if ( ( $trim !== '' ) && ( $trim[0] === '{' || $trim[0] === '[' ) ) {
				$decoded = json_decode( $raw, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					return wp_json_encode( self::strip_value( $decoded, $variants ) );
				}
			}
			return self::strip_text( $raw, $variants );
		}

		private static function strip_value( $value, $variants ) {
			if ( is_string( $value ) ) {
				return self::strip_text( $value, $variants );
			}
			if ( is_array( $value ) ) {
				$out = array();
				foreach ( $value as $k => $v ) {
					$out[ $k ] = self::strip_value( $v, $variants );
				}
				return $out;
			}
			return $value;
		}

		private static function strip_text( $content, $variants ) {
			$content = self::strip_contact_urls( $content, $variants );
			foreach ( $variants as $needle ) {
				$escaped = preg_quote( $needle, '/' );
				$content = preg_replace( '/(?<![0-9+])' . $escaped . '(?![0-9])/u', '', $content );
				if ( ! is_string( $content ) ) {
					$content = '';
				}
			}
			return self::cleanup_empty_anchors( $content );
		}

		private static function digit_keys( $variants ) {
			$keys = array();
			foreach ( $variants as $v ) {
				$digits = preg_replace( '/\D+/', '', (string) $v );
				if ( strlen( $digits ) < 8 ) {
					continue;
				}
				$keys[] = $digits;
				if ( strpos( $digits, '971' ) === 0 && strlen( $digits ) >= 11 ) {
					$local  = substr( $digits, 3 );
					$keys[] = $local;
					$keys[] = '0' . $local;
				}
			}
			$keys = array_values( array_unique( array_filter( $keys ) ) );
			usort(
				$keys,
				function ( $a, $b ) {
					return strlen( $b ) - strlen( $a );
				}
			);
			return $keys;
		}

		private static function strip_contact_urls( $content, $variants ) {
			foreach ( self::digit_keys( $variants ) as $digits ) {
				$d        = preg_quote( $digits, '/' );
				$patterns = array(
					'/<a\b[^>]*href=["\'][^"\']*(?:wa\.me\/|(?:api\.|web\.)?whatsapp\.com\/|tel:)[^"\']*' . $d . '[^"\']*["\'][^>]*>.*?<\/a>/ius',
					'/https?:\/\/(?:api\.|web\.)?whatsapp\.com\/[^\s"\'<>]*?[?&](?:amp;)?phone=(?:\+|%2B)?' . $d . '[^\s"\'<>]*/iu',
					'/https?:\/\/wa\.me\/(?:\+|%2B)?' . $d . '[^\s"\'<>]*/iu',
					'/(?<![\w\/])wa\.me\/(?:\+|%2B)?' . $d . '[^\s"\'<>]*/iu',
					'/https?:\/\/(?:www\.)?whatsapp\.com\/send[^\s"\'<>]*phone=(?:\+|%2B)?' . $d . '[^\s"\'<>]*/iu',
					'/(?:href=["\'])?tel:(?:\+|%2B)?' . $d . '(?:["\'])?/iu',
				);
				foreach ( $patterns as $pattern ) {
					$next = preg_replace( $pattern, '', $content );
					if ( is_string( $next ) ) {
						$content = $next;
					}
				}
			}
			return self::cleanup_empty_anchors( $content );
		}

		private static function cleanup_empty_anchors( $content ) {
			$next = preg_replace( '/<a\b[^>]*href=(["\'])\1[^>]*>\s*<\/a>/iu', '', $content );
			if ( is_string( $next ) ) {
				$content = $next;
			}
			$next = preg_replace( '/<a\b[^>]*>\s*<\/a>/iu', '', $content );
			return is_string( $next ) ? $next : $content;
		}
	}
}
