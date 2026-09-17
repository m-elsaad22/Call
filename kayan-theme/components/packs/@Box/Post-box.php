<?php
$uniqid = uniqid();
$thumbnail_id = get_post_thumbnail_id($post->ID);

$PermaLink = get_the_permalink($post->ID);
$mini__post_content = wp_trim_words($post->post_content, 18);
$lazyload = get_option('lazyload');
$kayan_kit_card = function_exists( 'kayan_homepage_uses_inner_layout' ) && kayan_homepage_uses_inner_layout();

$cats = get_the_terms( $post->ID, 'category' );
$cat_name = ( is_array( $cats ) && ! empty( $cats ) ) ? $cats[0]->name : '';

if ( $kayan_kit_card ) {
	echo '<article class="bcard -Post-box-single-item filt" data-trigger-action="' . esc_attr( $uniqid ) . '">';
	echo '<a href="' . esc_url( $PermaLink ) . '" data-trigger-url="' . esc_attr( $uniqid ) . '">';
	echo '<div class="bimg">';
	if ( ! empty( $thumbnail_id ) && function_exists( 'YC_get_attachment' ) ) {
		echo YC_get_attachment(
			array(
				'id'   => $thumbnail_id,
				'alt'  => $post->post_title,
				'size' => 'posts__box',
			)
		);
	} else {
		echo '<i class="fas fa-screwdriver-wrench"></i>';
	}
	if ( $cat_name !== '' ) {
		echo '<span class="bcat">' . esc_html( $cat_name ) . '</span>';
	}
	echo '</div>';
	echo '<div class="bbody">';
	echo '<div class="bmeta"><span><i class="fas fa-calendar"></i> ' . esc_html( get_the_date( '', $post ) ) . '</span></div>';
	echo '<h3>' . esc_html( $post->post_title ) . '</h3>';
	if ( $mini__post_content !== '' ) {
		echo '<p>' . esc_html( wp_strip_all_tags( $mini__post_content ) ) . '</p>';
	}
	echo '<span class="bread">اقرأ المزيد <i class="fas fa-arrow-left"></i></span>';
	echo '</div></a></article>';
	return;
}

echo '<div class="-Post-box-single-item" data-trigger-action="'.$uniqid.'">';
	echo '<div class="--thumb--blog-image--">';
		echo '<div class="-Post-box-item-Thumb">';
			if( !empty( $thumbnail_id ) ){
				echo '<div class="-YC-Loader-Cover" style="background-image: url(' .$lazyload. ');"></div>';
				echo YC_get_attachment(
					array(
						'id'=>$thumbnail_id,
						'alt'=>$post->post_title,
						'size'=>'posts__box'
					)
				);
			}
		echo '</div>';
	echo '</div>';
	echo '<div class="--blog-one-single--">';
		echo '<div class="posts_title"><a href="'.$PermaLink.'" data-trigger-url="'.$uniqid.'">'.$post->post_title.'</a></div>';
		echo '<div class="-P-content">'.$mini__post_content.'</div>';
	echo '</div>';
echo '</div>';
