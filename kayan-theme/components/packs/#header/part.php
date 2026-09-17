<?
ob_start();

if( !isset( $bodyClass ) ) $bodyClass = '';
# INTRO OPTIONS .

$site_color = yc_get_option('site_color'); 
$text_Color = yc_get_option('text_Color');
$kayan_skip_legacy_assets = (
	( function_exists( 'kayan_homepage_v3_active_request' ) && kayan_homepage_v3_active_request() )
	|| ( function_exists( 'kayan_homepage_inner_page_request' ) && kayan_homepage_inner_page_request() )
);
$kayan_kit_skip_style_files = array( 'kayan-home.css', 'kayan-inner.css' );
$kayan_seo_active = function_exists( 'kayan_seo_is_enabled' ) && kayan_seo_is_enabled();
$kayan_global_gradient = function_exists( 'kayan_get_global_gradient_css' ) ? kayan_get_global_gradient_css() : '';
$kayan_global_shadow = function_exists( 'kayan_global_shadows_to_css' ) ? kayan_global_shadows_to_css( kayan_get_global_shadows_option() ) : '';

if( !isset($_GET['ajax']) ) {
	echo '<!DOCTYPE html>';
	echo '<html lang="ar" dir="rtl">';
	echo '<head>';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<meta charset="utf-8">';
		if ( function_exists( 'kayan_perf_render_resource_hints' ) ) {
			kayan_perf_render_resource_hints();
		}
		if ( $kayan_seo_active ) {
			// Title + meta emitted via kayan-seo/frontend.php on wp_head.
		} else {
			if ( function_exists( 'kayan_seo_render_verification_meta' ) ) {
				kayan_seo_render_verification_meta();
			}
			if ( function_exists( 'kayan_seo_render_head_meta' ) && kayan_seo_is_enabled() ) {
				kayan_seo_render_head_meta();
			} else {
				$hide__description_show = yc_get_option('hide__description_show');
				if( empty( $hide__description_show ) || empty( $hide__description_show ) ) {
					echo'<meta name="description" content="'.esc_attr( get_bloginfo("name") ).'">';
				}
			}
		}


		//
		$hide__theme_seo = yc_get_option('hide__theme_seo');
		if( empty( $hide__theme_seo ) && ! $kayan_seo_active ) (new ThemeSeo)->Title();

		do_action('BeforeWPHead');
		// KAYAN hotfix: force Font Awesome Free solid icons to render on frontend.
		echo '<style id="kayan-fa-free-hotfix">';
			echo '.fa:not(.fa-brands):not(.fab),.fas,.fa-solid,.fa-regular,.far,i[class^="fa-"]:not(.fa-brands):not(.fab),i[class*=" fa-"]:not(.fa-brands):not(.fab),span[class^="fa-"]:not(.fa-brands):not(.fab),span[class*=" fa-"]:not(.fa-brands):not(.fab){font-family:"Font Awesome 6 Free" !important;font-weight:900 !important;}';
			echo '.fa:not(.fa-brands):not(.fab)::before,.fas::before,.fa-solid::before,.fa-regular::before,.far::before,i[class^="fa-"]:not(.fa-brands):not(.fab)::before,i[class*=" fa-"]:not(.fa-brands):not(.fab)::before,span[class^="fa-"]:not(.fa-brands):not(.fab)::before,span[class*=" fa-"]:not(.fa-brands):not(.fab)::before{font-family:"Font Awesome 6 Free" !important;font-weight:900 !important;}';
			echo '.fa-brands,.fab,.fa-brands::before,.fab::before{font-family:"Font Awesome 6 Brands" !important;font-weight:400 !important;}';
			echo '[class*="fa-"]:not(.fa-brands):not(.fab)::before,[class*="fa-"]:not(.fa-brands):not(.fab)::after{font-weight:900;}';
		echo '</style>';


		// Font Awesome — skip on v3 homepage (enqueued by kayan-homepage pack).
		if ( ! $kayan_skip_legacy_assets && ( IsSpeed() == false ) ) {
			echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">';
		}
		echo ( ( IsSpeed() == false && ( is_single() || is_page() || ( isset( $Widgets__list ) && in_array( 'works_v1',$Widgets__list ) ) ) ) ) ? '<link rel="stylesheet" data-loader-href="https://unpkg.com/photoswipe@5.2.2/dist/photoswipe.css">' : '';

		if( isset( $HeadCode ) && !empty( $HeadCode ) ){
			$kayan_head_injection = $HeadCode;
		}else{
			$kayan_head_injection = yc_get_option('header___codes');
		}
		if ( function_exists( 'kayan_lockdown_filter_header_injection' ) ) {
			$kayan_head_injection = kayan_lockdown_filter_header_injection( $kayan_head_injection );
		}
		if ( ! empty( $kayan_head_injection ) ) {
			echo $kayan_head_injection;
		}
		
		wp_head();

		$Styles['forms'] = 'forms.css';

       	$ips = ( is_array( yc_get_option( "open_css" ) ) ) ? yc_get_option( "open_css" ) : array();
		if( isset($_GET['open__css']) ) {
	       $ips[$_SERVER['REMOTE_ADDR']] = true;
	       yc_update_option("open_css", $ips);
		}

		if( isset( $ips[ $_SERVER['REMOTE_ADDR'] ]) ) {
			echo '<style>';
				if ( ! $kayan_skip_legacy_assets ) {
					$this->Part('fonts');
				}
			echo '</style>';
			if(isset($Styles)){
				foreach ($Styles as $skey => $meky) {
					if ( $kayan_skip_legacy_assets && in_array( $meky, $kayan_kit_skip_style_files, true ) ) {
						continue;
					}
					echo '<link rel="stylesheet" data-style-ajax="'.$skey.'" type="text/css" href="'.$this->StylesURL.$meky.'?v='.rand().'" />';
				}
			}

			if ( ! $kayan_skip_legacy_assets ) {
				echo '<link rel="stylesheet" data-style-ajax="main" type="text/css" href="'.$this->StylesURL.'main.css?v='.rand().'" />';
				echo '<link rel="stylesheet" data-style-ajax="hover" type="text/css" href="'.$this->StylesURL.'hover.css?v='.rand().'" />';
				echo '<link rel="stylesheet" data-style-ajax="responsive" type="text/css" href="'.$this->StylesURL.'responsive.css?v='.rand().'" />';
			}
		}else{
		    echo '<style>';
				if ( ! $kayan_skip_legacy_assets ) {
					$this->Part('fonts');
			    	require ($this->StylesPath."/main.css");
			    	require ($this->StylesPath."/hover.css");
			    	require ($this->StylesPath."/responsive.css");
				}
				if(isset($Styles)){
					foreach ($Styles as $skey => $meky) {
						if ( $kayan_skip_legacy_assets && in_array( $meky, $kayan_kit_skip_style_files, true ) ) {
							continue;
						}
						$style_file = $this->StylesPath.$meky;
						if ( is_file( $style_file ) ) {
			    			require $style_file;
						}
					}
				}
		    echo '</style>';			
		}

		do_action('AfterWPHead');
		if( !empty( yc_get_option('favicon') ) ) {
			echo '<link rel="shortcut icon" type="image/png" href="'.yc_get_option('favicon').'">';
		}

		echo '<meta name="apple-mobile-web-app-title" content="'.get_bloginfo('name').'">';
		echo '<meta http-equiv="Cache-control" content="public">';
		echo '<meta name="application-name" content="'.get_bloginfo('name').'">';
		
		// 2. إيقاف سطر التحذير
		// echo '<link rel="preload" as="font">';
		
		echo '<meta name="msapplication-TileColor" content="#a03576">';
		//
		echo '<style>';
			echo 'body { ';
				echo ( ( !empty( $site_color ) ) ) ? '--uicolor:'.$site_color.';' : '';
				echo ( ( !empty( $text_Color ) ) ) ? '--primary-text:'.$text_Color.';' : '';
				echo ( ( !empty( $kayan_global_gradient ) ) ) ? '--kayan-global-gradient:'.$kayan_global_gradient.';' : '';
				echo ( ( !empty( $kayan_global_shadow ) ) ) ? '--kayan-global-shadow:'.$kayan_global_shadow.';' : '';
			echo '}';
		echo '</style>';
	echo '</head>';
	$kayan_body_classes = trim( 'before-start ' . $bodyClass );
	if ( function_exists( 'get_body_class' ) ) {
		$kayan_body_classes = implode(
			' ',
			array_unique(
				array_filter(
					array_merge(
						array( 'before-start' ),
						get_body_class( $bodyClass )
					)
				)
			)
		);
	}
	echo '<body mode="light" class="' . esc_attr( $kayan_body_classes ) . '">';
	do_action('yc_hook_body_start');
}

if ( function_exists( 'kayan_homepage_build_logo_html' ) && function_exists( 'kayan_homepage_build_nav_links_html' ) ) {
	$kayan_header_logo = kayan_homepage_build_logo_html( 'header', 'logo' );
	$kayan_header_nav  = kayan_homepage_build_nav_links_html();
	$kayan_mobile_nav  = function_exists( 'kayan_homepage_build_mobile_nav_html' ) ? kayan_homepage_build_mobile_nav_html() : $kayan_header_nav;
	$kayan_wa_url      = function_exists( 'kayan_hp_resolve_whatsapp_url' ) ? kayan_hp_resolve_whatsapp_url() : '#';
	$kayan_wa_label    = function_exists( 'kayan_homepage_ui_string' ) ? kayan_homepage_ui_string( 'btn_whatsapp', 'واتساب' ) : 'واتساب';
	$kayan_menu_label  = function_exists( 'kayan_homepage_ui_string' ) ? kayan_homepage_ui_string( 'menu_open', 'القائمة' ) : 'القائمة';
	$kayan_close_label = function_exists( 'kayan_homepage_ui_string' ) ? kayan_homepage_ui_string( 'menu_close', 'إغلاق' ) : 'إغلاق';
	$kayan_switcher    = function_exists( 'kayan_i18n_get_switcher_html' ) ? kayan_i18n_get_switcher_html( array( 'instance_suffix' => 'Header' ) ) : '';

	$kayan_hide_search = yc_get_option( 'hide_search' );
	$kayan_search_placeholder = yc_get_option( 'search_placeholder' );
	if ( empty( $kayan_search_placeholder ) ) {
		$kayan_search_placeholder = 'ابحث عن خدمة أو مقال…';
	}

	echo '<root>';
		echo '<header id="hdr" class="fixedintro kayan-site-header">';
			echo '<div class="wrap nav">';
				echo $kayan_header_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<nav class="menu" aria-label="القائمة الرئيسية">' . $kayan_header_nav . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div class="nav-cta">';
					echo $kayan_switcher; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( empty( $kayan_hide_search ) ) {
						echo '<button type="button" class="icon-btn" onclick="toggleKitSearch(true)" aria-label="بحث"><i class="fas fa-search" aria-hidden="true"></i></button>';
					}
					echo '<a href="' . esc_url( $kayan_wa_url ) . '" class="btn btn-wa kayan-header-wa" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp" aria-hidden="true"></i> <span>' . esc_html( $kayan_wa_label ) . '</span></a>';
					echo '<button type="button" class="icon-btn" onclick="toggleMob(true)" aria-label="' . esc_attr( $kayan_menu_label ) . '"><i class="fas fa-bars" aria-hidden="true"></i></button>';
				echo '</div>';
			echo '</div>';
		echo '</header>';
		echo '<div class="mob" id="mob">';
			echo '<button class="mob-close" onclick="toggleMob(false)" aria-label="' . esc_attr( $kayan_close_label ) . '"><i class="fas fa-xmark" aria-hidden="true"></i></button>';
			echo $kayan_mobile_nav; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
		if ( empty( $kayan_hide_search ) ) {
			echo '<div id="kayanSearch" role="dialog" aria-modal="true" aria-label="بحث" onclick="if(event.target===this)toggleKitSearch(false)">';
			echo '<form action="' . esc_url( home_url( '/' ) ) . '" method="get">';
			echo '<input type="search" name="s" placeholder="' . esc_attr( $kayan_search_placeholder ) . '" aria-label="بحث" />';
			echo '<button type="submit" class="btn btn-quote"><i class="fas fa-search"></i></button>';
			echo '<button type="button" class="icon-btn" onclick="toggleKitSearch(false)" aria-label="إغلاق"><i class="fas fa-xmark"></i></button>';
			echo '</form></div>';
		}
} else {
# LOGO EDITS .
	$logo__data = yc_get_option( 'logo__data' );
	$logo__data = ( ( is_array( $logo__data ) ) ) ? $logo__data : array();
	if( empty( $logo__data ) || empty( $logo__data ) && ( !isset( $logo__data['logo__mode'] ) || ( isset( $logo__data['logo__mode'] ) && isset( $logo__data[ $logo__data['logo__mode'] ] ) ) ) ) 
		$logo__data = array( 'logo__mode'=>'Text','logo__mode'=>array('logo_Text'=>'YOUR{%COLOR%}') );

# SEARCHING AREA EDITS .
	$hide_search = yc_get_option( 'hide_search' );
	if( empty( $hide_search ) ) {
		$search_placeholder = yc_get_option('search_placeholder');
		$search_title = yc_get_option('search_title');
		$search__Button = yc_get_option('search__Button');
		$searchButtonType = 'icon';

	}
		if( !empty( $search__Button ) && isset( $search__Button['button_mode'] ) && $search__Button['button_mode'] == 'Text' && isset( $search__Button[ $search__Button['button_mode'] ] ) ) {
			$Text_search_button = $search__Button[ $search__Button['button_mode'] ]['logo_Text'];
			$searchButtonType = $search__Button['button_mode'];
		}else{
		    // 3. أيقونة البحث المجانية
			$Text_search_button = '<i class="fa-solid fa-magnifying-glass"></i>';
		}
# HIDE SOCIAL EDITS .
$hide_social_header = yc_get_option( 'hide_social_header' );
if( empty( $hide_social_header ) ) {
	$social_header_list = yc_get_option('social_header_list');
	$social_header_list = ( ( is_array( $social_header_list ) ) ) ? $social_header_list : array();		
}
$contact_icon = yc_get_option('contact_icon');
$contact_title = yc_get_option('contact_title');
$contact_number = yc_get_option('contact_number');
echo '<root>';
	echo '<header class="fixedintro">';
		echo '<div class="-Header-Fix">';
			echo '<div class="container">';

				echo '<div class="-mobile-menu-button background">';
				  	echo '<div class="menu__icon">';
				    	echo '<span></span>';
				    	echo '<span></span>';
				    	echo '<span></span>';
				  	echo '</div>';
				echo '</div>';
				# SITE LOGO	
				if( isset( $logo__data['logo__mode'] ) && $logo__data['logo__mode'] == 'Image' && isset( $logo__data[ $logo__data['logo__mode'] ] ) && isset( $logo__data[ $logo__data['logo__mode'] ]['image_logo'] ) && isset( $logo__data[ $logo__data['logo__mode'] ]['image_logo_id'] ) ){
					$logo__data[ $logo__data['logo__mode'] ]['header__alt'] = ( ( isset( $logo__data[ $logo__data['logo__mode'] ]['header__alt'] ) ) ) ? $logo__data[ $logo__data['logo__mode'] ]['header__alt'] : get_bloginfo('name');
					echo '<div class="-site-logo --logo-'.$logo__data['logo__mode'].' '.( ( empty( $hide_social_header ) || empty( $hide_search ) ) ? ' active':'').'">';					
					    echo '<a href="'.home_url().'" title="'.$logo__data[ $logo__data['logo__mode'] ]['header__alt'].'">';
					        echo YC_get_attachment(
					        	array(
						            'alt' =>$logo__data[ $logo__data['logo__mode'] ]['header__alt'],
						            'id'=>$logo__data[ $logo__data['logo__mode'] ]['image_logo_id'],
						            'alt'=>$logo__data[ $logo__data['logo__mode'] ]['header__alt'],
						            'size'=>'logo__size',
						        )
						    );
					    echo '</a>';
					echo '</div>';
				}else if( isset( $logo__data['logo__mode'] ) &&  $logo__data['logo__mode'] == 'Text' ){

					$logo__data[ $logo__data['logo__mode'] ]['header__alt'] = ( ( isset( $logo__data[ $logo__data['logo__mode'] ]['header__alt'] ) ) ) ? $logo__data[ $logo__data['logo__mode'] ]['header__alt'] : get_bloginfo('name');

						if( isset( $logo__data[ $logo__data['logo__mode'] ]['logo_Text'] ) && strpos( $logo__data[ $logo__data['logo__mode'] ]['logo_Text'],'{%') !== FALSE && strpos( $logo__data[ $logo__data['logo__mode'] ]['logo_Text'],'%}') !== FALSE ){
                            $logo__data[$logo__data['logo__mode']]['logo_Text'] = '<span class="first-logo-word">' . $logo__data[$logo__data['logo__mode']]['logo_Text'] . '</span>';
                            $logo__data[$logo__data['logo__mode']]['logo_Text'] = str_replace('{%', '</span><strong style="color:'.$logo__data[$logo__data['logo__mode']]['secondary_color'].'" class="second-logo-word">', $logo__data[$logo__data['logo__mode']]['logo_Text']);
							$logo__data[ $logo__data['logo__mode'] ]['logo_Text'] = str_replace('%}','</strong>',$logo__data[ $logo__data['logo__mode'] ]['logo_Text']);
						}
						echo '<div class="-site-logo --logo-'.$logo__data['logo__mode'].'">';
							echo'<div class="main-header"></div>';
							echo '<a href="'.home_url().'" title="'.$logo__data[ $logo__data['logo__mode'] ]['header__alt'].'">'.$logo__data[ $logo__data['logo__mode'] ]['logo_Text'].'</a>';
						echo '</div>';
				}
				echo '<div class="--Site--Menu">';
					wp_nav_menu(
				       	array(
							'theme_location' => 'main-menu',
							'menu'           => '',
							'container'      => '',
							'container_class' => '',
							'container_id'   => '',
							'menu_class'     => 'nav navbar-nav menu-master',
							'menu_id'        => '',
							'echo'           => true,
							'fallback_cb'    => 'wp_page_menu',
							'before'         => '',
							'after'          => '',
							'link_before'    => '',
							'link_after'     => '',
							'items_wrap'     => '<ul>%3$s</ul>',
							'depth'          => 0,
							'walker'         => '',
					   	)
					);
					$hide_contact__header = yc_get_option('hide_contact__header');
					if( empty($hide_contact__header) ) 
					$contact_header_list = yc_get_option('contact_header_list');
					if( empty( $hide_contact__header) && !empty($contact_header_list) ){
						$contact_header_list = ( ( is_array( $contact_header_list ) ) ) ? $contact_header_list : array();
						echo '<div class="--company-menu-mobile">';
							echo '<span>روابط المشاركة</span>';	
							echo '<ul class="-company-contact-minibox">';
								$SocialIcons = array(
									'phonenumber'=>'<i class="fa-solid fa-phone"></i>',
									'company__adress'=>'<i class="fa-solid fa-location-dot"></i>',
									'whatsapp'=>'<i class="fa-brands fa-whatsapp"></i>',
									'company__mail'=>'<i class="fa-solid fa-envelope"></i>',
								);
								foreach ( $contact_header_list as $social__item ) {
									if ( $social__item === 'phonenumber' && ( ! function_exists( 'kayan_ui_show_call_button' ) || ! kayan_ui_show_call_button() ) ) {
										continue;
									}
									$social_value = yc_get_option($social__item);
									if( !empty($social_value) ) {
										$URL__value = $social_value;
										$Name__value = $social_value;
										if( $social__item == 'whatsapp_number' ) {
											$URL__value = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $social_value ) : "https://wa.me/{$social_value}";
											$Name__value = 'تواصل عبر الواتساب ';
											$social__item = 'whatsapp';
										}
										if( $social__item == 'phonenumber' ) {
											$URL__value = "tel:{$social_value}";
											$Name__value = $social_value;
										}
										if( $social__item == 'company__mail' || $social__item == 'company__adress' ) {
											$URL__value = '#';
											$Name__value = $social_value;
										}

										echo '<li class="'.$social__item.'">';
											echo '<a target="_blank" href="'.$URL__value.'"  title="'.$Name__value.'">';
												echo $SocialIcons[ $social__item ];
												echo '<span>'.$Name__value.'</span>';
											echo '</a>';
										echo '</li>';
									}
								}

							echo '<ul>';
						echo '</div>';
						##
					}
				echo '</div>';
				echo '<div class="header--Tools">';

					# SEARCH
					echo '<div class="all-searsh-in">';
						if( empty( $hide_search ) ){
							if( empty( $search_placeholder ) ) $search_placeholder = 'ابحث';
							echo '<div class="--open--searching --search--buttonType-'.$searchButtonType.'" data-button="open-searching" data-searching-argums="'.base64_encode( json_encode( array('Text_search_button'=>$Text_search_button,'search_placeholder'=>$search_placeholder,'search_title'=>$search_title ) ) ).'">'.$Text_search_button.'</div>';
						}
					echo '</div>';

					# GEO + LANGUAGE SWITCHER — KAYAN by MAHMOUD ELSAAD
					if ( function_exists( 'kayan_i18n_render_switcher' ) ) {
						kayan_i18n_render_switcher();
					}

					# CITY
					$hide_city_bar = yc_get_option('hide_city_bar');
					$city = yc_get_option('city_groub');
					if( empty($hide_city_bar) ) {
						if(	isset( $city )  && !empty( $city ) ) {
							echo'<div class="all-taxonimes-in">';
								if ( function_exists( 'kayan_ui_show_call_button' ) && kayan_ui_show_call_button() ) {
									echo'<div class="-header-call-">';
										echo'<i class="fa-solid fa-phone"></i>';
									echo'</div>';
								}
								echo'<div class="-taxonomy--contact-">';
									foreach ($city as $c => $r) {
										$phonenumber = yc_get_option('number_city');
										$iconss = yc_get_option('city_name');
										echo'<div class="-taxonimes-">';
											if ( function_exists( 'kayan_ui_show_call_button' ) && kayan_ui_show_call_button() && ! empty( $r['number_city'] ) ) {
												echo'<a href="tel:'.$r['number_city'].'" class="tax-in-here">';
													echo'<i class="fa-solid fa-phone"></i>';
													echo'<div class="-cits-taxonimes-">';
														echo'<span class="-city-name-">' .$r['city_name']. '</span>';
														echo'<span class="-city-number-">' .$r['number_city']. '</span>';
													echo'</div>';
												echo'</a>';
											} else {
												echo'<div class="tax-in-here tax-in-here--label">';
													echo'<i class="fa-solid fa-location-dot"></i>';
													echo'<div class="-cits-taxonimes-">';
														echo'<span class="-city-name-">' .$r['city_name']. '</span>';
													echo'</div>';
												echo'</div>';
											}
										echo'</div>';
									}
									echo'<div class="contact-us-header">';
										echo'<div class="-conatct-text">';
										
										    // 4. أيقونة تواصل معنا المجانية
											echo'<i class="fa-solid fa-comment-dots"></i>';
											
											echo'<span class="conatct-place">تواصل معنا </span>';
										echo'</div>';
										# SOCIAL 
										echo '<div class="--top-area-social-">';
											if( empty( $hide_social_header ) ) {
												$SocialIcon = array(
													'facebook'=>'<i class="fab fa-facebook-f"></i>',
													'twitter'=>'<i class="fab fa-twitter"></i>',
													'telegram'=>'<i class="fa-brands fa-telegram"></i>',
													'youtube'=>'<i class="fab fa-youtube"></i>',
													'linkedin'=>'<i class="fab fa-linkedin-in"></i>',
													'instagram'=>'<i class="fab fa-instagram"></i>',
												);
												echo '<div class="--socialheader">';
													$SocialIcons = array(
														'phonenumber'=>'<i class="fa-solid fa-phone"></i>',
														'company__adress'=>'<i class="fa-solid fa-location-dot"></i>',
														'whatsapp'=>'<i class="fa-brands fa-whatsapp"></i>',
														'company__mail'=>'<i class="fa-solid fa-envelope"></i>',
													);
													echo'<ul class="list-unstyled">';
														foreach ( $social_header_list as $social__item ) {
															if ( $social__item === 'phonenumber' && ( ! function_exists( 'kayan_ui_show_call_button' ) || ! kayan_ui_show_call_button() ) ) {
																continue;
															}
															$social_value = yc_get_option( $social__item );
															if ( empty( $social_value ) ) {
																continue;
															}
															$icon_html = '';
															if ( isset( $SocialIcons[ $social__item ] ) ) {
																$icon_html = $SocialIcons[ $social__item ];
															} elseif ( isset( $SocialIcon[ $social__item ] ) ) {
																$icon_html = $SocialIcon[ $social__item ];
															}
															if ( $icon_html === '' ) {
																continue;
															}
															$url_value = $social_value;
															if ( $social__item === 'whatsapp' || $social__item === 'whatsapp_number' ) {
																$url_value = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $social_value ) : 'https://wa.me/' . preg_replace( '/\D+/', '', $social_value );
															} elseif ( $social__item === 'phonenumber' ) {
																$url_value = 'tel:' . $social_value;
															} elseif ( $social__item === 'company__mail' ) {
																$url_value = 'mailto:' . $social_value;
															} elseif ( $social__item === 'company__adress' ) {
																$url_value = '#';
															}
															echo '<li class="' . esc_attr( $social__item ) . '"><a class="hoverable" target="_blank" title="' . esc_attr( $social__item ) . '" href="' . esc_url( $url_value ) . '">' . $icon_html . '</a></li>';
														}
													echo'</ul>';
												echo '</div>';
											}
										echo '</div>';
									echo'</div>';
								echo'</div>';
							echo'</div>';
						}
					}

				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</header>';
}