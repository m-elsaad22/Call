<?php
/**
 * Widget class for handling categories.
 */
class Category extends YC__WidgetsMachine {
    // Define class properties
    public function __construct() {
        parent::__construct();

		$this->widget__name = 'category';
		$this->folder__name = basename(__DIR__);
		$this->ThemeStatic = (new ThemeStatic);

    }
	public function widget__ui($vars){
		extract($vars);

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}
	  	if( empty( $number ) ) $number = 8;

	  	if( isset( $taxonomy_option ) ){
	  		$get_terms = array();
	  		foreach ( array_slice($taxonomy_option,0,$number) as $tx__value){
	  			$s_tems = get_term_by('id',$tx__value,'category');
	  			if( isset( $s_tems->term_id ) ) $get_terms[] = $s_tems;
	  		}
	  	}else{
		  	$TermsArgums =  array(
	            'taxonomy' => 'category',
	            'number'    =>$number,
	        );
	        $get_terms = get_terms($TermsArgums);
	  	}
	  	$defualt__category_icon = get_option('defualt__category_icon_id');
	  	$UNIQ = uniqid();
	  	echo '<div class="--category--background">';
		  	echo '<div class="container">';
		  		
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
				echo '<div class="--PriceLists-Center-area animation-hidden" data-animation-id="fadeInUpBig">';
					echo '<div class="--YC-category-widget">';
						$VeDelay=0;
					 	foreach( $get_terms as $category){
					 		$uniqid = uniqid();
							$VeDelay = $VeDelay + 0.1;
	                        $CategoryName = $category->name;
	                        $CategoryURL = get_term_link($category);
	                        $icon = get_term_meta( $category->term_id,'icon',true );
	                        echo '<div class="--single--category--boxitem" data-trigger-action="'.$uniqid.'">';
	                        	echo '<div class="YC--service-shabe-style">';
		                        	echo '<div class="Yc-service-item-style">';
		                        		echo '<div class="service--item--icon">';
		                        			echo '<div class="--YC-before-back">';
					                	    	if( !empty( $icon ) ){
						                        	echo ''.$icon.'';
						                        }else{
					                    			echo '<i class="fa-solid fa-broom"></i>';
						                        }
					                        echo '</div>';
				                        echo '</div>';
				                        echo '<div class="--YC-category--">';
				                        	echo '<a href="'.$CategoryURL.'" data-trigger-url="' .$uniqid. '" title="'.$category->name.'"><div class="YC-serice-name">'.$category->name.'</div></a>';
				                        	echo '<div class="-p-category-desc"><p>'.wp_trim_words($category->description,15).'</public></div>';
			                        			if( empty( $hide_category_switch )){
			                        				echo '<div class="-btn--category">';
			                        			 		if( isset( $but_text ) && !empty( $but_text ) )echo '<span class="-category-button">' .$but_text. '</span>';
					                        			echo'<i class="fa-solid fa-arrow-left-long"></i>';
					                        		echo '</div>';
			                        			}
		                        		echo '</div>';
		                        	echo '</div>';
	                        	echo '</div>';
	                        echo '</div>';
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
			'title'=>'التصنيفات  ',
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
				array(
					'type'=>'Text',
					'id'=>'but_text',
					'title'=>'عنوان زر التصنيف',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_category_switch',
					'title'=>'هل تريد اخفاء الزر موقتا',
				),
				array(
					'type'=>'Title',
					'id'=>'grdsfg',
					'title'=>'الجزء الخاص بأعدادات زر صفحة التصنيفات',
				),
				array(
	                'type'=>'Posts-Select',
	                'id' => 'button_page',
	                'post_type_name'=>'page',
	                'title' =>'تحديد صفحة التصنيفات',
	            ),
	            array(
	                'type'=>'Text',
	                'id' => 'button_Text',
	                'title' =>'اضافة عنوان اخر لصفحة التصنيفات',
	            ),
				array(
					'type'=>'SwitchBox',
					'id' => 'Button__show',
					'title' =>'أظهار زر المذيد من التصنيفات',
				),
				#
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد التصنيفات',
				),   
				array(
			        'type'    => 'Taxonomy-CheckBox',
			        'id'      => 'taxonomy_option',
			        'title'   => 'اختار التصنيف',
                    'taxonomy_name' => 'category',
                    'pre'=>10
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
(new category)->Setup();