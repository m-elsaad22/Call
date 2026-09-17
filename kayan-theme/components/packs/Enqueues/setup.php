<?
$footer_pack = urlencode( '#footer' );
$footer_uri  = get_template_directory_uri() . '/components/packs/' . $footer_pack . '/js/';

function kayan_enqueue_theme_footer_scripts() {
	global $footer_uri;

	if ( function_exists( 'kayan_homepage_v3_active_request' ) && kayan_homepage_v3_active_request() ) {
		return;
	}

	wp_enqueue_script( 'yourcolor-script', $footer_uri . 'jquery-3.4.1.min.js', array(), null, true );

	$needs_carousel = ! function_exists( 'IsSpeed' ) || ! IsSpeed();
	if ( $needs_carousel ) {
		wp_enqueue_script( 'yourcolor-owlcarousel', $footer_uri . 'owl.carousel.min.js', array( 'yourcolor-script' ), null, true );
		wp_enqueue_script( 'yourcolor-setup-carousel', $footer_uri . 'setup-carousel.js', array( 'yourcolor-script', 'yourcolor-owlcarousel' ), null, true );
	}

	$deps = array( 'yourcolor-script' );
	if ( $needs_carousel ) {
		$deps[] = 'yourcolor-setup-carousel';
	}
	if ( ( ! function_exists( 'IsSpeed' ) || ! IsSpeed() ) && file_exists( get_template_directory() . '/components/packs/' . urlencode( '#footer' ) . '/js/setup-lazy.js' ) ) {
		wp_enqueue_script( 'yourcolor-setup-lazy', $footer_uri . 'setup-lazy.js', array( 'yourcolor-script' ), null, true );
		$deps[] = 'yourcolor-setup-lazy';
	}

	wp_enqueue_script( 'yourcolor-init', $footer_uri . 'setup.js', $deps, null, true );
}
add_action( 'wp_footer', 'kayan_enqueue_theme_footer_scripts', 5 );

function disable_unnecessary_scripts() {
	wp_dequeue_script( 'wp-embed' );
	wp_dequeue_script( 'wp-emoji' );
	wp_dequeue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'disable_unnecessary_scripts', 999 );

function disable_classic_theme_styless() {
	wp_deregister_style( 'classic-theme-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wp-block-library-rtl-css' );
}
if ( ! is_admin() ) {
	add_filter( 'wp_enqueue_scripts', 'disable_classic_theme_styless', 100 );
}

function remove_youtube_embed_scripts() {
	wp_deregister_style( 'wp-youtube-embed' );
	wp_deregister_script( 'wp-youtube-embed' );
	wp_deregister_script( 'base' );
}
add_action( 'wp_enqueue_scripts', 'remove_youtube_embed_scripts', 999 );

function kayan_theme_script_whitelist() {
	return array(
		'yourcolor-script',
		'yourcolor-owlcarousel',
		'yourcolor-setup-carousel',
		'yourcolor-setup-lazy',
		'yourcolor-init',
		'kayan-home',
		'kayan-home-fonts',
		'kayan-home-fa',
		'kayan-inner',
		'kayan-ui-fixes',
	);
}

function disable_all_scripts() {
	if ( function_exists( 'kayan_homepage_v3_active_request' ) && kayan_homepage_v3_active_request() ) {
		return;
	}

	global $wp_scripts;
	$whitelist = kayan_theme_script_whitelist();

	foreach ( (array) $wp_scripts->queue as $handle ) {
		if ( in_array( $handle, $whitelist, true ) ) {
			continue;
		}
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}

	global $wp_styles;
	$style_whitelist = array( 'kayan-home', 'kayan-home-fonts', 'kayan-home-fa', 'kayan-inner', 'kayan-locale' );
	foreach ( (array) $wp_styles->queue as $handle ) {
		if ( in_array( $handle, $style_whitelist, true ) ) {
			continue;
		}
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'disable_all_scripts', 999999 );

add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
	wp_dequeue_style( 'wp-block-library' );
}

function remove_script_version( $src ) {
	if ( strpos( $src, 'ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );

function remove_css_version( $src ) {
	if ( strpos( $src, 'ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'remove_css_version', 15, 1 );

function removeAppleTouchIcon() {
	remove_action( 'wp_head', 'wp_site_icon', 99 );
}
add_action( 'init', 'removeAppleTouchIcon' );

function update_protocol_links( $html ) {
	$html = preg_replace_callback(
		'/(src|href)=[\"\']\/\/(.*?)[\"\']/',
		function ( $match ) {
			return $match[1] . '="https://' . $match[2] . '"';
		},
		$html
	);
	$html = preg_replace_callback(
		'/Permissions-Policy: (.*)\r\n/',
		function ( $match ) {
			$policies           = explode( ',', $match[1] );
			$supported_policies = array( 'geolocation', 'midi', 'notifications', 'push', 'sync-xhr', 'microphone', 'camera', 'magnetometer', 'gyroscope', 'speaker', 'vibrate', 'fullscreen', 'payment', 'usb', 'accelerometer', 'vr', 'xr-spatial-tracking' );
			$filtered_policies  = array_filter(
				$policies,
				function ( $policy ) use ( $supported_policies ) {
					return in_array( trim( $policy ), $supported_policies, true );
				}
			);
			return 'Permissions-Policy: ' . implode( ',', $filtered_policies ) . "\r\n";
		},
		$html
	);
	$html = str_replace( array( 'ch-ua-form-factor', 'ch-ua-mobile' ), '', $html );
	return $html;
}

function orderHeader( $test ) {
	ob_start();
}

function orderFooter( $test ) {
	$html = ob_get_clean();
	$dom  = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
	$xpath = new DOMXpath( $dom );
	$html  = update_protocol_links( $html );
	$html  = str_replace( 'target="_blank"', 'target="_blank" rel="nofollow noopener noreferrer"', $html );
	echo $html;
}

add_action( 'BeforeHeader', 'orderHeader' );
add_action( 'AfterFooter', 'orderFooter' );

function remove_wp_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_wp_emoji' );
}
add_action( 'init', 'remove_wp_emoji' );

function disable_wp_emoji( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}
	return array();
}
