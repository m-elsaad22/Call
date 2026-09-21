<?php
defined( 'ABSPATH' ) || exit;
header( 'Content-Type: application/json' );
ob_start();

if ( ! kayan_ajax_verify_request() ) {
	kayan_ajax_reject();
}

$json = array();
$raw  = isset( $_POST['args'] ) ? wp_unslash( $_POST['args'] ) : '';
$args = json_decode( base64_decode( $raw ), true );

if ( ! is_array( $args ) ) {
	kayan_ajax_reject( 'بيانات غير صالحة', 400 );
}

$_POST['args'] = $args;
$json['kk']    = $args;

$file     = isset( $args['Blade_ID'] ) ? $args['Blade_ID'] : '';
$resolved = kayan_ajax_resolve_blade( 'Mega-Menu', $file );
if ( ! $resolved ) {
	kayan_ajax_reject( 'قالب غير مسموح', 400 );
}

$this->ThemeStatic->Blade( $resolved[0], $args, $resolved[1] );
$html = ob_get_clean();
$json['output'] = $html;

echo wp_json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
