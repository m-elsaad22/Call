<?
$metaboxes['citiesOptions'] = array(
	'title'    => 'إعدادات التصنيف',
	'number'=>1,
	'fields'  => array(
		array(
			'title'  => ' ايقونةالمدينة',
			'type'  => 'TextArea_Code',
			'id'    => 'icon',
		),
		array(
			'title'  => 'صورة المدينة',
			'en_title' => 'City image',
			'type'  => 'File',
			'id'    => 'image_blog_id',
		),
		array(
			'title'  => 'SEO عبر Rank Math',
			'type'  => 'Title',
			'id'    => 'kayan_rank_math_city_seo_note',
			'disc'  => 'عنوان ووصف المدينة يُعدَّلان من Rank Math (rank_math_title / rank_math_description) في تصنيف المدينة.',
		),
	),
);