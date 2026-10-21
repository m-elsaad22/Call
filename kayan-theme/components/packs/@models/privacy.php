<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

ob_start();
the_content();
$body = ob_get_clean();

echo '<section class="sec">';
echo '<div class="wrap article-layout">';
echo '<aside class="side-w">';
echo '<h4>' . esc_html__( 'محتويات الصفحة', 'yourcolor' ) . '</h4>';
echo '<div class="legal-toc">';
if ( preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/is', $body, $heads ) ) {
	foreach ( $heads[1] as $i => $heading ) {
		echo '<a href="#kit-h-' . ( $i + 1 ) . '"><b>' . ( $i + 1 ) . '</b> ' . kayan_kit_plain( $heading ) . '</a>';
	}
	$n = 0;
	$body = preg_replace_callback( '/<h2([^>]*)>/i', function( $m ) use ( &$n ) {
		$n++;
		return '<h2' . $m[1] . ' id="kit-h-' . $n . '">';
	}, $body );
}
echo '</div>';
echo '</aside>';
echo '<div class="article-body prose">';
if ( $post->post_modified ) {
	echo '<div class="legal-updated"><i class="fas fa-clock"></i> ' . esc_html( get_the_modified_date( '', $post ) ) . '</div>';
}
echo $body;
echo '</div>';
echo '</div>';
echo '</section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
