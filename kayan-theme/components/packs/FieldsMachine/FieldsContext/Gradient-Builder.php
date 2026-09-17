<?php
if ( ! function_exists( 'kayan_normalize_gradient_builder' ) ) {
	require_once get_template_directory() . '/components/packs/kayan-design-system/helpers.php';
}

if ( ! isset( $value ) || ! is_array( $value ) ) {
	$value = array();
}

$value = kayan_normalize_gradient_builder( $value );

if ( isset( $InsertElements ) ) {
	unset( $vars['InsertElements'] );
	$InputName = 'Insert_' . $id;
} elseif ( isset( $parent_id ) ) {
	$InputName = $parent_id . '[' . $id . ']';
} else {
	$InputName = $id;
}

$enabled = ! empty( $value['enabled'] );
$preview_css = kayan_gradient_builder_to_css( $value );
$vars['vars'] = base64_encode( json_encode( $vars ) );

echo '<div class="-fix-inputs-area kayan-gradient-builder-field" ' . ( ( isset( $parent_id ) ) ? 'data-field-argums="' . base64_encode( json_encode( $vars ) ) . '" ' : 'data-vars="' . base64_encode( json_encode( $vars ) ) . '"' ) . '>';

	echo '<div class="-fix-forms-field-title"><h3>' . esc_html( $title ) . '</h3></div>';

	echo '<div class="kayan-gradient-builder" data-input-name="' . esc_attr( $InputName ) . '">';

		echo '<div class="kayan-gradient-toolbar">';
			echo '<label class="kayan-gradient-toggle">';
				echo '<input type="checkbox" name="' . esc_attr( $InputName ) . '[enabled]" value="1"' . ( $enabled ? ' checked' : '' ) . ' />';
				echo '<span>' . esc_html( is_rtl() ? 'تفعيل التدرج' : 'Enable gradient' ) . '</span>';
			echo '</label>';

			echo '<div class="kayan-gradient-type">';
				echo '<label>' . esc_html( is_rtl() ? 'النوع' : 'Type' ) . '</label>';
				echo '<select name="' . esc_attr( $InputName ) . '[type]" class="kayan-gradient-type-select">';
					echo '<option value="linear"' . selected( $value['type'], 'linear', false ) . '>' . esc_html( is_rtl() ? 'خطي' : 'Linear' ) . '</option>';
					echo '<option value="radial"' . selected( $value['type'], 'radial', false ) . '>' . esc_html( is_rtl() ? 'دائري' : 'Radial' ) . '</option>';
				echo '</select>';
			echo '</div>';

			echo '<div class="kayan-gradient-target">';
				echo '<label>' . esc_html( is_rtl() ? 'تطبيق على' : 'Apply to' ) . '</label>';
				echo '<select name="' . esc_attr( $InputName ) . '[apply_target]" class="kayan-gradient-target-select">';
					$targets = array(
						'body' => is_rtl() ? 'خلفية الصفحة' : 'Page background',
						'header' => is_rtl() ? 'الهيدر' : 'Header',
						'buttons' => is_rtl() ? 'الأزرار' : 'Buttons',
					);
					foreach ( $targets as $target_key => $target_label ) {
						echo '<option value="' . esc_attr( $target_key ) . '"' . selected( $value['apply_target'], $target_key, false ) . '>' . esc_html( $target_label ) . '</option>';
					}
				echo '</select>';
			echo '</div>';
		echo '</div>';

		echo '<div class="kayan-gradient-preview-wrap">';
			echo '<div class="kayan-gradient-preview" style="background:' . esc_attr( $preview_css ? $preview_css : 'linear-gradient(135deg, #a03576 0%, #2563eb 100%)' ) . ';"></div>';
			echo '<code class="kayan-gradient-css-output">' . esc_html( $preview_css ? $preview_css : 'linear-gradient(135deg, #a03576 0%, #2563eb 100%)' ) . '</code>';
		echo '</div>';

		echo '<div class="kayan-gradient-linear-controls' . ( $value['type'] === 'radial' ? ' is-hidden' : '' ) . '">';
			echo '<label>' . esc_html( is_rtl() ? 'زاوية التدرج' : 'Gradient angle' ) . '</label>';
			echo '<div class="kayan-gradient-angle-row">';
				echo '<input type="range" min="0" max="360" step="1" value="' . esc_attr( $value['angle'] ) . '" class="kayan-gradient-angle-range" />';
				echo '<input type="number" min="0" max="360" name="' . esc_attr( $InputName ) . '[angle]" value="' . esc_attr( $value['angle'] ) . '" class="kayan-gradient-angle-input" />';
				echo '<span>°</span>';
			echo '</div>';
		echo '</div>';

		echo '<div class="kayan-gradient-radial-controls' . ( $value['type'] === 'linear' ? ' is-hidden' : '' ) . '">';
			echo '<label>' . esc_html( is_rtl() ? 'شكل التدرج الدائري' : 'Radial shape' ) . '</label>';
			echo '<select name="' . esc_attr( $InputName ) . '[radial_shape]" class="kayan-gradient-radial-shape">';
				echo '<option value="circle"' . selected( $value['radial_shape'], 'circle', false ) . '>circle</option>';
				echo '<option value="ellipse"' . selected( $value['radial_shape'], 'ellipse', false ) . '>ellipse</option>';
			echo '</select>';
			echo '<label>' . esc_html( is_rtl() ? 'موضع المركز' : 'Center position' ) . '</label>';
			echo '<select name="' . esc_attr( $InputName ) . '[radial_position]" class="kayan-gradient-radial-position">';
				foreach ( array( 'center', 'top', 'bottom', 'left', 'right' ) as $position ) {
					echo '<option value="' . esc_attr( $position ) . '"' . selected( $value['radial_position'], $position, false ) . '>' . esc_html( $position ) . '</option>';
				}
			echo '</select>';
		echo '</div>';

		echo '<div class="kayan-gradient-stops-header">';
			echo '<h4>' . esc_html( is_rtl() ? 'نقاط الألوان' : 'Color stops' ) . '</h4>';
			echo '<button type="button" class="button kayan-gradient-add-stop"><i class="fa-solid fa-plus"></i> ' . esc_html( is_rtl() ? 'إضافة لون' : 'Add color' ) . '</button>';
		echo '</div>';

		echo '<div class="kayan-gradient-stops">';
			foreach ( $value['stops'] as $index => $stop ) {
				echo '<div class="kayan-gradient-stop" data-stop-index="' . esc_attr( $index ) . '">';
					echo '<span class="kayan-gradient-stop-handle"><i class="fa-solid fa-grip-vertical"></i></span>';
					echo '<input type="text" class="ColorViewer kayan-gradient-stop-color" name="' . esc_attr( $InputName ) . '[stops][' . esc_attr( $index ) . '][color]" value="' . esc_attr( $stop['color'] ) . '" />';
					echo '<input type="number" min="0" max="100" class="kayan-gradient-stop-position" name="' . esc_attr( $InputName ) . '[stops][' . esc_attr( $index ) . '][position]" value="' . esc_attr( $stop['position'] ) . '" />';
					echo '<span class="kayan-gradient-stop-unit">%</span>';
					echo '<button type="button" class="kayan-gradient-remove-stop" title="' . esc_attr( is_rtl() ? 'حذف' : 'Remove' ) . '"><i class="fa-solid fa-trash"></i></button>';
				echo '</div>';
			}
		echo '</div>';

		echo '<div class="kayan-gradient-presets">';
			echo '<span>' . esc_html( is_rtl() ? 'قوالب جاهزة:' : 'Presets:' ) . '</span>';
			$presets = array(
				'sunset' => array( '#ff512f', '#dd2476' ),
				'ocean' => array( '#2193b0', '#6dd5ed' ),
				'royal' => array( '#a03576', '#2563eb' ),
			);
			foreach ( $presets as $preset_key => $preset_colors ) {
				echo '<button type="button" class="button kayan-gradient-preset" data-preset="' . esc_attr( $preset_key ) . '" data-colors="' . esc_attr( implode( ',', $preset_colors ) ) . '">' . esc_html( ucfirst( $preset_key ) ) . '</button>';
			}
		echo '</div>';

	echo '</div>';

	echo ( ( isset( $disc ) ) ) ? '<descor>' . $disc . '</descor>' : '';

echo '</div>';
