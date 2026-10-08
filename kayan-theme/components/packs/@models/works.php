<?php
$Styles = array();
$YC__WidgetsMachine = new YC__WidgetsMachine;
$widgets_works_page__meta = is_array( get_option( 'widgets_works_page__meta' ) ) ? get_option( 'widgets_works_page__meta' ) : array();
if ( ! empty( $widgets_works_page__meta ) ) {
	$Styles = array_merge( $Styles, $YC__WidgetsMachine->widgets__Enqueues( $widgets_works_page__meta ) );
}

$this->Part( 'header', array( 'Styles' => $Styles ) );

kayan_kit_hero( $post->post_title, kayan_kit_page_excerpt( $post ) );

$items = kayan_kit_posts( array( 'portfolio', 'before_after', 'post' ), array( 'posts_per_page' => 36 ) );
$cats  = kayan_kit_terms( array( 'service_categories', 'category' ), array( 'number' => 12, 'hide_empty' => true ) );

echo '<section class="sec"><div class="wrap">';
if ( ! empty( $cats ) ) {
	echo '<div class="filters">';
	echo '<span class="active">' . esc_html__( 'جميع المشاريع', 'yourcolor' ) . '</span>';
	foreach ( $cats as $term ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			echo '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
		}
	}
	echo '</div>';
}
if ( ! empty( $items ) ) {
	echo '<div class="gal-grid">';
	foreach ( $items as $item ) {
		$thumb = get_the_post_thumbnail_url( $item->ID, 'medium_large' );
		echo '<a class="gal-item" href="' . esc_url( get_permalink( $item ) ) . '">';
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $item->post_title ) . '" loading="lazy" />';
		} else {
			echo '<div class="gal-ph"><i class="fas fa-image"></i></div>';
		}
		echo '<span class="gtag">' . esc_html( $item->post_title ) . '</span>';
		echo '</a>';
	}
	echo '</div>';
} elseif ( ! empty( $widgets_works_page__meta ) ) {
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_works_page__meta,
			'WidgetID'     => 'widgets_works_page__meta',
		)
	);
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
echo '</div></section>';

$this->Part( 'footer', array( 'Styles' => $Styles ) );
