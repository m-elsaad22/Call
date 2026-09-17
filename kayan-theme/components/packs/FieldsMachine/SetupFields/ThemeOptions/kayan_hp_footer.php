<?
$metaboxes = array(
	'title'    => ' الرئيسية — الفوتر ',
	'en_title' => 'Homepage — Footer',
	'icon'     => '<i class="fa-solid fa-shoe-prints"></i>',
	'number'   => 45,
	'disc'     => 'الشعار والقوائم من «إعدادات الفوتر». الخريطة من «إعدادات الاتصال» (company__map_code). حقوق النشر من حقل Copyrights في الفوتر.',
	'fields'   => array(
		array(
			'title' => 'إخفاء الفوتر',
			'type'  => 'SwitchBox',
			'id'    => 'kayan_hp_footer_disable',
		),
		array(
			'type'  => 'Title',
			'title' => 'محتوى إضافي',
			'disc'  => 'إن تُرك فارغاً يُستخدم «وصف الشركة» من إعدادات الفوتر (footer__content).',
		),
		array(
			'title' => 'وصف التذييل (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_footer_tagline',
		),
		array(
			'title' => 'حقوق النشر (عربي) — احتياطي',
			'type'  => 'Text',
			'id'    => 'kayan_hp_footer_copyright',
			'desc'  => 'الأولوية لحقل Copyrights في إعدادات الفوتر',
		),
		array(
			'title' => 'عنوان القائمة الأولى (عربي)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_footer_menu1_title',
		),
		array(
			'title' => 'عنوان القائمة الثانية (عربي)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_footer_menu2_title',
		),
		array(
			'title' => 'عنوان الروابط السريعة (عربي)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_footer_menu3_title',
		),
		array(
			'type'  => 'Title',
			'title' => 'English',
		),
		array(
			'title' => 'Footer tagline (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_homepage_footer_tagline_en',
		),
		array(
			'title' => 'Copyright (EN)',
			'type'  => 'Text',
			'id'    => 'kayan_homepage_copyright_en',
		),
		array(
			'type'  => 'Title',
			'title' => 'الخريطة',
			'disc'  => 'تُدار من: إعدادات الاتصال → كود الخريطة. الإخفاء من: إعدادات الفوتر → إخفاء خريطة الفوتر.',
		),
	),
);
