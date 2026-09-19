<?php
$Styles = array();
$Styles['shortcodes'] = 'shortcodes.css';
$YC__WidgetsMachine = new YC__WidgetsMachine;

$hide__sidebar__single = get_option( 'hide__sidebar__single' );
$widgets_single__meta  = array();
if ( empty( $hide__sidebar__single ) ) {
	$widgets_single__meta = is_array( get_option( 'widgets_single__meta' ) ) ? get_option( 'widgets_single__meta' ) : array();
	if ( ! empty( $widgets_single__meta ) ) {
		$Styles = array_merge( $Styles, $YC__WidgetsMachine->widgets__Enqueues( $widgets_single__meta ) );
	}
}

ob_start();
the_content();
$body = kayan_kit_anchor_headings( ob_get_clean() );
$lead = '';
if ( function_exists( 'kayan_kit_split_article_lead' ) ) {
	list( $lead, $body ) = kayan_kit_split_article_lead( $body );
}
if ( $lead === '' ) {
	$lead = kayan_kit_page_excerpt( $post );
}

$cats = get_the_terms( $post->ID, 'category' );
$cat  = ( is_array( $cats ) && ! empty( $cats ) ) ? $cats[0] : null;
$tags = get_the_terms( $post->ID, 'post_tag' );
$author = get_userdata( $post->post_author );
$phone  = get_post_meta( $post->ID, 'phone_number', true );
if ( empty( $phone ) ) {
	$phone = kayan_kit_phone();
}
$wa = get_post_meta( $post->ID, 'whatsapp_number', true );
if ( empty( $wa ) ) {
	$wa = kayan_kit_whatsapp();
}

$chips = array();
if ( $cat ) {
	$chips[] = '<i class="fas fa-folder"></i> ' . esc_html( $cat->name );
}

$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, $lead, $post->post_title, array( 'chips' => $chips, 'ctas' => true, 'full_lead' => true ) );

$rel_args = array(
	'posts_per_page' => 4,
	'post__not_in'   => array( $post->ID ),
);
if ( $cat ) {
	$rel_args['category__in'] = array( $cat->term_id );
}
$related = kayan_kit_posts( array( $post->post_type ), $rel_args );

echo '<section class="sec"><div class="wrap article-layout">';
echo '<div class="article-body">';
$thumb = get_the_post_thumbnail_url( $post->ID, 'large' );
if ( $thumb && empty( get_option( 'hide__thumbnail__single' ) ) ) {
	echo '<div class="article-featured"><img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $post->post_title ) . '" /></div>';
}
echo '<div class="prose">' . $body . '</div>';
if ( is_array( $tags ) && ! empty( $tags ) && empty( get_option( 'hide__post__tags' ) ) ) {
	echo '<div class="article-tags">';
	foreach ( $tags as $tag ) {
		$link = get_term_link( $tag );
		if ( ! is_wp_error( $link ) ) {
			echo '<a href="' . esc_url( $link ) . '">' . esc_html( $tag->name ) . '</a>';
		}
	}
	echo '</div>';
}
if ( $author && empty( get_option( 'hide__post__author' ) ) ) {
	$initial = mb_substr( $author->display_name, 0, 1, 'UTF-8' );
	echo '<div class="author-box"><div class="aav">' . esc_html( $initial ) . '</div><div><b>' . esc_html( $author->display_name ) . '</b><small>' . esc_html( get_bloginfo( 'name' ) ) . '</small></div></div>';
}
if ( function_exists( 'kayan_kit_render_customer_ratings' ) && function_exists( 'kayan_kit_widgets_include' ) && ! kayan_kit_widgets_include( $widgets_single__meta, 'rating__widget' ) ) {
	kayan_kit_render_customer_ratings( $post );
}
$page_faqs = get_post_meta( $post->ID, 'yourcolor__faqs', true );
if ( is_array( $page_faqs ) && ! empty( $page_faqs ) && empty( get_option( 'hide__post__faqs' ) ) ) {
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
if ( ! kayan_kit_has_plugin_toc( $body ) && preg_match_all( '/<h2[^>]*>(.*?)<\/h2>/is', $body, $heads ) ) {
	echo '<aside class="side-w"><h4>' . esc_html__( 'محتويات المقال', 'yourcolor' ) . '</h4><div class="legal-toc">';
	foreach ( $heads[1] as $i => $heading ) {
		echo '<a href="#kit-h-' . ( $i + 1 ) . '"><b>' . ( $i + 1 ) . '.</b> ' . esc_html( kayan_kit_plain( $heading ) ) . '</a>';
	}
	echo '</div></aside>';
}
if ( ! empty( $related ) ) {
	echo '<aside class="side-w"><h4>' . esc_html__( 'مقالات ذات صلة', 'yourcolor' ) . '</h4>';
	foreach ( $related as $rel ) {
		echo '<a class="rel" href="' . esc_url( get_permalink( $rel ) ) . '"><div class="rth"><i class="fas fa-newspaper"></i></div><div><b>' . esc_html( $rel->post_title ) . '</b></div></a>';
	}
	echo '</aside>';
}
$cities = get_the_terms( $post->ID, kayan_kit_city_taxonomy() );
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
if ( empty( $hide__sidebar__single ) && ! empty( $widgets_single__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_single__meta,
			'WidgetID'     => 'widgets_single__meta',
		)
	);
}
echo '</div></div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
