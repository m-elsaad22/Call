<?
$metaboxes = array(
	'title'    => 'إعدادات الاتصال ',
	'en_title'  => 'CONTACT OPTIONS',
	'icon'    => '<i class="fa-solid fa-share-from-square"></i>',
	'number'=>14,
	'fields'  => array(
		array(
			'id'=> 'contact__fields',
			'type'=>'Title',
			'title'=> 'اعدادات الاتصال',
		),

		array(
			'id'=> 'company__mail',
			'type'=>'Text',
			'title'=> 'االبريد الاكتروني',
		),
		array(
			'id'=> 'phonenumber',
			'type'=>'Text',
			'title'=> 'رقم التواصل ',
		),
		array(
			'id'=> 'whatsapp_number',
			'type'=>'Text',
			'title'=> 'رقم WhatsApp',
		),
		array(
			'id'=> 'company__adress',
			'type'=>'Text',
			'title'=> 'العنوان ',
		),

		array(
			'id'=> 'company__map_code',
			'type'=>'TextArea_Code',
			'title'=> 'كود الخريطة',
		),
		array(
			'id'=> 'company__map_title',
			'type'=>'Text',
			'title'=> 'عنوان الخريطة ',
			'disc'=> 'يجب اضافة عنوان للخريطة اجبارياً',
		),


		array(
			'id'=> 'Social__adress',
			'type'=>'Title',
			'title'=> 'مواقع التواصل الاجتماعى ',
		),

		array(
			'title'  => 'فيسبوك',
			'type'  => 'Text',
			'id'    => 'facebook',
		),
		array(
			'title'  => 'تويتير',
			'type'  => 'Text',
			'id'    => 'twitter',
		),
		array(
			'title'  =>'تليجرام ',
			'type'  => 'Text',
			'id'    => 'telegram',
		),
		array(
			'title'  =>'يوتيوب',
			'type'  => 'Text',
			'id'    => 'youtube',
		),
		array(
			'title'  =>'لنيكد ان ',
			'type'  => 'Text',
			'id'    => 'linkedin',
		),
		array(
			'title'  =>'انتسجرام',
			'type'  => 'Text',
			'id'    => 'instagram',
		),
		array(
			'title'  =>'threads',
			'type'  => 'Text',
			'id'    => 'threads',
		),				
	)
);