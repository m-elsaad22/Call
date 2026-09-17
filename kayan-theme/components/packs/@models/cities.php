<?
global $post;

$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();

$page_title = get_the_title( $post );
$custom_meta = function_exists( 'kayan_seo_get_post_seo_description' )
	? kayan_seo_get_post_seo_description( $post->ID )
	: get_post_meta( $post->ID, 'kayan_meta_description', true );
$intro = ! empty( $custom_meta ) ? $custom_meta : $post->post_content;
$hero_sub = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $intro, 180 ) : wp_trim_words( wp_strip_all_tags( $intro ), 28, '…' );

$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$cities = get_terms(
	array(
		'taxonomy'   => 'city',
		'number'     => 60,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);
$cities     = is_array( $cities ) ? $cities : array();
$city_count = count( $cities );

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $page_title,
			'subtitle'  => $hero_sub,
			'image_url' => $page_background,
			'meta_html' => $city_count > 0 ? '<div><b>' . (int) $city_count . '</b><small>مدينة</small></div>' : '',
		)
	);
}

kayan_kit_open_section();
echo '<div class="toolbar">';
echo '<div class="search-box"><i class="fas fa-search"></i><input type="search" data-kit-search=".citycard" placeholder="ابحث عن مدينة…" /></div>';
echo '<div class="results">نخدم <b>' . (int) $city_count . '</b> مدينة</div>';
echo '</div>';

echo '<div class="city-grid">';
if ( empty( $cities ) ) {
	echo '<p>لم تُضف مدن بعد. أضف مدناً من لوحة التحكم → المقالات → المدن.</p>';
} else {
	foreach ( $cities as $city ) {
		$city_url = get_term_link( $city );
		if ( is_wp_error( $city_url ) ) {
			continue;
		}
		$city_image = function_exists( 'kayan_seo_get_term_image_url' ) ? kayan_seo_get_term_image_url( $city->term_id ) : '';
		$style      = $city_image ? ' style="background-image:url(' . esc_url( $city_image ) . ');background-size:cover;background-position:center"' : '';
		echo '<a class="citycard filt" href="' . esc_url( $city_url ) . '"' . $style . '>';
		echo '<span class="ccbadge">' . (int) $city->count . ' خدمة</span>';
		echo '<div class="cc-in"><h3>' . esc_html( $city->name ) . '</h3>';
		echo '<small><i class="fas fa-location-dot"></i> ' . esc_html( $city->name ) . '</small></div>';
		echo '</a>';
	}
}
echo '</div>';
kayan_kit_close_section();

$this->Part( 'footer', array( 'Styles' => $Styles ) );
