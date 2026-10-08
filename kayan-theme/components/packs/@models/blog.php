<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$cats  = kayan_kit_terms( array( 'category' ), array( 'number' => 12, 'hide_empty' => true ) );
$items = kayan_kit_posts( array( 'post' ), array( 'posts_per_page' => 24 ) );

echo '<section class="sec"><div class="wrap">';
echo '<div class="toolbar">';
echo '<form class="search-box" method="get" action="' . esc_url( home_url( '/' ) ) . '">';
echo '<i class="fas fa-search"></i><input type="search" name="s" placeholder="' . esc_attr__( 'ابحث في المقالات...', 'yourcolor' ) . '" />';
echo '</form>';
if ( ! empty( $cats ) ) {
	echo '<div class="pillbar">';
	echo '<a class="active" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html__( 'جميع المقالات', 'yourcolor' ) . '</a>';
	foreach ( $cats as $term ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			echo '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
		}
	}
	echo '</div>';
}
echo '</div>';

if ( ! empty( $items ) ) {
	echo '<div class="blog-grid">';
	$first = true;
	foreach ( $items as $item ) {
		kayan_kit_render_bcard( $item, $first );
		$first = false;
	}
	echo '</div>';
} else {
	kayan_kit_empty();
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
