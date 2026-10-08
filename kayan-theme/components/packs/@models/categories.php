<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$terms = kayan_kit_terms( array( 'category', 'service_categories' ), array( 'number' => 40 ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $terms ) ) {
	echo '<div class="cat-grid">';
	foreach ( $terms as $term ) {
		kayan_kit_render_catcard( $term );
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
