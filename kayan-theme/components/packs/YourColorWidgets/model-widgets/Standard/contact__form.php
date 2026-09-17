<?/**
 * 
 */
class contact__form extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'contact__form';
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

		$FieldSetup = array(
			array(
				'title'=>'الاسم بالكامل',
				'id'=>'user__name',
				'type'=>'Text',
				'Require'=>'on',
			),					
			array(
				'title'=>'البريد الالكتروني',
				'id'=>'user_mail',
				'type'=>'Email',
				'Require'=>'on',
			),
			array(
				'title'=>'رقم الهاتف',
				'id'=>'phone__number',
				'disc'=>'مثال : 12345678910',
				'type'=>'Number',
				'Require'=>true,
				'max'=>'99999999999'
			),
			array(
				'title'=>'الخدمة المطلوبة',
				'id'=>'description',
				'type'=>'TextArea',
				'Require'=>'on',
			)		
		);

	    if( !isset( $services_form ) || isset( $services_form ) && empty( $services_form ) ) $services_form = array();
	    $services_form = Sort__this__list($services_form);
	    $uniqid = uniqid();
		if( isset( $image ) ){
			$get_att_argums = array(
				'id'=>$image_id,
				'size'=>'contact_us',
			);
			if( isset( $imagae__alt ) ) $get_att_argums['alt'] = $imagae__alt;
		}
		echo '<div class="container'.( ( isset( $largerContainer ) && $largerContainer == 'on' ) ? ' largerContainer' : '' ).'">';
			echo '<div class="-YC-contact--form-container">';
				echo '<div class="-YC-contact--forms-start ">';
					echo '<div class="contact-start">';
						echo '<div class="-YC-contact--forms-title-s1">';
							if( isset( $before_title ) && !empty( $before_title ) ) echo '<div class="sup-title-widget-defualt animation-hidden" data-animation-id="fadeInUpBig">'.$before_title.'</div>';
							if( isset( $title ) && !empty( $title ) ) echo '<h2 class="-forms-title-h1 animation-hidden" data-animation-id="fadeInUpBig">'.$title.'</h2>';
							if( isset( $content ) && !empty( $content ) ) echo '<div class="P-content animation-hidden" data-animation-id="fadeInUpBig" data-animation-delay="0.1s">'.$content.'</div>';
						echo '</div>';
					echo '</div>';
					if(!empty($contact_footer_list)){
						echo '<div class="YC-wigdht-contact-minibox animation-hidden" data-animation-id="fadeInUpBig">';
							$SocialIcon = array(
								'phonenumber'=>'<i class="fa-solid fa-phone"></i>',
								'company__adress'=>'<i class="fa-solid fa-location-dot"></i>',
								'whatsapp'=>'<i class="fa-brands fa-whatsapp"></i>',
								'company__mail'=>'<i class="fa-solid fa-envelope"></i>',
							);
							$VeDelay=0;	
							foreach ( $contact_footer_list as $social__item ) {
								if ( $social__item === 'phonenumber' && ( ! function_exists( 'kayan_ui_show_call_button' ) || ! kayan_ui_show_call_button() ) ) {
									continue;
								}
								$VeDelay = $VeDelay + 0.1;
								$social_value = get_option($social__item);
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
										$Name__value = 'رقم الهاتف';
									}
									if( $social__item == 'company__mail' ) {
										$URL__value = get_option("footer__{$social__item}_url");
										$Name__value = 'راسلنا عبر البريد';
									}
									if( $social__item == 'company__adress' ) {
										$URL__value = get_option("footer__{$social__item}_url");
										$Name__value = 'العنوان ';
									} 
									echo '<div class="'.$social__item.'">';
										echo ( ( !empty( $URL__value ) ) ) ? '<a target="_blank" href="'.$URL__value.'"  title="'.$Name__value.'">' : '';
											echo $SocialIcon[ $social__item ];
										echo '<div class="value_info">';
											echo '<div class="_value">'.$Name__value.'</div>';
											echo '<span>'.$social_value.'</span>';
										echo'</div>';
										echo ( ( !empty( $URL__value ) ) ) ? '</a>' : '';
									echo '</div>';
								}
							}
						echo '</div>';
						echo '<div class="-YC-Forms-seviesRequest-form animation-hidden" data-animation-id="fadeInUpBig">';
							if( isset( $social_list ) ) {
								$SocialIcon = array(
								    'facebook'=>'<i class="fab fa-facebook-f"></i>',
								    'twitter'=>'<i class="fab fa-twitter"></i>',
								    'telegram'=>'<i class="fa-brands fa-telegram"></i>',
								    'youtube'=>'<i class="fab fa-youtube"></i>',
								    'linkedin'=>'<i class="fab fa-linkedin-in"></i>',
								    'instagram'=>'<i class="fab fa-instagram"></i>',
								    'threads'=>'<i class="fa-brands fa-threads"></i>'
								);
								echo '<div class="-seviesRequest-shares-items">';
									foreach ( $social_list as $social__item ) {
										$social_value = get_option($social__item);
										if( !empty($social_value) ) {
											echo'<a class="'.$social__item.'" title="'.$social__item.'" target="_blank" href="'.$social_value.'">'.$SocialIcon[ $social__item ].'</a>';
										}
									}
								echo '</div>';						
							}
				    	echo '</div>';
					}
				echo '</div>';
				echo '<div class="YC--contact--form-boxarea animation-hidden" data-animation-id="fadeInUpBig">';
					if( isset($Faqs_Steps)){
						echo'<div class="--counter-flex--">';
							foreach ($Faqs_Steps as $k => $v) {
								# counter blocks
								echo'<div class="-count-slice-">';
									echo'<div class="-counter-icon-">';
										echo ( ( !empty( $v['icon'] ) ) ) ? ''.$v['icon'].'' : '';
									echo'</div>';
									echo'<div class="-counter-number-icon-">';
										echo ( ( !empty( $v['number'] ) ) ) ? '<div class="--counter-number--" counterup="'.$v['number'].'">' .$v['number']. '<em><i class="fa-solid fa-plus"></i></em></div>' : '';
									echo'</div>';
									echo ( ( !empty( $v['description'] ) ) ) ? '<p class="--counter-des--">' .$v['description']. '</p>' : '';
								echo'</div>';
							}
						echo'</div>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,			
			'title'=>'نموذج الاتصال',
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
					'title'  => 'تحديد عناصر الاتصال المراد عرضها',
					'en_title'=> 'contact_footer_list',
					'type'  => 'CheckBox',
					'id'    => 'contact_footer_list',
					'options'=>array(
						'company__mail'=>'البريد الالكتروني للشركة',
						'whatsapp_number'=>'رقم واتساب',
						'company__adress'=>'عنوان الشركة',
						'phonenumber'=>'رقم الهاتف',
					)
				),
					array(
					'title'  => 'تحديد عناصر الاتصال المراد عرضها',
					'en_title'=> 'social_header_list',
					'type'  => 'CheckBox',
					'id'    => 'social_list',
					'options'=>array(
						'facebook'=>'facebook',
						'twitter'=>'twitter',
						'telegram'=>'telegram',
						'youtube'=>'youtube',
						'linkedin'=>'linkedin',
						'instagram'=>'instagram',
						'threads'=>'threads'
					)
				),
				array(
					'type'=>'GroupsField',
					'id' => 'Faqs_Steps',
					'title' =>'شكل الشريحة',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الايقونة',
						),
						array(
							'type'=>'Text',
							'id'=>'number',
							'title'=>'العدد',
						),
						array(
							'type'=>'TextArea',
							'id'=>'description',
							'title'=>'الوصف',
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
(new contact__form)->Setup();