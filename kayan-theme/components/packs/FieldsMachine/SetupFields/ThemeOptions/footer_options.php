<?
$menus = array();
foreach( wp_get_nav_menus() as $t ) {
    $menus[$t->term_id] = $t->name;
}
$metaboxes = array(
	'title'    =>'إعدادات الفوتر ',
	'en_title'  => 'Footer Options',
	'icon'    => '<i class="fa-solid fa-arrow-down"></i>',
	'number'=>3,
	'fields'  => array(

   		array(
			'title'  => 'الشريحة الاولى',
			'disc' => 'قُم بتحديد إعدادات الشريحة الاولى من الفوتر ',
			'en_title'=> 'logoo_setting',
			'type'  => 'Title',
			'id'    => 'First_footer__title',
		),

		array(
			'id'=>'footer__logo',
			'type'=>'Models-Selector',
			'title'=>'تحديد نوع شعار الفوتر',
			'select_field'=>array(
				'id'=>'logo__mode',
				'type'=>'Select',
				'selected_shows'=>true,
				'title'=>'تحديد نوع الشعار',
				'options'=>array(
					'Image' => 'صورة',
					'Text'=>'نص',
				),
			),
			'create_fields'=>true,
			'choose_fields'=>array(
				'Image' => array(
					'id'=>'Image',
					'title' => 'صورة',
					'fields'=> array(
						array(
							'id'    => 'image_logo',
							'type'  => 'File',
							'title' => 'الشعار',
						),
						array(
							'id'    => 'header__alt',
							'type'  => 'Text',
							'title' => 'النص البديل',
						),						
						array(
							'id'    => 'image_logo_width',
							'type'  => 'Text',
							'title' => 'عرض الشعار',
						),
						array(
							'id'    => 'image_logo_height',
							'type'  => 'Text',
							'title' => 'طول الشعار',
						),
					),
				),
				'Text'=>array(
					'id'=>'Text',
					'title' => 'نص',
					'fields'=> array(
						array(
							'id'    => 'logo_Text',
							'type'  => 'Text',
							'title' => 'نص الشعار ',
							'disc'=> "قَم بتمييز كلمة محددة في الشعار عن طريق إضافة ' {% ' قبل بداية الكلمة و ' %} ' بعد نهاية الكلمة .. كما يمكنك تحديد لون مخصص من خلال <p>#تحديد_الكلمة_المميزة_بالشعار </p>" ,
						),
						array(
							'type'=>'Color',
							'id' => 'primary_color',
							'title' =>'صورة الفيديو ',
						),
						array(
							'type'=>'Color',
							'id' => 'secondary_color',
							'title' =>'تحديد الكلمة المميزة بالشعار',
						),
						array(
							'id'    => 'header__alt',
							'type'  => 'Text',
							'title' => 'النص البديل',
						),

					),
				)
			)
		),
		array(
			'title'  => ' إخفاء شعار الفوتر',
			'en_title'=> 'logo_footer',
			'type'  => 'SwitchBox',
			'id'    => 'hide_logo_footer',
		),

		array(
			'id'=> 'footer__content',
			'type'=>'TextArea',
			'title'=> 'وصف الشركة',
		),
		array(
			'title'  => 'إخفاء الوصف',
			'en_title'=> 'descriptionfooter',
			'type'  => 'SwitchBox',
			'id'    => 'hide_description_footer',
		),	
		array(
			'title'  => 'إخفاء شريحة الشعار ',
			'en_title'=> 'hide_footer__first__slice',
			'type'  => 'SwitchBox',
			'id'    => 'hide_footer__first__slice',
		),	

		##
   		array(
			'title'  => 'الشريحة الثانية',
			'disc' => 'قُم بتحديد االقائمة المراد عرضها في الشريحة الثانية من الفوتر ',
			'en_title'=> 'logoo_setting',
			'type'  => 'Title',
			'id'    => 'second_footer__title',
		),
		array(
			'id'=> 'footer__title_first_menu',
			'type'=>'Text',
			'title'=> 'عنوان القائمة الاولى ',
		),
		array(
			'id'=> 'footer__first_menu',
			'type'=>'Select',
			'title'=> 'تحديد القائمة الاولى ',
			'options'=>$menus,
		),
		array(
			'title'  => ' إخفاء القائمة',
			'en_title'=> 'mapfooter',
			'type'  => 'SwitchBox',
			'id'    => 'hide_footer__first_menu',
		),	

		##
   		array(
			'title'  => 'الشريحة الثالثة',
			'disc' => 'قُم بتحديد المراد عرضه في الشريحة الثالثه من الفوتر ',
			'en_title'=> 'logoo_setting',
			'type'  => 'Title',
			'id'    => 'third_footer__title',
		),
		array(
			'id'=> 'footer__title_second_menu',
			'type'=>'Text',
			'title'=> 'عنوان القائمة الثالثه',
		),
		array(
			'id'=> 'footer__second_menu',
			'type'=>'Select',
			'title'=> 'تحديد القائمة الثانية',
			'options'=>$menus,
		),
		array(
			'title'  => ' إخفاء القائمة',
			'en_title'=> 'mapfooter',
			'type'  => 'SwitchBox',
			'id'    => 'hide_footer__second_menu',
		),	
		
		##
   		array(
			'title'  => 'شريحة بيانات الاتصال',
			'disc' => 'قُم بتحديد المراد عرضه في بيانات الاتصال ',
			'en_title'=> 'logoo_setting',
			'type'  => 'Title',
			'id'    => 'third_footer__title',
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
			'id'=> 'footer__company__adress_url',
			'type'=>'Text',
			'title'=> 'رابط عنوان الشركة',
		),
		array(
			'id'=> 'footer__company__mail_url',
			'type'=>'Text',
			'title'=> 'رابط البريد الالكتروني',
		),

		array(
			'title'  => 'إخفاء شريحة الاتصال',
			'type'  => 'SwitchBox',
			'id'    => 'hide_contact__footer',
		),

		##
 		array(
			'title'  => 'شريحة الخريطة',
			'en_title'=> 'social setting footer',
			'type'  => 'Title',
			'id'    => 'map_setting_footer',
		),
 		array(
			'title'  => 'إخفاء خريطة الفوتر',
			'type'  => 'SwitchBox',
			'id'    => 'hide_footer__map',
		),
		##
 		array(
			'title'  => 'إعدادات الأزرار العائمة',
			'en_title'=> 'floating buttons',
			'type'  => 'Title',
			'id'    => 'floating_btns_title',
		),
		array(
			'title'  => 'إخفاء زر الاتصال العائم من كل الصفحات',
			'disc'   => 'يُخفي زر الاتصال الأزرق العائم فقط من جميع صفحات الموقع — لا يؤثر على بطاقة المقال أو الودجات',
			'type'  => 'SwitchBox',
			'id'    => 'hide__floating__call',
		),
		array(
			'title'  => 'إخفاء زر واتساب العائم من كل الصفحات',
			'disc'   => 'يُخفي زر واتساب العائم وزر FAB من جميع صفحات الموقع',
			'type'  => 'SwitchBox',
			'id'    => 'hide__floating__whatsapp',
		),
		array(
			'title'  => 'إخفاء الزر العائم حسب التصنيف',
			'disc'   => 'اختر تصنيفاً أو أكثر — يُخفى زر الاتصال العائم فقط من مقالات هذه التصنيفات. بطاقة المقال والودجات لا تتأثر',
			'type'   => 'CheckBox',
			'id'     => 'hide__floating__call__categories',
			'options'=> ( function(){
				$cats = get_categories( array('hide_empty'=>false) );
				$opts = array();
				foreach( $cats as $cat ) $opts[ $cat->term_id ] = $cat->name;
				return $opts;
			} )(),
		),
		array(
			'title'  => 'إخفاء واتساب العائم حسب التصنيف',
			'disc'   => 'اختر تصنيفاً أو أكثر — يُخفى زر واتساب العائم من مقالات هذه التصنيفات',
			'type'   => 'CheckBox',
			'id'     => 'hide__floating__whatsapp__categories',
			'options'=> ( function(){
				$cats = get_categories( array('hide_empty'=>false) );
				$opts = array();
				foreach( $cats as $cat ) $opts[ $cat->term_id ] = $cat->name;
				return $opts;
			} )(),
		),
		array(
			'title'  => 'تفعيل نافذة واتساب حسب التصنيف',
			'disc'   => 'اختر تصنيفاً أو أكثر — ستظهر نافذة واتساب مفتوحة تلقائياً بدلاً من زري الاتصال والواتساب في مقالات هذه التصنيفات. يُستخدم رقم الواتساب من إعدادات القالب.',
			'type'   => 'CheckBox',
			'id'     => 'whatsapp_chat__categories',
			'options'=> ( function(){
				$cats = get_categories( array('hide_empty'=>false) );
				$opts = array();
				foreach( $cats as $cat ) $opts[ $cat->term_id ] = $cat->name;
				return $opts;
			} )(),
		),
		array(
			'title'  => 'عنوان نافذة الواتساب (إعداد عام)',
			'disc'   => 'الافتراضي: اسم الموقع. يمكنك استخدام {site} كمتغير. يُستخدم عند تفعيل وضع المحادثة من التصنيفات.',
			'type'   => 'Text',
			'id'     => 'whatsapp_chat_title',
		),
		array(
			'title'  => 'رسالة الترحيب (إعداد عام)',
			'disc'   => 'الافتراضي: مرحباً! {اسم المقال}، {اسم الموقع}. متغيرات: {title} {site}. اتركه فارغاً للنص التلقائي.',
			'type'   => 'TextArea',
			'id'     => 'whatsapp_chat_message',
		),
		array(
			'title'  => 'إعدادات اخري ',
			'en_title'=> 'social setting footer',
			'type'  => 'Title',
			'id'    => 'social_setting_footer',
		),
		array(
			'title'  => 'اخفاء روابط التواصل الاجتماعي من الفوتر',
			'en_title'=> 'social_footer',
			'type'  => 'SwitchBox',
			'id'    => 'social_footer',

		),
		array(
			'title'  => 'تحديد عناصر الاتصال المراد عرضها',
			'en_title'=> 'social_header_list',
			'type'  => 'CheckBox',
			'id'    => 'social_footer_list',
			'options'=>array(
				'facebook'=>'facebook',
				'twitter'=>'twitter',
				'telegram'=>'telegram',
				'youtube'=>'youtube',
				'linkedin'=>'linkedin',
				'instagram'=>'instagram',
			)
		),
		array(
			'id'=> 'Copyrights',
			'type'=>'Text',
			'title'=> 'حقوق النشر ',
			'disc'=> 'لإضافة السنة الحالية قم باستبدال نص السنة بـ  {%YEAR%} .. مثل حقوق النشر {%YEAR%} © جميع الحقوق محفوظة لصالح "اسم مدونتك" '
		),

	)
);