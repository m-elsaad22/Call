<?php
defined( 'ABSPATH' ) || exit;
header( 'Content-Type: application/json' );
ob_start();

if ( ! kayan_ajax_verify_request() ) {
	kayan_ajax_reject();
}

$json = array();
$raw  = isset( $_POST['Arguments'] ) ? wp_unslash( $_POST['Arguments'] ) : '';
$args = json_decode( base64_decode( $raw ), true );

if ( ! is_array( $args ) ) {
	kayan_ajax_reject( 'بيانات غير صالحة', 400 );
}

$_POST['Arguments'] = $args;
$json['Arguments']  = $args;

$pack = isset( $args['blade'] ) ? $args['blade'] : 'Popovers';
$file = isset( $args['ActionBlade'] ) ? $args['ActionBlade'] : '';
$resolved = kayan_ajax_resolve_blade( $pack, $file );
if ( ! $resolved ) {
	kayan_ajax_reject( 'قالب غير مسموح', 400 );
}

$this->ThemeStatic->Blade( $resolved[0], $args, $resolved[1] );

$output = ob_get_clean();
$json['output'] = $output;

echo wp_json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
