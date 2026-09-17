<?php
$Styles = array();
$Styles['contact__form'] = 'YourColor__Widgets/contact__form.css';
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero(
	$post->post_title,
	kayan_kit_page_excerpt( $post ),
	'',
	array(
		'meta' => array(
			array( 'b' => '3', 's' => __( 'خطوات فقط', 'yourcolor' ) ),
			array( 'b' => __( 'مجاني', 'yourcolor' ), 's' => __( 'معاينة أولية', 'yourcolor' ) ),
		),
	)
);

$services = kayan_kit_terms( array( 'category', 'service_categories' ), array( 'number' => 30 ) );
$cities   = kayan_kit_terms( array( 'city', 'cities' ), array( 'number' => 30 ) );
$phone    = kayan_kit_phone();
$note     = get_option( 'booking__note' );

$fields = array(
	array( 'title' => __( 'الاسم بالكامل', 'yourcolor' ), 'id' => 'user__name', 'type' => 'Text', 'Require' => 'on' ),
	array( 'title' => __( 'رقم الهاتف', 'yourcolor' ), 'id' => 'phone__number', 'type' => 'Number', 'Require' => 'on' ),
	array( 'title' => __( 'البريد الالكتروني', 'yourcolor' ), 'id' => 'user_mail', 'type' => 'Email', 'Require' => '' ),
	array( 'title' => __( 'تفاصيل الحجز', 'yourcolor' ), 'id' => 'description', 'type' => 'TextArea', 'Require' => 'on' ),
);
$encoded = base64_encode( wp_json_encode( $fields ) );

echo '<section class="sec"><div class="wrap" style="max-width:900px;margin:0 auto">';
echo '<div class="form-card">';
if ( $note ) {
	echo '<p style="color:var(--text2);margin-bottom:20px">' . esc_html( kayan_kit_plain( $note, 240 ) ) . '</p>';
}
echo '<form method="POST" action="contact__form" data-form-ajax="true" data-for-action="1" data-fields-arguments="' . esc_attr( $encoded ) . '">';
echo '<div class="form-grid">';
if ( ! empty( $services ) ) {
	echo '<div class="fld full"><label>' . esc_html__( 'اختر الخدمة', 'yourcolor' ) . '</label><select name="kit_service">';
	echo '<option value="">' . esc_html__( 'اختر الخدمة', 'yourcolor' ) . '</option>';
	foreach ( $services as $term ) {
		echo '<option value="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></div>';
}
if ( ! empty( $cities ) ) {
	echo '<div class="fld"><label>' . esc_html__( 'المدينة', 'yourcolor' ) . '</label><select name="kit_city">';
	echo '<option value="">' . esc_html__( 'المدينة', 'yourcolor' ) . '</option>';
	foreach ( $cities as $term ) {
		echo '<option value="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></div>';
}
foreach ( $fields as $field ) {
	$full = ( 'TextArea' === $field['type'] || 'user_mail' === $field['id'] ) ? ' full' : '';
	echo '<div class="fld' . $full . '" data-field-id="' . esc_attr( $field['id'] ) . '">';
	echo '<label>' . esc_html( $field['title'] ) . '</label>';
	if ( 'TextArea' === $field['type'] ) {
		echo '<textarea name="' . esc_attr( $field['id'] ) . '"></textarea>';
	} elseif ( 'Email' === $field['type'] ) {
		echo '<input type="email" name="' . esc_attr( $field['id'] ) . '" />';
	} elseif ( 'Number' === $field['type'] ) {
		echo '<input type="tel" name="' . esc_attr( $field['id'] ) . '" />';
	} else {
		echo '<input type="text" name="' . esc_attr( $field['id'] ) . '" />';
	}
	echo '</div>';
}
echo '</div>';
echo '<button type="submit" class="btn btn-quote" style="width:100%;margin-top:22px"><i class="fas fa-calendar-check"></i> ' . esc_html__( 'تأكيد طلب الحجز', 'yourcolor' ) . '</button>';
echo '<div class="form-note" style="justify-content:center"><i class="fas fa-shield-halved"></i> ' . esc_html__( 'معاينة مجانية بدون أي التزام مالي', 'yourcolor' ) . '</div>';
echo '</form></div>';
if ( $phone ) {
	echo '<div style="text-align:center;margin-top:26px;color:var(--text2)">' . esc_html__( 'تفضل الحجز المباشر؟', 'yourcolor' ) . ' <a href="tel:' . esc_attr( $phone ) . '" style="color:var(--turq);font-weight:700">' . esc_html__( 'اتصل بنا الآن', 'yourcolor' ) . '</a></div>';
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
