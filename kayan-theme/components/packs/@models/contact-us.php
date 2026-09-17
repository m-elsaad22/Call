<?
$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();
$UniqId = uniqid();

$YC__WidgetsMachine = new YC__WidgetsMachine;

$widgets_contactus__meta = ( is_array( get_option( 'widgets_contactus__meta' ) ) ) ? get_option( 'widgets_contactus__meta' ) : array();
if ( ! empty( $widgets_contactus__meta ) ) {
	$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues( $widgets_contactus__meta );
	$Styles            = array_merge( $Styles, $widgets__Enqueues );
}

$Styles['contact__form'] = 'YourColor__Widgets/contact__form.css';

$hero_sub = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $post->post_content, 180 ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$whatsapp_number = get_post_meta( $post->ID, 'whatsapp_number', true );
if ( empty( $whatsapp_number ) ) {
	$whatsapp_number = get_option( 'whatsapp_number' );
}
$phonenumber = get_post_meta( $post->ID, 'phone_number', true );
if ( empty( $phonenumber ) ) {
	$phonenumber = get_option( 'phonenumber' );
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

kayan_kit_open_section( 'kayan-contact-page' );
echo '<div class="contact-layout">';
if ( function_exists( 'kayan_kit_contact_info_card' ) ) {
	kayan_kit_contact_info_card( $post->ID );
}
echo '<div class="form-card">';
if ( ! empty( $post->post_content ) ) {
	echo '<div class="prose" style="margin-bottom:22px">' . apply_filters( 'the_content', $post->post_content ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
if ( ! empty( $widgets_contactus__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_contactus__meta,
			'WidgetID'     => 'widgets_contactus__meta',
		)
	);
}
echo '</div></div>';
kayan_kit_close_section();

$this->Part( 'footer', array( 'Styles' => $Styles ) );
