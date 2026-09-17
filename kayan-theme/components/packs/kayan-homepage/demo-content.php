<?
/**
 * تثبيت محتوى العرض للصفحة الرئيسية v3 (مدن، خدمات، FAQ، مشاريع، مدونة، خطط أسعار).
 */

if ( ! function_exists( 'kayan_homepage_demo_content_version' ) ) {
	function kayan_homepage_demo_content_version() {
		return '1.0.7';
	}
}

if ( ! function_exists( 'kayan_homepage_demo_content_version_installed' ) ) {
	function kayan_homepage_demo_content_version_installed() {
		return get_option( 'kayan_homepage_demo_v3_version', '' );
	}
}

if ( ! function_exists( 'kayan_homepage_maybe_install_demo_content' ) ) {
	function kayan_homepage_maybe_install_demo_content( $force = false ) {
		if ( ! $force && kayan_homepage_demo_content_version_installed() === kayan_homepage_demo_content_version() ) {
			return false;
		}
		if ( ! function_exists( 'wp_insert_post' ) ) {
			return false;
		}

		$company = function_exists( 'kayan_homepage_get_company_name' ) ? kayan_homepage_get_company_name() : get_bloginfo( 'name' );

		$cities = array(
			'دبي'         => 'خلال 60 دقيقة',
			'أبوظبي'      => 'خلال 90 دقيقة',
			'الشارقة'     => 'خلال 75 دقيقة',
			'عجمان'       => 'خلال 90 دقيقة',
			'رأس الخيمة'  => 'خلال ساعتين',
			'الفجيرة'     => 'خلال ساعتين',
			'أم القيوين'  => 'خلال ساعتين',
		);
		$city_ids = array();
		foreach ( $cities as $name => $time ) {
			$term = term_exists( $name, 'city' );
			if ( ! $term ) {
				$term = wp_insert_term( $name, 'city' );
			}
			if ( ! is_wp_error( $term ) ) {
				$tid = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
				$city_ids[ $name ] = $tid;
				update_term_meta( $tid, 'response_time', $time );
			}
		}

		$cat_map = array();
		$categories = array(
			'كشف تسربات' => 'leak',
			'عزل'        => 'insul',
			'صيانة'      => 'maint',
			'تنظيف'      => 'clean',
		);
		foreach ( $categories as $name => $slug ) {
			$term = term_exists( $name, 'category' );
			if ( ! $term ) {
				$term = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
			}
			if ( ! is_wp_error( $term ) ) {
				$cat_map[ $name ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
			}
		}

		$services = array(
			array( 'كشف تسربات المياه', 'كشف تسربات', 'دبي', 'كشف دقيق للتسربات بدون تكسير بأحدث الأجهزة.' ),
			array( 'عزل الأسطح', 'عزل', 'أبوظبي', 'عزل حراري ومائي للأسطح بضمان مكتوب.' ),
			array( 'صيانة التكييف', 'صيانة', 'الشارقة', 'صيانة شاملة للتكييف والتبريد.' ),
			array( 'تنظيف وتعقيم', 'تنظيف', 'دبي', 'تنظيف وتعقيم احترافي للمنازل والمكاتب.' ),
			array( 'مكافحة الحشرات', 'تنظيف', 'عجمان', 'مكافحة آمنة مع ضمان عدم العودة.' ),
			array( 'سباكة وصيانة عامة', 'صيانة', 'أبوظبي', 'سباكة وكهرباء وصيانة منزلية شاملة.' ),
		);
		foreach ( $services as $i => $svc ) {
			$exists = get_page_by_title( $svc[0], OBJECT, 'post' );
			if ( $exists ) {
				continue;
			}
			$post_id = wp_insert_post(
				array(
					'post_title'   => $svc[0],
					'post_content' => '<p>' . $svc[3] . '</p>',
					'post_excerpt' => $svc[3],
					'post_status'  => 'publish',
					'post_type'    => 'post',
					'menu_order'   => $i + 1,
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				if ( isset( $cat_map[ $svc[1] ] ) ) {
					wp_set_post_terms( $post_id, array( $cat_map[ $svc[1] ] ), 'category' );
				}
				if ( isset( $city_ids[ $svc[2] ] ) ) {
					wp_set_post_terms( $post_id, array( $city_ids[ $svc[2] ] ), 'city' );
				}
			}
		}

		$faqs = array(
			array( 'ما تكلفة الخدمة؟', '<p>نقدم معاينة مجانية وعرض سعر شفاف قبل البدء. تختلف التكلفة حسب نوع الخدمة والموقع.</p>' ),
			array( 'هل تقدمون خدمة طوارئ؟', '<p>نعم، فريق ' . esc_html( $company ) . ' متاح على مدار الساعة للحالات العاجلة.</p>' ),
			array( 'هل تعملون في جميع المناطق؟', '<p>نغطي المدن والمناطق المدرجة في موقعنا. تواصل معنا لتأكيد التوفر في منطقتك.</p>' ),
			array( 'ما الضمان على الأعمال؟', '<p>نقدم ضماناً مكتوباً على الأعمال المعتمدة وفق نوع الخدمة.</p>' ),
			array( 'كيف أطلب الخدمة؟', '<p>عبر واتساب أو الاتصال المباشر من الموقع، أو نموذج التواصل.</p>' ),
			array( 'هل الفنيون معتمدون؟', '<p>جميع فريقنا مدرب ومعتمد وفق معايير السلامة والجودة.</p>' ),
		);
		foreach ( $faqs as $i => $faq ) {
			if ( get_page_by_title( $faq[0], OBJECT, 'faq' ) ) {
				continue;
			}
			wp_insert_post(
				array(
					'post_title'   => $faq[0],
					'post_content' => $faq[1],
					'post_status'  => 'publish',
					'post_type'    => 'faq',
					'menu_order'   => $i + 1,
				)
			);
		}

		$works = array(
			array( 'كشف تسربات — فيلا', 'كشف تسربات', 'دبي', 'تحديد التسرب وإصلاحه بدون تكسير.' ),
			array( 'عزل سطح — مبنى', 'عزل', 'الشارقة', 'عزل حراري ومائي بمواد معتمدة.' ),
			array( 'صيانة تكييف', 'صيانة', 'أبوظبي', 'صيانة دورية وتحسين كفاءة التبريد.' ),
			array( 'تنظيف وتعقيم', 'تنظيف', 'دبي', 'تنظيف عميق وتعقيم آمن.' ),
		);
		foreach ( $works as $i => $work ) {
			if ( get_page_by_title( $work[0], OBJECT, 'works' ) ) {
				continue;
			}
			$post_id = wp_insert_post(
				array(
					'post_title'   => $work[0],
					'post_content' => '<p>' . $work[3] . '</p>',
					'post_excerpt' => $work[3],
					'post_status'  => 'publish',
					'post_type'    => 'works',
					'menu_order'   => $i + 1,
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				if ( isset( $cat_map[ $work[1] ] ) ) {
					wp_set_post_terms( $post_id, array( $cat_map[ $work[1] ] ), 'category' );
				}
				if ( isset( $city_ids[ $work[2] ] ) ) {
					wp_set_post_terms( $post_id, array( $city_ids[ $work[2] ] ), 'city' );
				}
			}
		}

		$blogs = array(
			array( 'نصائح للعناية بالمنزل', 'مقالات مفيدة حول صيانة المنزل والوقاية من الأعطال.' ),
			array( 'متى تحتاج لفحص التسربات؟', 'علامات مبكرة تدل على وجود تسربات يجب عدم تجاهلها.' ),
			array( 'أهمية الصيانة الدورية', 'كيف توفر الصيانة الوقائية تكاليف الإصلاح لاحقاً.' ),
		);
		foreach ( $blogs as $post ) {
			if ( get_page_by_title( $post[0], OBJECT, 'post' ) ) {
				continue;
			}
			wp_insert_post(
				array(
					'post_title'   => $post[0],
					'post_content' => '<p>' . $post[1] . '</p>',
					'post_excerpt' => $post[1],
					'post_status'  => 'publish',
					'post_type'    => 'post',
				)
			);
		}

		$prices = array(
			array( 'تكلفة كشف التسربات', '250 – 800 درهم' ),
			array( 'تكلفة عزل الأسطح', '15 – 35 درهم / م²' ),
			array( 'تكلفة عزل الخزانات', '400 – 1200 درهم' ),
			array( 'تكلفة تنظيف الخزانات', '150 – 500 درهم' ),
			array( 'تكلفة مكافحة الحشرات', '120 – 450 درهم' ),
			array( 'تكلفة الصيانة المنزلية', '100 – 600 درهم' ),
		);
		foreach ( $prices as $i => $plan ) {
			if ( get_page_by_title( $plan[0], OBJECT, 'price' ) ) {
				continue;
			}
			$post_id = wp_insert_post(
				array(
					'post_title'   => $plan[0],
					'post_content' => '<p>تقدير تقريبي — تواصل معنا لعرض سعر دقيق لحالتك.</p>',
					'post_excerpt' => 'تقدير تقريبي لـ ' . $plan[0],
					'post_status'  => 'publish',
					'post_type'    => 'price',
					'menu_order'   => $i + 1,
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, 'price_text', $plan[1] );
			}
		}

		kayan_homepage_seed_homepage_options( $company );

		$menu_id = kayan_homepage_ensure_main_menu();
		if ( $menu_id ) {
			$locations           = get_theme_mod( 'nav_menu_locations', array() );
			$locations['main-menu'] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		update_option( 'kayan_homepage_demo_v3_version', kayan_homepage_demo_content_version() );
		return true;
	}
}

if ( ! function_exists( 'kayan_homepage_seed_homepage_options' ) ) {
	function kayan_homepage_seed_homepage_options( $company ) {
		if ( ! is_array( yc_get_option( 'kayan_hp_trust_items' ) ) || empty( yc_get_option( 'kayan_hp_trust_items' ) ) ) {
			yc_update_option(
				'kayan_hp_trust_items',
				array(
					array( 'icon' => 'fas fa-shield-halved', 'text' => 'ضمان مكتوب' ),
					array( 'icon' => 'fas fa-clock', 'text' => 'خدمة طوارئ' ),
					array( 'icon' => 'fas fa-map-location-dot', 'text' => '{{all_regions}}' ),
					array( 'icon' => 'fas fa-user-shield', 'text' => 'فنيون معتمدون' ),
				)
			);
		}
		if ( ! is_array( yc_get_option( 'kayan_hp_stats_items' ) ) || empty( yc_get_option( 'kayan_hp_stats_items' ) ) ) {
			yc_update_option(
				'kayan_hp_stats_items',
				array(
					array( 'icon' => 'fas fa-briefcase', 'count' => '500', 'suffix' => '+', 'label' => 'خدمة', 'sublabel' => 'خدمة منجزة' ),
					array( 'icon' => 'fas fa-users', 'count' => '200', 'suffix' => '+', 'label' => 'عميل', 'sublabel' => 'عميل راضٍ' ),
					array( 'icon' => 'fas fa-star', 'count' => '4.9', 'dec' => '1', 'label' => 'تقييم', 'sublabel' => 'تقييم العملاء' ),
					array( 'icon' => 'fas fa-award', 'count' => '10', 'suffix' => '+', 'label' => 'سنوات', 'sublabel' => 'سنوات خبرة' ),
					array( 'icon' => 'fas fa-map-location-dot', 'label' => '{{all_regions}}', 'sublabel' => 'تغطية شاملة' ),
					array( 'icon' => 'fas fa-headset', 'label' => '24/7', 'sublabel' => 'دعم مستمر' ),
				)
			);
		}
		if ( empty( yc_get_option( 'kayan_hp_why_steps' ) ) ) {
			yc_update_option(
				'kayan_hp_why_steps',
				array(
					array( 'num' => '1', 'title' => 'تواصل ومعاينة', 'desc' => 'نصل إليك ونعاين الموقع.' ),
					array( 'num' => '2', 'title' => 'عرض سعر شفاف', 'desc' => 'تكلفة واضحة بدون مفاجآت.' ),
					array( 'num' => '3', 'title' => 'تنفيذ احترافي', 'desc' => 'فريق معتمد بأحدث الأدوات.' ),
					array( 'num' => '4', 'title' => 'ضمان ومتابعة', 'desc' => 'ضمان مكتوب ودعم بعد الخدمة.' ),
				)
			);
		}
		if ( empty( yc_get_option( 'kayan_hp_why_features' ) ) ) {
			yc_update_option(
				'kayan_hp_why_features',
				array(
					array( 'icon' => 'fas fa-microchip', 'title' => 'تقنية حديثة', 'desc' => 'أجهزة ومعايير معتمدة.' ),
					array( 'icon' => 'fas fa-user-shield', 'title' => 'فريق معتمد', 'desc' => 'فنيون مدربون ومرخصون.' ),
					array( 'icon' => 'fas fa-bolt', 'title' => 'استجابة سريعة', 'desc' => 'وصول سريع عند الطلب.' ),
					array( 'icon' => 'fas fa-file-contract', 'title' => 'ضمان مكتوب', 'desc' => 'ضمان موثق على الأعمال.' ),
					array( 'icon' => 'fas fa-tags', 'title' => 'أسعار عادلة', 'desc' => 'شفافية في التسعير.' ),
					array( 'icon' => 'fas fa-map-location-dot', 'title' => 'تغطية واسعة', 'desc' => 'خدمة في مدن متعددة.' ),
				)
			);
		}
		if ( empty( yc_get_option( 'kayan_hp_team_members' ) ) ) {
			yc_update_option(
				'kayan_hp_team_members',
				array(
					array( 'initials' => 'م.ع', 'name' => 'مدير العمليات', 'role' => 'قيادة الفريق', 'spec' => 'ضمان جودة التنفيذ.', 'badge1' => 'خبرة طويلة', 'badge2' => 'معتمد' ),
					array( 'initials' => 'ف.1', 'name' => 'فني معتمد', 'role' => 'كشف وصيانة', 'spec' => 'تشخيص دقيق للأعطال.', 'badge1' => 'شهادة معتمدة', 'badge2' => '' ),
					array( 'initials' => 'ف.2', 'name' => 'فني معتمد', 'role' => 'عزل وسباكة', 'spec' => 'تنفيذ بمعايير السلامة.', 'badge1' => 'خبير', 'badge2' => '' ),
				)
			);
		}
		if ( empty( yc_get_option( 'kayan_hp_compare_rows' ) ) ) {
			yc_update_option(
				'kayan_hp_compare_rows',
				array(
					array( 'label' => 'ضمان مكتوب', 'us' => '1', 'them' => '' ),
					array( 'label' => 'فنيون معتمدون', 'us' => '1', 'them' => '' ),
					array( 'label' => 'دعم 24/7', 'us' => '1', 'them' => '' ),
					array( 'label' => 'أسعار شفافة', 'us' => '1', 'them' => '' ),
					array( 'label' => 'استجابة سريعة', 'us' => '1', 'them' => '' ),
				)
			);
		}
		if ( empty( yc_get_option( 'kayan_seo_home_reviews' ) ) ) {
			yc_update_option(
				'kayan_seo_home_reviews',
				array(
					array( 'author' => 'عميل موثّق', 'rating' => 5, 'text' => 'خدمة ممتازة والفريق محترف.', 'city' => 'دبي' ),
					array( 'author' => 'عميل موثّق', 'rating' => 5, 'text' => 'تعامل راقٍ وأسعار واضحة.', 'city' => 'أبوظبي' ),
					array( 'author' => 'عميل موثّق', 'rating' => 5, 'text' => 'وصلوا في الوقت ونفذوا العمل بإتقان.', 'city' => 'الشارقة' ),
				)
			);
		}
	}
}

if ( ! function_exists( 'kayan_homepage_ensure_main_menu' ) ) {
	function kayan_homepage_ensure_main_menu() {
		$menu_name = 'القائمة الرئيسية';
		$menu      = wp_get_nav_menu_object( $menu_name );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $menu_name );
		} else {
			$menu_id = (int) $menu->term_id;
		}
		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			return 0;
		}
		$home = home_url( '/' );
		$links = array(
			array( 'الخدمات', $home . '#services' ),
			array( 'المدن', $home . '#areas' ),
			array( 'المشاريع', $home . '#projects' ),
			array( 'المدونة', $home . '#blog' ),
			array( 'من نحن', $home . '#why' ),
			array( 'الأسئلة الشائعة', $home . '#faq' ),
		);
		$existing = wp_get_nav_menu_items( $menu_id );
		$titles   = array();
		if ( $existing ) {
			foreach ( $existing as $item ) {
				$titles[] = $item->title;
			}
		}
		foreach ( $links as $link ) {
			if ( in_array( $link[0], $titles, true ) ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'  => $link[0],
					'menu-item-url'    => $link[1],
					'menu-item-status' => 'publish',
				)
			);
		}
		return $menu_id;
	}
}

add_action( 'after_switch_theme', 'kayan_homepage_maybe_install_demo_content' );
add_action( 'init', 'kayan_homepage_auto_install_demo', 99 );
add_action( 'init', 'kayan_homepage_demo_admin_install', 20 );

if ( ! function_exists( 'kayan_homepage_auto_install_demo' ) ) {
	function kayan_homepage_auto_install_demo() {
		if ( kayan_homepage_demo_content_version_installed() !== kayan_homepage_demo_content_version() ) {
			kayan_homepage_maybe_install_demo_content();
		}
	}
}

if ( ! function_exists( 'kayan_homepage_demo_admin_install' ) ) {
	function kayan_homepage_demo_admin_install() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( empty( $_GET['kayan_install_home_demo'] ) || empty( $_GET['_wpnonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'kayan_install_home_demo' ) ) {
			return;
		}
		kayan_homepage_maybe_install_demo_content( true );
		wp_safe_redirect( remove_query_arg( array( 'kayan_install_home_demo', '_wpnonce' ) ) );
		exit;
	}
}
