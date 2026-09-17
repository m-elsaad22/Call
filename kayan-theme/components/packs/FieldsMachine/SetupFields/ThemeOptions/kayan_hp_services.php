<?
$metaboxes = array(
	'title'    => ' الرئيسية — الخدمات ',
	'en_title' => 'Homepage — Services',
	'icon'     => '<i class="fa-solid fa-screwdriver-wrench"></i>',
	'number'   => 43,
	'disc'     => 'بشكل افتراضي تُعرض آخر المقالات/الخدمات المنشورة. يمكنك تعريف بطاقات مخصصة أدناه لتجاوز ذلك.',
	'fields'   => array(
		array(
			'title' => 'إخفاء قسم الخدمات',
			'type'  => 'SwitchBox',
			'id'    => 'kayan_hp_services_disable',
		),
		array(
			'title' => 'وسم القسم (عربي)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_services_tag',
		),
		array(
			'title' => 'عنوان القسم (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_services_title',
			'desc'  => 'يمكن استخدام &lt;span&gt; للتمييز',
		),
		array(
			'title' => 'وصف القسم (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_services_subtitle',
		),
		array(
			'title' => 'عدد الخدمات من الموقع',
			'type'  => 'Number',
			'id'    => 'kayan_hp_services_count',
			'desc'  => 'من المقالات المنشورة (1–12) إن لم تُعرّف بطاقات مخصصة',
		),
		array(
			'type'  => 'Title',
			'title' => 'English',
		),
		array(
			'title' => 'Section tag (EN)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_services_tag_en',
		),
		array(
			'title' => 'Section title (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_services_title_en',
		),
		array(
			'title' => 'Section subtitle (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_services_subtitle_en',
		),
		array(
			'type'  => 'Title',
			'title' => 'بطاقات مخصصة (اختياري)',
			'disc'  => 'إن أضفت بطاقة واحدة على الأقل تُستخدم بدلاً من مقالات الموقع.',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_services_cards',
			'title'  => 'بطاقة خدمة',
			'fields' => array(
				array( 'title' => 'الأيقونة (Font Awesome)', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'العنوان', 'type' => 'Text', 'id' => 'title' ),
				array( 'title' => 'الوصف', 'type' => 'TextArea', 'id' => 'desc' ),
				array( 'title' => 'الرابط', 'type' => 'Text', 'id' => 'url' ),
				array( 'title' => 'المميزات (سطر لكل نقطة)', 'type' => 'TextArea', 'id' => 'bullets' ),
			),
		),
	),
);
