<?php
$search_query = trim( (string) get_search_query() );
$Styles       = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero(
	sprintf( __( 'نتائج البحث عن %s', 'yourcolor' ), $search_query ),
	''
);

$items = array();
if ( $search_query !== '' ) {
	$items = get_posts(
		array(
			'post_type'      => array( 'post', 'page', 'services' ),
			'posts_per_page' => 28,
			'post_status'    => 'publish',
			's'              => $search_query,
		)
	);
}

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="blog-grid">';
	foreach ( $items as $item ) {
		kayan_kit_render_bcard( $item );
	}
	echo '</div>';
} else {
	kayan_kit_empty( sprintf( __( 'لم يتم العثور على "%s"', 'yourcolor' ), esc_html( $search_query ) ) );
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
