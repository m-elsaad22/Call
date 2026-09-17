<?
status_header( 404 );
nocache_headers();

$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

$home    = home_url( '/' );
$wa_url  = function_exists( 'kayan_hp_resolve_whatsapp_url' ) ? kayan_hp_resolve_whatsapp_url() : '#';
$company = function_exists( 'kayan_homepage_get_company_name' ) ? kayan_homepage_get_company_name() : get_bloginfo( 'name' );

$services_url = function_exists( 'kayan_kit_page_url_by_slugs' )
	? kayan_kit_page_url_by_slugs( array( 'services', 'الخدمات', 'service' ), $home )
	: $home;
$cities_url = function_exists( 'kayan_kit_page_url_by_slugs' )
	? kayan_kit_page_url_by_slugs( array( 'cities', 'المدن', 'city' ), $home )
	: $home;
$blog_url = get_permalink( (int) get_option( 'page_for_posts' ) );
if ( ! $blog_url ) {
	$blog_url = function_exists( 'kayan_kit_page_url_by_slugs' )
		? kayan_kit_page_url_by_slugs( array( 'blog', 'المدونة', 'articles' ), $home )
		: $home;
}
$contact_url = function_exists( 'kayan_kit_page_url_by_slugs' )
	? kayan_kit_page_url_by_slugs( array( 'contact', 'contact-us', 'تواصل-معنا', 'اتصل-بنا' ), $home )
	: $home;
$faq_url = function_exists( 'kayan_kit_page_url_by_slugs' )
	? kayan_kit_page_url_by_slugs( array( 'faq', 'faqs', 'الاسئلة-الشائعة', 'الأسئلة-الشائعة' ), $home )
	: $home;

echo '<section class="err-wrap">';
echo '<div class="wrap">';
echo '<div class="err-num">404</div>';
echo '<h2>عذراً، هذه الصفحة غير موجودة</h2>';
echo '<p>يبدو أن الرابط الذي وصلت منه غير صحيح أو أن الصفحة تم نقلها. يمكنك العودة للرئيسية أو تصفح خدمات ' . esc_html( $company ) . '.</p>';
echo '<div class="err-actions">';
echo '<a href="' . esc_url( $home ) . '" class="btn btn-quote"><i class="fas fa-house"></i> العودة للرئيسية</a>';
if ( $wa_url !== '#' && $wa_url !== '' ) {
	echo '<a href="' . esc_url( $wa_url ) . '" class="btn btn-wa" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> تحدث معنا</a>';
}
echo '</div>';
echo '<div class="err-links">';
echo '<a href="' . esc_url( $services_url ) . '">جميع الخدمات</a>';
echo '<a href="' . esc_url( $cities_url ) . '">المدن</a>';
echo '<a href="' . esc_url( $blog_url ) . '">المدونة</a>';
echo '<a href="' . esc_url( $contact_url ) . '">اتصل بنا</a>';
echo '<a href="' . esc_url( $faq_url ) . '">الأسئلة الشائعة</a>';
echo '</div>';
echo '</div>';
echo '</section>';

$this->Part( 'footer' );
