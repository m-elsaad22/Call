<?
$Styles = function_exists( 'kayan_kit_page_styles' ) ? kayan_kit_page_styles() : array();
$YC__WidgetsMachine = new YC__WidgetsMachine;

$widgets_works_page__meta = ( is_array( get_option( 'widgets_works_page__meta' ) ) ) ? get_option( 'widgets_works_page__meta' ) : array();
if ( ! empty( $widgets_works_page__meta ) ) {
	$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues( $widgets_works_page__meta );
	$Styles            = array_merge( $Styles, $widgets__Enqueues );
}

$hero_sub = function_exists( 'kayan_kit_excerpt' ) ? kayan_kit_excerpt( $post->post_content, 180 ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '…' );
$page_background = get_post_meta( $post->ID, 'page_back_image', true );
if ( empty( $page_background ) ) {
	$page_background = get_option( 'background_image' );
}

$works = get_posts(
	array(
		'post_type'      => 'works',
		'post_status'    => 'publish',
		'posts_per_page' => 24,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

$this->Part( 'header', array( 'Styles' => $Styles ) );

if ( function_exists( 'kayan_kit_render_phero' ) ) {
	kayan_kit_render_phero(
		array(
			'title'     => $post->post_title,
			'subtitle'  => $hero_sub,
			'image_url' => $page_background,
		)
	);
}

kayan_kit_open_section();

if ( ! empty( $works ) ) {
	$cats = array();
	echo '<div class="toolbar"><div class="pillbar">';
	echo '<button type="button" class="active" data-f="all" data-target=".gal-item">الكل</button>';
	foreach ( $works as $work ) {
		$terms = get_the_terms( $work->ID, 'category' );
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$cats[ $term->slug ] = $term->name;
			}
		}
	}
	foreach ( $cats as $slug => $name ) {
		echo '<button type="button" data-f="' . esc_attr( $slug ) . '" data-target=".gal-item">' . esc_html( $name ) . '</button>';
	}
	echo '</div></div>';

	echo '<div class="gal-grid">';
	foreach ( $works as $work ) {
		$thumb = get_the_post_thumbnail_url( $work, 'large' );
		$terms = get_the_terms( $work->ID, 'category' );
		$slug  = ( is_array( $terms ) && ! empty( $terms ) ) ? $terms[0]->slug : 'all';
		$label = ( is_array( $terms ) && ! empty( $terms ) ) ? $terms[0]->name : get_the_title( $work );
		$url   = get_permalink( $work );
		echo '<a class="gal-item filt" data-cat="' . esc_attr( $slug ) . '" href="' . esc_url( $url ) . '">';
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( get_the_title( $work ) ) . '" loading="lazy" />';
		} else {
			echo '<div class="gal-ph"><i class="fas fa-image"></i></div>';
		}
		echo '<span class="gtag">' . esc_html( $label ) . '</span>';
		echo '</a>';
	}
	echo '</div>';
}

if ( ! empty( $widgets_works_page__meta ) ) {
	echo '<div style="margin-top:40px">';
	$YC__WidgetsMachine->widgets___UI(
		array(
			'Widgets_data' => $widgets_works_page__meta,
			'WidgetID'     => 'widgets_works_page__meta',
		)
	);
	echo '</div>';
}

kayan_kit_close_section();
$this->Part( 'footer', array( 'Styles' => $Styles ) );
