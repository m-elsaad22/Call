<?
$obj = get_queried_object();
$Styles = array(
	'kayan-home'  => 'kayan-home.css',
	'kayan-inner' => 'kayan-inner.css',
	'shortcodes'  => 'shortcodes.css',
);

$city_name = esc_html( $obj->name );
$city_icon = get_term_meta( $obj->term_id, 'icon', true );
$city_image = function_exists( 'kayan_seo_get_term_image_url' ) ? kayan_seo_get_term_image_url( $obj->term_id ) : '';
$city_headline = function_exists( 'kayan_seo_get_city_headline' ) ? kayan_seo_get_city_headline( $obj ) : 'خدمات منزلية في ' . $city_name;

$seo_description = '';
if ( function_exists( 'kayan_seo_get_description' ) ) {
	$seo_description = kayan_seo_get_description();
}

$intro_content = $obj->description;
$intro_content = str_replace( '<br/>', PHP_EOL, $intro_content );
$intro_content = str_replace( '&nbsp;', ' ', $intro_content );
$intro_content = strip_tags( $intro_content );

if ( empty( $intro_content ) && ! empty( $seo_description ) ) {
	$intro_content = $seo_description;
}

if ( empty( $intro_content ) ) {
	$intro_content = 'نقدّم أفضل الخدمات المنزلية في ' . $city_name . ' بفريق محترف وأسعار منافسة.';
}

if ( strlen( $intro_content ) > 500 ) {
	$intro_content = mb_substr( $intro_content, 0, 500, 'utf-8' ) . '... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="taxonomeis" data-object-name="' . esc_attr( $obj->taxonomy ) . '" data-object-id="' . (int) $obj->term_id . '" class="readmore--category-item -BTN--hoverable">قراءة المزيد</a>';
}

$post_count = (int) $obj->count;
$phone = yc_get_option( 'contact_number' );
if ( empty( $phone ) ) {
	$phone = yc_get_option( 'phonenumber' );
}
$whatsapp = yc_get_option( 'whatsapp_number' );
$site_name = function_exists( 'kayan_seo_get_site_name' ) ? kayan_seo_get_site_name() : get_bloginfo( 'name' );

$intro_full_text = $obj->description;
$intro_full_text = str_replace( '<br/>', PHP_EOL, $intro_full_text );
$intro_full_text = str_replace( '&nbsp;', ' ', $intro_full_text );
$intro_full_text = trim( strip_tags( $intro_full_text ) );
if ( empty( $intro_full_text ) && ! empty( $seo_description ) ) {
	$intro_full_text = $seo_description;
}
if ( empty( $intro_full_text ) ) {
	$intro_full_text = 'نقدّم أفضل الخدمات المنزلية في ' . $city_name . ' بفريق محترف وأسعار منافسة.';
}

$intro_hero_text = $intro_full_text;
if ( mb_strlen( $intro_full_text, 'UTF-8' ) > 200 ) {
	$intro_hero_text = mb_substr( $intro_full_text, 0, 200, 'UTF-8' ) . '…';
}

$phone_cta = function_exists( 'kayan_hp_resolve_phone' ) ? kayan_hp_resolve_phone( null ) : $phone;
$whatsapp_cta = function_exists( 'kayan_hp_resolve_whatsapp' ) ? kayan_hp_resolve_whatsapp( null ) : $whatsapp;

if ( function_exists( 'kayan_hp_resolve_tel_url' ) ) {
	$tel_url = kayan_hp_resolve_tel_url( null );
} else {
	$tel_url = 'tel:' . preg_replace( '/\s+/', '', (string) $phone_cta );
}

if ( function_exists( 'kayan_hp_resolve_whatsapp_url' ) ) {
	$wa_url = kayan_hp_resolve_whatsapp_url( null );
} else {
	$wa_digits = preg_replace( '/\D+/', '', (string) $whatsapp_cta );
	$wa_url    = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $wa_digits, null, $city_headline ) : 'https://wa.me/' . $wa_digits;
}

$show_call = function_exists( 'kayan_ui_show_call_button' ) ? kayan_ui_show_call_button() : ! empty( $phone_cta );

$this->Part( 'header', array( 'Styles' => $Styles ) );

$city_meta = '';
if ( $post_count > 0 ) {
	$city_meta = '<div><b>' . (int) $post_count . '</b><small>خدمة في ' . esc_html( $city_name ) . '</small></div>';
}

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $city_headline,
			'subtitle'  => $intro_hero_text,
			'image_url' => $city_image,
			'meta_html' => $city_meta,
		)
	);
}

echo '<div class="kayan-inner-archive-shell kayan-city-archive">';
kayan_kit_open_section();
echo '<div class="shead rv"><span class="tag">' . esc_html( $site_name ) . '</span><h2>خدماتنا في <span>' . $city_name . '</span></h2><p>تصفّح جميع الخدمات المنزلية المتوفرة في ' . $city_name . '.</p></div>';
echo '<div class="blog-grid kayan-inner-archive-grid -archivePage-Posts-Grid">';
$this->Part(
	'Posts',
	array(
		'object__type'        => 'posts',
		'object__name'        => 'post',
		'part_object__name'   => 'post',
		'part__name'          => 'Post-box',
		'ObjectTerms'         => array( $obj ),
		'ScrollLoader'        => true,
		'per'                 => 8,
		'show__empty_part'    => 'object--empty',
		'data__empty_part'    => array(
			'__empty_icon'             => '<i class="fa-solid fa-ban"></i>',
			'__empty_title'            => 'لا توجد خدمات في ' . $city_name . ' حالياً',
			'__empty_description'      => '<a href="' . esc_url( home_url() ) . '">العودة للرئيسية</a>',
			'__Ajax_empty_title'       => 'لقد شاهدت جميع الخدمات',
			'__Ajax_empty_description' => 'تم عرض جميع الخدمات في ' . $city_name . ' — <a href="' . esc_url( home_url() ) . '">الرئيسية</a>',
		),
	)
);
echo '</div>';

if ( mb_strlen( $intro_full_text, 'UTF-8' ) > 200 ) {
	echo '<details class="kayan-inner-collapsible" style="margin-top:40px">';
	echo '<summary>عن الخدمات في ' . $city_name . '</summary>';
	echo '<div class="kayan-inner-collapsible__body kayan-inner-section__body prose">' . esc_html( $intro_full_text ) . '</div>';
	echo '</details>';
} elseif ( $intro_full_text !== $intro_hero_text ) {
	echo '<div class="article-body prose" style="margin-top:40px">' . esc_html( $intro_full_text ) . '</div>';
}

kayan_kit_close_section();
echo '</div>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
