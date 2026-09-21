<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'kayan_ajax_nonce_action' ) ) {
	function kayan_ajax_nonce_action() {
		return 'kayan_ajax_center';
	}
}

if ( ! function_exists( 'kayan_ajax_verify_request' ) ) {
	function kayan_ajax_verify_request() {
		$nonce = '';
		if ( isset( $_POST['kayan_ajax_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['kayan_ajax_nonce'] ) );
		} elseif ( isset( $_REQUEST['kayan_ajax_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_REQUEST['kayan_ajax_nonce'] ) );
		}
		return ( '' !== $nonce && wp_verify_nonce( $nonce, kayan_ajax_nonce_action() ) );
	}
}

if ( ! function_exists( 'kayan_ajax_reject' ) ) {
	function kayan_ajax_reject( $message = 'طلب غير مصرح به', $status = 403 ) {
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}
		status_header( (int) $status );
		if ( ! headers_sent() ) {
			header( 'Content-Type: application/json; charset=utf-8' );
		}
		echo wp_json_encode(
			array(
				'error'   => true,
				'success' => false,
				'message' => $message,
			)
		);
		exit;
	}
}

if ( ! function_exists( 'kayan_ajax_allowed_blades' ) ) {
	function kayan_ajax_allowed_blades() {
		return array(
			'Popovers'  => array( 'form_services' ),
			'Mega-Menu' => array( 'post', 'taxonomy' ),
		);
	}
}

if ( ! function_exists( 'kayan_ajax_resolve_blade' ) ) {
	function kayan_ajax_resolve_blade( $pack, $file ) {
		$pack = is_string( $pack ) ? wp_unslash( $pack ) : '';
		$file = is_string( $file ) ? wp_unslash( $file ) : '';
		$pack = preg_replace( '/[^A-Za-z0-9_-]/', '', $pack );
		$file = preg_replace( '/[^A-Za-z0-9_-]/', '', $file );
		if ( '' === $pack || '' === $file ) {
			return false;
		}
		$allowed = kayan_ajax_allowed_blades();
		if ( ! isset( $allowed[ $pack ] ) || ! in_array( $file, $allowed[ $pack ], true ) ) {
			return false;
		}
		return array( $pack, $file );
	}
}

class AjaxCenter {
	function __construct() {
		$this->ThemeStatic = new ThemeStatic;
	}
	public function QueryEndpoint() {
		add_rewrite_endpoint( 'AjaxCenter', EP_ROOT );
	}
	public function AjaxCenterPage() {
		if($AjaxCenter = get_query_var('AjaxCenter')){
			$Action = explode('/', $AjaxCenter)[0];
			if( strpos( $AjaxCenter , $Action.'/') !== FALSE ){
				$Params = explode($Action.'/', $AjaxCenter)[1];
			}else{
				$Params = '';
			}
			$AjaxCenterPath = get_template_directory().'/components/packs/AjaxCenter/';
			$AjaxCenterURL = get_template_directory_uri().'/components/packs/AjaxCenter/';

			# ═══ حماية LFI: الاسم يُقيَّد بالأحرف الآمنة ويُطابق قائمة ملفات المجلد الفعلية فقط ═══
			$Action = sanitize_key( basename( (string) $Action ) );
			$Allowed = array();
			$AllowedMap = array();
			foreach ( (array) glob( $AjaxCenterPath.'*.php' ) as $Allowed_file ) {
				$base = basename( $Allowed_file, '.php' );
				$Allowed[] = $base;
				$AllowedMap[ sanitize_key( $base ) ] = $base;
			}
			if ( '' === $Action || 'setup' === $Action || ! isset( $AllowedMap[ $Action ] ) ) {
				status_header( 404 );
				die();
			}
	    	require( $AjaxCenterPath.$AllowedMap[ $Action ].'.php' );
	    	die();
	    }
	}
	public function Setup() {
		add_action( 'init', array( $this, 'QueryEndpoint' ) );
		add_action( 'BeforeHeader', array( $this, 'AjaxCenterPage' ) );
	}
}
(new AjaxCenter)->Setup();