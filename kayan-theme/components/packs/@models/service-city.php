<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$city_id = (int) get_post_meta( $post->ID, 'kit_page_city', true );
$cat_id  = (int) get_post_meta( $post->ID, 'kit_page_category', true );
$tax_query = array();
if ( $city_id ) {
	$tax_query[] = array(
		'taxonomy' => taxonomy_exists( 'city' ) ? 'city' : 'cities',
		'field'    => 'term_id',
		'terms'    => array( $city_id ),
	);
}
if ( $cat_id ) {
	$tax_query[] = array(
		'taxonomy' => 'category',
		'field'    => 'term_id',
		'terms'    => array( $cat_id ),
	);
}

$query_args = array(
	'post_type'      => array( 'post', 'services' ),
	'posts_per_page' => 24,
	'post_status'    => 'publish',
	'post__not_in'   => array( $post->ID ),
);
if ( count( $tax_query ) > 1 ) {
	$tax_query['relation'] = 'AND';
}
if ( ! empty( $tax_query ) ) {
	$query_args['tax_query'] = $tax_query;
}

$items = get_posts( $query_args );
if ( empty( $items ) ) {
	$items = kayan_kit_posts( array( 'post', 'services' ) );
}

echo '<section class="sec">';
echo '<div class="wrap">';
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
echo '</div>';
echo '</section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
