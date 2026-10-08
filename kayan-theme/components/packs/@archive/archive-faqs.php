<?php
$obj    = get_queried_object();
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$title = isset( $obj->label ) ? $obj->label : __( 'الأسئلة الشائعة', 'yourcolor' );
kayan_kit_hero( $title, '' );

$posts = kayan_kit_posts( array( 'faqs', 'faq' ), array( 'posts_per_page' => 40 ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $posts ) ) {
	echo '<div class="faq-list">';
	foreach ( $posts as $i => $faq ) {
		kayan_kit_render_faq( $faq->post_title, $faq->post_content, 0 === $i );
	}
	echo '</div>';
} else {
	kayan_kit_empty();
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
