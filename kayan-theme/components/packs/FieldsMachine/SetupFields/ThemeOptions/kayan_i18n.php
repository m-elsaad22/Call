<?
$metaboxes = array(
	'title'    => 'اللغة والدولة',
	'en_title'  => 'LOCALE',
	'icon'    => '<i class="fa-solid fa-globe"></i>',
	'number'=>15,
	'disc'=>'تبديل العربية/English ومسارات الدول: /en/ و /sa/ و /eg/ … بعد الحفظ اذهب إلى الإعدادات → الروابط الدائمة واضغط حفظ مرة واحدة.',
	'fields'  => array(
		array(
			'type'  => 'Title',
			'title' => 'تفعيل التعدد اللغوي والجغرافي',
		),
		array(
			'title'   => 'تعطيل مبدّل اللغة والدولة',
			'en_title'=> 'Disable locale switcher',
			'type'    => 'SwitchBox',
			'id'      => 'kayan_i18n_disable',
		),
		array(
			'title'   => 'الدولة الافتراضية',
			'en_title'=> 'Default country',
			'type'    => 'Select',
			'id'      => 'kayan_i18n_default_country',
			'options' => array(
				'ae' => 'الإمارات',
				'sa' => 'السعودية',
				'qa' => 'قطر',
				'kw' => 'الكويت',
				'om' => 'عمان',
				'bh' => 'البحرين',
				'eg' => 'مصر',
			),
		),
		array(
			'type'  => 'Title',
			'title' => 'مسارات URL',
			'disc'  => 'الإمارات = الجذر /. السعودية = /sa/. مصر = /eg/. الإنجليزية = /en/ أو /sa/en/',
		),
	)
);
