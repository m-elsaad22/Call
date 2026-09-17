<?
$metaboxes = array(
	'title'    => ' الرئيسية — المحتوى ',
	'en_title' => 'Homepage — Content blocks',
	'icon'     => '<i class="fa-solid fa-puzzle-piece"></i>',
	'number'   => 47,
	'disc'     => 'بطاقات الثقة، الإحصائيات، الفريق، لماذا نحن، المقارنة، التقييمات، وCTA. تُملأ تلقائياً عند تثبيت محتوى العرض من صفحة الرئيسية.',
	'fields'   => array(
		array(
			'type'  => 'Title',
			'title' => 'شريط الثقة والإحصائيات',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_trust_items',
			'title'  => 'عنصر شريط الثقة',
			'fields' => array(
				array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'النص', 'type' => 'Text', 'id' => 'text' ),
			),
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_trust_items',
			'title'  => 'إحصائية',
			'fields' => array(
				array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'الرقم', 'type' => 'Text', 'id' => 'count' ),
				array( 'title' => 'لاحقة (+ أو %)', 'type' => 'Text', 'id' => 'suffix' ),
				array( 'title' => 'خانات عشرية', 'type' => 'Number', 'id' => 'dec' ),
				array( 'title' => 'التسمية', 'type' => 'Text', 'id' => 'label' ),
				array( 'title' => 'وصف صغير', 'type' => 'Text', 'id' => 'sublabel' ),
			),
		),
		array(
			'type'  => 'Title',
			'title' => 'لماذا نحن',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_why_steps',
			'title'  => 'خطوة',
			'fields' => array(
				array( 'title' => 'الرقم', 'type' => 'Text', 'id' => 'num' ),
				array( 'title' => 'العنوان', 'type' => 'Text', 'id' => 'title' ),
				array( 'title' => 'الوصف', 'type' => 'TextArea', 'id' => 'desc' ),
			),
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_why_features',
			'title'  => 'ميزة',
			'fields' => array(
				array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'العنوان', 'type' => 'Text', 'id' => 'title' ),
				array( 'title' => 'الوصف', 'type' => 'TextArea', 'id' => 'desc' ),
			),
		),
		array(
			'type'  => 'Title',
			'title' => 'الفريق',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_team_members',
			'title'  => 'عضو الفريق',
			'fields' => array(
				array( 'title' => 'الأحرف الأولى', 'type' => 'Text', 'id' => 'initials' ),
				array( 'title' => 'الاسم', 'type' => 'Text', 'id' => 'name' ),
				array( 'title' => 'الدور', 'type' => 'Text', 'id' => 'role' ),
				array( 'title' => 'التخصص', 'type' => 'TextArea', 'id' => 'spec' ),
				array( 'title' => 'شارة 1', 'type' => 'Text', 'id' => 'badge1' ),
				array( 'title' => 'شارة 2', 'type' => 'Text', 'id' => 'badge2' ),
			),
		),
		array(
			'type'  => 'Title',
			'title' => 'المقارنة',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_compare_rows',
			'title'  => 'صف مقارنة',
			'fields' => array(
				array( 'title' => 'المعيار', 'type' => 'Text', 'id' => 'label' ),
				array( 'title' => 'نحن (نعم)', 'type' => 'SwitchBox', 'id' => 'us' ),
				array( 'title' => 'آخرون (نعم)', 'type' => 'SwitchBox', 'id' => 'them' ),
			),
		),
		array(
			'type'  => 'Title',
			'title' => 'التقييمات (Schema + الرئيسية)',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_seo_home_reviews',
			'title'  => 'تقييم',
			'fields' => array(
				array( 'title' => 'الاسم', 'type' => 'Text', 'id' => 'author' ),
				array( 'title' => 'التقييم (1-5)', 'type' => 'Number', 'id' => 'rating' ),
				array( 'title' => 'النص', 'type' => 'TextArea', 'id' => 'text' ),
				array( 'title' => 'المدينة', 'type' => 'Text', 'id' => 'city' ),
			),
		),
		array(
			'type'  => 'Title',
			'title' => 'CTA والبطل',
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_cta_chips',
			'title'  => 'شارة CTA',
			'fields' => array(
				array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'النص', 'type' => 'Text', 'id' => 'text' ),
			),
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_hero_chips',
			'title'  => 'شارة البطل',
			'fields' => array(
				array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'النص', 'type' => 'Text', 'id' => 'text' ),
			),
		),
		array(
			'type'   => 'GroupsField',
			'id'     => 'kayan_hp_trust_badges',
			'title'  => 'شارة لوحة البطل',
			'fields' => array(
				array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
				array( 'title' => 'النص', 'type' => 'Text', 'id' => 'text' ),
			),
		),
	),
);
