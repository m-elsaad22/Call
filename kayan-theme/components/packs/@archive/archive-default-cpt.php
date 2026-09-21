<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$title = '';
$pt    = '';
if ( $obj instanceof WP_Post_Type ) {
	$title = isset( $obj->label ) ? $obj->label : $obj->name;
	$pt    = $obj->name;
} else {
	$pt = get_query_var( 'post_type' );
	if ( is_array( $pt ) ) {
		$pt = reset( $pt );
	}
	$pto   = $pt ? get_post_type_object( $pt ) : null;
	$title = ( $pto && isset( $pto->label ) ) ? $pto->label : get_bloginfo( 'name' );
}

kayan_kit_hero( $title, '' );

$items = array();
if ( $pt && post_type_exists( $pt ) ) {
	$items = kayan_kit_posts( array( $pt ), array( 'posts_per_page' => 48 ) );
}

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
