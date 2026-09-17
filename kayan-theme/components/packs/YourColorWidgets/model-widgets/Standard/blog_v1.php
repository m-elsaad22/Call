<?/**
 * 
 */
class blog_v1 extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'blog_v1';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);

		# Field_SelectOptions	
			$this->Field_SelectOptions = array(
				'latest'=>'بدون تحديد  ( الاحدث ) ',
				'most_views'=>'الاكتر مشاهدة ',
				'most_rate'=>'الاكثر تقيما ',
				'pin'=>'المثبت ',
			);

	}

	public function widget__ui($vars){
		extract($vars);

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}

		if( !isset( $Filter ) ) $Filter = 'latest';

		if( !isset( $posts_per_page ) ) $posts_per_page = 30;

		$PostsArguments = array(
			'post_type'=>'post',
			'posts_per_page'=> $posts_per_page,
		);

		$Filterservices = false;

		if( isset( $category ) && !empty( $category ) ){
			$category_term = get_term_by('id',$category,'category');

			$PostsArguments['tax_query']['relation']='AND';
			$PostsArguments['tax_query'][] = array(
		    'taxonomy'  => $category_term->taxonomy,
		    'field'   	=> ($category_term->taxonomy == 'category') ? 'term_id' : 'slug',
		    'terms'   	=> ($category_term->taxonomy == 'category') ? $category_term->term_id : $category_term->slug,
		    'operator'  => 'IN'
			);
		}


		if( isset( $current_obj ) && $current_obj == 'on' && isset( $obj ) ){
			$PostsArguments['tax_query']['relation']='AND';
			$PostsArguments['tax_query'][] = array(
		    'taxonomy'  => $obj->taxonomy,
		    'field'   	=> ($obj->taxonomy == 'category') ? 'term_id' : 'slug',
		    'terms'  		=> ($obj->taxonomy == 'category') ? $obj->term_id : $obj->slug,
		    'operator'  => 'IN'
			);
		}

		if( isset( $Filter ) && !empty( $Filter ) ){

			if( $Filter == 'most_views' ) {
			    $PostsArguments['meta_key'] = 'views';
			    $PostsArguments['orderby'] = 'meta_value_num';

			}else if( $Filter == 'most_rate' ) {
			    $PostsArguments['meta_key'] = 'TotalRate';
			    $PostsArguments['orderby'] = 'meta_value_num';

			}else if( $Filter == 'pin' ) {
		    	$PostsArguments['meta_key'] = 'pin';

			}else if( $Filter == 'rand' ) {
	    		$PostsArguments['orderby'] = 'rand';

			}else if( $Filter == 'old' ) {
	    		$PostsArguments['order'] = 'ASC';
			}
		}
		$uniqid = uniqid();
		echo '<div class="container'.( ( isset( $largerContainer ) && $largerContainer == 'on' ) ? ' largerContainer' : '' ).'">';
			echo '<div class="-widgets-blog-posts-container">';
				echo '<div class="-defult-widgets-felx-style-1">';

					if( isset( $before_title ) || isset( $title ) || isset( $content ) ){

						
						echo '<div class="-defult-widgets-title-style-1">';
							echo '<div class="-YC--main--wep-title-">';
								if( isset( $before_title ) && !empty( $before_title ) ) echo '<div class="sup-title-widget-defualt animation-hidden" data-animation-id="fadeInUpBig">'.$before_title.'</div>';
								if( isset( $title ) && !empty( $title ) ) echo '<h2 class="-widgets-h1-title animation-hidden" data-animation-id="fadeInUpBig">'.$title.'</h2>';
								if( isset( $content ) && !empty( $content ) ){
									echo '<div class="P-content animation-hidden" data-animation-id="fadeInUpBig">'.$content.'</div>';
								}
							echo '</div>';
							if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) || !empty( $second_button ) && isset( $second_button['button_mode'] ) && isset( $second_button[ $second_button['button_mode'] ] ) ){
								echo '<div class="-defult-widgets-title--URLArea-v1">';
									if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) ){
										$this->ThemeStatic->Part(
											'button_context',
											array(
												'attributes'=>'data-animation-id="fadeInUpBig" data-animation-delay="0.2s"',
												'class'=>' --Parent-URL-BTN animation-hidden',
												'href_class'=>'activable  btn-ket_2 -BTN--hoverable',
												'button_context'=>$first_button
											)
										);
									}

									if( !empty( $second_button ) && isset( $second_button['button_mode'] ) && isset( $second_button[ $second_button['button_mode'] ] ) ){
										$this->ThemeStatic->Part(
											'button_context',
											array(
												'attributes'=>'data-animation-id="fadeInUpBig" data-animation-delay="0.2s"',
												'class'=>' animation-hidden',
												'href_class'=>'activable  btn-ket_1 -BTN--hoverable button_url_2',
												'button_context'=>$second_button
											)
										);
									}	
								echo '</div>';
							}

						echo '</div>';
					}
				echo '</div>';

				echo '<div class="-widgets-blog-posts-center">';
					echo '<div class="-inner-widgets-blog-posts-center">';
						$i = 0;
						$VeDelay = 0;
						foreach ( get_posts($PostsArguments) as $post) {$i++;
							$VeDelay = $VeDelay + 0.1;
							$this->ThemeStatic->Blade('Box',array('post'=>$post,'animation'=>$VeDelay),'Post-box');
						}

					echo '</div>';
				echo '</div>';

			echo '</div>';

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,			
			'title'=>'المقالات ',
			'description'=>' # شكل ',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'قبل العنوان ',
				),

				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'عنوان الشريحة ',
					'disc'=> "قَم بتمييز كلمات محدده في العنوان عن طريق إضافة ' {% ' قبل بداية الجملة و ' %} ' بعد نهاية الجملة .. كما يمكنك تحديد لون مخصص من خلال <p>#تحديد_الكلمات_المميزة_بالعنوان </p>" ,
				),
				array(
					'type'=>'Editor',
					'id' => 'content',
					'title' =>'وصف الشريحة ',
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
							'default' => 'بدون تحديد ',
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
					'id'=>'second_button',
					'type'=>'Models-Selector',
					'title'=>'إعدادات رابط خطط الاسعار',
					'select_field'=>array(
						'id'=>'button_mode',
						'type'=>'Select',
						'selected_shows'=>true,
						'title'=>'تحديد نوع رابط الزرار',
						'options'=>array(
							'default' => 'بدون تحديد ',
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
				#
				array(
					'type'=>'Title',
					'id' => 'wsedewdfd',
					'title' =>'إعدادات الظهور ',
				),

				array(
					'type'=>'Text',
					'id' => 'posts_per_page',
					'title' =>'عدد المقالات',
				),

				array(
					'type'=>'Taxonomy-Select',
					'id' => 'category',
					'taxonomy_name'=>'category',
					'parent'=>0,
					'per'=>100,
					'title' =>'مقالات من تصنيف محدد',
				),

				array(
					'type'=>'SwitchBox',
					'id' => 'current_obj',
					'title' =>'مقالات حسب نوع الارشيف',
					'disc'=>'مقالات حسب صفحة الارشيف الحالية'
				),

				array(
					'type'=>'Select',
					'id' => 'Filter',
					'title' =>'فلترة حسب ',
					'options'=>$this->Field_SelectOptions,
				),
				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'wsedewdfd',
					'title' =>'إعدادات الظهور ',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'mobile_hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً في الموبيل',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'show_top_separator',
					'title' =>'تغيير لون الخلفيه',
					'disc'=>'هل تريد تغيير لون الخلفية؟',
				)				
			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new blog_v1)->Setup();