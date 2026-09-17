<?
$metaboxes = array(
	'title'    => ' الرئيسية — الهيدر ',
	'en_title' => 'Homepage — Header',
	'icon'     => '<i class="fa-solid fa-bars"></i>',
	'number'   => 41,
	'disc'     => 'الشعار والقائمة تُقرأ من «إعدادات الهيدر» (logo__data) وقائمة main-menu. هنا يمكن إخفاء القسم فقط.',
	'fields'   => array(
		array(
			'title' => 'إخفاء قسم الهيدر',
			'type'  => 'SwitchBox',
			'id'    => 'kayan_hp_header_disable',
		),
		array(
			'type'  => 'Title',
			'title' => 'مصدر البيانات',
			'disc'  => 'لتعديل الشعار: إعدادات القالب → إعدادات الهيدر. للقائمة: المظهر → القوائم → القائمة الرئيسية (main-menu).',
		),
	),
);
