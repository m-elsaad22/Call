<?php
/**
 * WPCode snippet (PHP, location: everywhere, auto-insert).
 * Do not keep the opening <?php tag when pasting into WPCode.
 *
 * Guards against:
 * PHP Fatal error: Uncaught Error: Call to a member function AddTaxonomy() on null
 * in kayan-theme/components/packs/taxonomies/setup.php:4
 */
function kayan_ensure_themetree_global() {
	if ( ! class_exists( 'ThemeTree' ) ) {
		if ( function_exists( 'Taxonomies' ) ) {
			remove_action( 'Initialize', 'Taxonomies', 10 );
		}
		return;
	}
	if ( isset( $GLOBALS['ThemeTree'] ) && $GLOBALS['ThemeTree'] instanceof ThemeTree ) {
		return;
	}
	$GLOBALS['ThemeTree'] = new ThemeTree();
}
add_action( 'after_setup_theme', 'kayan_ensure_themetree_global', 0 );
add_action( 'init', 'kayan_ensure_themetree_global', 0 );
add_action( 'Initialize', 'kayan_ensure_themetree_global', 0 );
