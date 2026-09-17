<?/**
 * 
 */
class Faqs__simple2 extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'Faqs__simple2';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}

		if( !isset( $ActiveStep ) || isset( $ActiveStep ) && !is_numeric( $ActiveStep )  ) $ActiveStep = 1;

	    if( !isset( $Faqs__List ) || isset( $Faqs__List ) && empty( $Faqs__List ) ) $Faqs__List = array();
	    $Faqs__List = Sort__this__list($Faqs__List);

		$phonenumber = get_option('phonenumber');
		$whatsapp_number = get_option('whatsapp_number');
		if( isset( $image ) ){
			$get_att_argums = array(
				'id'=>$image_id,
				'size'=>'faqs__image',
			);
			if( isset( $imagae__alt ) ) $get_att_argums['alt'] = $imagae__alt;
		}
		$backfaqs = get_template_directory_uri().'/components/styles/img/faq-one-shape-1.png';
		echo '<div class="container'.( ( isset( $largerContainer ) && $largerContainer == 'on' ) ? ' largerContainer' : '' ).'">';
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
			echo '<div class="-YC-FaqsSimple-Center-v1">';
				echo '<div class="-YC-faqs-simple-title-content">';
					echo '<div class="--faqs-img--">';
						echo ( ( isset( $get_att_argums ) ) ) ? YC_get_attachment( $get_att_argums ) : '';
					echo '</div>';
				echo '</div>';
				echo '<div class="-YC-FaqsSimple-Center-v1">';
					echo '<div class="-YC-FaqsSimple-ItemsCenter-v1">';
						if( !empty( $Faqs__List ) ){
							$v__s = 0;
							$VeDelay=0;
							foreach ( $Faqs__List as $fq__item ) { $v__s++;
								$VeDelay = $VeDelay + 0.1;
								$UN = uniqid();
								echo '<div class="--YC-faq-classes-in-- '.( ( $v__s == 1 ) ? ' active' : '').' animation-hidden"  data-animation-id="fadeInUpBig" data-animation-delay="'.$VeDelay.'s">';
									echo '<div class="-YC-FaqsSimple-Item-v1">';
										echo '<div class="-YC-FaqsSimple-Title" data-toggle-faqs="'.$UN.'">';
											echo '<h2>'.$fq__item[ 'question' ].'</h2>';
											echo '<div class="--YC-icon-faq-">';
												echo '<i class="fa-solid fa-plus"></i>';
											echo '</div>';
										echo '</div>';
										echo '<div class="-FaqsSimple-Content-Row-v1 -Toggle-Content">';
											echo '<div class="-p-FaqsSimple-ContentValue-v1 -ToggleContentValue">'.$fq__item['answer'].'</div>';
										echo '</div>';
									echo '</div>';
								echo '</div>';
							}  
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
			'title'=>'الاسئلة الشائعة ',
			'description'=>' # شكل 1',
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
					'type'=>'Title',
					'id'=>'hfrtrhfrtyhfrth',
					'title'=>'الاعدادت الخاصة بالزر',
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
					'type'=>'File',
					'id' => 'image',
					'title' =>'صورة الشريحة ',
					'disc'=>'يجب ان تكون الصورة  450x350'
				),
				array(
					'type'=>'Text',
					'id'=>'imagae__alt',
					'title'=>'عنوان الصورة ',
				),
				array(
					'type'=>'Title',
					'id' => 'wdwwdunter__titledqwdqwd',
					'title' =>'اعدادات الاسئلة الشائعة',
				),
				array(
					'type'=>'GroupsField',
					'id' => 'Faqs__List',
					'title' =>'إختيار الاسئلة الشائعة المراد إدراجها ',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'question',
							'title'=>'عنوان أخر ',
							'require'=>true,
						),
						array(
							'type'=>'Editor',
							'id'=>'answer',
							'title'=>'إجابة السؤال',
							'require'=>true,
						),
						array(
							'type'=>'Text',
							'id'=>'number',
							'title'=>'ترتيب السؤال داخل المجدموعة',
						),
					)
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
			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new Faqs__simple2)->Setup();