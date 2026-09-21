<?php
defined( 'ABSPATH' ) || exit;
header( 'Content-Type: application/json' );
ob_start();

if ( ! kayan_ajax_verify_request() ) {
	kayan_ajax_reject();
}

$resolved = kayan_ajax_resolve_blade( isset( $_POST['blade'] ) ? $_POST['blade'] : '', isset( $_POST['shape'] ) ? $_POST['shape'] : '' );
if ( ! $resolved ) {
	kayan_ajax_reject( 'قالب غير مسموح', 400 );
}

$json = array();
$this->ThemeStatic->Blade( $resolved[0], array( '_POST' => $_POST ), $resolved[1] );

$output = ob_get_clean();

if ( strpos( $output, '<Ex___Cut___Ajax>' ) !== false ) {

	$Next_output = explode( '<Ex___Cut___Ajax>', $output )[1];
	$Next_output = explode( '</Ex___Cut___Ajax>', $Next_output )[0];
	$json['Next_output'] = $Next_output;
	echo wp_json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
}
