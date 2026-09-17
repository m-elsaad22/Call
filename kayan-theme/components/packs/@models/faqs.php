<?
$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();

$hero_sub = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $post->post_content, 180 ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$faqs = get_posts(
	array(
		'post_type'      => 'faq',
		'posts_per_page' => 40,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $post->post_title,
			'subtitle'  => $hero_sub,
			'image_url' => $page_background,
			'meta_html' => ! empty( $faqs ) ? '<div><b>' . (int) count( $faqs ) . '</b><small>سؤال</small></div>' : '',
		)
	);
}

kayan_kit_open_section();
echo '<div class="faq-list">';
if ( empty( $faqs ) ) {
	echo '<p>لا توجد أسئلة بعد. أضف عناصر من نوع FAQ من لوحة التحكم.</p>';
} else {
	$i = 0;
	foreach ( $faqs as $faq ) {
		$open = $i === 0 ? ' faq-open' : '';
		echo '<div class="faq-item' . $open . '">';
		echo '<div class="faq-q" onclick="faqT(this)"><span>' . esc_html( get_the_title( $faq ) ) . '</span><i class="fas fa-chevron-down"></i></div>';
		echo '<div class="faq-a"' . ( $i === 0 ? ' style="max-height:none"' : '' ) . '><div style="padding:0 24px 22px">' . apply_filters( 'the_content', $faq->post_content ) . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
		$i++;
	}
}
echo '</div>';
kayan_kit_close_section();

$this->Part( 'footer', array( 'Styles' => $Styles ) );
