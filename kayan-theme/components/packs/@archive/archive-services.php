<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$title = isset( $obj->label ) ? $obj->label : __( 'الخدمات', 'yourcolor' );
kayan_kit_hero( $title, '' );

$items = kayan_kit_posts( array( 'services' ), array( 'posts_per_page' => 48 ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="services-grid">';
	foreach ( $items as $item ) {
		kayan_kit_render_svc( $item );
	}
	echo '</div>';
} else {
	kayan_kit_empty();
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
