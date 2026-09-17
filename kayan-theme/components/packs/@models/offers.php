<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$offers = get_option( 'offers_data' );
$offers = is_array( $offers ) ? $offers : array();
$badge  = get_option( 'offers_badge' );
$wa     = kayan_kit_whatsapp();
$phone  = kayan_kit_phone();

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $offers ) ) {
	echo '<div class="offer-grid">';
	foreach ( $offers as $offer ) {
		if ( ! is_array( $offer ) || empty( $offer['offer_title'] ) ) {
			continue;
		}
		$title = $offer['offer_title'];
		$body  = isset( $offer['offer_content'] ) ? $offer['offer_content'] : '';
		$price = isset( $offer['offer_price'] ) ? $offer['offer_price'] : '';
		$old   = isset( $offer['offer_old_price'] ) ? $offer['offer_old_price'] : '';
		echo '<div class="offer">';
		echo '<div class="offer-top">';
		if ( $badge ) {
			echo '<span class="offer-badge"><i class="fas fa-tag"></i> ' . esc_html( $badge ) . '</span>';
		}
		if ( $price ) {
			echo '<div class="off-pct">' . esc_html( $price ) . '</div>';
		}
		echo '<h3>' . esc_html( $title ) . '</h3>';
		echo '</div>';
		echo '<div class="offer-body">';
		if ( $body ) {
			echo '<p>' . esc_html( kayan_kit_plain( $body, 160 ) ) . '</p>';
		}
		if ( $old ) {
			echo '<div class="offer-exp"><i class="fas fa-clock"></i> ' . esc_html( $old ) . '</div>';
		}
		if ( $wa ) {
			echo '<a class="btn btn-quote" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener"><i class="fas fa-file-invoice-dollar"></i> ' . esc_html__( 'احصل على العرض', 'yourcolor' ) . '</a>';
		} elseif ( $phone ) {
			echo '<a class="btn btn-quote" href="tel:' . esc_attr( $phone ) . '"><i class="fas fa-phone"></i> ' . esc_html__( 'احصل على العرض', 'yourcolor' ) . '</a>';
		}
		echo '</div></div>';
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
