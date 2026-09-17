<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$rating      = (int) get_post_meta( $post->ID, 'rating', true );
$rating      = ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
$client_name = get_post_meta( $post->ID, 'client_name', true );
$client_name = $client_name ? $client_name : $post->post_title;
$client_city = get_post_meta( $post->ID, 'client_city', true );
$initial     = mb_substr( $client_name, 0, 1, 'UTF-8' );

ob_start();
the_content();
$body = ob_get_clean();

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

echo '<section class="sec"><div class="wrap" style="max-width:760px;margin:0 auto">';
echo '<div class="rvcard2">';
echo '<div class="gtop"><span class="gver"><i class="fas fa-star"></i> ' . esc_html__( 'تقييم', 'yourcolor' ) . '</span><span class="rstars">' . kayan_kit_stars( $rating ) . '</span></div>';
echo '<div class="txt prose">' . $body . '</div>';
echo '<div class="rclient"><span class="rav">' . esc_html( $initial ) . '</span><div><b>' . esc_html( $client_name ) . '</b>';
if ( $client_city ) {
	echo '<small>' . esc_html( $client_city ) . '</small>';
}
echo '</div></div></div></div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
