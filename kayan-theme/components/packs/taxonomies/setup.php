<?php 
function Taxonomies() {
	global $ThemeTree;
	$ThemeTree->AddTaxonomy('category',array('works','post'), ' تصنيفات ', array('slug'=>'category'),true);
	$ThemeTree->AddTaxonomy('city', array("post"), 'المدن', array('slug'=>get_option('cities_url')), false);
	$ThemeTree->AddTaxonomy('questions', "bot", 'الاسئلة', false, true);
}
add_action('Initialize', 'Taxonomies', 10, 3);

# Re-registering native `category` must not drop REST. Permalink slug stays `category`.
if ( ! function_exists( 'kayan_preserve_category_rest' ) ) {
	function kayan_preserve_category_rest( $args, $taxonomy ) {
		if ( 'category' !== $taxonomy ) {
			return $args;
		}
		$args['show_in_rest'] = true;
		$args['rest_base']    = 'categories';
		return $args;
	}
}
add_filter( 'register_taxonomy_args', 'kayan_preserve_category_rest', 20, 2 );