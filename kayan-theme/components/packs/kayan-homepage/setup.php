<?
require_once __DIR__ . '/options-panel.php';
require_once __DIR__ . '/contact-resolver.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/section-builders.php';
require_once __DIR__ . '/content-builders.php';
require_once __DIR__ . '/demo-content.php';
require_once __DIR__ . '/kit-layout.php';
require_once __DIR__ . '/inner-builders.php';

if ( ! function_exists( 'kayan_homepage_v3_active_request' ) ) {
	function kayan_homepage_v3_active_request() {
		if ( ! empty( yc_get_option( 'kayan_homepage_v3_disable' ) ) ) {
			return false;
		}
		if ( function_exists( 'kayan_homepage_uses_new_design' ) && ! kayan_homepage_uses_new_design() ) {
			return false;
		}
		if ( is_front_page() ) {
			return true;
		}
		if ( is_home() && 'posts' === get_option( 'show_on_front' ) ) {
			return true;
		}
		return false;
	}
}

function kayan_homepage_v3_asset_version() {
	return '1.5.0';
}

if ( ! function_exists( 'kayan_homepage_inner_page_request' ) ) {
	/**
	 * Non-homepage front-end views that should use kayan-inner.css.
	 */
	function kayan_homepage_inner_page_request() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return false;
		}
		if ( function_exists( 'kayan_homepage_v3_active_request' ) && kayan_homepage_v3_active_request() ) {
			return false;
		}
		return true;
	}
}

function kayan_homepage_enqueue_shared_design_assets() {
	$base = get_template_directory_uri() . '/components/packs/kayan-homepage/assets/';
	$ver  = kayan_homepage_v3_asset_version();

	wp_enqueue_style(
		'kayan-home-fonts',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'kayan-home-fa',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);

	$home_deps = array( 'kayan-home-fonts', 'kayan-home-fa', 'kayan-locale' );

	wp_enqueue_style( 'kayan-home', $base . 'kayan-home.css', $home_deps, $ver );

	return array( 'base' => $base, 'ver' => $ver );
}

function kayan_homepage_enqueue_inner_assets() {
	if ( ! kayan_homepage_inner_page_request() ) {
		return;
	}

	$assets = kayan_homepage_enqueue_shared_design_assets();
	wp_enqueue_style( 'kayan-inner', $assets['base'] . 'kayan-inner.css', array( 'kayan-home' ), $assets['ver'] );
	wp_enqueue_script( 'kayan-home', $assets['base'] . 'kayan-home.js', array(), $assets['ver'], true );
}
add_action( 'wp_enqueue_scripts', 'kayan_homepage_enqueue_inner_assets', 6 );

if ( ! function_exists( 'kayan_homepage_inner_body_class' ) ) {
	function kayan_homepage_inner_body_class( $classes ) {
		if ( function_exists( 'kayan_homepage_inner_page_request' ) && kayan_homepage_inner_page_request() ) {
			$classes[] = 'kayan-inner-page';
			$classes[] = 'kayan-kit';
		}
		return $classes;
	}
}
add_filter( 'body_class', 'kayan_homepage_inner_body_class' );

function kayan_homepage_v3_resource_hints() {
	if ( ! kayan_homepage_v3_active_request() ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
	echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />' . "\n";
	echo '<link rel="dns-prefetch" href="//fonts.googleapis.com" />' . "\n";
	echo '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com" />' . "\n";
}
add_action( 'wp_head', 'kayan_homepage_v3_resource_hints', 0 );

function kayan_homepage_v3_enqueue_assets() {
	if ( ! kayan_homepage_v3_active_request() ) {
		return;
	}

	$base = get_template_directory_uri() . '/components/packs/kayan-homepage/assets/';
	$ver  = kayan_homepage_v3_asset_version();

	wp_enqueue_style(
		'kayan-home-fonts',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'kayan-home-fa',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);

	wp_enqueue_style( 'kayan-home', $base . 'kayan-home.css', array( 'kayan-home-fonts', 'kayan-home-fa', 'kayan-locale' ), $ver );
	wp_enqueue_script( 'kayan-home', $base . 'kayan-home.js', array(), $ver, true );

	$inline = '
.fa:not(.fa-brands):not(.fab),.fas,.fa-solid,.fa-regular,.far,i[class^="fa-"]:not(.fa-brands):not(.fab),i[class*=" fa-"]:not(.fa-brands):not(.fab){font-family:"Font Awesome 6 Free" !important;font-weight:900 !important;}
.fa-brands,.fab,.fa-brands::before,.fab::before{font-family:"Font Awesome 6 Brands" !important;font-weight:400 !important;}
.kayan-no-content-call .btn-call,
.kayan-no-content-call .fcontact a[href^="tel:"] { display: none !important; }
.kayan-no-floating-call .--YourColor--phone-button { display: none !important; }
';
	wp_add_inline_style( 'kayan-home', $inline );
}
add_action( 'wp_enqueue_scripts', 'kayan_homepage_v3_enqueue_assets', 5 );

function kayan_homepage_v3_reenqueue_assets() {
	if ( ! kayan_homepage_v3_active_request() ) {
		return;
	}
	kayan_homepage_v3_enqueue_assets();
}
add_action( 'wp_enqueue_scripts', 'kayan_homepage_v3_reenqueue_assets', 1000000 );

function kayan_homepage_v3_dequeue_legacy_assets() {
	$kit_request = kayan_homepage_v3_active_request()
		|| ( function_exists( 'kayan_homepage_inner_page_request' ) && kayan_homepage_inner_page_request() );
	if ( ! $kit_request ) {
		return;
	}
	$handles = array( 'yourcolor-init', 'yourcolor-script', 'yourcolor-owlcarousel', 'kayan-ui-fixes', 'yourcolor-setup-carousel', 'yourcolor-setup-lazy' );
	foreach ( $handles as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'kayan_homepage_v3_dequeue_legacy_assets', 100 );
add_action( 'wp_footer', 'kayan_homepage_v3_dequeue_legacy_assets', 999 );

if ( ! function_exists( 'kayan_homepage_v3_render' ) ) {
	/**
	 * Render full homepage v3 document (BeforeBlade_* + kayan-stabilization template_redirect).
	 *
	 * Pipeline:
	 * 1. Load template-parts/body.html.php
	 * 2. kayan_homepage_v3_filter_html() — {{tokens}} via builders + kayan_hp_apply_section_visibility()
	 * 3. BeforeWPHead → wp_head() → AfterWPHead (same hook order as #header/part.php)
	 * 4. Body markup + wp_footer()
	 *
	 * Echoes directly — do not nest ob_start(); lockdown may buffer template_redirect.
	 *
	 * @return void
	 */
	function kayan_homepage_v3_render() {
		$body_file = __DIR__ . '/template-parts/body.html.php';
		if ( ! file_exists( $body_file ) ) {
			return;
		}

		if ( ! function_exists( 'kayan_homepage_v3_filter_html' ) ) {
			return;
		}

		$body_html = kayan_homepage_v3_filter_html( file_get_contents( $body_file ) );

		global $ThemeStatic;
		$theme = ( isset( $ThemeStatic ) && $ThemeStatic instanceof ThemeStatic ) ? $ThemeStatic : new ThemeStatic();

		$theme->Part( 'header', array( 'bodyClass' => kayan_home_body_classes() ) );
		echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in kayan_homepage_get_tokens().
		$theme->Part( 'footer' );
	}
}

function kayan_homepage_v3_takeover() {
	if ( ! kayan_homepage_v3_active_request() ) {
		return;
	}
	kayan_homepage_v3_render();
	die();
}
add_action( 'BeforeBlade_index', 'kayan_homepage_v3_takeover', 0 );
add_action( 'BeforeBlade_page', 'kayan_homepage_v3_takeover', 0 );
