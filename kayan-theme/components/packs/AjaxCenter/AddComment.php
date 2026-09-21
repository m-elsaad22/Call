<?php
defined( 'ABSPATH' ) || exit;
header( 'Content-type: application/json' );

if ( ! kayan_ajax_verify_request() ) {
	kayan_ajax_reject();
}

$json = array();
$post_id   = absint( $_POST['postID'] ?? 0 );
$user_name = isset( $_POST['user_name'] ) ? sanitize_text_field( wp_unslash( $_POST['user_name'] ) ) : '';
$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
$comment   = isset( $_POST['comment'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ) : '';
$parent    = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;
$rate      = isset( $_POST['rate'] ) ? absint( $_POST['rate'] ) : 0;
$customtype = isset( $_POST['customtype'] ) ? sanitize_key( wp_unslash( $_POST['customtype'] ) ) : '';

$post_obj = $post_id ? get_post( $post_id ) : null;

if ( $post_id && $user_name && $email && $post_obj && 'publish' === $post_obj->post_status && is_email( $email ) ) {
	if ( $rate && ( $rate < 1 || $rate > 5 ) ) {
		$json['error'] = true;
		echo wp_json_encode( $json );
		return;
	}

	$time = current_time( 'mysql' );

	$approve = 0;
	if ( get_option( 'default_comments_approval' ) == 'on' ) {
		$approve = 1;
	}

	$data = array(
		'comment_post_ID'      => $post_id,
		'comment_author'       => $user_name,
		'comment_author_email' => $email,
		'comment_content'      => $comment,
		'comment_date'         => $time,
		'comment_approved'     => $approve,
	);

	if ( $parent ) {
		$data['comment_parent'] = $parent;
	}

	if ( is_user_logged_in() ) {
		global $current_user;
		$data['user_id'] = $current_user->ID;
		if ( in_array( 'administrator', $current_user->roles ) || in_array( 'author', $current_user->roles ) || in_array( 'editor', $current_user->roles ) ) {
			$data['comment_approved'] = 1;
		}
	}

	$cid = wp_insert_comment( $data );
	if ( $rate ) {
		update_comment_meta( $cid, 'rating', $rate );
	}
	if ( $customtype ) {
		update_comment_meta( $cid, 'type', $customtype );
	}
	if ( $rate >= 3 ) {
		update_post_meta( $post_id, 'wp_review_comments_positive_count', (int) get_post_meta( $post_id, 'wp_review_comments_positive_count', true ) + 1 );
	} else {
		update_post_meta( $post_id, 'wp_review_comments_negative_count', (int) get_post_meta( $post_id, 'wp_review_comments_negative_count', true ) + 1 );
	}
	$json['comment_ID'] = $cid;

	$RatingData = array();

	$arguments = array(
		'status'  => 'approve',
		'post_id' => $post_id,
		'parent'  => 0,
		'order'   => 'ASC',
	);
	$comments = get_comments( $arguments );
	$max      = 0;
	foreach ( $comments as $comment_row ) {
		$rating = (int) get_comment_meta( $comment_row->comment_ID, 'rating', true );
		$max    = $max + $rating;
		if ( ! isset( $RatingData[ $rating ] ) ) {
			$RatingData[ $rating ] = 0;
		}
		$RatingData[ $rating ] = $RatingData[ $rating ] + 1;
	}
	$count = count( $comments );
	if ( $count > 0 ) {
		update_post_meta( $post_id, '_wc_average_rating', mb_substr( (string) ( $max / $count ), 0, 3 ) );
	}
	update_post_meta( $post_id, '_wc_average_data', $RatingData );

	ob_start();
	$this->ThemeStatic->Part( 'CommentItem', array( 'comment' => get_comment( $cid ), 'post' => $post_id ) );
	$json['output'] = ob_get_clean();
} else {
	$json['error'] = true;
}
echo wp_json_encode( $json );
