<?php
if ( ! function_exists( 'kayan_i18n_show_header_lang_switcher' ) ) {
	function kayan_i18n_show_header_lang_switcher() {
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return false;
		}
		$hide = function_exists( 'yc_get_option' ) ? yc_get_option( 'kayan_hide_header_lang_switcher' ) : get_option( 'kayan_hide_header_lang_switcher' );
		return empty( $hide );
	}
}

if ( ! function_exists( 'kayan_i18n_show_header_country_switcher' ) ) {
	function kayan_i18n_show_header_country_switcher() {
		if ( function_exists( 'kayan_i18n_is_enabled' ) && ! kayan_i18n_is_enabled() ) {
			return false;
		}
		$hide = function_exists( 'yc_get_option' ) ? yc_get_option( 'kayan_hide_header_country_switcher' ) : get_option( 'kayan_hide_header_country_switcher' );
		return empty( $hide );
	}
}

if ( ! function_exists( 'kayan_i18n_render_lang_switcher' ) ) {
	/**
	 * زرّا اللغة AR / EN — يوضعان في نهاية قائمة الهيدر.
	 */
	function kayan_i18n_render_lang_switcher( $args = array() ) {
		if ( ! kayan_i18n_show_header_lang_switcher() ) {
			return;
		}

		$args   = wp_parse_args( $args, array( 'instance_suffix' => '' ) );
		$suffix = sanitize_html_class( (string) $args['instance_suffix'] );
		$lang   = function_exists( 'kayan_i18n_get_lang' ) ? kayan_i18n_get_lang() : 'ar';
		$ar_url = function_exists( 'kayan_i18n_get_localized_url' ) ? kayan_i18n_get_localized_url( 'ar' ) : home_url( '/' );
		$en_url = function_exists( 'kayan_i18n_get_localized_url' ) ? kayan_i18n_get_localized_url( 'en' ) : home_url( '/en/' );

		echo '<div class="kayan-lang-switcher" id="kayanLangSwitcher' . esc_attr( $suffix ) . '" role="group" aria-label="' . esc_attr( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'section_language', 'اللغة' ) : 'اللغة' ) . '">';
		echo '<a class="kayan-lang-link' . ( 'ar' === $lang ? ' is-active' : '' ) . '" href="' . esc_url( $ar_url ) . '" hreflang="ar" lang="ar" data-lang="ar" aria-current="' . ( 'ar' === $lang ? 'true' : 'false' ) . '">AR</a>';
		echo '<a class="kayan-lang-link' . ( 'en' === $lang ? ' is-active' : '' ) . '" href="' . esc_url( $en_url ) . '" hreflang="en" lang="en" data-lang="en" aria-current="' . ( 'en' === $lang ? 'true' : 'false' ) . '">EN</a>';
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_i18n_render_country_switcher' ) ) {
	/**
	 * مبدّل الدولة: علم + اسم الدولة الحالي، وقائمة الدول في القائمة المنسدلة.
	 */
	function kayan_i18n_render_country_switcher( $args = array() ) {
		if ( ! kayan_i18n_show_header_country_switcher() ) {
			return;
		}

		$args    = wp_parse_args( $args, array( 'instance_suffix' => '' ) );
		$suffix  = sanitize_html_class( (string) $args['instance_suffix'] );
		$wrap_id = 'kayanCountrySwitcher' . $suffix;
		$btn_id  = 'kayanCountryBtn' . $suffix;
		$drop_id = 'kayanCountryDrop' . $suffix;

		$country = function_exists( 'kayan_i18n_get_country' ) ? kayan_i18n_get_country() : 'ae';
		$lang    = function_exists( 'kayan_i18n_get_lang' ) ? kayan_i18n_get_lang() : 'ar';
		$data    = function_exists( 'kayan_i18n_get_country_data' ) ? kayan_i18n_get_country_data( $country ) : array();
		$flag    = isset( $data['flag'] ) ? $data['flag'] : '🌐';
		$label   = function_exists( 'kayan_i18n_country_label' ) ? kayan_i18n_country_label( $country, $lang ) : ( isset( $data['label_ar'] ) ? $data['label_ar'] : '' );
		$dir     = ( 'en' === $lang ) ? 'ltr' : 'rtl';

		static $script_printed = false;

		echo '<div class="kayan-country-switcher kayan-switcher-wrap" id="' . esc_attr( $wrap_id ) . '" dir="' . esc_attr( $dir ) . '">';
		echo '<button type="button" class="kayan-country-btn" id="' . esc_attr( $btn_id ) . '" aria-haspopup="true" aria-expanded="false" aria-label="' . esc_attr( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'section_country', 'الدولة' ) : 'الدولة' ) . '">';
		echo '<span class="ksw-flag" aria-hidden="true">' . esc_html( $flag ) . '</span>';
		echo '<span class="ksw-country-name">' . esc_html( $label ) . '</span>';
		echo '<i class="fas fa-chevron-down ksw-caret" aria-hidden="true"></i>';
		echo '</button>';
		echo '<div class="ksw-dropdown kayan-country-drop" id="' . esc_attr( $drop_id ) . '" role="menu" aria-hidden="true">';
		echo '<div class="ksw-arrow" aria-hidden="true"></div>';
		echo '<p class="ksw-section-label">' . esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'section_country', 'الدولة' ) : 'الدولة' ) . '</p>';
		echo '<div class="ksw-countries" role="group">';
		foreach ( kayan_i18n_get_countries() as $code => $row ) {
			$active = $code === $country ? ' is-active' : '';
			$url    = function_exists( 'kayan_i18n_build_url' ) ? kayan_i18n_build_url( $code, $lang, '/' ) : home_url( isset( $row['path'] ) ? $row['path'] : '/' );
			$name   = function_exists( 'kayan_i18n_country_label' ) ? kayan_i18n_country_label( $code, $lang ) : ( isset( $row['label_ar'] ) ? $row['label_ar'] : $code );
			echo '<a class="ksw-country-btn' . esc_attr( $active ) . '" href="' . esc_url( $url ) . '" data-country="' . esc_attr( $code ) . '" role="menuitem" aria-current="' . ( $code === $country ? 'true' : 'false' ) . '">';
			echo '<span class="ksw-btn-flag" aria-hidden="true">' . esc_html( isset( $row['flag'] ) ? $row['flag'] : '' ) . '</span>';
			echo '<span>' . esc_html( $name ) . '</span>';
			echo '</a>';
		}
		echo '</div></div></div>';

		if ( ! $script_printed ) {
			$script_printed = true;
			kayan_i18n_print_switcher_script();
		}
	}
}

if ( ! function_exists( 'kayan_i18n_render_header_switchers' ) ) {
	/**
	 * مبدّلا اللغة والدولة في نهاية قائمة الهيدر.
	 */
	function kayan_i18n_render_header_switchers( $args = array() ) {
		$show_lang    = kayan_i18n_show_header_lang_switcher();
		$show_country = kayan_i18n_show_header_country_switcher();
		if ( ! $show_lang && ! $show_country ) {
			return;
		}

		$args = wp_parse_args( $args, array( 'instance_suffix' => '' ) );
		echo '<div class="kayan-menu-switchers">';
		if ( $show_lang ) {
			kayan_i18n_render_lang_switcher( $args );
		}
		if ( $show_country ) {
			kayan_i18n_render_country_switcher( $args );
		}
		echo '</div>';
	}
}

if ( ! function_exists( 'kayan_i18n_render_switcher' ) ) {
	function kayan_i18n_render_switcher( $args = array() ) {
		kayan_i18n_render_header_switchers( $args );
	}
}

if ( ! function_exists( 'kayan_i18n_print_switcher_script' ) ) {
	function kayan_i18n_print_switcher_script() {
		?>
<script>
(function(){
"use strict";
function closeAll(except){
  document.querySelectorAll(".kayan-country-switcher").forEach(function(w){
    if(except && w===except)return;
    w.classList.remove("is-open");
    var b=w.querySelector(".kayan-country-btn"),d=w.querySelector(".kayan-country-drop");
    if(b)b.setAttribute("aria-expanded","false");
    if(d)d.setAttribute("aria-hidden","true");
  });
}
function place(drop,btn){
  if(!drop||!btn)return;
  var rect=btn.getBoundingClientRect(),dropW=Math.max(220,drop.offsetWidth||220),margin=8,left=rect.right-dropW;
  if(left<margin)left=margin;
  if(left+dropW>window.innerWidth-margin)left=window.innerWidth-dropW-margin;
  drop.style.top=(rect.bottom+margin)+"px";
  drop.style.left=left+"px";
  drop.style.right="auto";
}
document.addEventListener("click",function(e){
  var btn=e.target.closest&&e.target.closest(".kayan-country-btn");
  if(btn){
    e.preventDefault();
    e.stopPropagation();
    var wrap=btn.closest(".kayan-country-switcher");
    var open=wrap&&wrap.classList.contains("is-open");
    closeAll();
    if(!open&&wrap){
      wrap.classList.add("is-open");
      btn.setAttribute("aria-expanded","true");
      var drop=wrap.querySelector(".kayan-country-drop");
      if(drop){drop.setAttribute("aria-hidden","false");place(drop,btn);}
    }
    return;
  }
  if(!e.target.closest||!e.target.closest(".kayan-country-switcher"))closeAll();
},true);
window.addEventListener("resize",function(){closeAll();});
})();
</script>
		<?php
	}
}

if ( ! function_exists( 'kayan_i18n_get_switcher_html' ) ) {
	function kayan_i18n_get_switcher_html( $args = array() ) {
		ob_start();
		kayan_i18n_render_header_switchers( $args );
		return ob_get_clean();
	}
}
