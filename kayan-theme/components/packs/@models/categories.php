<?
$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();

$hero_sub = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $post->post_content, 180 ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$number = isset( $number ) ? (int) $number : 30;
$get_terms = array();
if ( isset( $taxonomy_option ) && is_array( $taxonomy_option ) ) {
	foreach ( $taxonomy_option as $tx__value ) {
		$s_tems = get_term_by( 'id', $tx__value, 'category' );
		if ( isset( $s_tems->term_id ) ) {
			$get_terms[] = $s_tems;
		}
	}
} else {
	$get_terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'number'     => $number,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $get_terms ) ) {
		$get_terms = array();
	}
}

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $post->post_title,
			'subtitle'  => $hero_sub,
			'image_url' => $page_background,
			'meta_html' => ! empty( $get_terms ) ? '<div><b>' . (int) count( $get_terms ) . '</b><small>تصنيف</small></div>' : '',
		)
	);
}

kayan_kit_open_section();
echo '<div class="toolbar">';
echo '<div class="search-box"><i class="fas fa-search"></i><input type="search" data-kit-search=".catcard" placeholder="ابحث عن خدمة…" /></div>';
echo '<div class="results">عرض <b>' . (int) count( $get_terms ) . '</b> تصنيف</div>';
echo '</div>';

echo '<div class="cat-grid">';
if ( empty( $get_terms ) ) {
	echo '<p>لم تُضف تصنيفات بعد.</p>';
} else {
	foreach ( $get_terms as $category ) {
		$CategoryURL = get_term_link( $category );
		if ( is_wp_error( $CategoryURL ) ) {
			continue;
		}
		$icon = get_term_meta( $category->term_id, 'icon', true );
		if ( empty( $icon ) ) {
			$icon = '<i class="fas fa-screwdriver-wrench"></i>';
		}
		$desc = wp_trim_words( wp_strip_all_tags( $category->description ), 14, '…' );
		$but_text = get_term_meta( $category->term_id, 'but_text', true );
		if ( empty( $but_text ) ) {
			$but_text = 'عرض الخدمات';
		}
		echo '<a class="catcard filt" href="' . esc_url( $CategoryURL ) . '">';
		echo '<div class="cic">' . $icon . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h3>' . esc_html( $category->name ) . '</h3>';
		if ( $desc !== '' ) {
			echo '<p>' . esc_html( $desc ) . '</p>';
		}
		echo '<span class="ccount">' . (int) $category->count . ' خدمة</span>';
		echo '</a>';
	}
}
echo '</div>';
kayan_kit_close_section();

$this->Part( 'footer', array( 'Styles' => $Styles ) );
