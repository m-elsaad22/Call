<?
$metaboxes = array(
	'title'    => ' الرئيسية — المناطق ',
	'en_title' => 'Homepage — Areas',
	'icon'     => '<i class="fa-solid fa-map-location-dot"></i>',
	'number'   => 44,
	'disc'     => 'بطاقات المدن من تصنيف «city» في الموقع. النص من {{all_regions}} و {{areas_intro}}.',
	'fields'   => array(
		array(
			'title' => 'إخفاء قسم المناطق',
			'type'  => 'SwitchBox',
			'id'    => 'kayan_hp_areas_disable',
		),
		array(
			'title' => 'وسم القسم (عربي)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_areas_tag',
		),
		array(
			'title' => 'عنوان القسم (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_areas_title',
		),
		array(
			'title' => 'مقدمة المناطق (عربي)',
			'type'  => 'TextArea',
			'id'    => 'kayan_homepage_areas_intro',
			'desc'  => 'يُستخدم أيضاً في خريطة المناطق — افتراضي: {{areas_intro}}',
		),
		array(
			'title' => 'نص زر «لم تجد مدينتك»',
			'type'  => 'Text',
			'id'    => 'kayan_hp_areas_cta_text',
		),
		array(
			'title' => 'وصف الزر',
			'type'  => 'Text',
			'id'    => 'kayan_hp_areas_cta_sub',
		),
		array(
			'type'  => 'Title',
			'title' => 'English',
		),
		array(
			'title' => 'Section tag (EN)',
			'type'  => 'Text',
			'id'    => 'kayan_hp_areas_tag_en',
		),
		array(
			'title' => 'Section title (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_hp_areas_title_en',
		),
		array(
			'title' => 'Areas intro (EN)',
			'type'  => 'TextArea',
			'id'    => 'kayan_homepage_areas_intro_en',
		),
	),
);
