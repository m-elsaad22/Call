<?
/**
 * Extended Schema nodes: reviews, team, enriched Person/Organization.
 */

if ( ! function_exists( 'kayan_seo_parse_opening_hours' ) ) {
	function kayan_seo_parse_opening_hours( $raw ) {
		$raw = trim( (string) $raw );
		if ( $raw === '' ) {
			return array();
		}
		// Expected: "Mo-Fr 09:00-18:00" or "09:00-22:00" (all days).
		if ( preg_match( '/^(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', $raw, $m ) ) {
			return array(
				array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ),
					'opens'     => $m[1],
					'closes'    => $m[2],
				),
			);
		}
		return array();
	}
}

if ( ! function_exists( 'kayan_seo_get_home_reviews_data' ) ) {
	function kayan_seo_get_home_reviews_data() {
		$stored = yc_get_option( 'kayan_seo_home_reviews' );
		if ( is_array( $stored ) && ! empty( $stored ) ) {
			return $stored;
		}

		return array();
	}
}

if ( ! function_exists( 'kayan_seo_build_review_schema_nodes' ) ) {
	function kayan_seo_build_review_schema_nodes( $business ) {
		$reviews = kayan_seo_get_home_reviews_data();
		if ( empty( $reviews ) ) {
			return array();
		}

		$nodes   = array();
		$total   = 0;
		$count   = 0;
		$biz_id  = home_url( '/#business' );
		$biz_name = isset( $business['name'] ) ? $business['name'] : kayan_seo_get_site_name();

		foreach ( $reviews as $i => $review ) {
			if ( empty( $review['text'] ) ) {
				continue;
			}
			$rating = isset( $review['rating'] ) ? (int) $review['rating'] : 5;
			$total += $rating;
			$count++;
			$nodes[] = array(
				'@type'         => 'Review',
				'@id'           => home_url( '/#review-' . ( $i + 1 ) ),
				'author'        => array(
					'@type' => 'Person',
					'name'  => kayan_seo_text( isset( $review['author'] ) ? $review['author'] : '' ),
				),
				'reviewRating'  => array(
					'@type'       => 'Rating',
					'ratingValue' => $rating,
					'bestRating'  => 5,
					'worstRating' => 1,
				),
				'reviewBody'    => kayan_seo_text( $review['text'] ),
				'itemReviewed'  => array( '@id' => $biz_id ),
				'publisher'     => array(
					'@type' => 'Organization',
					'name'  => 'Google',
				),
			);
		}

		if ( $count > 0 ) {
			$nodes[] = array(
				'@type'       => 'AggregateRating',
				'@id'         => home_url( '/#aggregate-rating' ),
				'itemReviewed'=> array( '@id' => $biz_id ),
				'ratingValue' => round( $total / $count, 1 ),
				'reviewCount' => $count,
				'bestRating'  => 5,
				'worstRating' => 1,
			);
		}

		return $nodes;
	}
}

if ( ! function_exists( 'kayan_seo_build_team_schema_nodes' ) ) {
	function kayan_seo_build_team_schema_nodes() {
		if ( ! post_type_exists( 'team' ) ) {
			return array();
		}

		$members = get_posts(
			array(
				'post_type'      => 'team',
				'post_status'    => 'publish',
				'posts_per_page' => 12,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);

		if ( empty( $members ) ) {
			return array();
		}

		$nodes = array();
		foreach ( $members as $member ) {
			$role = get_post_meta( $member->ID, 'team_role', true );
			if ( empty( $role ) ) {
				$role = get_post_meta( $member->ID, 'member_role', true );
			}
			$image = get_the_post_thumbnail_url( $member->ID, 'medium' );
			$node  = array(
				'@type'    => 'Person',
				'@id'      => get_permalink( $member ) . '#person',
				'name'     => kayan_seo_text( get_the_title( $member ) ),
				'jobTitle' => kayan_seo_text( $role ),
				'worksFor' => array(
					'@type' => 'Organization',
					'name'  => kayan_seo_get_site_name(),
					'url'   => home_url( '/' ),
				),
			);
			if ( $image ) {
				$node['image'] = esc_url( $image );
			}
			$nodes[] = $node;
		}

		return $nodes;
	}
}

if ( ! function_exists( 'kayan_seo_build_author_person_node' ) ) {
	function kayan_seo_build_author_person_node( $author ) {
		if ( ! $author || empty( $author->ID ) ) {
			return array(
				'@type' => 'Person',
				'name'  => kayan_seo_get_site_name(),
			);
		}
		$node = array(
			'@type' => 'Person',
			'name'  => $author->display_name,
			'url'   => get_author_posts_url( $author->ID ),
		);
		$avatar = get_avatar_url( $author->ID, array( 'size' => 256 ) );
		if ( $avatar ) {
			$node['image'] = esc_url( $avatar );
		}
		$desc = get_the_author_meta( 'description', $author->ID );
		if ( $desc ) {
			$node['description'] = kayan_seo_text( $desc );
		}
		return $node;
	}
}
