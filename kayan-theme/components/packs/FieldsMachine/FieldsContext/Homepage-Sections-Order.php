<?php
if ( ! function_exists( 'kayan_normalize_homepage_sections_order' ) ) {
	require_once get_template_directory() . '/components/packs/kayan-design-system/helpers.php';
}

if ( ! isset( $value ) || ! is_array( $value ) ) {
	$value = array();
}

$value = kayan_normalize_homepage_sections_order( $value );
$sections = $value['sections'];
$catalog_count = count( kayan_get_homepage_sections_catalog() );

if ( isset( $InsertElements ) ) {
	unset( $vars['InsertElements'] );
	$InputName = 'Insert_' . $id;
} elseif ( isset( $parent_id ) ) {
	$InputName = $parent_id . '[' . $id . ']';
} else {
	$InputName = $id;
}

$enabled = ! empty( $value['enabled'] );
$vars['vars'] = base64_encode( json_encode( $vars ) );

echo '<div class="-fix-inputs-area kayan-homepage-sections-field" ' . ( ( isset( $parent_id ) ) ? 'data-field-argums="' . base64_encode( json_encode( $vars ) ) . '" ' : 'data-vars="' . base64_encode( json_encode( $vars ) ) . '"' ) . '>';

	echo '<div class="-fix-forms-field-title"><h3>' . esc_html( $title ) . '</h3></div>';

	echo '<div class="kayan-homepage-sections-order" data-input-name="' . esc_attr( $InputName ) . '">';

		echo '<div class="kayan-homepage-sections-toolbar">';
			echo '<label class="kayan-homepage-sections-toggle">';
				echo '<input type="checkbox" name="' . esc_attr( $InputName ) . '[enabled]" value="1"' . ( $enabled ? ' checked' : '' ) . ' />';
				echo '<span>' . esc_html( is_rtl() ? 'تفعيل ترتيب الأقسام المخصص' : 'Enable custom section order' ) . '</span>';
			echo '</label>';
			echo '<p class="kayan-homepage-sections-note">';
				echo esc_html(
					is_rtl()
						? 'يتم سحب الأقسام من إعدادات الصفحة الرئيسية (Intro + الودجات). أضف الودجات أولاً من تبويب الرئيسية إن لم تظهر هنا.'
						: 'Sections are loaded from Home settings (Intro + widgets). Add widgets in the Home tab if none appear here.'
				);
			echo '</p>';
		echo '</div>';

		if ( empty( $sections ) ) {
			echo '<div class="kayan-homepage-sections-empty">';
				echo esc_html( is_rtl() ? 'لا توجد أقسام متاحة حالياً.' : 'No homepage sections available yet.' );
			echo '</div>';
		} else {
			echo '<div class="kayan-homepage-sections-list">';
				foreach ( $sections as $index => $section ) {
					$type_label = $section['type'] === 'intro'
						? ( is_rtl() ? 'مقدمة' : 'Intro' )
						: ( is_rtl() ? 'ودجت' : 'Widget' );
					$visible = ! empty( $section['visible'] );
					$meta = isset( $section['meta'] ) ? $section['meta'] : ( isset( $section['widget_id'] ) ? $section['widget_id'] : '' );

					echo '<div class="kayan-homepage-section-item" data-section-index="' . esc_attr( $index ) . '">';
						echo '<span class="kayan-homepage-section-handle"><i class="fa-solid fa-grip-vertical"></i></span>';
						echo '<div class="kayan-homepage-section-content">';
							echo '<strong>' . esc_html( $section['label'] ) . '</strong>';
							echo '<span class="kayan-homepage-section-badge">' . esc_html( $type_label ) . '</span>';
							if ( ! empty( $meta ) ) {
								echo '<code>' . esc_html( $meta ) . '</code>';
							}
						echo '</div>';
						echo '<label class="kayan-homepage-section-visible" title="' . esc_attr( is_rtl() ? 'إظهار/إخفاء' : 'Show/Hide' ) . '">';
							echo '<input type="checkbox" class="kayan-homepage-section-visible-input" name="' . esc_attr( $InputName ) . '[sections][' . esc_attr( $index ) . '][visible]" value="1"' . ( $visible ? ' checked' : '' ) . ' />';
							echo '<i class="fa-solid fa-eye"></i>';
						echo '</label>';
						echo '<input type="hidden" class="kayan-homepage-section-id" name="' . esc_attr( $InputName ) . '[sections][' . esc_attr( $index ) . '][section_id]" value="' . esc_attr( $section['section_id'] ) . '" />';
						echo '<input type="hidden" class="kayan-homepage-section-type" name="' . esc_attr( $InputName ) . '[sections][' . esc_attr( $index ) . '][type]" value="' . esc_attr( $section['type'] ) . '" />';
						if ( $section['type'] === 'widget' ) {
							echo '<input type="hidden" class="kayan-homepage-section-widget-key" name="' . esc_attr( $InputName ) . '[sections][' . esc_attr( $index ) . '][widget_key]" value="' . esc_attr( $section['widget_key'] ) . '" />';
							echo '<input type="hidden" class="kayan-homepage-section-widget-post" name="' . esc_attr( $InputName ) . '[sections][' . esc_attr( $index ) . '][widget_post__id]" value="' . esc_attr( $section['widget_post__id'] ) . '" />';
							echo '<input type="hidden" class="kayan-homepage-section-widget-id" name="' . esc_attr( $InputName ) . '[sections][' . esc_attr( $index ) . '][widget_id]" value="' . esc_attr( $section['widget_id'] ) . '" />';
						}
					echo '</div>';
				}
			echo '</div>';
		}

		echo '<div class="kayan-homepage-sections-summary">';
			echo '<span>' . esc_html( is_rtl() ? 'عدد الأقسام المتاحة:' : 'Available sections:' ) . ' <strong>' . intval( $catalog_count ) . '</strong></span>';
			echo '<span>' . esc_html( is_rtl() ? 'المفعّل في الترتيب:' : 'In order list:' ) . ' <strong class="kayan-homepage-sections-count">' . count( $sections ) . '</strong></span>';
		echo '</div>';

	echo '</div>';

	echo ( ( isset( $disc ) ) ) ? '<descor>' . $disc . '</descor>' : '';

echo '</div>';
