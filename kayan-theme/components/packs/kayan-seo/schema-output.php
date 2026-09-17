<?
if ( ! function_exists( 'kayan_seo_build_local_business_node' ) ) {
	function kayan_seo_build_local_business_node( $settings, $node_id = 'https://example.com/#business' ) {
		$node = array(
			'@type' => $settings['@type'],
			'@id' => $node_id,
			'name' => $settings['name'],
			'description' => $settings['description'],
			'url' => home_url( '/' ),
			'telephone' => $settings['telephone'],
			'priceRange' => $settings['priceRange'],
			'address' => array(
				'@type' => 'PostalAddress',
				'streetAddress' => $settings['streetAddress'],
				'addressLocality' => $settings['addressLocality'],
				'addressRegion' => $settings['addressRegion'],
				'postalCode' => $settings['postalCode'],
				'addressCountry' => $settings['addressCountry'],
			),
		);

		if ( ! empty( $settings['image'] ) ) {
			$node['image'] = $settings['image'];
		}
		if ( ! empty( $settings['logo'] ) ) {
			$node['logo'] = $settings['logo'];
		}

		$same_as = kayan_seo_parse_same_as( $settings );
		if ( ! empty( $same_as ) ) {
			$node['sameAs'] = $same_as;
		}

		if ( ! empty( $settings['latitude'] ) && ! empty( $settings['longitude'] ) ) {
			$node['geo'] = array(
				'@type' => 'GeoCoordinates',
				'latitude' => $settings['latitude'],
				'longitude' => $settings['longitude'],
			);
		}

		if ( ! empty( $settings['openingHours'] ) ) {
			$hours = kayan_seo_parse_opening_hours( $settings['openingHours'] );
			if ( ! empty( $hours ) ) {
				$node['openingHoursSpecification'] = $hours;
			}
		}

		return $node;
	}
}

if ( ! function_exists( 'kayan_seo_append_single_service_faq_nodes' ) ) {
	function kayan_seo_append_single_service_faq_nodes( array &$graph, $post, $permalink, $business ) {
		$graph[] = array(
			'@type' => 'Service',
			'@id' => $permalink . '#service',
			'name' => kayan_seo_text( get_the_title( $post ) ),
			'description' => kayan_seo_get_description(),
			'url' => $permalink,
			'provider' => kayan_seo_build_local_business_node( $business, home_url( '/#business' ) ),
			'areaServed' => kayan_seo_get_post_area_served( $post->ID ),
		);

		$questions = get_post_meta( $post->ID, 'yourcolor__faqs', true );
		if ( ! is_array( $questions ) || empty( $questions ) ) {
			return;
		}

		$entities = array();
		foreach ( $questions as $faq ) {
			if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
				continue;
			}
			$entities[] = array(
				'@type' => 'Question',
				'name' => kayan_seo_text( $faq['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text' => kayan_seo_text( $faq['answer'] ),
				),
			);
		}
		if ( ! empty( $entities ) ) {
			$graph[] = array(
				'@type' => 'FAQPage',
				'@id' => $permalink . '#faq',
				'mainEntity' => $entities,
			);
		}
	}
}

if ( ! function_exists( 'kayan_seo_collect_home_schema_nodes' ) ) {
	function kayan_seo_collect_home_schema_nodes( array &$graph ) {
		if ( ! kayan_seo_uses_modern_schema() || ( ! is_front_page() && ! is_home() ) ) {
			return;
		}

		$business = kayan_seo_get_business_settings();
		$site_id = home_url( '/#website' );
		$business_id = home_url( '/#business' );
		$org_id = home_url( '/#organization' );

		$nodes = array(
			array(
				'@type' => 'WebSite',
				'@id' => $site_id,
				'url' => home_url( '/' ),
				'name' => kayan_seo_get_site_name(),
				'description' => kayan_seo_get_description(),
				'inLanguage' => kayan_seo_get_schema_lang(),
				'publisher' => array( '@id' => $org_id ),
				'potentialAction' => array(
					'@type' => 'SearchAction',
					'target' => home_url( '/search/{search_term_string}' ),
					'query-input' => 'required name=search_term_string',
				),
			),
			array(
				'@type' => 'Organization',
				'@id' => $org_id,
				'name' => $business['name'],
				'url' => home_url( '/' ),
				'logo' => ! empty( $business['logo'] ) ? $business['logo'] : $business['image'],
				'sameAs' => kayan_seo_parse_same_as( $business ),
			),
			kayan_seo_build_local_business_node( $business, $business_id ),
		);

		if ( function_exists( 'kayan_seo_build_review_schema_nodes' ) ) {
			$nodes = array_merge( $nodes, kayan_seo_build_review_schema_nodes( $business ) );
		}
		if ( function_exists( 'kayan_seo_build_team_schema_nodes' ) ) {
			$nodes = array_merge( $nodes, kayan_seo_build_team_schema_nodes() );
		}

		$graph = array_merge( $graph, $nodes );
	}
}

if ( ! function_exists( 'kayan_seo_collect_single_schema_nodes' ) ) {
	function kayan_seo_collect_single_schema_nodes( array &$graph ) {
		if ( ! kayan_seo_uses_modern_schema() || ! is_singular( 'post' ) ) {
			return;
		}

		global $post;
		$permalink = get_permalink( $post );
		$business = kayan_seo_get_business_settings();
		$thumbnail = get_the_post_thumbnail_url( $post, 'full' );
		$author = get_userdata( $post->post_author );

		$nodes = array(
			array(
				'@type' => 'Article',
				'@id' => $permalink . '#article',
				'headline' => kayan_seo_text( get_the_title( $post ) ),
				'description' => kayan_seo_get_description(),
				'datePublished' => get_the_date( 'c', $post ),
				'dateModified' => get_the_modified_date( 'c', $post ),
				'mainEntityOfPage' => $permalink,
				'inLanguage' => kayan_seo_get_schema_lang(),
				'author' => kayan_seo_build_author_person_node( $author ),
				'publisher' => array(
					'@type' => 'Organization',
					'name' => $business['name'],
					'logo' => array(
						'@type' => 'ImageObject',
						'url' => $business['logo'] ? $business['logo'] : $business['image'],
					),
				),
				'image' => $thumbnail ? array( $thumbnail ) : array(),
			),
		);

		kayan_seo_append_single_service_faq_nodes( $nodes, $post, $permalink, $business );
		$nodes = kayan_seo_merge_breadcrumb_into_graph( $nodes );
		$graph = array_merge( $graph, $nodes );
	}
}

if ( ! function_exists( 'kayan_seo_collect_page_schema_nodes' ) ) {
	function kayan_seo_collect_page_schema_nodes( array &$graph ) {
		if ( ! kayan_seo_uses_modern_schema() || ! is_page() ) {
			return;
		}
		if ( function_exists( 'kayan_seo_is_cities_index_page' ) && kayan_seo_is_cities_index_page() ) {
			return;
		}

		global $post;
		$permalink = get_permalink( $post );
		$business = kayan_seo_get_business_settings();
		$thumbnail = get_the_post_thumbnail_url( $post, 'full' );

		$nodes = array(
			array(
				'@type' => 'WebPage',
				'@id' => $permalink . '#webpage',
				'name' => kayan_seo_text( get_the_title( $post ) ),
				'description' => kayan_seo_get_description(),
				'url' => $permalink,
				'inLanguage' => kayan_seo_get_schema_lang(),
				'isPartOf' => array(
					'@type' => 'WebSite',
					'@id' => home_url( '/#website' ),
					'name' => kayan_seo_get_site_name(),
				),
				'publisher' => array(
					'@type' => 'Organization',
					'name' => $business['name'],
					'logo' => array(
						'@type' => 'ImageObject',
						'url' => $business['logo'] ? $business['logo'] : $business['image'],
					),
				),
			),
		);

		if ( $thumbnail ) {
			$nodes[0]['primaryImageOfPage'] = array(
				'@type' => 'ImageObject',
				'url' => $thumbnail,
			);
		}

		$nodes = kayan_seo_merge_breadcrumb_into_graph( $nodes );
		$graph = array_merge( $graph, $nodes );
	}
}

if ( ! function_exists( 'kayan_seo_collect_cities_index_schema_nodes' ) ) {
	function kayan_seo_collect_cities_index_schema_nodes( array &$graph ) {
		if ( ! kayan_seo_uses_modern_schema() || ! function_exists( 'kayan_seo_is_cities_index_page' ) || ! kayan_seo_is_cities_index_page() ) {
			return;
		}

		global $post;
		$permalink = get_permalink( $post );
		$list_items = array();
		$position = 0;

		$cities = get_terms(
			array(
				'taxonomy' => 'city',
				'hide_empty' => false,
				'number' => 60,
			)
		);

		if ( is_array( $cities ) ) {
			foreach ( $cities as $city ) {
				$city_link = get_term_link( $city );
				if ( is_wp_error( $city_link ) ) {
					continue;
				}
				$position++;
				$list_items[] = array(
					'@type' => 'ListItem',
					'position' => $position,
					'name' => kayan_seo_text( $city->name ),
					'url' => $city_link,
				);
			}
		}

		$nodes = array(
			array(
				'@type' => 'CollectionPage',
				'@id' => $permalink . '#cities-index',
				'name' => kayan_seo_text( get_the_title( $post ) ),
				'description' => kayan_seo_get_description(),
				'url' => $permalink,
				'inLanguage' => kayan_seo_get_schema_lang(),
				'isPartOf' => array(
					'@type' => 'WebSite',
					'@id' => home_url( '/#website' ),
					'name' => kayan_seo_get_site_name(),
				),
			),
		);

		if ( ! empty( $list_items ) ) {
			$nodes[] = array(
				'@type' => 'ItemList',
				'@id' => $permalink . '#city-list',
				'name' => 'المدن التي نخدمها',
				'itemListElement' => $list_items,
			);
		}

		$nodes = kayan_seo_merge_breadcrumb_into_graph( $nodes );
		$graph = array_merge( $graph, $nodes );
	}
}

if ( ! function_exists( 'kayan_seo_collect_city_archive_schema_nodes' ) ) {
	function kayan_seo_collect_city_archive_schema_nodes( array &$graph ) {
		if ( ! kayan_seo_uses_modern_schema() || ! is_tax( 'city' ) ) {
			return;
		}

		$term = get_queried_object();
		if ( ! $term || empty( $term->term_id ) ) {
			return;
		}

		$term_link = get_term_link( $term );
		if ( is_wp_error( $term_link ) ) {
			return;
		}

		$business = kayan_seo_get_business_settings();
		$nodes = array(
			array(
				'@type' => 'CollectionPage',
				'@id' => $term_link . '#collection',
				'name' => kayan_seo_text( $term->name ),
				'description' => kayan_seo_get_description(),
				'url' => $term_link,
				'inLanguage' => kayan_seo_get_schema_lang(),
				'isPartOf' => array(
					'@type' => 'WebSite',
					'@id' => home_url( '/#website' ),
					'name' => kayan_seo_get_site_name(),
				),
				'about' => array(
					'@type' => 'City',
					'name' => kayan_seo_text( $term->name ),
				),
				'provider' => kayan_seo_build_local_business_node( $business, home_url( '/#business' ) ),
			),
		);

		$term_image = kayan_seo_get_term_image_url( $term->term_id );
		if ( ! empty( $term_image ) ) {
			$nodes[0]['primaryImageOfPage'] = array(
				'@type' => 'ImageObject',
				'url' => $term_image,
			);
		}

		$list_items = array();
		$posts = get_posts(
			array(
				'post_type' => 'post',
				'posts_per_page' => 12,
				'tax_query' => array(
					array(
						'taxonomy' => 'city',
						'field' => 'term_id',
						'terms' => $term->term_id,
					),
				),
			)
		);

		$position = 0;
		foreach ( $posts as $city_post ) {
			$position++;
			$list_items[] = array(
				'@type' => 'ListItem',
				'position' => $position,
				'url' => get_permalink( $city_post ),
				'name' => kayan_seo_text( get_the_title( $city_post ) ),
			);
		}

		if ( ! empty( $list_items ) ) {
			$nodes[] = array(
				'@type' => 'ItemList',
				'@id' => $term_link . '#itemlist',
				'name' => 'خدمات في ' . kayan_seo_text( $term->name ),
				'itemListElement' => $list_items,
			);
		}

		$nodes = kayan_seo_merge_breadcrumb_into_graph( $nodes );
		$graph = array_merge( $graph, $nodes );
	}
}

if ( ! function_exists( 'kayan_seo_build_schema_graph' ) ) {
	function kayan_seo_build_schema_graph() {
		$graph = array();
		kayan_seo_collect_home_schema_nodes( $graph );
		kayan_seo_collect_single_schema_nodes( $graph );
		kayan_seo_collect_page_schema_nodes( $graph );
		kayan_seo_collect_cities_index_schema_nodes( $graph );
		kayan_seo_collect_city_archive_schema_nodes( $graph );

		if ( empty( $graph ) && ! is_singular() ) {
			$breadcrumb = kayan_seo_get_breadcrumb();
			if ( ! empty( $breadcrumb ) ) {
				$graph[] = $breadcrumb;
			}
		}

		return $graph;
	}
}

if ( ! function_exists( 'kayan_seo_get_breadcrumb' ) ) {
	/**
	 * BreadcrumbList schema node only — no HTML output.
	 *
	 * @return array|null
	 */
	function kayan_seo_get_breadcrumb() {
		if ( ! kayan_seo_uses_modern_schema() || is_front_page() || is_home() ) {
			return null;
		}

		$items = array();
		$position = 1;
		$items[] = array(
			'@type' => 'ListItem',
			'position' => $position,
			'name' => kayan_seo_get_site_name(),
			'item' => home_url( '/' ),
		);

		if ( is_singular() ) {
			$categories = get_the_terms( get_queried_object_id(), 'category' );
			if ( is_array( $categories ) ) {
				foreach ( array_slice( $categories, 0, 1 ) as $category ) {
					$position++;
					$items[] = array(
						'@type' => 'ListItem',
						'position' => $position,
						'name' => $category->name,
						'item' => get_term_link( $category ),
					);
				}
			}
			$cities = get_the_terms( get_queried_object_id(), 'city' );
			if ( is_array( $cities ) ) {
				foreach ( array_slice( $cities, 0, 1 ) as $city ) {
					$position++;
					$items[] = array(
						'@type' => 'ListItem',
						'position' => $position,
						'name' => $city->name,
						'item' => get_term_link( $city ),
					);
				}
			}
			$position++;
			$items[] = array(
				'@type' => 'ListItem',
				'position' => $position,
				'name' => kayan_seo_get_title(),
				'item' => get_permalink(),
			);
		} elseif ( is_category() || is_tax() || is_tag() ) {
			$obj = get_queried_object();
			$position++;
			$items[] = array(
				'@type' => 'ListItem',
				'position' => $position,
				'name' => $obj->name,
				'item' => get_term_link( $obj ),
			);
		}

		if ( count( $items ) < 2 ) {
			return null;
		}

		return array(
			'@type' => 'BreadcrumbList',
			'@id' => kayan_seo_get_current_url() . '#breadcrumb',
			'itemListElement' => $items,
		);
	}
}

if ( ! function_exists( 'kayan_seo_merge_breadcrumb_into_graph' ) ) {
	/**
	 * Append BreadcrumbList node to @graph array (no script output).
	 *
	 * @param array $graph
	 * @return array
	 */
	function kayan_seo_merge_breadcrumb_into_graph( array $graph ) {
		foreach ( $graph as $node ) {
			if ( is_array( $node ) && isset( $node['@type'] ) && 'BreadcrumbList' === $node['@type'] ) {
				return $graph;
			}
		}

		$breadcrumb = kayan_seo_get_breadcrumb();
		if ( ! empty( $breadcrumb ) ) {
			$graph[] = $breadcrumb;
		}
		return $graph;
	}
}

if ( ! function_exists( 'kayan_seo_output_breadcrumb_schema' ) ) {
	function kayan_seo_output_breadcrumb_schema() {
		if ( is_singular() || is_page() ) {
			return;
		}

		$node = kayan_seo_get_breadcrumb();
		if ( empty( $node ) ) {
			return;
		}

		kayan_seo_print_json_ld( array( $node ) );
	}
}

if ( ! function_exists( 'kayan_seo_output_schema_graph' ) ) {
	function kayan_seo_output_schema_graph() {
		static $rendered = false;
		if ( $rendered ) {
			return;
		}
		if ( ! kayan_seo_uses_modern_schema() ) {
			return;
		}
		if ( get_option( 'validate__schema' ) ) {
			return;
		}

		$rendered = true;
		$graph = kayan_seo_build_schema_graph();
		if ( ! empty( $graph ) ) {
			kayan_seo_print_json_ld( $graph );
		}
	}
}
