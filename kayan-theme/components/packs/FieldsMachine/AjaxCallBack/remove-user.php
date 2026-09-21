<?php
defined( 'ABSPATH' ) || exit;
header( 'Content-Type: application/json' );
ob_start();
$json = array();

if ( ! is_user_logged_in() || ! current_user_can( 'delete_users' ) ) {
	$json['type'] = 'error';
	echo wp_json_encode( $json );
	return;
}

$nonce = isset( $Ajax__data['kayan_fm_nonce'] ) ? sanitize_text_field( wp_unslash( $Ajax__data['kayan_fm_nonce'] ) ) : '';
if ( ! $nonce || ! wp_verify_nonce( $nonce, 'kayan_fm_ajax' ) ) {
	$json['type'] = 'error';
	echo wp_json_encode( $json );
	return;
}

if ( isset( $Ajax__data['removedID'] ) ) {
	$RemoveList = array();
	$raw        = (string) $Ajax__data['removedID'];
	if ( strpos( $raw, ',' ) !== false ) {
		$RemoveList = explode( ',', $raw );
	} else {
		$RemoveList[] = $raw;
	}

	$current_id = get_current_user_id();
	foreach ( $RemoveList as $user_id ) {
		$user_id = absint( $user_id );
		if ( $user_id < 1 || $user_id === $current_id ) {
			continue;
		}
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			continue;
		}
		wp_delete_user( $user_id );
		$json['type'] = 'sucsses';
	}

	if ( empty( $json['type'] ) ) {
		$json['type'] = 'error';
	}

	if ( isset( $Ajax__data['location'] ) && $Ajax__data['location'] != 'stay' ) {
		$json['reload__page'] = esc_url_raw( $Ajax__data['location'] );
	}

} else {
	$json['type'] = 'error';
}
echo wp_json_encode( $json );
