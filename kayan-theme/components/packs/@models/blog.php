<?
$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();
$UniqId = uniqid();

$raw_content = $post->post_content;
$hero_sub    = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $raw_content, 180 ) : wp_trim_words( wp_strip_all_tags( $raw_content ), 28, '…' );
$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$cats = get_terms(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
		'number'     => 12,
	)
);
$cats = is_array( $cats ) ? $cats : array();

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

kayan_kit_open_section();

echo '<div class="toolbar">';
echo '<div class="search-box"><i class="fas fa-search"></i><input type="search" data-kit-search=".bcard,-Post-box-single-item" placeholder="ابحث في المقالات…" /></div>';
if ( ! empty( $cats ) ) {
	echo '<div class="pillbar">';
	echo '<button type="button" class="active" data-f="all" data-target=".bcard,.-Post-box-single-item">الكل</button>';
	foreach ( $cats as $cat ) {
		echo '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
	}
	echo '</div>';
}
echo '</div>';

echo '<div class="blog-grid kayan-inner-archive-grid -archivePage-Posts-Grid">';
$this->Part(
	'Posts',
	array(
		'object__type'           => 'posts',
		'object__name'           => 'post',
		'part_object__name'      => 'post',
		'part__name'             => 'Post-box',
		'ScrollLoader'           => true,
		'per'                    => 9,
		'show___empty__part'     => 'object--empty',
		'data___empty__part'     => array(
			'__empty_icon'             => '<i class="fa-solid fa-ban"></i>',
			'__empty_title'            => 'لن يتم العثور على المقالات ',
			'__empty_description'      => '<a href="' . esc_url( home_url() ) . '">الرئيسية </a>',
			'__Ajax_empty_title'       => 'لقد شاهدت جميع الالمقالات',
			'__Ajax_empty_description' => 'تم عرض جميع المقالات <a href="' . esc_url( home_url() ) . '">الرئيسية </a>',
		),
	)
);
echo '</div>';

kayan_kit_close_section();
$this->Part( 'footer', array( 'Styles' => $Styles ) );
