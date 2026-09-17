<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$subtitle = isset( $obj->description ) ? $obj->description : '';
kayan_kit_hero( $obj->name, $subtitle, $obj->name );

$items = kayan_kit_posts(
	array( 'post', 'services' ),
	array(
		'tax_query' => array(
			array(
				'taxonomy' => $obj->taxonomy,
				'field'    => 'term_id',
				'terms'    => array( $obj->term_id ),
			),
		),
	)
);

echo '<section class="sec">';
echo '<div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="services-grid">';
	foreach ( $items as $item ) {
		kayan_kit_render_svc( $item );
	}
	echo '</div>';
} else {
	kayan_kit_empty( __( 'لا توجد عناصر في هذه المدينة', 'yourcolor' ) );
}
echo '</div>';
echo '</section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
