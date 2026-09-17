<?
if ( ! function_exists( 'kayan_seo_is_enabled' ) ) {
	function kayan_seo_is_enabled() {
		return empty( yc_get_option( 'kayan_seo_disable' ) );
	}
}

if ( ! function_exists( 'kayan_seo_uses_modern_schema' ) ) {
	function kayan_seo_uses_modern_schema() {
		if ( ! kayan_seo_is_enabled() ) {
			return false;
		}
		return empty( yc_get_option( 'kayan_seo_legacy_schema' ) );
	}
}

if ( ! function_exists( 'kayan_seo_text' ) ) {
	function kayan_seo_text( $value ) {
		$value = wp_strip_all_tags( (string) $value );
		$value = preg_replace( '/\s+/u', ' ', $value );
		return trim( $value );
	}
}

if ( ! function_exists( 'kayan_seo_get_site_name' ) ) {
	function kayan_seo_get_site_name() {
		$name = yc_get_option( 'sitename' );
		if ( empty( $name ) ) {
			$name = get_option( 'sitename__schema' );
		}
		if ( empty( $name ) ) {
			$name = get_bloginfo( 'name' );
		}
		return kayan_seo_text( $name );
	}
}

if ( ! function_exists( 'kayan_seo_render_hreflang_links' ) ) {
	function kayan_seo_render_hreflang_links() {
		if ( function_exists( 'kayan_i18n_render_hreflang' ) && kayan_i18n_is_enabled() ) {
			kayan_i18n_render_hreflang();
			return;
		}

		if ( empty( yc_get_option( 'kayan_seo_hreflang_enabled' ) ) ) {
			return;
		}

		$alternates = yc_get_option( 'kayan_seo_hreflang_alternates' );
		if ( ! is_array( $alternates ) || count( $alternates ) < 2 ) {
			return;
		}

		foreach ( $alternates as $row ) {
			if ( ! is_array( $row ) || empty( $row['locale'] ) || empty( $row['url'] ) ) {
				continue;
			}
			echo '<link rel="alternate" hreflang="' . esc_attr( sanitize_text_field( $row['locale'] ) ) . '" href="' . esc_url( $row['url'] ) . '" />' . "\n";
		}
	}
}

if ( ! function_exists( 'kayan_seo_get_schema_lang' ) ) {
	function kayan_seo_get_schema_lang() {
		if ( function_exists( 'kayan_i18n_get_schema_language' ) ) {
			return kayan_i18n_get_schema_language();
		}
		return 'ar';
	}
}

if ( ! function_exists( 'kayan_seo_get_title' ) ) {
	function kayan_seo_get_title() {
		$title = kayan_seo_resolve_title();
		return apply_filters( 'kayan_seo_resolved_title', $title );
	}
}

if ( ! function_exists( 'kayan_seo_resolve_title' ) ) {
	function kayan_seo_resolve_title() {
		if ( is_singular() ) {
			$post_id = get_queried_object_id();
			$native = get_post_meta( $post_id, 'kayan_meta_title', true );
			if ( ! empty( $native ) ) {
				return kayan_seo_text( $native );
			}
			$seo_title = function_exists( 'kayan_seo_get_post_seo_title' ) ? kayan_seo_get_post_seo_title( $post_id ) : '';
			if ( ! empty( $seo_title ) ) {
				return $seo_title;
			}
			return kayan_seo_text( get_the_title() );
		}
		if ( is_category() || is_tax() || is_tag() ) {
			$obj = get_queried_object();
			if ( isset( $obj->term_id ) && function_exists( 'kayan_seo_get_term_seo_title' ) ) {
				$term_title = kayan_seo_get_term_seo_title( $obj->term_id );
				if ( ! empty( $term_title ) ) {
					return $term_title;
				}
			}
			if ( is_tax( 'city' ) && ! empty( $obj->name ) ) {
				return kayan_seo_get_city_headline( $obj );
			}
			return kayan_seo_text( $obj->name ?? '' );
		}
		if ( is_search() ) {
			return 'نتائج البحث عن: ' . kayan_seo_text( get_search_query() );
		}
		if ( is_home() || is_front_page() ) {
			if ( function_exists( 'kayan_seo_rank_math_active' ) && kayan_seo_rank_math_active() ) {
				$rm_titles = kayan_seo_get_rank_math_titles_option();
				if ( ! empty( $rm_titles['homepage_title'] ) ) {
					return kayan_seo_rank_math_replace_vars( $rm_titles['homepage_title'] );
				}
			}
			$title = yc_get_option( 'home__title' );
			return kayan_seo_text( $title ? $title : kayan_seo_get_site_name() );
		}
		return kayan_seo_get_site_name();
	}
}

if ( ! function_exists( 'kayan_seo_get_description' ) ) {
	function kayan_seo_get_description() {
		$description = kayan_seo_resolve_description();
		return apply_filters( 'kayan_seo_resolved_description', $description );
	}
}

if ( ! function_exists( 'kayan_seo_resolve_description' ) ) {
	function kayan_seo_resolve_description() {
		if ( is_singular() ) {
			global $post;
			$native = get_post_meta( $post->ID, 'kayan_meta_description', true );
			if ( ! empty( $native ) ) {
				return kayan_seo_text( $native );
			}
			$custom = function_exists( 'kayan_seo_get_post_seo_description' ) ? kayan_seo_get_post_seo_description( $post->ID ) : '';
			if ( ! empty( $custom ) ) {
				return $custom;
			}
			if ( has_excerpt( $post ) ) {
				return kayan_seo_text( get_the_excerpt( $post ) );
			}
			return kayan_seo_text( wp_trim_words( wp_strip_all_tags( $post->post_content ), 32, '…' ) );
		}

		if ( is_category() || is_tax() || is_tag() ) {
			$obj = get_queried_object();
			if ( isset( $obj->term_id ) ) {
				$custom = function_exists( 'kayan_seo_get_term_seo_description' ) ? kayan_seo_get_term_seo_description( $obj->term_id ) : '';
				if ( ! empty( $custom ) ) {
					return $custom;
				}
			}
			if ( ! empty( $obj->description ) ) {
				return kayan_seo_text( $obj->description );
			}
			if ( is_tax( 'city' ) ) {
				return kayan_seo_text( 'خدمات منزلية في ' . ( $obj->name ?? '' ) . ' | ' . kayan_seo_get_site_name() );
			}
			return kayan_seo_text( ( $obj->name ?? '' ) . ' | ' . kayan_seo_get_site_name() );
		}

		if ( is_home() || is_front_page() ) {
			if ( function_exists( 'kayan_seo_rank_math_active' ) && kayan_seo_rank_math_active() ) {
				$rm_titles = kayan_seo_get_rank_math_titles_option();
				if ( ! empty( $rm_titles['homepage_description'] ) ) {
					return kayan_seo_rank_math_replace_vars( $rm_titles['homepage_description'] );
				}
			}
		}

		$default = yc_get_option( 'kayan_seo_default_description' );
		if ( ! empty( $default ) ) {
			return kayan_seo_text( $default );
		}

		$business = get_option( 'YourColor_Schema_business' );
		if ( is_array( $business ) && ! empty( $business['description'] ) ) {
			return kayan_seo_text( $business['description'] );
		}

		$tagline = get_bloginfo( 'description' );
		if ( ! empty( $tagline ) ) {
			return kayan_seo_text( $tagline );
		}

		return kayan_seo_get_site_name();
	}
}

if ( ! function_exists( 'kayan_seo_get_canonical_url' ) ) {
	function kayan_seo_get_canonical_url() {
		if ( is_search() || is_404() ) {
			return '';
		}
		if ( is_singular() ) {
			$url = get_permalink();
			$paged = get_query_var( 'page' );
			if ( $paged && $paged > 1 ) {
				$url = trailingslashit( $url ) . user_trailingslashit( $paged, 'single_paged' );
			}
			return $url;
		}
		if ( is_front_page() ) {
			return home_url( '/' );
		}
		if ( is_home() ) {
			$posts_page = (int) get_option( 'page_for_posts' );
			if ( $posts_page ) {
				return get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
			}
			return home_url( '/' );
		}
		if ( is_category() || is_tax() || is_tag() ) {
			$link = get_term_link( get_queried_object() );
			if ( is_wp_error( $link ) ) {
				return '';
			}
			$paged = max( 1, (int) get_query_var( 'paged' ) );
			if ( $paged > 1 ) {
				return get_pagenum_link( $paged );
			}
			return $link;
		}
		if ( is_author() ) {
			return get_author_posts_url( get_queried_object_id() );
		}
		if ( is_post_type_archive() ) {
			return get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
		}
		if ( is_archive() ) {
			return get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
		}
		return '';
	}
}

if ( ! function_exists( 'kayan_seo_get_current_url' ) ) {
	function kayan_seo_get_current_url() {
		$canonical = kayan_seo_get_canonical_url();
		if ( ! empty( $canonical ) ) {
			return $canonical;
		}

		global $wp;
		if ( isset( $wp ) && is_object( $wp ) ) {
			return home_url( add_query_arg( array(), $wp->request ) );
		}

		return home_url( '/' );
	}
}

if ( ! function_exists( 'kayan_seo_get_term_image_url' ) ) {
	function kayan_seo_get_term_image_url( $term_id ) {
		$image_id = get_term_meta( $term_id, 'image_blog_id_id', true );
		if ( empty( $image_id ) ) {
			$image_id = get_term_meta( $term_id, 'image_blog_id', true );
		}
		if ( ! empty( $image_id ) && is_numeric( $image_id ) ) {
			$url = wp_get_attachment_image_url( (int) $image_id, 'full' );
			if ( $url ) {
				return esc_url( $url );
			}
		}

		$url_meta = get_term_meta( $term_id, 'image_blog_id', true );
		if ( ! empty( $url_meta ) && is_string( $url_meta ) && filter_var( $url_meta, FILTER_VALIDATE_URL ) ) {
			return esc_url( $url_meta );
		}

		return '';
	}
}

if ( ! function_exists( 'kayan_seo_get_post_area_served' ) ) {
	function kayan_seo_get_post_area_served( $post_id ) {
		$terms = get_the_terms( $post_id, 'city' );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			$served = array();
			foreach ( $terms as $term ) {
				$served[] = array(
					'@type' => 'City',
					'name' => kayan_seo_text( $term->name ),
				);
			}
			return $served;
		}

		$business = kayan_seo_get_business_settings();
		if ( ! empty( $business['addressLocality'] ) ) {
			return kayan_seo_text( $business['addressLocality'] );
		}
		return kayan_seo_text( $business['addressCountry'] );
	}
}

if ( ! function_exists( 'kayan_seo_get_og_image' ) ) {
	function kayan_seo_get_og_image() {
		if ( is_singular() && has_post_thumbnail() ) {
			$url = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
			if ( $url ) {
				return esc_url( $url );
			}
		}

		if ( is_tax() || is_category() || is_tag() ) {
			$obj = get_queried_object();
			if ( isset( $obj->term_id ) ) {
				$term_image = kayan_seo_get_term_image_url( $obj->term_id );
				if ( ! empty( $term_image ) ) {
					return $term_image;
				}
			}
		}

		$custom = yc_get_option( 'kayan_seo_og_image' );
		if ( ! empty( $custom ) ) {
			return esc_url( $custom );
		}

		$logo = get_option( 'logo__schema' );
		if ( ! empty( $logo ) ) {
			return esc_url( $logo );
		}

		$logo_data = yc_get_option( 'logo__data' );
		if ( is_array( $logo_data ) && isset( $logo_data['Image']['image_logo'] ) ) {
			return esc_url( $logo_data['Image']['image_logo'] );
		}

		return '';
	}
}

if ( ! function_exists( 'kayan_seo_should_noindex' ) ) {
	function kayan_seo_should_noindex() {
		return is_search() || is_404() || ! empty( $_GET['ajax'] );
	}
}

if ( ! function_exists( 'kayan_seo_get_business_settings' ) ) {
	function kayan_seo_get_business_settings() {
		$business = get_option( 'YourColor_Schema_business' );
		$business = is_array( $business ) ? $business : array();

		$type = yc_get_option( 'kayan_seo_business_type' );
		if ( empty( $type ) ) {
			$type = 'HomeAndConstructionBusiness';
		}

		$phone = '';
		if ( ! empty( $business['telephone'] ) ) {
			$phone = $business['telephone'];
		} elseif ( ! empty( yc_get_option( 'phonenumber' ) ) ) {
			$phone = yc_get_option( 'phonenumber' );
		} elseif ( ! empty( yc_get_option( 'contact_number' ) ) ) {
			$phone = yc_get_option( 'contact_number' );
		}

		return array(
			'@type' => $type,
			'name' => ! empty( $business['Business_Name'] ) ? kayan_seo_text( $business['Business_Name'] ) : kayan_seo_get_site_name(),
			'description' => ! empty( $business['description'] ) ? kayan_seo_text( $business['description'] ) : kayan_seo_get_description(),
			'telephone' => kayan_seo_text( $phone ),
			'streetAddress' => kayan_seo_text( $business['Street_Address'] ?? '' ),
			'addressLocality' => kayan_seo_text( $business['City'] ?? '' ),
			'addressRegion' => kayan_seo_text( $business['State'] ?? '' ),
			'postalCode' => kayan_seo_text( $business['Postal_Code'] ?? '' ),
			'addressCountry' => kayan_seo_text( $business['Country'] ?? 'SA' ),
			'priceRange' => kayan_seo_text( $business['Price_Range'] ?? '$$' ),
			'openingHours' => kayan_seo_text( $business['openingHours'] ?? '' ),
			'latitude' => kayan_seo_text( yc_get_option( 'kayan_seo_latitude' ) ),
			'longitude' => kayan_seo_text( yc_get_option( 'kayan_seo_longitude' ) ),
			'gbp_url' => esc_url_raw( yc_get_option( 'kayan_seo_gbp_url' ) ),
			'same_as_raw' => yc_get_option( 'kayan_seo_same_as' ),
			'logo' => esc_url_raw( get_option( 'logo__schema' ) ),
			'image' => kayan_seo_get_og_image(),
		);
	}
}

if ( ! function_exists( 'kayan_seo_parse_same_as' ) ) {
	function kayan_seo_parse_same_as( $settings ) {
		$urls = array();
		if ( ! empty( $settings['gbp_url'] ) ) {
			$urls[] = $settings['gbp_url'];
		}
		if ( ! empty( $settings['same_as_raw'] ) ) {
			$lines = preg_split( '/\r\n|\r|\n/', $settings['same_as_raw'] );
			foreach ( $lines as $line ) {
				$line = esc_url_raw( trim( $line ) );
				if ( ! empty( $line ) ) {
					$urls[] = $line;
				}
			}
		}
		$business = get_option( 'YourColor_Schema_business' );
		if ( is_array( $business ) ) {
			foreach ( array( 'Facebook', 'Twitter', 'Instagram', 'Linkedin', 'Youtube' ) as $social_key ) {
				if ( ! empty( $business[ $social_key ] ) ) {
					$urls[] = esc_url_raw( $business[ $social_key ] );
				}
			}
		}
		return array_values( array_unique( array_filter( $urls ) ) );
	}
}

if ( ! function_exists( 'kayan_seo_print_json_ld' ) ) {
	function kayan_seo_print_json_ld( $graph ) {
		if ( empty( $graph ) ) {
			return;
		}
		$payload = array(
			'@context' => 'https://schema.org',
			'@graph' => array_values( $graph ),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
	}
}

if ( ! function_exists( 'kayan_seo_get_city_headline' ) ) {
	function kayan_seo_get_city_headline( $term = null ) {
		if ( ! $term ) {
			$term = get_queried_object();
		}
		$name = kayan_seo_text( $term->name ?? '' );
		if ( empty( $name ) ) {
			return '';
		}
		return kayan_seo_text( 'خدمات منزلية في ' . $name );
	}
}

if ( ! function_exists( 'kayan_seo_render_verification_meta' ) ) {
	function kayan_seo_render_verification_meta() {
		$gsc = trim( (string) yc_get_option( 'kayan_seo_gsc_verification' ) );
		if ( ! empty( $gsc ) ) {
			echo '<meta name="google-site-verification" content="' . esc_attr( $gsc ) . '" />' . "\n";
		}

		$bing = trim( (string) yc_get_option( 'kayan_seo_bing_verification' ) );
		if ( ! empty( $bing ) ) {
			echo '<meta name="msvalidate.01" content="' . esc_attr( $bing ) . '" />' . "\n";
		}

		$yandex = trim( (string) yc_get_option( 'kayan_seo_yandex_verification' ) );
		if ( ! empty( $yandex ) ) {
			echo '<meta name="yandex-verification" content="' . esc_attr( $yandex ) . '" />' . "\n";
		}
	}
}

if ( ! function_exists( 'kayan_seo_render_head_meta' ) ) {
	function kayan_seo_render_head_meta() {
		if ( ! kayan_seo_is_enabled() ) {
			return;
		}

		$title = kayan_seo_get_title();
		$description = kayan_seo_get_description();
		$canonical = kayan_seo_get_canonical_url();
		$image = kayan_seo_get_og_image();
		$site_name = kayan_seo_get_site_name();
		$locale = yc_get_option( 'kayan_seo_locale' );
		if ( empty( $locale ) ) {
			$locale = 'ar_SA';
		}

		if ( kayan_seo_should_noindex() ) {
			echo '<meta name="robots" content="noindex, follow" />' . "\n";
		} else {
			echo '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />' . "\n";
		}

		if ( ! empty( $description ) ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}

		if ( ! empty( $canonical ) ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
		}

		echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '" />' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( kayan_seo_get_og_type() ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
		if ( ! empty( $description ) ) {
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		if ( ! empty( $canonical ) ) {
			echo '<meta property="og:url" content="' . esc_url( $canonical ) . '" />' . "\n";
		}
		if ( ! empty( $image ) ) {
			echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
		}

		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
		if ( ! empty( $description ) ) {
			echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		if ( ! empty( $image ) ) {
			echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
		}

		kayan_seo_render_hreflang_links();

		if ( ! kayan_seo_should_noindex() && get_option( 'blog_public' ) ) {
			echo '<link rel="sitemap" type="application/xml" title="Sitemap" href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '" />' . "\n";
		}
	}
}
