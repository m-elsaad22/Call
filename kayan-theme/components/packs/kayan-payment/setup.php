<?php
defined( 'ABSPATH' ) || exit;
/**
 * kayan-payment — بوابة دفع تجريبية (v1.0.0 — Phase 2)
 * ════════════════════════════════════════════════════════════════
 * Pack مستقل، بنفس نمط kayan-booking. Demo Gateway فقط — لا تكامل
 * حقيقي مع أي بنك أو معالج دفع. الهدف: تجربة مستخدم كاملة تحاكي
 * تدفق الدفع الحقيقي (بطاقة + OTP / محفظة رقمية / دفع عند الاستلام)
 * ثم إصدار فاتورة برقم مرجعي و QR.
 * ════════════════════════════════════════════════════════════════
 */

if ( ! class_exists( 'Kayan_Payment' ) ) {

	class Kayan_Payment {

		const DEMO_OTP = '123456'; // unused — demo bypass disabled in Phase 1

		public static function option( $key, $default = '' ) {
			$val = get_option( $key );
			return ( $val === false || $val === '' ) ? $default : $val;
		}

		public static function enabled_methods() {
			$methods = get_option( 'kayan_payment_methods', array( 'card', 'wallet', 'cash' ) );
			return is_array( $methods ) ? $methods : array( 'card', 'wallet', 'cash' );
		}

		public static function invoice_company_name() {
			return self::option( 'kayan_invoice_company_name', get_bloginfo( 'name' ) );
		}

		public static function otp_transient_key( $txn_ref ) {
			return 'kayan_otp_' . md5( (string) $txn_ref );
		}

		public static function generate_otp() {
			return str_pad( (string) random_int( 0, 999999 ), 6, '0', STR_PAD_LEFT );
		}

		public static function store_otp( $txn_ref, $otp ) {
			return set_transient( self::otp_transient_key( $txn_ref ), wp_hash_password( (string) $otp ), 10 * MINUTE_IN_SECONDS );
		}

		public static function verify_stored_otp( $txn_ref, $otp ) {
			$hash = get_transient( self::otp_transient_key( $txn_ref ) );
			if ( ! is_string( $hash ) || '' === $hash ) {
				return false;
			}
			return wp_check_password( (string) $otp, $hash );
		}

		public static function consume_otp( $txn_ref ) {
			delete_transient( self::otp_transient_key( $txn_ref ) );
		}
			global $wpdb;
			$table = $wpdb->prefix . 'kayan_payments';
			do {
				$ref = 'TXN-' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 8 ) );
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE txn_ref = %s", $ref ) );
			} while ( $exists );
			return $ref;
		}

		public static function generate_invoice_number() {
			global $wpdb;
			$table  = $wpdb->prefix . 'kayan_payments';
			$prefix = self::option( 'kayan_invoice_prefix', 'INV' );
			do {
				$num = $prefix . '-' . date( 'Ymd' ) . '-' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 4 ) );
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE invoice_number = %s", $num ) );
			} while ( $exists );
			return $num;
		}

		public static function card_brand( $number ) {
			$number = preg_replace( '/\D/', '', (string) $number );
			if ( '' === $number ) return '';
			if ( 0 === strpos( $number, '4' ) ) return 'Visa';
			if ( preg_match( '/^5[1-5]/', $number ) ) return 'Mastercard';
			if ( preg_match( '/^3[47]/', $number ) ) return 'Amex';
			return 'Card';
		}

		public static function get_booking( $booking_id ) {
			global $wpdb;
			$table = $wpdb->prefix . 'kayan_bookings';
			return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", absint( $booking_id ) ) );
		}

		public static function log_activity( $booking_id, $status, $note ) {
			global $wpdb;
			$wpdb->insert(
				$wpdb->prefix . 'kayan_booking_activity',
				array(
					'booking_id' => $booking_id,
					'status'     => $status,
					'note'       => $note,
					'actor'      => 'payment_gateway',
					'created_at' => current_time( 'mysql' ),
				),
				array( '%d', '%s', '%s', '%s', '%s' )
			);
		}
	}
}
