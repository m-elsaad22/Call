<?
/**
 * لوحة إعدادات الصفحة الرئيسية في FieldsMachine (ThemeOptions).
 */
if ( ! function_exists( 'kayan_homepage_register_options_panel' ) ) {
	function kayan_homepage_register_options_panel() {
		global $YC__CFM__global_setup_fields;

		if ( ! isset( $YC__CFM__global_setup_fields['ThemeOptions'] ) ) {
			$YC__CFM__global_setup_fields['ThemeOptions'] = array();
		}

		$YC__CFM__global_setup_fields['ThemeOptions']['kayan_homepage_options'] = array(
			'title'    => ' إعدادات الصفحة الرئيسية ',
			'en_title' => 'Homepage settings',
			'icon'     => '<i class="fa-solid fa-house"></i>',
			'number'   => 48,
			'id'       => 'kayan_homepage_options',
			'disc'     => 'إعدادات عامة للبطل والإحصائيات، إخفاء الأقسام، وتعطيل تصميم الرئيسية الجديد.',
			'fields'   => array(
				array(
					'type'  => 'Title',
					'title' => 'إعدادات عامة',
				),
				array(
					'title' => 'الجزء الأول من اسم الشركة',
					'type'  => 'Text',
					'id'    => 'kayan_homepage_brand_first',
				),
				array(
					'title' => 'الجزء الثاني من اسم الشركة',
					'type'  => 'Text',
					'id'    => 'kayan_homepage_brand_second',
				),
				array(
					'title' => 'عنوان Hero',
					'type'  => 'Text',
					'id'    => 'kayan_hp_hero_title',
				),
				array(
					'title' => 'الكلمة المميزة في العنوان',
					'type'  => 'Text',
					'id'    => 'kayan_hp_hero_title_highlight',
				),
				array(
					'title' => 'وصف Hero',
					'type'  => 'TextArea',
					'id'    => 'kayan_hp_hero_subtitle',
				),
				array(
					'type'   => 'GroupsField',
					'id'     => 'kayan_hp_hero_chips',
					'title'  => 'شارات البطل',
					'fields' => array(
						array( 'title' => 'أيقونة', 'type' => 'Text', 'id' => 'icon' ),
						array( 'title' => 'النص', 'type' => 'Text', 'id' => 'text' ),
					),
				),
				array(
					'type'  => 'Title',
					'title' => 'الإحصائيات',
				),
				array(
					'type'   => 'GroupsField',
					'id'     => 'kayan_hp_stats_items',
					'title'  => 'إحصائية',
					'fields' => array(
						array( 'title' => 'التسمية', 'type' => 'Text', 'id' => 'label' ),
						array( 'title' => 'الرقم', 'type' => 'Number', 'id' => 'count' ),
						array( 'title' => 'لاحقة (+ أو %)', 'type' => 'Text', 'id' => 'suffix' ),
						array( 'title' => 'خانات عشرية', 'type' => 'Number', 'id' => 'dec' ),
					),
				),
				array(
					'type'  => 'Title',
					'title' => 'إخفاء الأقسام',
				),
				array(
					'title' => 'إخفاء الهيدر',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_header_disable',
				),
				array(
					'title' => 'إخفاء قسم البطل',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_hero_disable',
				),
				array(
					'title' => 'إخفاء شريط الثقة',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_trust_disable',
				),
				array(
					'title' => 'إخفاء قسم الخدمات',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_services_disable',
				),
				array(
					'title' => 'إخفاء «لماذا نحن»',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_why_disable',
				),
				array(
					'title' => 'إخفاء الفريق',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_team_disable',
				),
				array(
					'title' => 'إخفاء الإحصائيات',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_stats_disable',
				),
				array(
					'title' => 'إخفاء المناطق',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_areas_disable',
				),
				array(
					'title' => 'إخفاء المدونة',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_blog_disable',
				),
				array(
					'title' => 'إخفاء الأسئلة الشائعة',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_faq_disable',
				),
				array(
					'title' => 'إخفاء قسم الاتصال',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_hp_contact_disable',
				),
				array(
					'type'  => 'Title',
					'title' => 'تعطيل الصفحة الرئيسية الجديدة',
				),
				array(
					'title' => 'تعطيل التصميم الجديد والرجوع للقديم',
					'type'  => 'SwitchBox',
					'id'    => 'kayan_homepage_v3_disable',
				),
			),
		);
	}
}

add_action( 'YC__CFM__global_setup_fields', 'kayan_homepage_register_options_panel', 10 );
