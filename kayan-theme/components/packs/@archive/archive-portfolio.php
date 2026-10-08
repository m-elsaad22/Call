<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$title = isset( $obj->label ) ? $obj->label : __( 'معرض الأعمال', 'yourcolor' );
kayan_kit_hero( $title, '' );

$items = kayan_kit_posts( array( 'portfolio', 'before_after' ), array( 'posts_per_page' => 36 ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="gal-grid">';
	foreach ( $items as $item ) {
		$thumb = get_the_post_thumbnail_url( $item->ID, 'medium_large' );
		echo '<a class="gal-item" href="' . esc_url( get_permalink( $item ) ) . '">';
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $item->post_title ) . '" loading="lazy" />';
		} else {
			echo '<div class="gal-ph"><i class="fas fa-image"></i></div>';
		}
		echo '<span class="gtag">' . esc_html( $item->post_title ) . '</span></a>';
	}
	echo '</div>';
} else {
	kayan_kit_empty();
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
