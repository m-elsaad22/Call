<?
if ( ! function_exists( 'kayan_seo_get_dashboard_data' ) ) {
	function kayan_seo_get_dashboard_data() {
		$business = get_option( 'YourColor_Schema_business' );
		$business = is_array( $business ) ? $business : array();

		$total_posts = (int) wp_count_posts( 'post' )->publish;
		$total_pages = (int) wp_count_posts( 'page' )->publish;
		$cities = get_terms(
			array(
				'taxonomy' => 'city',
				'hide_empty' => false,
			)
		);
		$cities = is_array( $cities ) ? $cities : array();
		$city_count = count( $cities );

		$cities_with_seo = 0;
		$cities_with_image = 0;
		foreach ( $cities as $city ) {
			$city_desc = get_term_meta( $city->term_id, 'rank_math_description', true );
			if ( empty( $city_desc ) ) {
				$city_desc = get_term_meta( $city->term_id, 'kayan_meta_description', true );
			}
			if ( ! empty( $city_desc ) ) {
				$cities_with_seo++;
			}
			if ( function_exists( 'kayan_seo_get_term_image_url' ) && kayan_seo_get_term_image_url( $city->term_id ) ) {
				$cities_with_image++;
			}
		}

		$posts_with_meta = 0;
		$posts_with_city = 0;
		if ( $total_posts > 0 ) {
			global $wpdb;
			$posts_with_meta = (int) $wpdb->get_var(
				"SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE p.post_type = 'post' AND p.post_status = 'publish'
				AND pm.meta_key IN ('rank_math_description','kayan_meta_description')
				AND pm.meta_value != ''"
			);
			$posts_with_city = (int) $wpdb->get_var(
				"SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
				INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE p.post_type = 'post' AND p.post_status = 'publish'
				AND tt.taxonomy = 'city'"
			);
		}

		$checks = array(
			array(
				'label' => 'KAYAN SEO مفعّل',
				'ok' => function_exists( 'kayan_seo_is_enabled' ) && kayan_seo_is_enabled(),
				'hint' => 'إعدادات القالب → KAYAN SEO',
			),
			array(
				'label' => 'Rank Math متصل',
				'ok' => function_exists( 'kayan_seo_rank_math_active' ) && kayan_seo_rank_math_active(),
				'hint' => 'العناوين والأوصاف تُخزَّن في rank_math_title / rank_math_description',
			),
			array(
				'label' => 'الوصف الافتراضي للموقع',
				'ok' => ! empty( yc_get_option( 'kayan_seo_default_description' ) ),
				'hint' => 'يُستخدم في الصفحة الرئيسية والأرشيف',
			),
			array(
				'label' => 'صورة OG الافتراضية',
				'ok' => ! empty( yc_get_option( 'kayan_seo_og_image' ) ),
				'hint' => 'تحسين المشاركة على السوشيال',
			),
			array(
				'label' => 'بيانات النشاط (Schema)',
				'ok' => ! empty( $business['Business_Name'] ) && ! empty( $business['telephone'] ),
				'hint' => 'إعدادات القالب → Schema',
			),
			array(
				'label' => 'تحقق Google Search Console',
				'ok' => ! empty( yc_get_option( 'kayan_seo_gsc_verification' ) ),
				'hint' => 'الصق رمز التحقق من GSC',
			),
			array(
				'label' => 'رابط Google Business Profile',
				'ok' => ! empty( yc_get_option( 'kayan_seo_gbp_url' ) ),
				'hint' => 'مهم للـ Local SEO',
			),
			array(
				'label' => 'مدن مُعرَّفة في الموقع',
				'ok' => $city_count > 0,
				'hint' => 'أضف مدناً من تصنيف المدن',
			),
		);

		$score = 0;
		foreach ( $checks as $check ) {
			if ( ! empty( $check['ok'] ) ) {
				$score++;
			}
		}
		$score_percent = count( $checks ) > 0 ? (int) round( ( $score / count( $checks ) ) * 100 ) : 0;

		return array(
			'score_percent' => $score_percent,
			'score_passed' => $score,
			'score_total' => count( $checks ),
			'checks' => $checks,
			'sitemap_url' => home_url( '/wp-sitemap.xml' ),
			'home_url' => home_url( '/' ),
			'gsc_url' => 'https://search.google.com/search-console',
			'stats' => array(
				'posts' => $total_posts,
				'pages' => $total_pages,
				'cities' => $city_count,
				'posts_with_meta' => $posts_with_meta,
				'posts_with_city' => $posts_with_city,
				'cities_with_seo' => $cities_with_seo,
				'cities_with_image' => $cities_with_image,
			),
		);
	}
}

if ( ! function_exists( 'kayan_seo_is_cities_index_page' ) ) {
	function kayan_seo_is_cities_index_page( $post_id = 0 ) {
		if ( ! is_page() && ! $post_id ) {
			return false;
		}
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}
		$template = get_post_meta( $post_id, 'template', true );
		return is_array( $template ) && isset( $template['SelectedModel'] ) && $template['SelectedModel'] === 'cities';
	}
}
