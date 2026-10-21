<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$items = kayan_kit_posts( array( 'testimonials', 'reviews' ), array( 'posts_per_page' => 48 ) );

$scores = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
$sum    = 0;
$count  = 0;
foreach ( $items as $review ) {
	$rating = (int) get_post_meta( $review->ID, 'rating', true );
	if ( $rating < 1 || $rating > 5 ) {
		$rating = 5;
	}
	$scores[ $rating ]++;
	$sum += $rating;
	$count++;
}
$avg = $count ? round( $sum / $count, 1 ) : 0;

echo '<section class="sec"><div class="wrap">';
if ( $count ) {
	echo '<div class="rev-summary">';
	echo '<div class="rev-score"><div class="big">' . esc_html( $avg ) . '</div>';
	echo '<div class="stars">' . kayan_kit_stars( (int) round( $avg ) ) . '</div>';
	echo '<small>' . esc_html( sprintf( __( 'من %s تقييم', 'yourcolor' ), number_format_i18n( $count ) ) ) . '</small></div>';
	echo '<div class="rev-bars">';
	for ( $s = 5; $s >= 1; $s-- ) {
		$pct = $count ? round( ( $scores[ $s ] / $count ) * 100 ) : 0;
		echo '<div class="rbar-row"><span>' . esc_html( $s ) . '</span><div class="track"><i style="width:' . esc_attr( $pct ) . '%"></i></div><span>' . esc_html( $pct ) . '%</span></div>';
	}
	echo '</div></div>';
	echo '<div class="review-grid">';
	foreach ( $items as $review ) {
		$rating      = (int) get_post_meta( $review->ID, 'rating', true );
		$rating      = ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
		$client_name = get_post_meta( $review->ID, 'client_name', true );
		$client_name = $client_name ? $client_name : $review->post_title;
		$client_city = get_post_meta( $review->ID, 'client_city', true );
		$initial     = mb_substr( $client_name, 0, 1, 'UTF-8' );
		echo '<div class="rvcard2">';
		echo '<div class="gtop"><span class="gver"><i class="fas fa-star"></i> ' . esc_html__( 'تقييم', 'yourcolor' ) . '</span><span class="rstars">' . kayan_kit_stars( $rating ) . '</span></div>';
		echo '<p class="txt">' . esc_html( kayan_kit_plain( $review->post_content, 220 ) ) . '</p>';
		echo '<div class="rclient"><span class="rav">' . esc_html( $initial ) . '</span><div><b>' . esc_html( $client_name ) . '</b>';
		if ( $client_city ) {
			echo '<small>' . esc_html( $client_city ) . '</small>';
		}
		echo '</div></div></div>';
	}
	echo '</div>';
} else {
	ob_start();
	the_content();
	$body = ob_get_clean();
	if ( trim( wp_strip_all_tags( $body ) ) !== '' ) {
		echo '<div class="article-body prose">' . $body . '</div>';
	} else {
		kayan_kit_empty();
	}
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
