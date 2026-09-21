<?php
defined( 'ABSPATH' ) || exit;
ob_start();
header( 'Content-Type: application/json' );

if ( ! kayan_ajax_verify_request() ) {
	kayan_ajax_reject();
}

$json        = array();
$object_id   = absint( $_POST['id'] ?? 0 );
$type        = isset( $_POST['Type'] ) ? sanitize_key( wp_unslash( $_POST['Type'] ) ) : '';
$rate_value  = absint( $_POST['RateValue'] ?? 0 );
$last_value  = isset( $_POST['LastValueRate'] ) ? absint( $_POST['LastValueRate'] ) : 0;

if ( $object_id < 1 || $rate_value < 1 || $rate_value > 5 ) {
	kayan_ajax_reject( 'تقييم غير صالح', 400 );
}
if ( $last_value && ( $last_value < 1 || $last_value > 5 ) ) {
	$last_value = 0;
}

if ( 'taxonomy' === $type ) {

	$term = get_term( $object_id );
	if ( ! $term || is_wp_error( $term ) ) {
		kayan_ajax_reject( 'التصنيف غير موجود', 400 );
	}

	$RatingValue_v1  = (int) get_term_meta( $object_id, 'RatingValue_v1', true );
	$RateUserCount_v1 = (int) get_term_meta( $object_id, 'RateUserCount_v1', true );

	$RatingData = get_term_meta( $object_id, 'RateUsersData_v1', true );
	$RatingData = is_array( $RatingData ) ? $RatingData : array();

	for ( $i = 1; $i < 6; $i++ ) {
		if ( ! isset( $RatingData[ $i ] ) ) {
			$RatingData[ $i ] = 0;
		}
	}

	if ( $last_value && $RatingData[ $last_value ] > 0 ) {
		$RatingData[ $last_value ] = $RatingData[ $last_value ] - 1;
	}

	$RatingData[ $rate_value ] = $RatingData[ $rate_value ] + 1;

	update_term_meta( $object_id, 'RateUsersData_v1', $RatingData );

	if ( ! $last_value ) {
		$RateUserCount_v1 = $RateUserCount_v1 + 1;
		update_term_meta( $object_id, 'RateUserCount_v1', $RateUserCount_v1 );
	}
	$RatingValue_v1 = ( ( $RatingValue_v1 > $last_value ) ) ? $RatingValue_v1 - $last_value : 0;
	$RatingValue_v1 = $RatingValue_v1 + $rate_value;
	update_term_meta( $object_id, 'RatingValue_v1', $RatingValue_v1 );

	for ( $q = 5; $q >= 1; $q-- ) {
		if ( isset( $RatingData[ $q ] ) ) {
			$AverageCalc = $RateUserCount_v1 > 0 ? ( $RatingData[ $q ] * 100 / $RateUserCount_v1 ) : 0;
			echo '<div class="-Rate-Average-element">';
				echo '<em>' . (int) $q . '</em>';
				echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="' . esc_attr( $AverageCalc ) . '"></div></div>';
				echo '<span>' . (int) $RatingData[ $q ] . '</span>';
			echo '</div>';
		}
	}

	$HTML = ob_get_clean();
	$json['output'] = $HTML;

	$json['RateUserCount_v1'] = $RateUserCount_v1;
	$json['RatingValue_v1']   = $RatingValue_v1;

	$UsersTotalRate = $RateUserCount_v1 * 5;
	$TotalValue     = $UsersTotalRate > 0 ? ( $RatingValue_v1 * 5 / $UsersTotalRate ) : 0;

	update_term_meta( $object_id, 'TotalRate_v1', $TotalValue );

	$json['TotalValue'] = $TotalValue;

} else {
	$post = get_post( $object_id );

	if ( ! $post || 'publish' !== $post->post_status ) {
		kayan_ajax_reject( 'المنشور غير موجود', 400 );
	}

	$RatingValue_v1   = (int) get_post_meta( $object_id, 'RatingValue_v1', true );
	$RateUserCount_v1 = (int) get_post_meta( $object_id, 'RateUserCount_v1', true );

	$RatingData = get_post_meta( $object_id, 'RateUsersData_v1', true );
	$RatingData = is_array( $RatingData ) ? $RatingData : array();

	for ( $i = 1; $i < 6; $i++ ) {
		if ( ! isset( $RatingData[ $i ] ) ) {
			$RatingData[ $i ] = 0;
		}
	}

	if ( $last_value && $RatingData[ $last_value ] > 0 ) {
		$RatingData[ $last_value ] = $RatingData[ $last_value ] - 1;
	}

	$RatingData[ $rate_value ] = $RatingData[ $rate_value ] + 1;

	update_post_meta( $object_id, 'RateUsersData_v1', $RatingData );

	if ( ! $last_value ) {
		$RateUserCount_v1 = $RateUserCount_v1 + 1;
		update_post_meta( $object_id, 'RateUserCount_v1', $RateUserCount_v1 );
	}
	$RatingValue_v1 = ( ( $RatingValue_v1 > $last_value ) ) ? $RatingValue_v1 - $last_value : 0;
	$RatingValue_v1 = $RatingValue_v1 + $rate_value;
	update_post_meta( $object_id, 'RatingValue_v1', $RatingValue_v1 );

	for ( $q = 5; $q >= 1; $q-- ) {
		if ( isset( $RatingData[ $q ] ) ) {
			$AverageCalc = $RateUserCount_v1 > 0 ? ( $RatingData[ $q ] * 100 / $RateUserCount_v1 ) : 0;
			$AverageCalc = round( $AverageCalc, 1 );
			echo '<div class="-Rate-Average-element">';
				echo '<em>' . (int) $q . '</em>';
				echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="' . esc_attr( $AverageCalc ) . '"></div></div>';
				echo '<span>' . esc_html( $AverageCalc ) . '%</span>';
			echo '</div>';
		}
	}

	$HTML = ob_get_clean();
	$json['output'] = $HTML;

	$json['RateUserCount_v1'] = $RateUserCount_v1;
	$json['RatingValue_v1']   = $RatingValue_v1;

	$UsersTotalRate = $RateUserCount_v1 * 5;
	$TotalValue     = $UsersTotalRate > 0 ? ( $RatingValue_v1 * 5 / $UsersTotalRate ) : 0;
	$TotalValue     = round( $TotalValue, 1 );
	update_post_meta( $object_id, 'TotalRate_v1', $TotalValue );

	$json['TotalValue'] = $TotalValue;
}
echo wp_json_encode( $json );
