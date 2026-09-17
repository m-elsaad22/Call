<?
$Styles  = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();
$UniqId  = uniqid();
$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
$name    = $curauth ? $curauth->display_name : '';

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'    => $name,
			'subtitle' => 'مقالات الكاتب ' . $name,
		)
	);
}

kayan_kit_open_section();
echo '<div class="blog-grid kayan-inner-archive-grid -archivePage-Posts-Grid">';
$this->Part(
	'Posts',
	array(
		'AutoLoadmore' => true,
		'UniqId'       => $UniqId,
		'author'       => $curauth ? $curauth->ID : 0,
	)
);
echo '</div>';
kayan_kit_close_section();

$this->Part( 'footer', array( 'Styles' => $Styles ) );
