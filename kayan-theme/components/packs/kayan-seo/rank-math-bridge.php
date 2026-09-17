<?
if ( ! function_exists( 'kayan_seo_rank_math_active' ) ) {
	function kayan_seo_rank_math_active() {
		return defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
	}
}

if ( ! function_exists( 'kayan_seo_rank_math_replace_vars' ) ) {
	function kayan_seo_rank_math_replace_vars( $value, $object = null ) {
		if ( empty( $value ) ) {
			return '';
		}
		if ( kayan_seo_rank_math_active() && class_exists( 'RankMath\Helper' ) && method_exists( 'RankMath\Helper', 'replace_vars' ) ) {
			return kayan_seo_text( RankMath\Helper::replace_vars( $value, $object ) );
		}
		return kayan_seo_text( $value );
	}
}

if ( ! function_exists( 'kayan_seo_get_rank_math_titles_option' ) ) {
	function kayan_seo_get_rank_math_titles_option() {
		$options = get_option( 'rank-math-options-titles' );
		return is_array( $options ) ? $options : array();
	}
}

if ( ! function_exists( 'kayan_seo_get_post_seo_title' ) ) {
	function kayan_seo_get_post_seo_title( $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}
		if ( ! $post_id ) {
			return '';
		}

		if ( kayan_seo_rank_math_active() ) {
			$stored = get_post_meta( $post_id, 'rank_math_title', true );
			if ( ! empty( $stored ) ) {
				return kayan_seo_rank_math_replace_vars( $stored, get_post( $post_id ) );
			}
		}

		return '';
	}
}

if ( ! function_exists( 'kayan_seo_get_post_seo_description' ) ) {
	function kayan_seo_get_post_seo_description( $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}
		if ( ! $post_id ) {
			return '';
		}

		if ( kayan_seo_rank_math_active() ) {
			$stored = get_post_meta( $post_id, 'rank_math_description', true );
			if ( ! empty( $stored ) ) {
				return kayan_seo_rank_math_replace_vars( $stored, get_post( $post_id ) );
			}
		}

		$legacy = get_post_meta( $post_id, 'kayan_meta_description', true );
		if ( ! empty( $legacy ) ) {
			return kayan_seo_text( $legacy );
		}

		return '';
	}
}

if ( ! function_exists( 'kayan_seo_get_term_seo_title' ) ) {
	function kayan_seo_get_term_seo_title( $term_id ) {
		if ( kayan_seo_rank_math_active() ) {
			$stored = get_term_meta( $term_id, 'rank_math_title', true );
			if ( ! empty( $stored ) ) {
				$term = get_term( $term_id );
				return kayan_seo_rank_math_replace_vars( $stored, $term );
			}
		}
		return '';
	}
}

if ( ! function_exists( 'kayan_seo_get_term_seo_description' ) ) {
	function kayan_seo_get_term_seo_description( $term_id ) {
		if ( kayan_seo_rank_math_active() ) {
			$stored = get_term_meta( $term_id, 'rank_math_description', true );
			if ( ! empty( $stored ) ) {
				$term = get_term( $term_id );
				return kayan_seo_rank_math_replace_vars( $stored, $term );
			}
		}

		$legacy = get_term_meta( $term_id, 'kayan_meta_description', true );
		if ( ! empty( $legacy ) ) {
			return kayan_seo_text( $legacy );
		}

		return '';
	}
}

if ( ! function_exists( 'kayan_seo_update_rank_math_post_meta' ) ) {
	function kayan_seo_update_rank_math_post_meta( $post_id, $title = '', $description = '' ) {
		if ( ! kayan_seo_rank_math_active() || ! $post_id ) {
			return;
		}
		if ( $title !== '' ) {
			update_post_meta( $post_id, 'rank_math_title', $title );
		}
		if ( $description !== '' ) {
			update_post_meta( $post_id, 'rank_math_description', $description );
		}
	}
}

if ( ! function_exists( 'kayan_seo_update_rank_math_term_meta' ) ) {
	function kayan_seo_update_rank_math_term_meta( $term_id, $title = '', $description = '' ) {
		if ( ! kayan_seo_rank_math_active() || ! $term_id ) {
			return;
		}
		if ( $title !== '' ) {
			update_term_meta( $term_id, 'rank_math_title', $title );
		}
		if ( $description !== '' ) {
			update_term_meta( $term_id, 'rank_math_description', $description );
		}
	}
}

if ( ! function_exists( 'kayan_seo_sync_homepage_rank_math_titles' ) ) {
	function kayan_seo_sync_homepage_rank_math_titles() {
		if ( ! kayan_seo_rank_math_active() ) {
			return;
		}

		$options = kayan_seo_get_rank_math_titles_option();
		$default_description = yc_get_option( 'kayan_seo_default_description' );
		if ( ! empty( $default_description ) ) {
			$options['homepage_description'] = $default_description;
		}

		$home_title = yc_get_option( 'home__title' );
		if ( ! empty( $home_title ) ) {
			$options['homepage_title'] = $home_title;
		}

		update_option( 'rank-math-options-titles', $options );
	}
}
add_action( 'YC__CFM__After_Save_Options_metabox_kayan_seo', 'kayan_seo_sync_homepage_rank_math_titles' );
