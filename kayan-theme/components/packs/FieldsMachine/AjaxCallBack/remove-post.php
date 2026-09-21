<?php
defined( 'ABSPATH' ) || exit;
header( 'Content-Type: application/json' );
ob_start();
$json = array();

if ( ! is_user_logged_in() || ! current_user_can( 'delete_posts' ) ) {
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
	foreach ( $RemoveList as $post_id ) {
		$post_id = absint( $post_id );
		if ( $post_id < 1 ) {
			continue;
		}
		$post = get_post( $post_id );
		if ( isset( $post->ID ) && current_user_can( 'delete_post', $post->ID ) ) {
			delete_post_meta( $post->ID, 'video_id' );
			delete_post_meta( $post->ID, 'character_id' );
			wp_delete_post( $post->ID );
			$json['type'] = 'sucsses';
		}
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
