<?php
$metaboxes = array(
	'title'    => 'الدولة واللغة',
	'en_title' => 'Country & Language',
	'icon'     => '<i class="fa-solid fa-globe"></i>',
	'number'   => 2,
	'disc'     => 'الدولة الافتراضية، أزرار الهيدر، ونصوص التغطية — تظهر في الواجهة من هذه الإعدادات فقط.',
	'fields'   => array(
		array(
			'id'    => 'kayan_locale_title_1',
			'type'  => 'Title',
			'title' => 'الدولة الافتراضية',
		),
		array(
			'id'      => 'kayan_i18n_default_country',
			'type'    => 'Select',
			'title'   => 'دولة الموقع الأساسية',
			'disc'    => 'تُستخدم عندما لا يوجد مسار دولة في الرابط. الإمارات = الرابط الأساسي للموقع.',
			'options' => array(
				'ae' => 'الإمارات — /',
				'sa' => 'السعودية — /sa',
				'qa' => 'قطر — /qa',
				'om' => 'عمان — /om',
				'kw' => 'الكويت — /kw',
				'bh' => 'البحرين — /bh',
				'eg' => 'مصر — /eg',
				'lb' => 'لبنان — /lb',
				'jo' => 'الأردن — /jo',
				'iq' => 'العراق — /iq',
			),
			'value' => 'ae',
		),
		array(
			'id'    => 'kayan_site_country_name',
			'type'  => 'Text',
			'title' => 'اسم الدولة الظاهر في القالب',
			'disc'  => 'اتركه فارغاً لاستخدام اسم الدولة المختارة تلقائياً (مثال: الإمارات / السعودية).',
		),
		array(
			'id'    => 'kayan_site_coverage_text',
			'type'  => 'Text',
			'title' => 'نص التغطية الجغرافية',
			'disc'  => 'يظهر في أقسام المدن والمميزات. اتركه فارغاً لنص الدولة الافتراضي.',
		),
		array(
			'id'    => 'kayan_i18n_disable',
			'type'  => 'SwitchBox',
			'title' => 'إيقاف تبديل الدولة واللغة',
		),

		array(
			'id'    => 'kayan_locale_title_2',
			'type'  => 'Title',
			'title' => 'أزرار نهاية قائمة الهيدر',
		),
		array(
			'id'    => 'kayan_hide_header_lang_switcher',
			'type'  => 'SwitchBox',
			'title' => 'إخفاء زر اللغة (AR / EN) من نهاية القائمة',
		),
		array(
			'id'    => 'kayan_hide_header_country_switcher',
			'type'  => 'SwitchBox',
			'title' => 'إخفاء زر الدولة (علم + الاسم)',
		),

		array(
			'id'    => 'kayan_locale_title_3',
			'type'  => 'Title',
			'title' => 'أزرار الحجز والاتصال',
		),
		array(
			'id'    => 'kayan_booking_button_label',
			'type'  => 'Text',
			'title' => 'نص زر الحجز / الأسعار',
			'value' => 'إحجز الآن',
			'disc'  => 'يظهر على زر باقات الأسعار ونموذج الحجز بدلاً من ادفع الآن.',
		),
		array(
			'id'    => 'kayan_show_call_buttons',
			'type'  => 'SwitchBox',
			'title' => 'إظهار أزرار الاتصال داخل المحتوى',
		),
		array(
			'id'    => 'hide__floating__call',
			'type'  => 'SwitchBox',
			'title' => 'إخفاء زر الاتصال العائم',
		),
	),
);
