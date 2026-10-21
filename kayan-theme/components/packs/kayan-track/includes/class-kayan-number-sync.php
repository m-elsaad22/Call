<?php
if ( ! class_exists( 'Kayan_Number_Sync' ) ) {

	class Kayan_Number_Sync {

		const IGNORE_OPTION = 'kayan_track_ignored_numbers';

		public static function canonical( $raw ) {
			$digits = preg_replace( '/\D+/', '', (string) $raw );
			if ( $digits === '' ) {
				return '';
			}
			if ( strpos( $digits, '00' ) === 0 ) {
				$digits = substr( $digits, 2 );
			}
			if ( isset( $digits[0] ) && $digits[0] === '0' && ( strlen( $digits ) === 9 || strlen( $digits ) === 10 ) ) {
				$digits = '971' . substr( $digits, 1 );
			}
			if ( strlen( $digits ) === 9 && $digits[0] === '5' ) {
				$digits = '971' . $digits;
			}
			if ( strlen( $digits ) < 8 || strlen( $digits ) > 15 ) {
				return '';
			}
			return $digits;
		}

		public static function match_key( $raw ) {
			$canonical = self::canonical( $raw );
			if ( $canonical === '' ) {
				return '';
			}
			if ( strpos( $canonical, '971' ) === 0 && strlen( $canonical ) > 5 ) {
				return substr( $canonical, 3 );
			}
			return $canonical;
		}

		public static function variants( $raw ) {
			$canonical = self::canonical( $raw );
			if ( $canonical === '' ) {
				return array();
			}
			$out = array( $canonical );
			if ( strpos( $canonical, '971' ) === 0 ) {
				$local = '0' . substr( $canonical, 3 );
				$out[] = $local;
				$out[] = substr( $canonical, 3 );
				$out[] = '00' . $canonical;
			}
			return array_values( array_unique( array_filter( $out ) ) );
		}

		public static function display( $raw ) {
			$canonical = self::canonical( $raw );
			if ( $canonical === '' ) {
				return '';
			}
			return '+' . $canonical;
		}

		public static function ignored_keys() {
			$stored = kayan_track_get_option( self::IGNORE_OPTION, array() );
			return is_array( $stored ) ? array_values( array_filter( array_map( 'strval', $stored ) ) ) : array();
		}

		public static function ignore( $raw ) {
			$key = self::match_key( $raw );
			if ( $key === '' ) {
				return;
			}
			$keys   = self::ignored_keys();
			$keys[] = $key;
			kayan_track_update_option( self::IGNORE_OPTION, array_values( array_unique( $keys ) ) );
		}

		public static function unignore( $raw ) {
			$key = self::match_key( $raw );
			if ( $key === '' ) {
				return;
			}
			$keys = array_values(
				array_filter(
					self::ignored_keys(),
					function ( $item ) use ( $key ) {
						return (string) $item !== (string) $key;
					}
				)
			);
			kayan_track_update_option( self::IGNORE_OPTION, $keys );
		}

		public static function clear_ignored() {
			kayan_track_update_option( self::IGNORE_OPTION, array() );
		}

		public static function sync() {
			global $wpdb;
			$table   = kayan_track_table( 'numbers' );
			$ignored = array_flip( self::ignored_keys() );
			$found   = self::discover();
			$added   = 0;

			$existing = $wpdb->get_results( "SELECT id, phone, wa_number FROM {$table}", ARRAY_A );
			$by_key   = array();
			foreach ( $existing as $row ) {
				foreach ( array( $row['phone'], $row['wa_number'] ) as $value ) {
					$key = self::match_key( $value );
					if ( $key !== '' && ! isset( $by_key[ $key ] ) ) {
						$by_key[ $key ] = (int) $row['id'];
					}
				}
			}

			foreach ( $found as $item ) {
				$key = $item['key'];
				if ( $key === '' || isset( $ignored[ $key ] ) || isset( $by_key[ $key ] ) ) {
					continue;
				}
				$wpdb->insert(
					$table,
					array(
						'label'       => '',
						'phone'       => $item['phone'],
						'wa_number'   => $item['phone'],
						'color'       => '#0056b3',
						'description' => $item['source'],
						'active'      => 1,
						'created_at'  => current_time( 'mysql' ),
					)
				);
				$id = (int) $wpdb->insert_id;
				if ( $id ) {
					$by_key[ $key ] = $id;
					$added++;
					self::attach_clicks( $id, $item['phone'] );
				}
			}

			foreach ( $by_key as $key => $id ) {
				$phone = '';
				foreach ( $found as $item ) {
					if ( $item['key'] === $key ) {
						$phone = $item['phone'];
						break;
					}
				}
				if ( $phone === '' ) {
					$row = $wpdb->get_row( $wpdb->prepare( "SELECT phone, wa_number FROM {$table} WHERE id = %d", $id ), ARRAY_A );
					$phone = $row ? ( $row['phone'] ? $row['phone'] : $row['wa_number'] ) : '';
				}
				if ( $phone !== '' ) {
					self::attach_clicks( (int) $id, $phone );
				}
			}

			return array(
				'found'   => count( $found ),
				'added'   => $added,
				'ignored' => count( $ignored ),
			);
		}

		public static function attach_clicks( $number_id, $raw ) {
			global $wpdb;
			$number_id = (int) $number_id;
			$variants  = self::variants( $raw );
			if ( ! $number_id || ! $variants ) {
				return;
			}
			$table        = kayan_track_table( 'conversions' );
			$placeholders = implode( ',', array_fill( 0, count( $variants ), '%s' ) );
			$args         = array_merge( array( $number_id ), $variants, array( $number_id ) );
			$wpdb->query(
				self::prepare(
					"UPDATE {$table} SET number_id = %d WHERE phone_raw IN ({$placeholders}) AND (number_id IS NULL OR number_id = 0 OR number_id = %d)",
					$args
				)
			);
		}

		public static function prepare( $sql, $args ) {
			global $wpdb;
			if ( ! $args ) {
				return $sql;
			}
			return call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $args ) );
		}

		public static function click_match_sql( $number_id, $raw ) {
			$variants = self::variants( $raw );
			$args     = array( (int) $number_id );
			$sql      = 'number_id = %d';
			if ( $variants ) {
				$placeholders = implode( ',', array_fill( 0, count( $variants ), '%s' ) );
				$sql         .= " OR phone_raw IN ({$placeholders})";
				$args         = array_merge( $args, $variants );
			}
			return array(
				'sql'  => '(' . $sql . ')',
				'args' => $args,
			);
		}

		private static function discover() {
			global $wpdb;
			$bucket = array();

			self::absorb_value( $bucket, self::option_value( 'phonenumber' ), 'إعدادات القالب: رقم التواصل' );
			self::absorb_value( $bucket, self::option_value( 'whatsapp_number' ), 'إعدادات القالب: رقم الواتساب' );
			self::absorb_value( $bucket, self::option_value( 'contact_number' ), 'إعدادات القالب: رقم الاتصال' );

			$meta_keys = array(
				'whatsapp_number',
				'phone_number',
				'phonenumber',
				'rukn_call_number',
				'rukn_wa_number',
				'contact_number',
				'memo-meta-phone',
			);
			$in        = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );
			$meta_rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT pm.meta_value, pm.post_id, p.post_title
					FROM {$wpdb->postmeta} pm
					LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key IN ({$in}) AND pm.meta_value <> ''
					LIMIT 1500",
					$meta_keys
				),
				ARRAY_A
			);
			foreach ( $meta_rows as $row ) {
				$title  = isset( $row['post_title'] ) ? $row['post_title'] : '';
				$source = $title !== '' ? 'مقال: ' . $title : 'حقل مقال';
				self::absorb_text( $bucket, $row['meta_value'], $source, true );
			}

			if ( ! empty( $wpdb->termmeta ) ) {
				$term_rows = $wpdb->get_results(
					"SELECT tm.meta_value, t.name
					FROM {$wpdb->termmeta} tm
					LEFT JOIN {$wpdb->terms} t ON t.term_id = tm.term_id
					WHERE tm.meta_key IN ('rukn_call_number','rukn_wa_number','whatsapp_number','phone_number')
					AND tm.meta_value <> ''
					LIMIT 500",
					ARRAY_A
				);
				foreach ( $term_rows as $row ) {
					$name = isset( $row['name'] ) ? $row['name'] : '';
					self::absorb_text( $bucket, $row['meta_value'], $name !== '' ? 'تصنيف: ' . $name : 'تصنيف', true );
				}
			}

			$posts = $wpdb->get_results(
				"SELECT ID, post_title, post_content FROM {$wpdb->posts}
				WHERE post_status = 'publish'
				AND post_type NOT IN ('revision','nav_menu_item','attachment')
				AND (post_content LIKE '%tel:%' OR post_content LIKE '%wa.me%' OR post_content LIKE '%whatsapp.com%' OR post_content LIKE '%api.whatsapp%')
				LIMIT 800",
				ARRAY_A
			);
			foreach ( $posts as $post ) {
				$title = $post['post_title'] !== '' ? 'صفحة: ' . $post['post_title'] : 'محتوى منشور';
				self::absorb_text( $bucket, $post['post_content'], $title, false );
			}

			$options = $wpdb->get_results(
				"SELECT option_name, option_value FROM {$wpdb->options}
				WHERE option_name NOT LIKE '\\_transient%'
				AND option_name NOT LIKE '\\_site\\_transient%'
				AND (option_value LIKE '%tel:%' OR option_value LIKE '%wa.me/%' OR option_value LIKE '%whatsapp.com%')
				LIMIT 200",
				ARRAY_A
			);
			foreach ( $options as $option ) {
				self::absorb_text( $bucket, $option['option_value'], 'إعداد: ' . $option['option_name'], false );
			}

			$clicked = $wpdb->get_col(
				'SELECT DISTINCT phone_raw FROM ' . kayan_track_table( 'conversions' ) . " WHERE phone_raw <> '' AND click_type IN ('call','whatsapp') LIMIT 300"
			);
			foreach ( $clicked as $phone ) {
				self::absorb_value( $bucket, $phone, 'من ضغطات سابقة على الموقع' );
			}

			return array_values( $bucket );
		}

		private static function option_value( $key ) {
			if ( function_exists( 'yc_get_option' ) ) {
				$value = yc_get_option( $key, '' );
				if ( is_string( $value ) && $value !== '' ) {
					return $value;
				}
			}
			$value = get_option( $key, '' );
			return is_string( $value ) ? $value : '';
		}

		private static function absorb_value( &$bucket, $raw, $source ) {
			self::remember( $bucket, $raw, $source );
		}

		private static function absorb_text( &$bucket, $text, $source, $allow_bare ) {
			if ( ! is_string( $text ) || $text === '' ) {
				return;
			}
			if ( strlen( $text ) > 400000 ) {
				$text = substr( $text, 0, 400000 );
			}
			if ( preg_match_all( '/tel:\s*\+?([0-9][0-9\s\-\(\)]{6,20})/i', $text, $matches ) ) {
				foreach ( $matches[1] as $raw ) {
					self::remember( $bucket, $raw, $source );
				}
			}
			if ( preg_match_all( '/wa\.me\/\+?([0-9]{8,15})/i', $text, $matches ) ) {
				foreach ( $matches[1] as $raw ) {
					self::remember( $bucket, $raw, $source );
				}
			}
			if ( preg_match_all( '/[?&]phone=([0-9+]{8,16})/i', $text, $matches ) ) {
				foreach ( $matches[1] as $raw ) {
					self::remember( $bucket, $raw, $source );
				}
			}
			if ( $allow_bare ) {
				self::remember( $bucket, $text, $source );
			}
		}

		private static function remember( &$bucket, $raw, $source ) {
			$phone = self::canonical( $raw );
			$key   = self::match_key( $phone );
			if ( $key === '' ) {
				return;
			}
			if ( ! isset( $bucket[ $key ] ) ) {
				$bucket[ $key ] = array(
					'key'    => $key,
					'phone'  => $phone,
					'source' => mb_substr( wp_strip_all_tags( $source ), 0, 180 ),
				);
				return;
			}
			$current = $bucket[ $key ]['source'];
			if ( strpos( $current, 'ضغطات سابقة' ) !== false && strpos( $source, 'ضغطات سابقة' ) === false ) {
				$bucket[ $key ]['source'] = mb_substr( wp_strip_all_tags( $source ), 0, 180 );
			}
		}
	}
}
