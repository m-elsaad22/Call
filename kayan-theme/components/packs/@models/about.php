<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

ob_start();
the_content();
$body = ob_get_clean();

echo '<section class="sec">';
echo '<div class="wrap article-layout">';
echo '<div class="article-body prose">';
echo $body;
echo '</div>';
echo '<div>';
kayan_kit_side_cta( get_bloginfo( 'name' ), kayan_kit_page_excerpt( $post ) );
echo '</div>';
echo '</div>';
echo '</section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
