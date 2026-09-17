<?php
$UniqID = uniqid();
# POST CONTENT.
	ob_start();
	the_content();
	$post_content = ob_get_clean();

# PAGE SETUP .	
	$YC__WidgetsMachine = new YC__WidgetsMachine;
	# Style FILES.
		$Styles = array();

		$Path_style__Intro = $this->StylesPath.'singular';
		$StyleFields__Intro = array();
		#
		foreach( glob($Path_style__Intro.'/*.css') as $cs__file ) {
			$current_file_name = explode('singular/', $cs__file)[1];
			$current_file_name = explode('/', $current_file_name)[0];
			$current_file_name = explode('.css', $current_file_name)[0];
			$StyleFields__Intro[] = $current_file_name;
		}
		$Styles['shortcodes'] = 'shortcodes.css';
		if ( ! function_exists( 'kayan_homepage_uses_inner_layout' ) || ! kayan_homepage_uses_inner_layout() ) {
			$Styles[$post->post_type] = 'singular/single.css';
		} else {
			$Styles['kayan-home']  = 'kayan-home.css';
			$Styles['kayan-inner']   = 'kayan-inner.css';
		}

#

$ShareHastags = array();

# PAGE CONTENT.

	$permalink = get_the_permalink($post->ID);

	# IMAGE THUMBNAIL .
		$post_thumb = get_the_post_thumbnail_url($post->ID);
		$Image__size__value = ( wp_is_mobile() ) ? 'mobile_post_size' : 'post_single_size';
		$post_thumb_id = get_post_thumbnail_id($post->ID);
	#
	$publishedAt = date('Y-m-d',strtotime($post->post_date));
	$DayDate = StaticThemeDate($publishedAt,'d F Y');

	# POST AUTHOUR.
		$post_author = get_user_by('id',$post->post_author);
		$avatar_author = '<i class="fa-solid fa-user-pen"></i>';
		$author__url = get_author_posts_url($post_author->ID);

	# POST META.	
		$TotalRate_v1 = get_post_meta($post->ID,'TotalRate_v1',true);
		if( empty( $TotalRate_v1 ) || !empty( $TotalRate_v1 ) && !is_numeric($TotalRate_v1) ) $TotalRate_v1 = (INT) $TotalRate_v1;
		$RatingCounter = (INT) get_post_meta($post->ID,'RateUserCount_v1',true);
		$RateValues = ( is_array( get_post_meta( $post->ID, 'RateUsersData_v1', true) ) ) ? get_post_meta($post->ID, 'RateUsersData_v1', true) : array();
		#
		$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
		if( empty( $whatsapp_number ) ) $whatsapp_number = get_option('whatsapp_number');

		$phonenumber = get_post_meta( $post->ID,'phone_number',true );
		if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');


	# POST TERMS.
		$Related_Terms = array();
		$terms_category = get_the_terms( $post->ID,'category',true );
		$terms_category = ( is_array( $terms_category ) ) ? $terms_category : array();
		$category = array();
		$category__ids = array();
		$category__sorted = array();

		foreach ( $terms_category as $cats ) {

			$category__ids[] = $cats->term_id;
			
			#
			$cats__color = get_term_meta( $cats->term_id,'color',true );
			if( !empty( $cats__color ) ) $cats->uicolor = $cats__color;

			$cats__icon = get_term_meta( $cats->term_id,'icon',true );
			if( !empty( $cats__icon ) ) $cats->icon = $cats__icon;
			$cats->term_link = get_term_link( $cats );
			$category[] = $cats;

			#

			if( $cats->parent == 0 ){
				$category__sorted['parent'][] = $cats;
			}else{
				$category__sorted[ 'childs_'.$cats->parent ][ $cats->term_id ] = $cats;
			}
			$Related_Terms[ $cats->taxonomy ][] = $cats;
		}

		$terms_services = get_the_terms( $post->ID,'services',true );
		$terms_services = ( is_array( $terms_services ) ) ? $terms_services : array();
		foreach ( $terms_services as $t__s) {
			$Related_Terms[ $t__s->taxonomy ][] = $t__s;
		}

		$terms_city = get_the_terms( $post->ID, 'city', true );
		$terms_city = ( is_array( $terms_city ) ) ? $terms_city : array();
		foreach ( $terms_city as $city_term ) {
			$Related_Terms[ $city_term->taxonomy ][] = $city_term;
		}

		$post_tag = get_the_terms( $post->ID,'post_tag',true );
		$post_tag = ( is_array( $post_tag ) ) ? $post_tag : array();


		$position__post_card = get_post_meta( $post->ID,'position__post_card',true );
		if( empty( $position__post_card ) ) $position__post_card = 'top_content';

		# POPOVER EDITS
		    $PopOver__Attr = '';
			$hide__post__popover = get_option('hide__post__popover');
			if( empty( $hide__post__popover ) ) {
				$meta_hide_popover = get_post_meta($post->ID,'hide__single__popover',true);
				if( empty( $meta_hide_popover ) ){
					$found__popover = false;
					$post__popover__data = get_post_meta( $post->ID,'post__popover__data',true );
					$post__popover__data = ( ( is_array( $post__popover__data ) ) ) ? $post__popover__data : array();
					if( !empty( $post__popover__data ) && isset( $post__popover__data['popover_call_title'] ) && !empty( $post__popover__data['popover_call_title'] ) ){
						$found__popover = true;
						$post__popover__data['whatsapp_number'] = $whatsapp_number;
						if ( function_exists( 'kayan_wa_default_message' ) ) {
							$post__popover__data['whatsapp_message'] = kayan_wa_default_message( get_the_title( $post->ID ) );
						}
						if ( function_exists( 'kayan_ui_show_call_button' ) && kayan_ui_show_call_button() ) {
							$post__popover__data['phonenumber'] = $phonenumber;
						} else {
							unset( $post__popover__data['phonenumber'] );
						}
						$PopOver__Attr = ' data-scroll-popover="'.base64_encode( json_encode( $post__popover__data ) ).'"';
					}

					if( $found__popover == false ){
						$post__popover__data = get_option('post__popover__data');
						$post__popover__data = ( ( is_array( $post__popover__data ) ) ) ? $post__popover__data : array();
						if( !empty( $post__popover__data ) && isset( $post__popover__data['popover_call_title'] ) && !empty( $post__popover__data['popover_call_title'] ) ){
							$found__popover = true;
							$post__popover__data['whatsapp_number'] = $whatsapp_number;
							if ( function_exists( 'kayan_ui_show_call_button' ) && kayan_ui_show_call_button() ) {
								$post__popover__data['phonenumber'] = $phonenumber;
							} else {
								unset( $post__popover__data['phonenumber'] );
							}
							$PopOver__Attr = ' data-scroll-popover="'.base64_encode( json_encode( $post__popover__data ) ).'"';
						}
					}
				}
			}

		# SINGLE OPTIONS .
			$show_comments = ( ( wp_is_mobile() ) ) ? get_option('disable_mobile_comments') : get_option('disable_comments');
			$hide__thumbnail__single = get_option('hide__thumbnail__single');
			$hide__next_prev_post__single = get_option('hide__next_prev_post__single');
			$hide__post__references = get_option('hide__post__references');
			$hide__post__faqs = get_option('hide__post__faqs');
			$hide__post__shares = get_option('hide__post__shares');
			$hide__feedback__rating = get_option('hide__feedback__rating');
			$hide__post__tags = get_option('hide__post__tags');
			$hide__related__section = get_option('hide__related__section');

			$hide__post__boxx = get_option('hide__post__boxx');
			$hide__post__category = get_option('hide__post__category');
			$hide__post__author = get_option('hide__post__author');
			$hide__post__date = get_option('hide__post__date');

			# SINGLE SIDE BAR .
			$hide__sidebar__single = get_option('hide__sidebar__single');
			if( empty( $hide__sidebar__single ) ){

				$widgets_single__meta = ( is_array( get_option( 'widgets_single__meta' ) ) ) ? get_option( 'widgets_single__meta' ) : array();
				$widgets_single__meta = ( is_array( $widgets_single__meta ) ) ? $widgets_single__meta : array();

				if( !empty( $widgets_single__meta ) ){
					$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues($widgets_single__meta);
					$Styles = array_merge($Styles,$widgets__Enqueues);
				}
			}

	# HEADER .
	$this->Part('header',array('Styles'=>$Styles));

		$kayan_inner_layout = function_exists( 'kayan_homepage_uses_inner_layout' ) && kayan_homepage_uses_inner_layout();
		$hero_category      = isset( $category__sorted['parent'][0] ) ? $category__sorted['parent'][0] : null;

		if ( $kayan_inner_layout ) {
			kayan_homepage_render_inner_hero(
				array(
					'title'         => $post->post_title,
					'image_url'     => ! empty( $post_thumb ) ? $post_thumb : '',
					'image_alt'     => $post->post_title,
					'category_term' => $hero_category,
					'total_rate'    => (float) $TotalRate_v1,
				)
			);
			kayan_homepage_render_inner_breadcrumb();

			echo '<div class="-singular-pages-container kayan-inner-singular-shell"'.$PopOver__Attr.'>';
			kayan_homepage_render_inner_layout_open( true );

			echo '<article class="kayan-inner-post-content">';
			do_action( 'yc_hook_ad_location_content_above' );
			echo $post_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			do_action( 'yc_hook_ad_location_content_below' );
			echo '</article>';

			if ( empty( $hide__post__faqs ) ) {
				echo '<div class="kayan-inner-post-block kayan-inner-section__body">';
				$this->Blade( 'content-single-models', array( 'post' => $post ), 'post__faqs' );
				echo '</div>';
			}

			if ( empty( $hide__post__tags ) && ! empty( $post_tag ) ) {
				echo '<div class="kayan-inner-post-block kayan-inner-section__body">';
				$this->Blade( 'content-single-models', array( 'post_tag' => $post_tag ), 'post__tags' );
				echo '</div>';
			}

			if ( empty( $hide__next_prev_post__single ) ) {
				echo '<div class="kayan-inner-post-block kayan-inner-post-nav">';
				$this->Blade( 'content-single-models', array( 'post' => $post ), 'next_prev_post' );
				echo '</div>';
			}

			kayan_homepage_render_inner_sidebar_open();
			kayan_homepage_render_contact_box( $post->ID, $phonenumber, $whatsapp_number );
			kayan_homepage_render_sidebar_related_services( $post->ID, $category__ids );

			if ( empty( $hide__feedback__rating ) ) {
				echo '<div class="side-w kayan-inner-sidebar__rating">';
				$this->Blade(
					'content-single-models',
					array(
						'post'           => $post,
						'RatingCounter'  => $RatingCounter,
						'RateValues'     => $RateValues,
						'TotalRate_v1'   => $TotalRate_v1,
					),
					'feedback__rating'
				);
				echo '</div>';
			}

			if ( empty( $hide__sidebar__single ) && ! empty( $widgets_single__meta ) ) {
				echo '<div class="side-w kayan-inner-sidebar__widgets">';
				$YC__WidgetsMachine->widgets___UI(
					array(
						'Widgets_data'             => $widgets_single__meta,
						'WidgetID'                 => 'widgets_single__meta',
						'Parent__section__class'   => '-first-single-post-bar',
						'Single__section__class'   => '--Single--page--widget-item',
						'section_InnerRow_class'   => 'Single--page-widget-innerRow',
					)
				);
				echo '</div>';
			}

			kayan_homepage_render_inner_layout_close( true );
			echo '</div>';

			if ( empty( $show_comments ) ) {
				echo '<div class="kayan-inner-body"><div class="kayan-inner-layout kayan-inner-layout--no-sidebar">';
				echo '<div class="kayan-inner-section__body">';
				$this->Part( 'comments', array( 'post' => $post ) );
				echo '</div></div></div>';
			}
		} else {
		echo '<div class="YC-single-title">';
			echo '<div class="container">';
				echo '<div class="--YC-title-breadcrhumb-">';
					echo '<div class="single-post-title single-'.$post->post_type.'-post-title">';
						echo '<h1>'.$post->post_title.'</h1>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		echo '<div class="-Yc-breadcrumb-">';
			echo '<div class="container">';
				echo '<div class="YC-BreadCrumb -BreadCrumb-PT-'.$post->post_type.'">';
					Breadcrumb();
				echo '</div>';
			echo '</div>';
		echo '</div>';
		# MAIN SINGLE CONTAINER .	
		echo '<div class="-singular-pages-container"'.$PopOver__Attr.'>';
			echo '<div class="container">';

				echo '<div class="-Yc-single-main -YC-singleType-'.$post->post_type.'">';
					# SERVICE REQUST

					global $post;
					$category = get_the_terms( $post->ID,'category',true );
					$category = ( ( is_array( $category ) ) ) ? $category : array();

					if( isset( $title ) ){
						if( empty( $title_color ) ) $title_color = 'var(--uicolor2)';

						$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
						$title = str_replace('%}','</c--color>',$title);
					}

					$hide__sidebar__service_request = get_post_meta( $post->ID,'hide__sidebar__service_request',true );
					$hide__sidebar__service_request_single = get_option('hide__sidebar__service_request_single');

					$post__service_request__data = get_post_meta( $post->ID,'post__service_request__data',true );
					$post__service_request__data = ( ( is_array( $post__service_request__data ) ) ) ? $post__service_request__data : array();

					if( ( !isset( $post__service_request__data['orderservices'] ) || ( isset( $post__service_request__data['orderservices'] ) && empty( $post__service_request__data['orderservices'] ) ) ) && isset( $defualt__title ) ) $post__service_request__data['orderservices'] = $defualt__title;
					if( ( !isset( $post__service_request__data['orderservices'] ) || ( isset( $post__service_request__data['orderservices'] ) && empty( $post__service_request__data['orderservices'] ) ) ) && !isset( $defualt__title ) ) $post__service_request__data['orderservices'] = 'اطلب الخدمة الان ';

					if( ( !isset( $post__service_request__data['contentservices'] ) || ( isset( $post__service_request__data['contentservices'] ) && empty( $post__service_request__data['contentservices'] ) ) ) && isset( $defualt__content ) ) $post__service_request__data['contentservices'] = $defualt__content;
					if( ( !isset( $post__service_request__data['contentservices'] ) || ( isset( $post__service_request__data['contentservices'] ) && empty( $post__service_request__data['contentservices'] ) ) ) && !isset( $defualt__content ) ) $post__service_request__data['contentservices'] = 'خدمة علي مدار 24 ساعه';

					if( !isset( $post__service_request__data['icon'] ) && isset( $defualt__icon ) ) $post__service_request__data['icon'] = $defualt__icon;
					if( !isset( $post__service_request__data['icon'] ) && !isset( $defualt__icon ) ) $post__service_request__data['icon'] = '<i class="fa-solid fa-phone"></i>';

					$phonenumber = get_post_meta( $post->ID,'phone_number',true );
					if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');
					$bg_shap = get_template_directory_uri().'/components/styles/img/service-details.webp';
					$post__service__data = get_post_meta( $post->ID,'post__service__data',true );
					$post__service__data = ( ( is_array( $post__service__data ) ) ) ? $post__service__data : array();
					if( !isset( $whatsapp_number ) ){
						$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
						if( empty( $whatsapp_number ) ) $whatsapp_number = get_option('whatsapp_number');
					}
					if( !isset( $phonenumber ) ){
						$phonenumber = get_post_meta( $post->ID,'phonenumber',true );
						if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');
					}
					if( empty( $hide__sidebar__service_request_single ) ){
						if( empty( $hide__sidebar__service_request ) ){
							echo '<div class="--YC-service-requset-widget--">';
								echo '<div class="--YC-service-back-ground--" style="background-image:url('.$bg_shap.');"></div>';
								echo '<div class="widget--sidebar--orders-UI--area">';
									echo '<div class="widget--sidebar--orders-info">';
										echo '<div class="widget--sidebar--orders-title">'.$post__service_request__data['orderservices'].'</div>';
										echo '<div class="widget--sidebar--orders-content">'.$post__service_request__data['contentservices'].'</div>';
						            echo'</div>';
						            echo '<div class="-post-card--burrons--area">';

							        	if( function_exists( 'kayan_ui_show_call_button' ) && kayan_ui_show_call_button() && ( ! isset( $post__service_request__data['hide__service__callbutton'] ) || empty( $post__service_request__data['hide__service__callbutton'] ) ) ){
							                echo '<a class="post-card-buttons -callbutton--post-card -BTN--hoverable" href="tel:'.$phonenumber.'" rel="nofollow">';
							                    echo '<i class="fa-solid fa-phone"></i>';
							                    echo '<strong>اتصل بنا</strong>';
							                echo '</a>';
							        	}

							        	if( !isset( $post__service_request__data['hide__service__whatsapp'] ) || isset( $post__service_request__data['hide__service__whatsapp'] ) && empty( $post__service_request__data['hide__service__whatsapp'] ) ){
											$wa_service_url = function_exists( 'kayan_wa_build_url' ) ? kayan_wa_build_url( $whatsapp_number, null, get_the_title( $post->ID ) ) : 'https://wa.me/' . preg_replace( '/\D+/', '', $whatsapp_number );
							                echo '<a target="_blank" rel="nofollow" class="post-card-buttons whatsapp--callbutton--post-card -BTN--hoverable" href="'.esc_url( $wa_service_url ).'">';
							                    echo '<i class="fa-brands fa-whatsapp"></i>';
							                    echo '<strong>   الواتساب</strong>';
							                echo '</a>';
							            }
							        echo '</div>';

						        echo '</div>';
						    echo '</div>';
				    	}
			    	}
					# CENTER CONTENT.
						echo '<div class="single-content-context-elements">';

							echo '<div class="-secodary-single-post-bar for-content-single">';

								# POST CARD .
									if( $position__post_card == 'top_image' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

								if( !empty( $post_thumb_id ) && empty( $hide__thumbnail__single ) ){
									echo '<div class="-single-parent-flexes--content-inner-thumb">';
										echo '<div class="-single-parent-flexes--content-inner-thumb--inner">';
											echo YC_get_attachment(
												array(
												    'id' => $post_thumb_id,
												    'size' => $Image__size__value,
												    'alt' => $post->post_title,
												)
											);
										echo '</div>';
									echo '</div>';
								}

								echo '<div class="--Yc-single-content--in-">';
									if( empty( $hide__post__boxx ) && ( empty( $hide__post__category ) || empty( $hide__post__author ) || empty( $hide__post__date ) ) ){

										echo '<div class="-single-bottom-title-list">';
											echo '<ul>';
												if( empty( $hide__post__author ) ){
													echo '<li class="-single-bottom-list-user-area">';
														echo '<a href="'.$author__url.'"><i class="fa-solid fa-pencil"></i><span>'.$post_author->display_name.'</span></a>';
													echo '</li>';
												}

												if( isset( $category__sorted['parent'][0] ) && empty( $hide__post__category ) ){
													echo '<li class="-single-bottom-list-category-terms" style="--categoryuicolor:'.( ( isset( $category__sorted['parent'][0]->uicolor ) ) ? $category__sorted['parent'][0]->uicolor : 'var(--uicolor2)').';">';
														echo '<a href="'.$category__sorted['parent'][0]->term_link.'"><i class="fa-solid fa-list-ul"></i><span>'.$category__sorted['parent'][0]->name.'</span></a>';
													echo '</li>';
												}

												if( empty( $hide__post__date ) ){
													echo '<li class="-single-bottom-list-date">';
														echo '<i class="fa-solid fa-calendar-days"></i><span>'.$DayDate.'</span>';
													echo '</li>';
												}
											echo '</ul>';
										echo '</div>';

									}

									
									# POST CARD .
										if( $position__post_card == 'top_content' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

									# POST CONTENT 	
										echo '<div class="-single-post-content single-'.$post->post_type.'-post-content">';
											do_action('yc_hook_ad_location_content_above');
											#
											echo $post_content;
											#
											do_action('yc_hook_ad_location_content_below');
										echo '</div>';

									# POST CARD .
										if( $position__post_card == 'bottom_content' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );


									# POST REFERNCES .
										if( empty( $hide__post__references ) ) $this->Blade('content-single-models',array('post'=>$post ),'post__references' );

									# POST CARD .
										if( $position__post_card == 'before__references' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

									# POST TAGS .	
										if( empty( $hide__post__tags ) &&  !empty( $post_tag ) ) $this->Blade('content-single-models',array('post_tag'=>$post_tag),'post__tags' );

									# POST CARD .
										if( $position__post_card == 'before_post_tags' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

									# POST FAQS .
										if( empty( $hide__post__faqs ) ) $this->Blade('content-single-models',array('post'=>$post ),'post__faqs' );

									# POST CARD .
										if( $position__post_card == 'before_faqs' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

									# POST CARD .
										if( $position__post_card == 'before_next_and_prev' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

									# NEXT AND PREVIUS POST .	
										if( empty( $hide__next_prev_post__single ) ) $this->Blade('content-single-models',array('post'=>$post ),'next_prev_post' );						

									# POST CARD .
										if( $position__post_card == 'end__page' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );
								echo '</div>';

							echo '</div>';
							if( empty( $hide__feedback__rating ) ) $this->Blade('content-single-models',array('post'=>$post,'RatingCounter'=>$RatingCounter,'RateValues'=>$RateValues,'TotalRate_v1'=>$TotalRate_v1),'feedback__rating' );

							# POST SHARE .	
								if( empty( $hide__post__shares ) ) $this->Blade('content-single-models',array('Title'=>$post->post_title,'Permalink'=>$permalink,'ShareTags'=>$ShareHastags,'Description'=>$post->post_content,'Picture'=>$post_thumb),'ShareItems' );
									

						echo '</div>';

					# SIDEBAR POSTS .
						if( empty( $hide__sidebar__single ) && !empty( $widgets_single__meta ) ) {
							$YC__WidgetsMachine->widgets___UI(
								array(
									'Widgets_data'=>$widgets_single__meta,
									'WidgetID'=>'widgets_single__meta',
									'Parent__section__class'=>'-first-single-post-bar',
									'Single__section__class'=>'--Single--page--widget-item',
									'section_InnerRow_class'=>'Single--page-widget-innerRow',
								)
							);
						}

				echo '</div>';
						if( empty( $show_comments ) ) {
							echo '<div class="-secodary-single-post-bar for-content-single">';
								$this->Part('comments',array('post'=>$post));
							echo '</div>';
						}
			echo '</div>';
		echo '</div>';
		}


	# SINGLE RELATED PAGE .
		if( empty( $hide__related__section ) ) $this->Blade('content-single-models',array( 'post'=>$post,'Related_Terms'=>$Related_Terms ),'related__sections' );

	# FOOTER .
		$this->Part('footer',array('Styles'=>$Styles));