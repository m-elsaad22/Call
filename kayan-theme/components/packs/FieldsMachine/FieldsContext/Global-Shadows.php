<?php
if ( ! function_exists( 'kayan_normalize_global_shadows' ) ) {
	require_once get_template_directory() . '/components/packs/kayan-design-system/helpers.php';
}

if ( ! isset( $value ) || ! is_array( $value ) ) {
	$value = array();
}

$value = kayan_normalize_global_shadows( $value );
$preview_css = kayan_global_shadows_to_css( $value );
$presets = kayan_global_shadows_presets();

if ( isset( $InsertElements ) ) {
	unset( $vars['InsertElements'] );
	$InputName = 'Insert_' . $id;
} elseif ( isset( $parent_id ) ) {
	$InputName = $parent_id . '[' . $id . ']';
} else {
	$InputName = $id;
}

$enabled = ! empty( $value['enabled'] );
$is_custom = $value['depth_preset'] === 'custom';
$vars['vars'] = base64_encode( json_encode( $vars ) );

echo '<div class="-fix-inputs-area kayan-global-shadows-field" ' . ( ( isset( $parent_id ) ) ? 'data-field-argums="' . base64_encode( json_encode( $vars ) ) . '" ' : 'data-vars="' . base64_encode( json_encode( $vars ) ) . '"' ) . '>';

	echo '<div class="-fix-forms-field-title"><h3>' . esc_html( $title ) . '</h3></div>';

	echo '<div class="kayan-global-shadows" data-input-name="' . esc_attr( $InputName ) . '">';

		echo '<div class="kayan-shadow-toolbar">';
			echo '<label class="kayan-shadow-toggle">';
				echo '<input type="checkbox" name="' . esc_attr( $InputName ) . '[enabled]" value="1"' . ( $enabled ? ' checked' : '' ) . ' />';
				echo '<span>' . esc_html( is_rtl() ? 'تفعيل الظلال' : 'Enable shadows' ) . '</span>';
			echo '</label>';

			echo '<div class="kayan-shadow-preset">';
				echo '<label>' . esc_html( is_rtl() ? 'مستوى العمق' : 'Depth preset' ) . '</label>';
				echo '<select name="' . esc_attr( $InputName ) . '[depth_preset]" class="kayan-shadow-preset-select">';
					$preset_labels = array(
						'subtle' => is_rtl() ? 'خفيف' : 'Subtle',
						'medium' => is_rtl() ? 'متوسط' : 'Medium',
						'deep' => is_rtl() ? 'عميق' : 'Deep',
						'floating' => is_rtl() ? 'عائم' : 'Floating',
						'custom' => is_rtl() ? 'مخصص' : 'Custom',
					);
					foreach ( $preset_labels as $preset_key => $preset_label ) {
						echo '<option value="' . esc_attr( $preset_key ) . '"' . selected( $value['depth_preset'], $preset_key, false ) . '>' . esc_html( $preset_label ) . '</option>';
					}
				echo '</select>';
			echo '</div>';

			echo '<div class="kayan-shadow-target">';
				echo '<label>' . esc_html( is_rtl() ? 'تطبيق على' : 'Apply to' ) . '</label>';
				echo '<select name="' . esc_attr( $InputName ) . '[apply_target]" class="kayan-shadow-target-select">';
					$targets = array(
						'cards' => is_rtl() ? 'البطاقات' : 'Cards',
						'buttons' => is_rtl() ? 'الأزرار' : 'Buttons',
						'header' => is_rtl() ? 'الهيدر' : 'Header',
						'widgets' => is_rtl() ? 'الويدجت' : 'Widgets',
						'all' => is_rtl() ? 'الكل' : 'All',
					);
					foreach ( $targets as $target_key => $target_label ) {
						echo '<option value="' . esc_attr( $target_key ) . '"' . selected( $value['apply_target'], $target_key, false ) . '>' . esc_html( $target_label ) . '</option>';
					}
				echo '</select>';
			echo '</div>';
		echo '</div>';

		echo '<div class="kayan-shadow-controls">';
			echo '<div class="kayan-shadow-color-wrap">';
				echo '<label>' . esc_html( is_rtl() ? 'لون الظل' : 'Shadow color' ) . '</label>';
				echo '<input type="text" class="ColorViewer kayan-shadow-color" name="' . esc_attr( $InputName ) . '[color]" value="' . esc_attr( $value['color'] ) . '" />';
			echo '</div>';
			echo '<div class="kayan-shadow-opacity-wrap">';
				echo '<label>' . esc_html( is_rtl() ? 'الشفافية %' : 'Opacity %' ) . '</label>';
				echo '<input type="number" min="0" max="100" class="kayan-shadow-opacity" name="' . esc_attr( $InputName ) . '[opacity]" value="' . esc_attr( $value['opacity'] ) . '" />';
			echo '</div>';
			echo '<div class="kayan-shadow-intensity-wrap">';
				echo '<label>' . esc_html( is_rtl() ? 'الشدة' : 'Intensity' ) . '</label>';
				echo '<input type="range" min="50" max="150" step="1" class="kayan-shadow-intensity-range" value="' . esc_attr( $value['intensity'] ) . '" />';
				echo '<input type="number" min="50" max="150" class="kayan-shadow-intensity-input" name="' . esc_attr( $InputName ) . '[intensity]" value="' . esc_attr( $value['intensity'] ) . '" />';
			echo '</div>';
		echo '</div>';

		echo '<div class="kayan-shadow-preview-wrap">';
			echo '<div class="kayan-shadow-preview-card" style="box-shadow:' . esc_attr( $preview_css ? $preview_css : '0 4px 12px rgba(15,23,42,.16)' ) . ';"></div>';
			echo '<code class="kayan-shadow-css-output">' . esc_html( $preview_css ? $preview_css : '0 4px 12px rgba(15,23,42,.16)' ) . '</code>';
		echo '</div>';

		echo '<div class="kayan-shadow-depth-tokens">';
			$tokens = kayan_get_global_shadow_depth_tokens( $value );
			foreach ( $tokens as $token_key => $token_css ) {
				echo '<div class="kayan-shadow-token" data-token="' . esc_attr( $token_key ) . '">';
					echo '<span>' . esc_html( strtoupper( $token_key ) ) . '</span>';
					echo '<em>' . esc_html( $token_css ) . '</em>';
				echo '</div>';
			}
		echo '</div>';

		echo '<div class="kayan-shadow-layers-wrap' . ( $is_custom ? '' : ' is-hidden' ) . '">';
			echo '<div class="kayan-shadow-layers-header">';
				echo '<h4>' . esc_html( is_rtl() ? 'طبقات الظل' : 'Shadow layers' ) . '</h4>';
				echo '<button type="button" class="button kayan-shadow-add-layer"><i class="fa-solid fa-plus"></i> ' . esc_html( is_rtl() ? 'إضافة طبقة' : 'Add layer' ) . '</button>';
			echo '</div>';
			echo '<div class="kayan-shadow-layers">';
				foreach ( $value['layers'] as $index => $layer ) {
					echo '<div class="kayan-shadow-layer" data-layer-index="' . esc_attr( $index ) . '">';
						echo '<span class="kayan-shadow-layer-handle"><i class="fa-solid fa-grip-vertical"></i></span>';
						echo '<label>X</label><input type="number" class="kayan-shadow-layer-x" name="' . esc_attr( $InputName ) . '[layers][' . esc_attr( $index ) . '][x]" value="' . esc_attr( $layer['x'] ) . '" />';
						echo '<label>Y</label><input type="number" class="kayan-shadow-layer-y" name="' . esc_attr( $InputName ) . '[layers][' . esc_attr( $index ) . '][y]" value="' . esc_attr( $layer['y'] ) . '" />';
						echo '<label>Blur</label><input type="number" min="0" class="kayan-shadow-layer-blur" name="' . esc_attr( $InputName ) . '[layers][' . esc_attr( $index ) . '][blur]" value="' . esc_attr( $layer['blur'] ) . '" />';
						echo '<label>Spread</label><input type="number" class="kayan-shadow-layer-spread" name="' . esc_attr( $InputName ) . '[layers][' . esc_attr( $index ) . '][spread]" value="' . esc_attr( $layer['spread'] ) . '" />';
						echo '<button type="button" class="kayan-shadow-remove-layer"><i class="fa-solid fa-trash"></i></button>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="kayan-shadow-presets">';
			echo '<span>' . esc_html( is_rtl() ? 'قوالب سريعة:' : 'Quick presets:' ) . '</span>';
			foreach ( array_keys( $presets ) as $preset_key ) {
				echo '<button type="button" class="button kayan-shadow-preset-btn" data-preset="' . esc_attr( $preset_key ) . '">' . esc_html( ucfirst( $preset_key ) ) . '</button>';
			}
		echo '</div>';

	echo '</div>';

	echo ( ( isset( $disc ) ) ) ? '<descor>' . $disc . '</descor>' : '';

echo '</div>';
