<?
$metaboxes = array(
	'title'    => ' الرئيسية — البطل ',
	'en_title' => 'Homepage — Hero',
	'icon'     => '<i class="fa-solid fa-star"></i>',
	'number'   => 42,
	'disc'     => 'قسم Hero. استخدم {{company_name}} و {{country_name}} و {{year}}. الهاتف والواتساب من إعدادات الاتصال.',
	'fields'   => array(
		array(
			'title' => 'إخفاء قسم البطل',
			'type'  => 'SwitchBox',
			'id'    => 'kayan_hp_hero_disable',
		),
		array(
			'title' => 'عنوان البطل (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_hero_title',
			'desc'  => 'افتراضي: {{company_name}} — منصة الخدمات المنزلية…',
		),
		array(
			'title' => 'وصف البطل (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_hero_subtitle',
		),
		array(
			'title' => 'عنوان لوحة الخدمات (عربي)',
			'type'  => 'Text',
			'id'    => 'kayan_homepage_dashboard_title',
		),
		array(
			'type'  => 'Title',
			'title' => 'English',
		),
		array(
			'title' => 'Hero title (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_hero_title_en',
		),
		array(
			'title' => 'Hero subtitle (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_hero_subtitle_en',
		),
		array(
			'title' => 'Dashboard title (EN)',
			'type'  => 'Text',
			'id'    => 'kayan_homepage_dashboard_title_en',
		),
		array(
			'type'  => 'Title',
			'title' => 'الهوية في الشعار',
			'disc'  => 'اترك فارغاً لاستخدام «إسم الموقع» من الإعدادات العامة.',
		),
		array(
			'title' => 'جزء الشعار الأول',
			'type'  => 'Text',
			'id'    => 'kayan_homepage_brand_first',
		),
		array(
			'title' => 'جزء الشعار الثاني',
			'type'  => 'Text',
			'id'    => 'kayan_homepage_brand_second',
		),
	),
);
