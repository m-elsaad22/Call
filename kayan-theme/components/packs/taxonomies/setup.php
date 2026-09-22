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
		if ( empty( $args['rest_base'] ) ) {
			$args['rest_base'] = 'categories';
		}
		return $args;
	}
}
add_filter( 'register_taxonomy_args', 'kayan_preserve_category_rest', 20, 2 );

if ( ! function_exists( 'kayan_preserve_category_rest_object' ) ) {
	function kayan_preserve_category_rest_object() {
		global $wp_taxonomies;
		if ( ! isset( $wp_taxonomies['category'] ) ) {
			return;
		}
		$wp_taxonomies['category']->show_in_rest = true;
		if ( empty( $wp_taxonomies['category']->rest_base ) ) {
			$wp_taxonomies['category']->rest_base = 'categories';
		}
	}
}
add_action( 'init', 'kayan_preserve_category_rest_object', 99 );