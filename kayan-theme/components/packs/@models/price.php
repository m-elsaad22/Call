<?
$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();
$YC__WidgetsMachine = new YC__WidgetsMachine;

$widgets_price_page__meta = ( is_array( get_option( 'widgets_price_page__meta' ) ) ) ? get_option( 'widgets_price_page__meta' ) : array();
if ( ! empty( $widgets_price_page__meta ) ) {
	$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues( $widgets_price_page__meta );
	$Styles            = array_merge( $Styles, $widgets__Enqueues );
}

$hero_sub = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $post->post_content, 180 ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $post->post_title,
			'subtitle'  => $hero_sub,
			'image_url' => $page_background,
		)
	);
}

kayan_kit_open_article_layout( true );
if ( ! empty( $post->post_content ) ) {
	echo apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
if ( ! empty( $widgets_price_page__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_price_page__meta,
			'WidgetID'     => 'widgets_price_page__meta',
		)
	);
}
kayan_kit_open_sidebar();
if ( function_exists( 'kayan_kit_render_cta_widget' ) ) {
	kayan_kit_render_cta_widget( $post->ID );
}
kayan_kit_close_article_layout( true );

$this->Part( 'footer', array( 'Styles' => $Styles ) );
