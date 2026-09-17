<?/**
 * 
 */
class slider_intro_v1 extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'slider_intro_v1';
			$this->folder__name = basename(__DIR__);
			
		# CUSTOM $VARIABLES .	
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);

		if( isset( $title ) ){
			$title = str_replace('{%','<strong>',$title);
			$title = str_replace('%}','</strong>',$title);
		}

		$phonenumber = get_option('phonenumber');
		$whatsapp_number = get_option('whatsapp_number');

		if( !isset( $Number ) || ( isset( $Number ) && !is_numeric( $Number ) ) ) $Number = 5;
		if( !isset( $words_post_content ) || ( isset( $words_post_content ) && !is_numeric( $words_post_content ) ) ) $words_post_content = 20;
		if( wp_is_mobile( $words_post_content ) ) $words_post_content = 20;

		$postsArguments = array(
			'post_type'=>'post',
			'posts_per_page'=>$Number
		);

		if( isset( $posts__filter ) && $posts__filter == 'pin' ){
			$postsArguments['meta_key'] = $posts__filter;
		}

		$UNIQ = uniqid();
		echo '<div class="YourColor-IntroBoxes intro-model-'.$this->widget__name.'">';

			echo '<div class="total-IntroBoxes--parent">';
	
				echo '<div class="YourColor-Intro--sliderArea" data-uniq="'.$UNIQ.'">';

					foreach ( get_posts( $postsArguments ) as $post ) {

						$category = get_the_terms( $post->ID,'category',true);
						$category = ( ( is_array( $category ) ) ) ? $category : array();
						$cover = get_post_meta( $post->ID,'cover_id',true );
						$size_cover = ( ( wp_is_mobile() ) ) ? 'intro__cover_size' : 'full';
						if( !empty( $cover ) ){
							$intro__image = YC_get_attachment(array('id'=>$cover,'alt'=>$post->post_title,'size'=>'full','return__output'=>false) )['src'];
						}else if( isset( $defualt__intro_cover_id ) && !empty( $defualt__intro_cover_id ) ){
							$intro__image = YC_get_attachment(array('id'=>$defualt__intro_cover_id,'alt'=>$post->post_title,'size'=>'full','return__output'=>false) )['src'];
						}
						echo '<div class="Intro-slider-master--singleposts"'.( ( isset( $intro__image ) ) ? ' data-loader-style="--bg-intro:url('.$intro__image.')"': '' ).'>';
							echo '<div class="--intor--thumb-bg"></div>';
							echo '<div class="--intro--bg--styles"></div>';
							echo '<div class="Intro-slider-Container">';
								echo '<div class="back-intro-items-in"></div>';
								echo '<div class="-Intro-slider-BoxInfo">';

									echo '<div class="-Intro-slider-inner-Info">';

										if( isset( $category[0] ) && ( !isset( $hide_category_box ) || isset( $hide_category_box ) && empty( $hide_category_box ) ) ){
											echo '<span>'.$category[0]->name.'</span>';
										}
										echo '<div class="-intro-h1-title">'.$post->post_title.'</div>';
										if( !empty( $post->post_content ) && ( !isset( $hide_post_content ) || isset( $hide_post_content ) && empty( $hide_post_content ) ) ){
											$new_post_content = wp_trim_words( $post->post_content,$words_post_content );
											echo '<div class="-p-content">'.$new_post_content.'</div>';
										}

									echo '</div>';

									echo '<div class="-Intro-slider-URLArea">';
										if( !isset( $hide_post_url ) || ( isset( $hide_post_url ) && empty( $hide_post_url ) ) ) echo '<div class="-btn-areia-l"><a href="'.get_the_permalink( $post->ID ).'" class="-BTN--hoverable activable button_url_2"><span>'.( ( isset( $FirstButtontitle ) ) ? $FirstButtontitle : 'عرض المزيد' ).'</span>'.( ( isset( $FirstButtonIcon ) ) ? $FirstButtonIcon : '' ).'</a></div>';

										if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) && ( !isset( $hide_contact_us_page ) || isset( $hide_contact_us_page ) && empty( $hide_contact_us_page ) ) ){
											$this->ThemeStatic->Part(
												'button_context',
												array(
													'class'=>'-btn-areia-l -btn-areia-l2',
													'href_class'=>'activable -BTN--hoverable',
													'button_context'=>$first_button
												)
											);
										}
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
			if( isset( $intro_lists ) && !empty( $intro_lists ) ){
				echo'<div class="container list_container">';
					echo'<ul class="list-group animation-hidden" data-animation-id="fadeInUpBig">';
						foreach ( $intro_lists as $intro_list ) {
			        		$features = $intro_list['icon'];
			        		if(empty($features)){
			        			$features = '<i class="fa-solid fa-newspaper"></i>';
			        		}
		        			echo'<li class="item_group">';
			                    echo'<div class="item-icon">';
			                        echo'<div class="item_group-icon">';
			                          echo $features ; 
			                        echo'</div>';
			                       if( isset( $intro_list['title'] ) ) echo '<h4 class="feature-title">'.$intro_list['title'].'</h4>';
			                    echo'</div>';
			                    if(!empty($intro_list['url'])){
			                    if( isset( $intro_list['url'] ) ) echo'<a href="'.$intro_list['url'].'" title="'.$intro_list['title'].'" class="item-arrow">';echo'<i class="fa-solid fa-arrow-right"></i>';
			                    }
			                    echo'</a>';
			                echo'</li>';
		    			}
		            echo'</ul>';
		        echo '</div>';
	        }
		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__selector;

		$yc__widgets__selector[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'title'=>'SLIDER INTRO',
			'id'=>$this->widget__name,
			'fields'=> array(

				array(
					'type'=>'Title',
					'id'=>'post_opttts__number',
					'title'=>'إعدادات الظهور',
				),
				array(
					'type'=>'Radio',
					'id'=>'posts__filter',
					'title'=>'فلترة حسب ',
					'options'=>array(
						'latest'=>'الاحدث',
						'pin'=>'المثبت',
					)
				),
				array(
					'type'=>'Number',
					'id'=>'Number',
					'title'=>'عدد المقالات ',
				),
				array(
					'type'=>'File',
					'id'=>'defualt__intro_cover',
					'title'=>'الصورة الافتراضية ',
				),

				array(
					'type'=>'Title',
					'id'=>'post_opttts__number',
					'title'=>'إعدادات شكل المقال ',
				),

				array(
					'type'=>'SwitchBox',
					'id' => 'hide_category_box',
					'title' =>'إخفاء التصنيف ',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_post_content',
					'title' =>'إخفاء المحتوى ',
				),
				array(
					'type'=>'Number',
					'id' => 'words_post_content',
					'title' =>'عدد  كلمات المحتوى ',
				),
	            array(
	                'type'=>'Text',
	                'id' => 'button_Text',
	                'title' =>'عنوان زرار عرض المزيد',
	            ),
				array(
					'type'=>'TextArea_Code',
					'id'=>'FirstButtonIcon',
					'title'=>'ايقونة زرار عرض المزيد',
				),
				array(
					'id'=>'first_button',
					'type'=>'Models-Selector',
					'title'=>'إعدادات رابط خطط الاسعار',
					'select_field'=>array(
						'id'=>'button_mode',
						'type'=>'Select',
						'selected_shows'=>true,
						'title'=>'تحديد نوع رابط الزرار',
						'options'=>array(
							'manual' => 'يدويا',
							'watshapp'=>'WhatsApp',
							'phonenumber'=>'Phone',
							'page'=>'Page',
						),
					),
					'create_fields'=>true,
					'choose_fields'=>array(
						'manual' => array(
							'id'=>'manual',
							'title' => 'يدوي',
							'fields'=> array(
								array(
									'id'    => 'button__URL',
									'type'  => 'Text',
									'title' => 'الرابط',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'watshapp'=>array(
							'id'=>'watshapp',
							'title' => 'رقم watshapp',
							'fields'=> array(
								array(
									'id'    => 'watshapp',
									'type'  => 'Text',
									'title' => 'رقم watshapp',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'phonenumber'=>array(
							'id'=>'phonenumber',
							'title' => 'رقم phonenumber',
							'fields'=> array(
								array(
									'id'    => 'phonenumber',
									'type'  => 'Text',
									'title' => 'phonenumber',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'page'=>array(
							'id'=>'page',
							'title' => 'تحديد صفحة من الصفحات',
							'fields'=> array(
					            array(
					                'type'=>'Posts-Select',
					                'id' => 'button_page',
					                'post_type_name'=>'page',
					                'title' =>'تحديد الصفحة',
					            ),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),			            
					            		            
							),
						)
					)
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_contact_us_page',
					'title' =>'إخفاء زرار الاتصال',
				),		
				array(
					'type'=>'GroupsField',
					'id' => 'intro_lists',
					'title' =>'عناصر المقدمة ',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الايكونة '
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'العنوان ',
						),
						array(
							'type'=>'Text',
							'id'=>'url',
							'title'=>'الرابط ',
						),
					)
				),		

			)
		);

	}

	public function Setup(){
		add_action('yc__widgets__selector',array($this,'widget__setup'));
	}

}
(new slider_intro_v1)->Setup();