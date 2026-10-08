<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$phone = kayan_kit_phone();
$wa    = kayan_kit_whatsapp();

echo '<section class="err-wrap">';
echo '<div class="wrap">';
echo '<div class="err-num">404</div>';
echo '<h2>' . esc_html__( 'الصفحة غير موجودة', 'yourcolor' ) . '</h2>';
echo '<p>' . esc_html__( 'تعذر العثور على الصفحة المطلوبة. يمكنك العودة للرئيسية أو تصفح الصفحات الظاهرة في الموقع.', 'yourcolor' ) . '</p>';
echo '<div class="err-actions">';
echo '<a class="btn btn-quote" href="' . esc_url( home_url( '/' ) ) . '"><i class="fas fa-house"></i> ' . esc_html__( 'الرئيسية', 'yourcolor' ) . '</a>';
if ( $wa ) {
	echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> ' . esc_html__( 'واتساب', 'yourcolor' ) . '</a>';
} elseif ( $phone ) {
	echo '<a class="btn btn-call" href="tel:' . esc_attr( $phone ) . '"><i class="fas fa-phone"></i> ' . esc_html( $phone ) . '</a>';
}
echo '</div>';
echo '<div class="err-links">';
$pages = get_pages( array( 'number' => 8, 'sort_column' => 'menu_order' ) );
foreach ( $pages as $p ) {
	echo '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $p->post_title ) . '</a>';
}
echo '</div>';
echo '</div>';
echo '</section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
