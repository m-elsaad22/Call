<?
$search_query = trim( get_search_query() );
$Styles       = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();

global $wpdb;
$Value_search_query = '%' . $wpdb->esc_like( $search_query ) . '%';
$myposts            = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s AND post_status = 'publish'", $Value_search_query ) );

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'    => $search_query !== '' ? 'نتائج البحث عن «' . $search_query . '»' : 'البحث',
			'subtitle' => ! empty( $myposts ) ? count( $myposts ) . ' نتيجة' : 'لم يتم العثور على نتائج مطابقة',
		)
	);
}

kayan_kit_open_section();
echo '<div class="blog-grid kayan-inner-archive-grid -archivePage-Posts-Grid">';
if ( ! empty( $myposts ) ) {
	$post_ids = array();
	foreach ( $myposts as $mypost ) {
		$post_ids[] = $mypost->ID;
	}
	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => 28,
		'post__in'       => array_values( $post_ids ),
	);
	$this->Part(
		'Posts',
		array(
			'object__type'       => 'posts',
			'object__name'       => 'post',
			'part_object__name'  => 'post',
			'part__name'         => 'Post-box',
			'ScrollLoader'       => true,
			'PostsArguments'     => $args,
			'per'                => 20,
			'show___empty__part' => 'object--empty',
			'data___empty__part' => array(
				'__empty_icon'             => '<i class="fa-solid fa-ban"></i>',
				'__empty_title'            => 'لم يتم العثور  علي  "' . $search_query . '"',
				'__empty_description'      => '<a href="' . esc_url( home_url() ) . '">الرئيسية </a>',
				'__Ajax_empty_title'       => 'لم يتم العثور  علي  "' . $search_query . '"',
				'__Ajax_empty_description' => '<a href="' . esc_url( home_url() ) . '">الرئيسية </a>',
			),
		)
	);
} else {
	$this->Blade(
		'empty__objects',
		array(
			'__empty_icon'             => '<i class="fa-solid fa-ban"></i>',
			'__empty_title'            => 'لم يتم العثور  علي  "' . $search_query . '"',
			'__empty_description'      => '<a href="' . esc_url( home_url() ) . '">الرئيسية </a>',
			'__Ajax_empty_title'       => 'لم يتم العثور  علي  "' . $search_query . '"',
			'__Ajax_empty_description' => '<a href="' . esc_url( home_url() ) . '">الرئيسية </a>',
		),
		'object--empty'
	);
}
echo '</div>';
kayan_kit_close_section();

$this->Part( 'footer', array( 'Styles' => $Styles ) );
