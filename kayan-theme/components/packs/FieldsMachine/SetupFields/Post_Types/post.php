<?
$metaboxes['page_image'] = array(
	'title'    => 'تحديد صورة وايقونة المقال',
	'fields' => array(
		array(
			'title'  => 'الايقونة',
			'type'  => 'TextArea_Code',
			'id'    => 'articon',
		),

	)
);

$metaboxes['first__posts_edits'] = array(
	'title'    => 'إعدادات المقال ',
	'fields' => array(
		array(
			'title'  => 'SEO عبر Rank Math',
			'type'  => 'Title',
			'id'    => 'kayan_rank_math_post_seo_note',
			'disc'  => 'العنوان والوصف يُخزَّنان في Rank Math (rank_math_title / rank_math_description) من لوحة المقال. بدون Rank Math يُستخدم المقتطف والمحتوى تلقائياً.',
		),
		array(
			'id'=> 'pin',
			'type'=>'SwitchBox',
			'title'=>'المثبت ',
		),
		array(
			'title'  => 'بوستر الاسلايدر',
			'type'  => 'File',
			'id'    => 'cover',
		),
		array(
			'title'  => 'المراجع ',
			'type'  => 'TextArea',
			'id'    => 'references',
		),
		array(
			'title'  => 'رقم واتس اب',
			'type'  => 'Text',
			'id'    => 'whatsapp_number',
		),
		array(
			'title'  => 'رقم الاتصال',
			'type'  => 'Text',
			'id'    => 'phone_number',
		),
        array(
            'type'=>'Text',
            'id' => 'VideoID',
            'title' =>'فيديو القائمة الجانبية',
        ),

	)
);

$insert_fields = array(
	array(
		'title'  => 'التقييم الافتراضي',
		'type'  => 'Number',
		'id'    => 'ratingValue',
	),
);
for ($i=1; $i < 6; $i++) { 
	$insert_fields[] = array(
		'title'  => 'اجمالى عدد الاشخاص المختارة ('.$i.')',
		'type'  => 'Number',
		'id'    => 'ratingUsers_'.$i,
	);
}

$metaboxes['rating__post_edits'] = array(
	'title'=>'التقيمات',
	'fields'=>array(
		array(
			'title'  =>'إعدادات التقييم الافتراضية',
			'type'  => 'SingleGroup',
			'id'    => 'defualt__rating',
			'is__open'=>true,
			'fields'=> $insert_fields
		)
	)
);

$metaboxes['cards__posts_edits'] = array(
	'title'=>'بطاقة المقال',
	'fields'=>array(
		array(
			'title'  => 'إخفاء بطاقة طلب الخدمة',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post_card',
		),

		array(
			'title'  => 'تحديد مكان الشريحة',
			'type'  => 'Select',
			'id'    => 'position__post_card',
			'options'=>array(
				'top_image'=>'اعلى الصورة',
				'top_content'=>'اعلى المحتوى',
				'bottom_content'=>'اسفل المحتوى',
				'before__references'=>'اسفل المراجع',
				'before_faqs'=>'اسفل الاسئلة الشائعة',
				'before_post_tags'=>'اسفل الوسوم',
				'before_next_and_prev'=>'اعلى المقال التالى والسابق',
				'end__page'=>'اسفل الصفحة'

			)
		),

		array(
			'title'  => 'إعدادات بطاقة طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'post__card__data',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'العنوان',
					'type'  => 'Text',
					'id'    => 'post_card_title',
				),
				array(
					'title'  =>'وصف مختصر',
					'type'  => 'TextArea',
					'id'    => 'post_card_content',
				),
				array(
					'title'  => 'إخفاء زرار الاتصال',
					'type'  => 'SwitchBox',
					'id'    => 'hide__card__callbutton',
				),
				array(
					'title'  => 'تفعيل وضع محادثة الواتساب',
					'disc'   => 'يُخفي زر الاتصال ويُظهر نافذة واتساب عائمة في أسفل الشاشة بدلاً منه',
					'type'   => 'SwitchBox',
					'id'     => 'whatsapp_chat_mode',
				),
				array(
					'title'  => 'عنوان نافذة الواتساب',
					'disc'   => 'اسم يظهر في رأس نافذة المحادثة',
					'type'   => 'Text',
					'id'     => 'whatsapp_chat_title',
				),
				array(
					'title'  => 'رسالة الترحيب',
					'disc'   => 'النص الذي يظهر في فقاعة المحادثة',
					'type'   => 'TextArea',
					'id'     => 'whatsapp_chat_message',
				),
				array(
					'title'  => 'إخفاء زرار الواتساب',
					'type'  => 'SwitchBox',
					'id'    => 'hide__card__whatsapp',
				)
			),
		),
	
	)
);

$metaboxes['card__Popverposts_edits'] = array(
	'title'=>'إعدادات POPUP',
	'fields'=>array(
		array(
			'title'  => 'إخفاء POPUP',
			'type'  => 'SwitchBox',
			'id'    => 'hide__single__popover',
		),
		array(
			'title'  =>'إعدادات POPUP طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'post__popover__data',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'عنوان POPUP',
					'type'  => 'Text',
					'id'    => 'popover_call_title',
				),
				array(
					'title'  => 'نص POPUP',
					'type'  => 'TextArea',
					'id'    => 'popover_call_content',
				),
				array(
					'title'  => 'ايقونة POPUP',
					'disc'=>'في حالة عدم تحديد ايقونة مخصصه سيتم تحديد ايقونة افتراضية',
					'type'  => 'TextArea_Code',
					'id'    => 'popover_call_icon',
				)
			)
		),
	)
);

$metaboxes['third__posts_edits'] = array(
	'title'=>'إطلب الخدمة',
	'fields'=>array(
		array(
			'title'  => 'إخفاء ودجت طلب الخدمة من هذا المقال',
			'type'  => 'SwitchBox',
			'id'    => 'hide__sidebar__service_request',
		),
		array(
			'title'  =>'إعدادات ودجت طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'post__service_request__data',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'العنوان',
					'type'  => 'Text',
					'id'    => 'orderservices',
				),
				array(
					'title'  =>'وصف مختصر',
					'type'  => 'TextArea',
					'id'    => 'contentservices',
				),
				array(
					'title'  => 'إخفاء زرار الاتصال',
					'type'  => 'SwitchBox',
					'id'    => 'hide__service__callbutton',
				),
				array(
					'title'  => 'إخفاء زرار الواتساب',
					'type'  => 'SwitchBox',
					'id'    => 'hide__service__whatsapp',
				)
			)
		)
	)
);

$metaboxes['secondary__posts_edits'] = array(
	'title'=>'الاسئلة الشائعة ',
	'fields'=>array(
        array(
            'title'=>'تحديد خيارات العملة المراد عرضها ',
            'id'=> 'yourcolor__faqs',
            'type'=>'GroupsField',
            'fields'=>array(
                array(
                    'id'=> 'question',
                    'type'=>'Text',
                    'title'=>'السؤال',
                ),
                array(
                    'id'=> 'answer',
                    'type'=>'TextArea',
                    'title'=> 'الاجابه',
                )
            ),
        ),
	)
);

$metaboxes['pages__posts_edits'] = array(
	'title'=>'صفحات القائمة الجانبية',
	'fields'=>array(
		array(
			'type'=>'GroupsField',
			'id' => 'Pages__List__URL',
			'title' =>'اختر الصفحات المراد عرضها ',
			'disc'=>'في حالة تحديدها من صفحة المقالة يتم اظهار الصفحات المختارة في المقالة',
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
	                'title' =>'إضافة عنوان اخر للصفحة',
	            ),
				array(
					'type'=>'TextArea_Code',
					'id'=>'button_Icon',
					'title'=>'ايقونة',
				),
				array(
					'type'=>'Text',
					'id'=>'number',
					'title'=>'رقم الترتيب ',
					'require'=>true,
				),
			)
		)
	)
);

$metaboxes['posts_ImageObject'] = array(
	'title'=>'Schema ImageObject',
	'fields'=>array(
		array(
			'title'  => 'KAYAN Schema ImageObject',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_ImageObject',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'Hide Schema business',
					'titleEN'=> 'disable local',
					'type'  => 'SwitchBox',
					'id'    => 'hide_schema_ImageObject',
				),
				array(
					'title'  => 'الوصف الافتراضي',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'قيمة contentLocation الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'contentLocation',
				)
			)
		),
	)
);

$metaboxes['posts_YourColor_Service'] = array(
	'title'=>'Schema Service',
	'fields'=>array(
		array(
			'title'  => 'KAYAN Schema Service',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Service',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'Hide Schema Service',
					'titleEN'=> 'disable local',
					'type'  => 'SwitchBox',
					'id'    => 'hide_schema_Service',
				),				
				array(
					'title'  => 'priceRange',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'priceRange',
				),
				array(
					'title'  => 'description',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'addressLocality',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'addressLocality',
				),
				array(
					'title'  => 'postalCode',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'postalCode',
				),
				array(
					'title'  => 'telephone',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'telephone',
				),
				array(
					'title'  => 'addressCountry',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'addressCountry',
				),
				array(
					'title'  => 'streetAddress',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'streetAddress',
				),
				array(
					'title'  => 'addressRegion',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'addressRegion',
				),
				array(
					'title'  => 'areaServed',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'areaServed',
				),
				array(
					'title'  => 'OfferCatalog',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'OfferCatalog',
				),
				array(
					'title'  => 'identifier',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'identifier',
				),
				array(
					'title'  => 'additionalType',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'additionalType',
				),
			)
		),
	)
);		

$metaboxes['posts_YourColor_Article'] = array(
	'title'=>'Schema Article',
	'fields'=>array(
		array(
			'title'  => 'KAYAN Schema Article',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Article',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'Hide Schema Article',
					'titleEN'=> 'disable local',
					'type'  => 'SwitchBox',
					'id'    => 'hide_schema_Article',
				),
				array(
					'title'  => 'headline',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'headline',
				),
				array(
					'title'  => 'description',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'articleBody',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'articleBody',
				),
			)
		),
	)
);

$metaboxes['posts_YourColor_Rating'] = array(
	'title'=>'Rating Schema',
	'fields'=>array(
		array(
			'title'  => 'KAYAN Schema Rating',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor__Rating',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'Rating Value(تقيم المقال )',
					'titleEN'=> 'Rating Value',
					'type'  => 'Number',
					'id'    => 'RatingValue_def',
				),
				array(
					'title'  => 'Best Rating (أفضل تقيم)',
					'titleEN'=> 'Best Rating',
					'type'  => 'Number',
					'id'    => 'Best_Rating_def',
				),
				array(
					'title'  => 'Rating Count(عدد التقيمات)',
					'titleEN'=> 'Rating Count',
					'type'  => 'Number',
					'id'    => 'RatingCount_def',
				),
				
			)
		),
	)
);


$metaboxes['floating__btn__edits'] = array(
	'title'=>'الزر العائم',
	'fields'=>array(
		array(
			'title'  => 'إعدادات الزر العائم',
			'type'   => 'Title',
			'id'     => 'floating__btn__title',
		),
		array(
			'title'  => 'إخفاء زر الاتصال العائم',
			'disc'   => 'يُخفي زر الاتصال العائم فقط في أسفل الشاشة لهذا المقال — لا يؤثر على بطاقة المقال أو ودجات الاتصال/طلب الخدمة',
			'type'   => 'SwitchBox',
			'id'     => 'hide__floating__call',
		),
		array(
			'title'  => 'تفعيل نافذة محادثة الواتساب',
			'disc'   => 'يُخفي زر الاتصال وزر الواتساب العائمين ويُظهر بدلاً منهما نافذة محادثة واتساب مفتوحة تلقائياً. عنوان النافذة = اسم الموقع، والرسالة = مرحباً! اسم المقال، اسم الموقع.',
			'type'   => 'SwitchBox',
			'id'     => 'floating_whatsapp_chat_mode',
		),
		array(
			'title'  => 'عنوان نافذة الواتساب',
			'disc'   => 'الاسم الذي يظهر في رأس نافذة المحادثة — اتركه فارغاً لاستخدام الإعداد العام',
			'type'   => 'Text',
			'id'     => 'floating_whatsapp_chat_title',
		),
		array(
			'title'  => 'رسالة الترحيب',
			'disc'   => 'النص الذي يظهر في فقاعة المحادثة — اتركه فارغاً لاستخدام الإعداد العام',
			'type'   => 'TextArea',
			'id'     => 'floating_whatsapp_chat_message',
		),
	)
);

# --- إعدادات التصنيفات للتحكم بالزر العائم ---
# نجلب قائمة التصنيفات ليختار منها في إعدادات الفوتر (footer_options)
# هذا الحقل يُضاف في footer_options لا في metaboxes المقال
