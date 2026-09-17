<?
# LOGO EDITS .
	$whatsapp = yc_get_option('whatsapp_number');
	if ( function_exists( 'kayan_homepage_build_footer_html' ) ) {
		echo kayan_homepage_build_footer_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
	echo '<footer>';
		echo '<div class="--YC-footer--">';
			echo '<div class="container">';
				echo '<div class="Yc--footer">';
					# menus area
					echo '<div class="footer-left">';
						# bottom area
						echo '<div class="footer-bottom-menus">';
							echo '<div class="all--footer--menu--in">';
								# FIRST LOGO SECTION .
								$hide_footer__first__slice = yc_get_option('hide_footer__first__slice');
								if( empty( $hide_footer__first__slice ) ){


									echo '<div class="-footer-widgets-single -current-widgets-logo">';

										# LOGO FOOTER .
											$hide_logo_footer = yc_get_option('hide_logo_footer');
											if( empty( $hide_logo_footer ) ){
												$footer__logo = yc_get_option( 'footer__logo' );
												$footer__logo = ( ( is_array( $footer__logo ) ) ) ? $footer__logo : array();
												if( empty( $footer__logo ) || empty( $footer__logo ) && ( !isset( $footer__logo['logo__mode'] ) || ( isset( $footer__logo['logo__mode'] ) && isset( $footer__logo[ $footer__logo['logo__mode'] ] ) ) ) ) $footer__logo = array( 'logo__mode'=>'Text','logo__mode'=>array('logo_Text'=>'YOUR{%COLOR%}') );
												if( isset( $footer__logo['logo__mode'] ) && $footer__logo['logo__mode'] == 'Image' && isset( $footer__logo[ $footer__logo['logo__mode'] ] ) && isset( $footer__logo[ $footer__logo['logo__mode'] ]['image_logo'] ) && isset( $footer__logo[ $footer__logo['logo__mode'] ]['image_logo_id'] ) ){
													$footer__logo[ $footer__logo['logo__mode'] ]['header__alt'] = ( ( isset( $footer__logo[ $footer__logo['logo__mode'] ]['header__alt'] ) ) ) ? $footer__logo[ $footer__logo['logo__mode'] ]['header__alt'] : get_bloginfo('name');
													echo '<div class="-footer-site-logo --logo-'.$footer__logo['logo__mode'].'">';
													    echo '<a href="'.home_url().'" title="'.$footer__logo[ $footer__logo['logo__mode'] ]['header__alt'].'">';
													        echo YC_get_attachment(
													        	array(
														            'alt' =>$footer__logo[ $footer__logo['logo__mode'] ]['header__alt'],
														            'id'=>$footer__logo[ $footer__logo['logo__mode'] ]['image_logo_id'],
														            'alt'=>$footer__logo[ $footer__logo['logo__mode'] ]['header__alt'],
														            'size'=>'footer_sizelogo',
														        )
														    );
													    echo '</a>';
													echo '</div>';
												}else if( isset( $footer__logo['logo__mode'] ) &&  $footer__logo['logo__mode'] == 'Text' ){

													$footer__logo[ $footer__logo['logo__mode'] ]['header__alt'] = ( ( isset( $footer__logo[ $footer__logo['logo__mode'] ]['header__alt'] ) ) ) ? $footer__logo[ $footer__logo['logo__mode'] ]['header__alt'] : get_bloginfo('name');

													if( isset( $footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'] ) && strpos( $footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'],'{%') !== FALSE && strpos( $footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'],'%}') !== FALSE ){
														$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'] = "<span class='first-logo-word'>{$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text']}";
														$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'] = str_replace('{%','</span><strong class="second-logo-word">',$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text']);
														$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'] = str_replace('%}','</strong>',$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text']);
													}
													echo '<div class="-footer-site-logo --logo-'.$footer__logo['logo__mode'].'">';
														echo '<a href="'.home_url().'" title="'.$footer__logo[ $footer__logo['logo__mode'] ]['header__alt'].'">'.$footer__logo[ $footer__logo['logo__mode'] ]['logo_Text'].'</a>';
													echo '</div>';
												}									
											}

										# FOOTER DESCRIPTION .	
											$hide_description_footer = yc_get_option('hide_description_footer');
											if( empty( $hide_description_footer ) )	$footer__content = yc_get_option('footer__content');
											if( empty( $hide_description_footer ) && !empty( $footer__content ) ) echo '<div class="-footer-p-content">'.$footer__content.'</div>';


										# HIDE SOCIAL EDITS .
											$social_footer = yc_get_option( 'social_footer' );
											if( empty( $social_footer ) ) {
												$social_footer_list = yc_get_option('social_footer_list');
												$social_footer_list = ( ( is_array( $social_footer_list ) ) ) ? $social_footer_list : array();
												if( !empty( $social_footer_list ) ){
													$SocialIcon = array(
														'facebook'=>'<i class="fab fa-facebook-f"></i>',
														'twitter'=>'<i class="fab fa-twitter"></i>',
														'telegram'=>'<i class="fa-brands fa-telegram"></i>',
														'youtube'=>'<i class="fab fa-youtube"></i>',
														'linkedin'=>'<i class="fab fa-linkedin-in"></i>',
														'instagram'=>'<i class="fab fa-instagram"></i>',
													);
													echo '<div class="-row-shares-items">';
														foreach ( $social_footer_list as $social__item ) {
															$social_value = yc_get_option($social__item);
															if( !empty($social_value) ) {
																echo'<a class="'.$social__item.'" title="'.$social__item.'" target="_blank" href="'.$social_value.'">'.$SocialIcon[ $social__item ].'</a>';
															}
														}
													echo '</div>';										
												}
											}

									echo '</div>';
								}
							

								# SECOND FOOTER SECTION .
								$hide_footer__first_menu = yc_get_option('hide_footer__first_menu');
								if( empty( $hide_footer__first_menu ) ) $footer__first_menu = yc_get_option('footer__first_menu');

								if( empty( $hide_footer__first_menu ) && !empty( $footer__first_menu ) ){
									echo '<div class="-footer-widgets-single -current-widgets-menu1">';
										$footer__title_first_menu = yc_get_option('footer__title_first_menu');
										echo '<div class="-footer-widgets-title">';
											echo ''.( ( !empty( $footer__title_first_menu ) ) ? $footer__title_first_menu : '' ).'';
										echo '</div>';
										$NavList = wp_get_nav_menu_items($footer__first_menu);

										echo '<ul class="-footer-widgets-links">';
											foreach ( is_array( $NavList ) ? $NavList : array() as $pages) {
												echo '<li>';
													echo '<a href="'.$pages->url.'" class="activable">'.$pages->title.'</a>';
												echo '</li>';
											}
										echo '</ul>';
									echo '</div>';
								}

								# THIRD FOOTER SECTION .
								$hide_footer__second_menu = yc_get_option('hide_footer__second_menu');
								if( empty( $hide_footer__second_menu ) ) $footer__second_menu = yc_get_option('footer__second_menu');	
													
								if( empty( $hide_footer__second_menu ) && !empty( $footer__second_menu ) ){
									echo '<div class="-footer-widgets-single -current-widgets-menu2">';

										$footer__title_second_menu = yc_get_option('footer__title_second_menu');
										echo '<div class="-footer-widgets-title">';
											echo ''.( ( !empty( $footer__title_second_menu ) ) ? $footer__title_second_menu : '' ).'';
										echo '</div>';

										$NavList = wp_get_nav_menu_items($footer__second_menu);

										echo '<ul class="-footer-widgets-links">';
											foreach ( is_array( $NavList ) ? $NavList : array() as $pages) {
												echo '<li>';
													echo '<a href="'.$pages->url.'" class="activable">'.$pages->title.'</a>';
												echo '</li>';
											}
										echo '</ul>';
									echo '</div>';
								}

								# FOUTH FOOTER SECTION .
								$hide_contact__footer = yc_get_option('hide_contact__footer');
								if( empty( $hide_contact__footer ) ) $contact_footer_list = yc_get_option('contact_footer_list');

								if( empty( $hide_contact__footer ) && !empty( $contact_footer_list ) ){
									$contact_footer_list = ( ( is_array( $contact_footer_list ) ) ) ? $contact_footer_list : array();

									$footer__title_contact_menu = yc_get_option('footer__title_contact_menu');
									echo '<div class="-footer-widgets-single -current-widgets-contact">';

										echo '<div class="-footer-widgets-title">';
											echo ''.( ( !empty( $footer__title_contact_menu ) ) ? $footer__title_contact_menu : 'تواصل معنا' ).'';
										echo '</div>';

										echo '<div class="-company-contact-minibox">';
											$SocialIcon = array(
												'phonenumber'=>'<i class="fa-solid fa-phone"></i>',
												'company__adress'=>'<i class="fa-solid fa-location-dot"></i>',
												'whatsapp'=>'<i class="fa-brands fa-whatsapp"></i>',
												'company__mail'=>'<i class="fa-solid fa-envelope"></i>',
											);
												
											foreach ( $contact_footer_list as $social__item ) {
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
														$Name__value = $social_value;
														$URL__value = yc_get_option("footer__{$social__item}_url");
													}
													echo '<div class="'.$social__item.'">';
														echo ( ( !empty( $URL__value ) ) ) ? '<a target="_blank" href="'.$URL__value.'"  title="'.$Name__value.'">' : '';
															echo $SocialIcon[ $social__item ];
															echo '<span>'.$Name__value.'</span>';
														echo ( ( !empty( $URL__value ) ) ) ? '</a>' : '';
													echo '</div>';
												}
											}

										echo '</div>';
									echo '</div>';
								}


								# FOOTER MAP .	
								$hide_footer__map = yc_get_option('hide_footer__map');
								if( empty( $hide_footer__map ) ) {
									$company__map_code = yc_get_option('company__map_code');
									$company__map_title = yc_get_option('company__map_title');
								}

								if( empty( $hide_footer__map ) && !empty( $company__map_code ) && !empty( $company__map_title ) ){

									if( !empty( $company__map_title ) ) $company__map_code = str_replace('src=','title="'.$company__map_title.'" src=',$company__map_code);
									$company__map_code = str_replace('src=', "data-loader-src=", $company__map_code);

									echo '<div class="-footer-widgets-single -current-widgets-maps">';
										echo '<div class="--Inner--footer--sit-map">'.$company__map_code.'</div>';
									echo '</div>';
								}
							echo '</div>';
						echo '</div>';
						# end area

					echo '</div>';
					# end menu area

				echo '</div>';
			echo '</div>';
			echo '<div class="-current-widgets-payments">';
				echo '<div class="container">';

					echo '<footer-bottom>';
						$copyrights = yc_get_option('copyrights');
						if( !empty( $copyrights ) ){
							if( strpos( $copyrights,'{%YEAR%}' ) !== FALSE ) {
								$currentYear = date('Y');
								$copyrights = str_replace('{%YEAR%}', $currentYear, $copyrights);
							}
							echo '<p class="copyrights">'.$copyrights.'</p>';
						}
				// 		echo'<div class="yourcolor--copyright">';
				// 			echo '<p>تصميم وبرمجه <a target="_blank" rel="noopener" href="https://yourcolor.net"><img width="96" height="16" data-loader-src="'.$CurrentURL.'/yourcolor.png" alt="Yourcolor Workshop"/></a></p>';
				// 		echo'</div>';
					echo '</footer-bottom>';


				echo '</div>';

			echo '</div>';		
		echo '</div>';
	echo '</footer>';
	}

	// btn contact — floating buttons
	if( is_single() || is_page() ){
		global $post;
		$phonenumber = get_post_meta( $post->ID,'phone_number',true );
		if( empty( $phonenumber ) ) $phonenumber = yc_get_option('phonenumber');

		$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
		if( empty( $whatsapp_number ) ) $whatsapp_number = yc_get_option('whatsapp_number');

		# هل زر الاتصال العائم مخفي لهذا المقال؟
		$hide__floating__call = get_post_meta( $post->ID, 'hide__floating__call', true );

		# هل وضع محادثة الواتساب مفعّل لهذا المقال؟
		$floating_whatsapp_chat_mode = get_post_meta( $post->ID, 'floating_whatsapp_chat_mode', true );

		# التحقق من إخفاء التصنيف — إذا كان المقال ينتمي لتصنيف مُخفى
		if( empty( $hide__floating__call ) ){
			$hide_categories = yc_get_option('hide__floating__call__categories');
			if( !empty( $hide_categories ) && is_array( $hide_categories ) ){
				$post_cats = wp_get_post_categories( $post->ID, array('fields'=>'ids') );
				if( !empty( array_intersect( $post_cats, array_map('intval', $hide_categories) ) ) ){
					$hide__floating__call = '1';
				}
			}
		}

		# وضع المحادثة من التصنيف
		if( empty( $floating_whatsapp_chat_mode ) ){
			$chat_categories = yc_get_option('whatsapp_chat__categories');
			if( !empty( $chat_categories ) && is_array( $chat_categories ) ){
				$post_cats = isset($post_cats) ? $post_cats : wp_get_post_categories( $post->ID, array('fields'=>'ids') );
				if( !empty( array_intersect( $post_cats, array_map('intval', $chat_categories) ) ) ){
					$floating_whatsapp_chat_mode = '1';
				}
			}
		}

	}else{
		$phonenumber = yc_get_option('phonenumber');
		$whatsapp_number = yc_get_option('whatsapp_number');
		$hide__floating__call = false;
		$floating_whatsapp_chat_mode = false;
	}

	# إعداد عام لإخفاء زر الاتصال العائم من كل الصفحات
	$hide__floating__call__global = yc_get_option('hide__floating__call');

	# وضع المحادثة يخفي زر الاتصال وزر الواتساب معاً
	$chat_mode_active = !empty( $floating_whatsapp_chat_mode );

	echo '<div class ="btn-fixed-bh">';

		# زر الاتصال العائم — مستقل عن kayan_show_call_buttons
		$post_id_for_floating = ( ( is_single() || is_page() ) && isset( $post ) ) ? (int) $post->ID : 0;
		$show_floating_call_btn = function_exists( 'kayan_ui_show_floating_call_button' )
			? kayan_ui_show_floating_call_button( $post_id_for_floating, $chat_mode_active )
			: ( empty( $hide__floating__call__global ) && empty( $hide__floating__call ) && ! $chat_mode_active );
		if( $show_floating_call_btn ){
			echo '<div class="--yourcolor--button--phones --YourColor--phone-button">';
				echo'<a href="tel:'.$phonenumber.'" aria-label="اتصل بنا :" data-call="Phone" data-tooltip="اتصل بنا " data-position="top">';
					echo '<div class="footer-header">';
						echo '<i class="fa-solid fa-phone"></i>';
					echo '</div>';
				echo'</a>';
			echo'</div>';
		}

		# زر الواتساب الأساسي — يختفي عند تفعيل وضع المحادثة
		if( !$chat_mode_active ){
			echo'<div class="--yourcolor--button--phones --YourColor--whatsapp-button">';
				$wa_float_url = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $whatsapp_number ) : 'https://wa.me/' . preg_replace( '/\D+/', '', $whatsapp_number );
				echo '<a href="'.esc_url( $wa_float_url ).'" target="_blank" aria-label="  الواتساب  " data-call="whatsapp" data-tooltip="تواصل عبر واتساب" data-position="top">';
					echo '<div class="footer-header">';
						echo '<i class="fa-brands fa-whatsapp"></i>';
					echo '</div>';
				echo '</a>';
			echo'</div>';
		}

	echo'</div>';

	# نافذة الواتساب العائمة — تحل محل زر الاتصال وزر الواتساب
	if( $chat_mode_active ){
		$wa_title = '';
		$wa_msg   = '';
		if( isset($post) ){
			$wa_title = get_post_meta( $post->ID, 'floating_whatsapp_chat_title', true );
			$wa_msg   = get_post_meta( $post->ID, 'floating_whatsapp_chat_message', true );
		}
		if( empty( $wa_title ) ) $wa_title = yc_get_option('whatsapp_chat_title');
		if( empty( $wa_msg ) )   $wa_msg   = yc_get_option('whatsapp_chat_message');
		$page_title_for_wa = isset( $post ) ? kayan_wa_get_page_title( $post->ID ) : kayan_wa_get_page_title();
		$wa_title = function_exists( 'kayan_wa_resolve_title' ) ? kayan_wa_resolve_title( $wa_title ) : $wa_title;
		$wa_msg   = function_exists( 'kayan_wa_resolve_message' ) ? kayan_wa_resolve_message( $wa_msg, $page_title_for_wa ) : $wa_msg;
		$wa_link  = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $whatsapp_number, $wa_msg, $page_title_for_wa ) : 'https://wa.me/' . preg_replace( '/\D+/', '', $whatsapp_number );
		$pid = isset( $post ) ? $post->ID : 0;

		echo '<div class="yc-float-wa-bubble" id="yc-float-wa-'.$pid.'">';

			# النافذة — مفتوحة تلقائياً (بدون yc-float-wa-hidden)
			echo '<div class="yc-float-wa-box" id="yc-float-wa-box-'.$pid.'">';
				echo '<div class="yc-float-wa-header">';
					echo '<div class="yc-float-wa-header-info">';
						echo '<div class="yc-float-wa-icon"><i class="fa-brands fa-whatsapp"></i></div>';
						echo '<div>';
							echo '<span class="yc-float-wa-name">'.esc_html( $wa_title ).'</span>';
							echo '<span class="yc-float-wa-status"><b></b>متاح الآن</span>';
						echo '</div>';
					echo '</div>';
					echo '<button class="yc-float-wa-close" onclick="document.getElementById(\'yc-float-wa-box-'.$pid.'\').classList.add(\'yc-float-wa-hidden\')" aria-label="إغلاق">&times;</button>';
				echo '</div>';
				echo '<div class="yc-float-wa-body">';
					echo '<div class="yc-float-wa-msg"><p>'.esc_html( $wa_msg ).'</p><span class="yc-float-wa-time">'.date('H:i').'</span></div>';
				echo '</div>';
				echo '<div class="yc-float-wa-footer">';
					echo '<a class="yc-float-wa-btn" href="'.esc_url( $wa_link ).'" target="_blank" rel="nofollow">';
						echo '<i class="fa-brands fa-whatsapp"></i> ابدأ المحادثة';
					echo '</a>';
				echo '</div>';
			echo '</div>';

			# زر الواتساب الجديد (يحل محل زر الاتصال وزر الواتساب القديمين)
			echo '<button class="yc-float-wa-toggle" onclick="var b=document.getElementById(\'yc-float-wa-box-'.$pid.'\');b.classList.toggle(\'yc-float-wa-hidden\')" aria-label="واتساب">';
				echo '<i class="fa-brands fa-whatsapp"></i>';
				echo '<span class="yc-float-wa-pulse"></span>';
			echo '</button>';

		echo '</div>';

		echo '<style>
.yc-float-wa-bubble{position:fixed;bottom:24px;right:24px;z-index:10000;display:flex;flex-direction:column-reverse;align-items:flex-end;gap:10px}
.yc-float-wa-toggle{width:58px;height:58px;border-radius:50%;background:#25D366;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.5);position:relative;transition:transform .2s;flex-shrink:0}
.yc-float-wa-toggle:hover{transform:scale(1.08)}
.yc-float-wa-toggle .fa-whatsapp{font-size:28px;color:#fff}
.yc-float-wa-pulse{position:absolute;top:2px;left:2px;width:13px;height:13px;border-radius:50%;background:#FF3B30;border:2px solid #fff;animation:yc-wa-ping 1.5s infinite}
@keyframes yc-wa-ping{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.3);opacity:.7}}
.yc-float-wa-box{width:min(310px,82vw);background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,.2);overflow:hidden;transition:opacity .25s,transform .25s;transform-origin:bottom right}
.yc-float-wa-hidden{opacity:0!important;pointer-events:none!important;transform:scale(.92) translateY(10px)!important}
.yc-float-wa-header{background:#075E54;padding:13px 15px;display:flex;align-items:center;justify-content:space-between}
.yc-float-wa-header-info{display:flex;align-items:center;gap:10px}
.yc-float-wa-icon{width:40px;height:40px;border-radius:50%;background:#128C7E;display:flex;align-items:center;justify-content:center}
.yc-float-wa-icon .fa-whatsapp{font-size:22px;color:#fff}
.yc-float-wa-name{display:block;color:#fff;font-weight:700;font-size:13.5px}
.yc-float-wa-status{display:flex;align-items:center;gap:5px;color:#dcf8c6;font-size:11px}
.yc-float-wa-status b{width:7px;height:7px;border-radius:50%;background:#25D366;display:inline-block;font-weight:normal}
.yc-float-wa-close{background:transparent;border:none;color:#fff;font-size:22px;cursor:pointer;line-height:1;padding:2px 4px}
.yc-float-wa-body{background:#ECE5DD;padding:14px;min-height:80px}
.yc-float-wa-msg{background:#fff;border-radius:0 10px 10px 10px;padding:9px 13px;display:inline-block;box-shadow:0 1px 3px rgba(0,0,0,.1);max-width:95%}
.yc-float-wa-msg p{margin:0 0 5px;font-size:13px;line-height:1.55;color:#303030;direction:rtl;text-align:right}
.yc-float-wa-time{font-size:10px;color:#999;float:left}
.yc-float-wa-footer{padding:11px 14px;background:#f5f5f5;border-top:1px solid #e8e8e8}
.yc-float-wa-btn{display:flex;align-items:center;justify-content:center;gap:7px;background:#25D366;color:#fff;border-radius:50px;padding:9px 16px;text-decoration:none;font-size:13.5px;font-weight:700;transition:background .2s}
.yc-float-wa-btn:hover{background:#128C7E;color:#fff}
@media(max-width:480px){.yc-float-wa-bubble{bottom:14px;right:14px}.yc-float-wa-box{width:min(290px,88vw)}}
</style>';
	}

echo '</root>';

$HTML_otput = ob_get_clean();
$SVG_List = array();
if( strpos( $HTML_otput , 'data-svg-loaders="') !== FALSE ){
	$SVGLoader = explode('data-svg-loaders="', $HTML_otput);unset($SVGLoader[0]);
	foreach ( $SVGLoader as $w => $ee) {
		$SvgName = explode('"', $ee)[0];
		if( !isset( $SVG_List[ $SvgName ] ) ){
			$SVG_List[ $SvgName ] = SVGIcon($SvgName);
		}
	}
}

echo $HTML_otput;

# STYLE CSS
$Css_List = array();
if(isset($Styles)){
	foreach ($Styles as $skey => $meky) {
		$Css_List[$skey] = $this->StylesURL.$meky.'?v='.rand();
	}
}

if( isset($_GET['ajax']) ) {

	$output = ob_get_clean();
	$title = wp_title( '|', false, 'right' );
	$CurrentURL = $this->GetCurrentURL();
	$CurrentURL = remove_query_arg( 'ajax', $CurrentURL );
	if( is_search() ) {
		$search_query = get_search_query();
		$CurrentURL = home_url('/search/'.str_replace(' ', '-', $search_query).'/');
	}
	$array = array(
		"output"	=> $output,
		"title"		=> $title,
		"url"		=> $CurrentURL,
		"Css_List" => $Css_List,
		"SVG_List" => $SVG_List,
	);
	$json = safe_json_encode($array);
	footer('Content-Length: '.strlen($json)); // HERE IS THE PROBLEM
	footer('Content-type: application/json');
	echo $json;
}else {
	###
	global $current_user;
	###
	$TempDIR = $this->TempURL;
	echo '<script type="text/javascript">';
		echo "var WPAdminAjax = '".admin_url('admin-ajax.php')."';";
		echo "var AdminAjax = '".home_url('/ajaxcenter/')."';";
		echo "var HomeURL = '".home_url()."';";
		echo "var TmpDIR = '".get_template_directory_uri()."';";
		echo "var ISMobile = ".((wp_is_mobile()) ? 'true' : 'false').";";
		echo "var IsSpeed = ".( ( IsSpeed() != false ) ? 'true' : 'false').";";
		echo "var kayanShowCallButtons = ".( ( function_exists( 'kayan_ui_show_call_button' ) && kayan_ui_show_call_button() ) ? 'true' : 'false' ).";";
		$floating_call_js = true;
		if ( function_exists( 'kayan_ui_show_floating_call_button' ) ) {
			$floating_call_js = kayan_ui_show_floating_call_button(
				( ( is_single() || is_page() ) && isset( $post ) ) ? (int) $post->ID : 0,
				! empty( $chat_mode_active )
			);
		} elseif ( ! empty( $hide__floating__call__global ) || ! empty( $hide__floating__call ) || ! empty( $chat_mode_active ) ) {
			$floating_call_js = false;
		}
		echo "var kayanShowFloatingCallButton = ".( $floating_call_js ? 'true' : 'false' ).";";
		echo "var IsHome = ".((is_home()) ? 'true' : 'false').";";
		echo "var IsSingle = ".(is_single() ? 'true' : 'false').";";

		echo "var Currentuser_ID = '".$current_user->ID."';";
		echo "var Currentuser_first_name = '".$current_user->first_name."';";
		echo "var Currentuser_last_name = '".$current_user->last_name."';";
		echo "var Currentuser_display_name = '".$current_user->display_name."';";
		echo "var Currentuser_email = '".$current_user->user_email."';";
		echo "var Currentuser_Logged = true;";
		echo "var SVG_List = ".json_encode($SVG_List).";";
		echo 'function onTouchStart() {}document.addEventListener(\'touchstart\', onTouchStart, {passive: true});';
	echo '</script>';
	
	if( IsSpeed() == false && ( is_single() || is_page() || ( isset( $Widgets__list ) && in_array( 'works_v1',$Widgets__list ) ) ) ){
		echo "<script id='rendered-js' type='module'>";
			echo "import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe/dist/photoswipe-lightbox.esm.js';";
			echo "if( $( '[pswp]' ).length ){";
				echo "var lightbox = new PhotoSwipeLightbox({";
				  echo "gallery: '[pswp]',";
				  echo "children: 'a',";
				  echo "initialZoomLevel: 'fit',";
				  echo "secondaryZoomLevel: 1,";
				  echo "maxZoomLevel: 5,";
				  echo "pswpModule: () => import('https://unpkg.com/photoswipe') });";

				  echo "function LightboxInit() {";
				  	echo "lightbox.init();";
				  echo "}";
				  echo "LightboxInit();";
				echo "$( document ).ajaxComplete(function() {";
					echo "LightboxInit();";
				echo "});";
			echo "}";

			echo "if( $( '[data-gallery-popover]' ).length ){";
				echo "const loadGalleryElements = document.querySelectorAll('[data-gallery-popover]');";
				echo "const lightboxes = [];";

				echo "loadGalleryElements.forEach((element, index) => {";
				  	echo "const argumentsBase64 = element.getAttribute('data-gallery-popover');";
				  	echo "const decodedArguments = atob(argumentsBase64);";
				  	echo "const parsedArguments = JSON.parse(decodedArguments);";

					echo "const options = {";
					  	echo "dataSource: parsedArguments,";
					  	echo "showHideAnimationType: 'none',";
					  	echo "pswpModule: () => import('https://unpkg.com/photoswipe'),";
					echo "};";

				  	//echo "options.dataSource.push(...parsedArguments);";

				  	echo "const lightbox = new PhotoSwipeLightbox(options);";
				  	echo "lightbox.init();";
				  	//echo "lightboxes.push(lightbox);";

				  	echo "element.onclick = () => {";
				    	echo "lightbox.loadAndOpen(index);";
				  	echo "};";
				echo "});";
			echo "}";

	    echo "</script>";
	}

	wp_footer();
	echo '<div class="GotoTop"><i class="fa-solid fa-arrow-up"></i></div>';
	
	echo '</body>';
	echo '</html>';
}