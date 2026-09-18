<?php
$Styles = array();
$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$page_faqs = get_post_meta( $post->ID, 'yourcolor__faqs', true );
$items     = array();
if ( is_array( $page_faqs ) && ! empty( $page_faqs ) ) {
	foreach ( $page_faqs as $row ) {
		if ( empty( $row['question'] ) ) {
			continue;
		}
		$items[] = array(
			'q' => $row['question'],
			'a' => isset( $row['answer'] ) ? $row['answer'] : '',
		);
	}
}
if ( empty( $items ) ) {
	$posts = kayan_kit_posts( array( 'faqs', 'faq' ), array( 'posts_per_page' => 40 ) );
	foreach ( $posts as $faq ) {
		$items[] = array(
			'q' => $faq->post_title,
			'a' => $faq->post_content,
		);
	}
}

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $items ) ) {
	echo '<div class="faq-list">';
	$i = 0;
	foreach ( $items as $faq ) {
		kayan_kit_render_faq( $faq['q'], $faq['a'], 0 === $i );
		$i++;
	}
	echo '</div>';
} else {
	ob_start();
	the_content();
	$body = ob_get_clean();
	if ( trim( wp_strip_all_tags( $body ) ) !== '' ) {
		echo '<div class="article-body prose">' . $body . '</div>';
	} else {
		kayan_kit_empty();
	}
}
$wa = kayan_kit_whatsapp();
if ( $wa ) {
	echo '<div style="text-align:center;margin-top:44px">';
	echo '<p style="color:var(--text2);margin-bottom:16px">' . esc_html__( 'لم تجد إجابة سؤالك؟', 'yourcolor' ) . '</p>';
	echo '<a class="btn btn-wa" href="https://wa.me/' . esc_attr( $wa ) . '" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> ' . esc_html__( 'اسألنا مباشرة', 'yourcolor' ) . '</a>';
	echo '</div>';
}
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
