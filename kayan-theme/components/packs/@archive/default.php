<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$subtitle = isset( $obj->description ) ? $obj->description : '';
kayan_kit_hero( $obj->name, $subtitle, $obj->name );

$children = array();
if ( isset( $obj->taxonomy ) && is_taxonomy_hierarchical( $obj->taxonomy ) ) {
	$children = get_terms(
		array(
			'taxonomy'   => $obj->taxonomy,
			'parent'     => $obj->term_id,
			'hide_empty' => false,
			'number'     => 40,
		)
	);
	if ( is_wp_error( $children ) ) {
		$children = array();
	}
}

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

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $children ) ) {
	echo '<div class="cat-grid">';
	foreach ( $children as $term ) {
		kayan_kit_render_catcard( $term );
	}
	echo '</div>';
}
if ( ! empty( $items ) ) {
	$is_blog = ( 'category' === $obj->taxonomy );
	echo '<div class="' . ( $is_blog && empty( $children ) ? 'blog-grid' : 'services-grid' ) . '" style="margin-top:' . ( ! empty( $children ) ? '40px' : '0' ) . '">';
	foreach ( $items as $item ) {
		if ( $is_blog && empty( $children ) ) {
			kayan_kit_render_bcard( $item );
		} else {
			kayan_kit_render_svc( $item );
		}
	}
	echo '</div>';
}
if ( empty( $children ) && empty( $items ) ) {
	kayan_kit_empty();
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
