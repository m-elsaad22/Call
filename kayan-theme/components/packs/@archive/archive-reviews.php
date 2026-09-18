<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$title = isset( $obj->label ) ? $obj->label : __( 'التقييمات', 'yourcolor' );
kayan_kit_hero( $title, '' );

$items = kayan_kit_posts( array( 'reviews', 'testimonials' ), array( 'posts_per_page' => 48 ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="review-grid">';
	foreach ( $items as $review ) {
		$rating      = (int) get_post_meta( $review->ID, 'rating', true );
		$rating      = ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
		$client_name = get_post_meta( $review->ID, 'client_name', true );
		$client_name = $client_name ? $client_name : $review->post_title;
		$initial     = mb_substr( $client_name, 0, 1, 'UTF-8' );
		echo '<a class="rvcard2" href="' . esc_url( get_permalink( $review ) ) . '">';
		echo '<div class="gtop"><span class="rstars">' . kayan_kit_stars( $rating ) . '</span></div>';
		echo '<p class="txt">' . esc_html( kayan_kit_plain( $review->post_content, 180 ) ) . '</p>';
		echo '<div class="rclient"><span class="rav">' . esc_html( $initial ) . '</span><div><b>' . esc_html( $client_name ) . '</b></div></div>';
		echo '</a>';
	}
	echo '</div>';
} else {
	kayan_kit_empty();
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
