<?php 
function Taxonomies() {
	global $ThemeTree;
	if ( ! ( $ThemeTree instanceof ThemeTree ) ) {
		if ( isset( $GLOBALS['ThemeTree'] ) && $GLOBALS['ThemeTree'] instanceof ThemeTree ) {
			$ThemeTree = $GLOBALS['ThemeTree'];
		} elseif ( class_exists( 'ThemeTree' ) ) {
			$ThemeTree = $GLOBALS['ThemeTree'] = new ThemeTree();
		} else {
			return;
		}
	}
	$ThemeTree->AddTaxonomy('category',array('works','post'), ' تصنيفات ', array('slug'=>'category'),true);
	$ThemeTree->AddTaxonomy('questions', "bot", 'الاسئلة', false, true);
}
add_action('Initialize', 'Taxonomies', 10, 3);

# Cities live only on `cities` (`/city/{slug}/`). Do not re-register legacy `city`.
if ( ! function_exists( 'kayan_unregister_legacy_city_taxonomy' ) ) {
	function kayan_unregister_legacy_city_taxonomy() {
		if ( taxonomy_exists( 'city' ) ) {
			unregister_taxonomy( 'city' );
		}
	}
}
add_action( 'Initialize', 'kayan_unregister_legacy_city_taxonomy', 20 );
add_action( 'init', 'kayan_unregister_legacy_city_taxonomy', 30 );

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
if ( ! function_exists( 'kayan_keep_city_rewrite_on_cities' ) ) {
	function kayan_keep_city_rewrite_on_cities( $args, $taxonomy ) {
		if ( 'cities' === $taxonomy ) {
			$args['rewrite'] = array(
				'slug'         => 'city',
				'with_front'   => false,
				'hierarchical' => true,
			);
			return $args;
		}
		if ( isset( $args['rewrite'] ) && is_array( $args['rewrite'] ) && isset( $args['rewrite']['slug'] ) && 'city' === $args['rewrite']['slug'] && 'cities' !== $taxonomy ) {
			$args['rewrite']['slug'] = $taxonomy;
		}
		return $args;
	}
}
add_filter( 'register_taxonomy_args', 'kayan_preserve_category_rest', 20, 2 );
add_filter( 'register_taxonomy_args', 'kayan_keep_city_rewrite_on_cities', 30, 2 );