<?php
$Styles = array();
$YC__WidgetsMachine = new YC__WidgetsMachine;
$widgets_price_page__meta = is_array( get_option( 'widgets_price_page__meta' ) ) ? get_option( 'widgets_price_page__meta' ) : array();
if ( ! empty( $widgets_price_page__meta ) ) {
	$Styles = array_merge( $Styles, $YC__WidgetsMachine->widgets__Enqueues( $widgets_price_page__meta ) );
}

$this->Part( 'header', array( 'Styles' => $Styles ) );
kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$items = kayan_kit_posts( array( 'pricing', 'price' ), array( 'posts_per_page' => 24 ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="offer-grid">';
	foreach ( $items as $item ) {
		$price = get_post_meta( $item->ID, 'service_price', true );
		echo '<div class="offer"><div class="offer-top">';
		if ( $price ) {
			echo '<div class="off-pct">' . esc_html( $price ) . '</div>';
		}
		echo '<h3>' . esc_html( $item->post_title ) . '</h3></div>';
		echo '<div class="offer-body"><p>' . esc_html( kayan_kit_plain( $item->post_content, 140 ) ) . '</p>';
		echo '<a class="btn btn-quote" href="' . esc_url( get_permalink( $item ) ) . '">' . esc_html__( 'التفاصيل', 'yourcolor' ) . '</a>';
		echo '</div></div>';
	}
	echo '</div>';
} elseif ( ! empty( $widgets_price_page__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_price_page__meta,
			'WidgetID'     => 'widgets_price_page__meta',
		)
	);
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
