<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

# CPT archives (WP_Post_Type) must never read taxonomy/term_id — that fatals on PHP 8+.
if ( $obj instanceof WP_Post_Type || ! is_object( $obj ) || ! isset( $obj->taxonomy ) || ! isset( $obj->term_id ) ) {
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
	$items = ( $pt && post_type_exists( $pt ) ) ? kayan_kit_posts( array( $pt ), array( 'posts_per_page' => 48 ) ) : array();
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
	return;
}

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
