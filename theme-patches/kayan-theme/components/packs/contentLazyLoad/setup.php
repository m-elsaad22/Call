<?php
function YourColor__ContextLazyLoad( $content ) {
	if ( ! is_string( $content ) || $content === '' ) {
		return $content;
	}
	# Keep iframe src intact so YouTube/Maps render without waiting for jQuery LazyloaderHook.
	$content = preg_replace( '/(<img[^>]+)(srcset\s*=\s*[\'"]([^"\']*)[\'"])/Ui', '$1data-loader-srcset="$3"', $content );
	return $content;
}
add_filter( 'the_content', 'YourColor__ContextLazyLoad' );
add_filter( 'widget_text', 'YourColor__ContextLazyLoad' );

if ( ! function_exists( 'kayan_promote_loader_src' ) ) {
	function kayan_promote_loader_src() {
		echo '<script id="kayan-promote-loader-src">(function(){function p(){document.querySelectorAll("[data-loader-src]").forEach(function(el){var u=el.getAttribute("data-loader-src");if(!u)return;var s=el.getAttribute("src");if(!s||s==="about:blank"){el.setAttribute("src",u);}el.removeAttribute("data-loader-src");});document.querySelectorAll("[data-loader-href]").forEach(function(el){if(!el.getAttribute("href"))el.setAttribute("href",el.getAttribute("data-loader-href"));});}p();if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",p);window.addEventListener("load",p);})();</script>' . "\n";
	}
	add_action( 'wp_footer', 'kayan_promote_loader_src', 1 );
}
