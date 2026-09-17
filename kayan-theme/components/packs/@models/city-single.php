<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$city_id  = (int) get_post_meta( $post->ID, 'kit_page_city', true );
$city_tax = kayan_kit_city_taxonomy();
$items    = array();

if ( $city_id ) {
	$items = kayan_kit_posts(
		array( 'post', 'services' ),
		array(
			'tax_query' => array(
				array(
					'taxonomy' => $city_tax,
					'field'    => 'term_id',
					'terms'    => array( $city_id ),
				),
			),
		)
	);
}
if ( empty( $items ) ) {
	$items = kayan_kit_posts( array( 'post', 'services' ) );
}

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="services-grid">';
	foreach ( $items as $item ) {
		kayan_kit_render_svc( $item );
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
