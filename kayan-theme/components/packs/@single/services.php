<?php
$Styles = array();
$Styles['shortcodes'] = 'shortcodes.css';

ob_start();
the_content();
$body = ob_get_clean();

$price = get_post_meta( $post->ID, 'service_price', true );
$icon  = get_post_meta( $post->ID, 'service_icon', true );
$phone = kayan_kit_phone();
$wa    = kayan_kit_whatsapp();

$related = kayan_kit_posts(
	array( 'services', 'post' ),
	array(
		'posts_per_page' => 4,
		'post__not_in'   => array( $post->ID ),
	)
);
$cities = get_the_terms( $post->ID, kayan_kit_city_taxonomy() );

$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero(
	$post->post_title,
	kayan_kit_page_excerpt( $post ),
	$post->post_title,
	array(
		'ctas'  => true,
		'chips' => $price ? array( '<i class="fas fa-tag"></i> ' . esc_html( $price ) ) : array(),
	)
);

echo '<section class="sec"><div class="wrap article-layout">';
echo '<div class="article-body">';
$thumb = get_the_post_thumbnail_url( $post->ID, 'large' );
if ( $thumb ) {
	echo '<div class="prose"><img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $post->post_title ) . '" /></div>';
}
echo '<div class="prose">' . $body . '</div>';
$page_faqs = get_post_meta( $post->ID, 'yourcolor__faqs', true );
if ( is_array( $page_faqs ) && ! empty( $page_faqs ) ) {
	echo '<div class="faq-list" style="max-width:none;margin-top:30px">';
	foreach ( $page_faqs as $i => $faq ) {
		if ( empty( $faq['question'] ) ) {
			continue;
		}
		kayan_kit_render_faq( $faq['question'], isset( $faq['answer'] ) ? $faq['answer'] : '', 0 === $i );
	}
	echo '</div>';
}
echo '</div>';
echo '<div>';
kayan_kit_side_cta( __( 'احصل على معاينة مجانية', 'yourcolor' ), get_bloginfo( 'name' ) );
if ( ! empty( $related ) ) {
	echo '<aside class="side-w"><h4>' . esc_html__( 'خدمات ذات صلة', 'yourcolor' ) . '</h4>';
	foreach ( $related as $rel ) {
		$ricon = get_post_meta( $rel->ID, 'service_icon', true );
		echo '<a class="rel" href="' . esc_url( get_permalink( $rel ) ) . '"><div class="rth">' . ( $ricon && function_exists( 'kayan_icon_html' ) ? kayan_icon_html( $ricon ) : '<i class="fas fa-screwdriver-wrench"></i>' ) . '</div><div><b>' . esc_html( $rel->post_title ) . '</b><small>' . esc_html( kayan_kit_plain( $rel->post_content, 40 ) ) . '</small></div></a>';
	}
	echo '</aside>';
}
if ( is_array( $cities ) && ! empty( $cities ) ) {
	echo '<aside class="side-w"><h4>' . esc_html__( 'متوفر في مدينتك', 'yourcolor' ) . '</h4><div class="toc">';
	foreach ( $cities as $city ) {
		$link = get_term_link( $city );
		if ( ! is_wp_error( $link ) ) {
			echo '<a href="' . esc_url( $link ) . '"><i class="fas fa-circle"></i> ' . esc_html( $city->name ) . '</a>';
		}
	}
	echo '</div></aside>';
}
echo '</div></div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
