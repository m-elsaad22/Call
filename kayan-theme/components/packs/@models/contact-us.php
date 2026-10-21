<?php
$Styles = array();
$YC__WidgetsMachine = new YC__WidgetsMachine;
$widgets_contactus__meta = is_array( get_option( 'widgets_contactus__meta' ) ) ? get_option( 'widgets_contactus__meta' ) : array();
if ( ! empty( $widgets_contactus__meta ) ) {
	$Styles = array_merge( $Styles, $YC__WidgetsMachine->widgets__Enqueues( $widgets_contactus__meta ) );
}

$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$phone   = kayan_kit_phone();
$wa      = kayan_kit_whatsapp();
$mail    = get_option( 'company__mail' );
$address = get_option( 'company__adress' );
$map     = get_option( 'company__map_code' );

echo '<section class="sec"><div class="wrap contact-layout">';
echo '<div class="cinfo-card"><div class="inner">';
echo '<h3 style="color:#fff;margin-bottom:22px">' . esc_html__( 'معلومات التواصل', 'yourcolor' ) . '</h3>';
if ( $phone ) {
	echo '<div class="cinfo-item"><i class="fas fa-phone"></i><div><b>' . esc_html__( 'اتصال مباشر', 'yourcolor' ) . '</b><small>' . esc_html( $phone ) . '</small></div></div>';
}
if ( $wa ) {
	echo '<div class="cinfo-item"><i class="fab fa-whatsapp"></i><div><b>' . esc_html__( 'واتساب', 'yourcolor' ) . '</b><small>' . esc_html( $wa ) . '</small></div></div>';
}
if ( $mail ) {
	echo '<div class="cinfo-item"><i class="fas fa-envelope"></i><div><b>' . esc_html__( 'البريد الإلكتروني', 'yourcolor' ) . '</b><small>' . esc_html( $mail ) . '</small></div></div>';
}
if ( $address ) {
	echo '<div class="cinfo-item"><i class="fas fa-location-dot"></i><div><b>' . esc_html__( 'الموقع', 'yourcolor' ) . '</b><small>' . esc_html( $address ) . '</small></div></div>';
}
if ( $map ) {
	echo '<div class="map-ph">' . $map . '</div>';
}
if ( $wa ) {
	echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener" style="width:100%;margin-top:20px"><i class="fab fa-whatsapp"></i> ' . esc_html__( 'تواصل عبر واتساب الآن', 'yourcolor' ) . '</a>';
}
echo '</div></div>';

echo '<div class="form-card">';
echo '<h3 style="margin-bottom:6px">' . esc_html__( 'أرسل لنا رسالة', 'yourcolor' ) . '</h3>';
echo '<p style="color:var(--text2);margin-bottom:24px">' . esc_html( kayan_kit_page_excerpt( $post ) ) . '</p>';
kayan_kit_contact_form();
echo '</div>';
echo '</div></section>';

if ( ! empty( $widgets_contactus__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_contactus__meta,
			'WidgetID'     => 'widgets_contactus__meta',
		)
	);
}

$this->Part( 'footer', array( 'Styles' => $Styles ) );
