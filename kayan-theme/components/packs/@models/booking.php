<?php
$Styles = array();
$Styles['contact__form'] = 'YourColor__Widgets/contact__form.css';
$this->Part( 'header', array( 'Styles' => $Styles ) );

$ctx     = kayan_kit_booking_context();
$phone   = kayan_kit_phone();
$wa      = kayan_kit_whatsapp();
$note    = get_option( 'booking__note' );
$hero_t  = $post->post_title;
$hero_s  = kayan_kit_page_excerpt( $post );

if ( $ctx['service'] && $ctx['city'] ) {
	$hero_t = sprintf(
		__( 'احجز <em>%s</em> في %s', 'yourcolor' ),
		esc_html( $ctx['service'] ),
		esc_html( $ctx['city'] )
	);
	$hero_s = $ctx['excerpt'] ? $ctx['excerpt'] : sprintf( __( 'أكمل طلبك لـ %s — فريق الكونسيرج يؤكد الموعد خلال دقائق.', 'yourcolor' ), $ctx['service'] );
} elseif ( $ctx['service'] ) {
	$hero_t = sprintf( __( 'احجز <em>%s</em>', 'yourcolor' ), esc_html( $ctx['service'] ) );
	$hero_s = $ctx['excerpt'] ? $ctx['excerpt'] : sprintf( __( 'تفاصيل خدمتك جاهزة — اختر الباقة وأكمل البيانات.', 'yourcolor' ), $ctx['service'] );
}

kayan_kit_hero(
	$hero_t,
	$hero_s,
	$post->post_title,
	array(
		'chips' => array_filter(
			array(
				$ctx['service'] ? '<i class="fas fa-screwdriver-wrench"></i> ' . esc_html( $ctx['service'] ) : '',
				$ctx['city'] ? '<i class="fas fa-location-dot"></i> ' . esc_html( $ctx['city'] ) : '',
				$ctx['package'] ? '<i class="fas fa-gem"></i> ' . esc_html( $ctx['package'] ) : '',
			)
		),
		'meta'  => array(
			array( 'b' => '3', 's' => __( 'خطوات فقط', 'yourcolor' ) ),
			array( 'b' => __( 'مجاني', 'yourcolor' ), 's' => __( 'معاينة أولية', 'yourcolor' ) ),
			array( 'b' => '60', 's' => __( 'دقيقة متوسط الرد', 'yourcolor' ) ),
		),
	)
);

$services = empty( $ctx['service'] ) ? kayan_kit_terms( array( 'category', 'service_categories' ), array( 'number' => 30 ) ) : array();
$cities   = empty( $ctx['city'] ) ? kayan_kit_terms( array( 'city', 'cities' ), array( 'number' => 30 ) ) : array();

echo '<section class="sec kbook">';
echo '<div class="wrap kbook-layout">';

echo '<aside class="kbook-summary">';
echo '<div class="kbook-badge"><i class="fas fa-gem"></i> ' . esc_html__( 'كونسيرج الحجز الخاص', 'yourcolor' ) . '</div>';
echo '<h2>' . esc_html( $ctx['service'] ? $ctx['service'] : get_bloginfo( 'name' ) ) . '</h2>';
if ( $ctx['city'] ) {
	echo '<p class="kbook-city"><i class="fas fa-location-dot"></i> ' . esc_html( $ctx['city'] ) . '</p>';
}
if ( $ctx['title'] && $ctx['url'] ) {
	echo '<a class="kbook-from" href="' . esc_url( $ctx['url'] ) . '"><i class="fas fa-arrow-right"></i> ' . esc_html( $ctx['title'] ) . '</a>';
}
if ( $ctx['excerpt'] ) {
	echo '<p class="kbook-excerpt">' . esc_html( $ctx['excerpt'] ) . '</p>';
}
if ( ! empty( $ctx['packages'] ) ) {
	echo '<div class="kbook-sum-packs">';
	echo '<span>' . esc_html__( 'الباقات المتاحة', 'yourcolor' ) . '</span>';
	foreach ( $ctx['packages'] as $pack ) {
		$on = ( $ctx['package'] && $ctx['package'] === $pack['title'] ) ? ' class="on"' : '';
		echo '<div' . $on . '><b>' . esc_html( $pack['title'] ) . '</b>';
		if ( ! empty( $pack['value'] ) ) {
			echo '<small>' . esc_html( $pack['value'] ) . '</small>';
		}
		echo '</div>';
	}
	echo '</div>';
}
echo '<ul class="kbook-trust">';
echo '<li><i class="fas fa-shield-halved"></i> ' . esc_html__( 'معاينة أولى بدون التزام', 'yourcolor' ) . '</li>';
echo '<li><i class="fas fa-clock"></i> ' . esc_html__( 'تأكيد الموعد خلال دقائق', 'yourcolor' ) . '</li>';
echo '<li><i class="fas fa-user-tie"></i> ' . esc_html__( 'تنسيق خاص عبر فريق الكونسيرج', 'yourcolor' ) . '</li>';
if ( $phone ) {
	echo '<li><i class="fas fa-phone"></i> <a href="tel:' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a></li>';
}
echo '</ul>';
if ( $wa ) {
	echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener" style="width:100%"><i class="fab fa-whatsapp"></i> ' . esc_html__( 'واتساب مباشر', 'yourcolor' ) . '</a>';
}
echo '</aside>';

echo '<div class="kbook-panel">';
echo '<div class="kbook-steps" aria-hidden="true">';
echo '<span class="on"><b>1</b> ' . esc_html__( 'الخدمة', 'yourcolor' ) . '</span>';
echo '<span class="on"><b>2</b> ' . esc_html__( 'التفاصيل', 'yourcolor' ) . '</span>';
echo '<span><b>3</b> ' . esc_html__( 'بياناتك', 'yourcolor' ) . '</span>';
echo '</div>';
if ( $note ) {
	echo '<p class="kbook-note">' . esc_html( kayan_kit_plain( $note, 280 ) ) . '</p>';
}

echo '<form class="kbook-form" id="kayanKitBookingForm" method="POST" action="contact__form">';
echo '<input type="text" name="yc_website_url" value="" tabindex="-1" autocomplete="off" class="kbook-hp" aria-hidden="true" />';
echo '<input type="hidden" name="kit_from" value="' . esc_attr( $ctx['from_id'] ) . '" />';
echo '<input type="hidden" name="kit_from_url" value="' . esc_attr( $ctx['url'] ) . '" />';

if ( $ctx['service'] ) {
	echo '<input type="hidden" name="kit_service" value="' . esc_attr( $ctx['service'] ) . '" />';
	echo '<div class="kbook-locked"><i class="fas fa-screwdriver-wrench"></i><div><small>' . esc_html__( 'الخدمة', 'yourcolor' ) . '</small><b>' . esc_html( $ctx['service'] ) . '</b></div></div>';
} elseif ( ! empty( $services ) ) {
	echo '<div class="fld full"><label>' . esc_html__( 'اختر الخدمة', 'yourcolor' ) . '</label><div class="sel"><i class="fas fa-toolbox"></i><select name="kit_service" required>';
	echo '<option value="">' . esc_html__( 'اختر الخدمة', 'yourcolor' ) . '</option>';
	foreach ( $services as $term ) {
		echo '<option value="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></div></div>';
}

if ( $ctx['city'] ) {
	echo '<input type="hidden" name="kit_city" value="' . esc_attr( $ctx['city'] ) . '" />';
	echo '<div class="kbook-locked"><i class="fas fa-location-dot"></i><div><small>' . esc_html__( 'المدينة', 'yourcolor' ) . '</small><b>' . esc_html( $ctx['city'] ) . '</b></div></div>';
} elseif ( ! empty( $cities ) ) {
	echo '<div class="fld"><label>' . esc_html__( 'المدينة', 'yourcolor' ) . '</label><div class="sel"><i class="fas fa-location-dot"></i><select name="kit_city" required>';
	echo '<option value="">' . esc_html__( 'المدينة', 'yourcolor' ) . '</option>';
	foreach ( $cities as $term ) {
		echo '<option value="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></div></div>';
}

if ( ! empty( $ctx['packages'] ) ) {
	echo '<div class="kbook-q"><div class="kbook-qlabel">' . esc_html__( 'اختر الباقة', 'yourcolor' ) . ' <i>*</i></div>';
	echo '<div class="kbook-packs">';
	foreach ( $ctx['packages'] as $i => $pack ) {
		$checked = ( $ctx['package'] && $ctx['package'] === $pack['title'] ) || ( ! $ctx['package'] && 0 === $i );
		echo '<label class="kbook-pack">';
		echo '<input type="radio" name="kit_package" value="' . esc_attr( $pack['title'] ) . '"' . ( $checked ? ' checked' : '' ) . ' required />';
		echo '<span><b>' . esc_html( $pack['title'] ) . '</b>';
		if ( ! empty( $pack['value'] ) ) {
			echo '<small>' . esc_html( $pack['value'] ) . '</small>';
		}
		echo '</span></label>';
	}
	echo '</div></div>';
}

if ( ! empty( $ctx['questions'] ) ) {
	echo '<div class="kbook-qs">';
	echo '<h3>' . esc_html( $ctx['service'] ? sprintf( __( 'تفاصيل تناسب %s', 'yourcolor' ), $ctx['service'] ) : __( 'تفاصيل تناسب خدمتك', 'yourcolor' ) ) . '</h3>';
	foreach ( $ctx['questions'] as $field ) {
		kayan_kit_render_booking_field( $field );
	}
	echo '</div>';
}

echo '<div class="kbook-qs">';
echo '<h3>' . esc_html__( 'بيانات التواصل', 'yourcolor' ) . '</h3>';
echo '<div class="form-grid">';
echo '<div class="fld"><label>' . esc_html__( 'الاسم بالكامل', 'yourcolor' ) . '</label><input type="text" name="user__name" required placeholder="' . esc_attr__( 'اسمك الكامل', 'yourcolor' ) . '" /></div>';
echo '<div class="fld"><label>' . esc_html__( 'رقم الهاتف', 'yourcolor' ) . '</label><input type="tel" name="phone__number" required placeholder="05xxxxxxxx" /></div>';
echo '<div class="fld full"><label>' . esc_html__( 'البريد الالكتروني', 'yourcolor' ) . '</label><input type="email" name="user_mail" /></div>';
echo '<div class="fld full"><label>' . esc_html__( 'ملاحظات إضافية', 'yourcolor' ) . '</label><textarea name="description" placeholder="' . esc_attr__( 'أي تفاصيل تساعدنا على خدمتك بشكل أفضل...', 'yourcolor' ) . '"></textarea></div>';
echo '</div></div>';

echo '<button type="submit" class="btn btn-quote kbook-submit"><i class="fas fa-calendar-check"></i> ' . esc_html__( 'تأكيد طلب الحجز', 'yourcolor' ) . '</button>';
echo '<div class="form-note" style="justify-content:center"><i class="fas fa-lock"></i> ' . esc_html__( 'معاينة مجانية بدون أي التزام مالي — بياناتك محفوظة لتنسيق الموعد فقط.', 'yourcolor' ) . '</div>';
echo '<div class="kbook-alert" hidden></div>';
echo '</form>';
echo '</div></div></section>';

echo '<script>';
echo '(function(){';
echo 'var form=document.getElementById("kayanKitBookingForm");if(!form)return;';
echo 'var base=' . wp_json_encode( trailingslashit( home_url() ) ) . ';';
echo 'form.addEventListener("submit",function(e){';
echo 'e.preventDefault();';
echo 'var btn=form.querySelector(".kbook-submit");';
echo 'var box=form.querySelector(".kbook-alert");';
echo 'btn.disabled=true;btn.style.opacity=".65";';
echo 'var data=new URLSearchParams(new FormData(form));';
echo 'fetch(base+"AjaxCenter/contact__nonce/",{cache:"no-store"}).then(function(r){return r.json()}).then(function(n){';
echo 'data.append("yc_contact_nonce",n.nonce);';
echo 'return fetch(base+"AjaxCenter/contact__form/",{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:data.toString()});';
echo '}).then(function(r){return r.json()}).then(function(msg){';
echo 'box.hidden=false;box.className="kbook-alert ok";';
echo 'var t=(msg&&msg.alert_output&&msg.alert_output.alert)?msg.alert_output.alert:"تم استلام طلب الحجز بنجاح.";';
echo 'box.innerHTML=\'<i class="fas fa-circle-check"></i> \'+t;';
echo 'btn.disabled=false;btn.style.opacity="1";';
echo '}).catch(function(){';
echo 'box.hidden=false;box.className="kbook-alert err";';
echo 'box.innerHTML=\'<i class="fas fa-circle-xmark"></i> حدث خطأ، حاول مرة أخرى.\';';
echo 'btn.disabled=false;btn.style.opacity="1";';
echo '});';
echo '});';
echo '})();';
echo '</script>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
