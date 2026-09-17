<?
/**
 * بناء محتوى أقسام الصفحة الرئيسية من بيانات الموقع وإعدادات القالب.
 */

if ( ! function_exists( 'kayan_hp_get_group_items' ) ) {
	function kayan_hp_get_group_items( $key ) {
		$items = yc_get_option( $key );
		return is_array( $items ) ? $items : array();
	}
}

if ( ! function_exists( 'kayan_homepage_build_hero_proof_html' ) ) {
	function kayan_homepage_build_hero_proof_html() {
		$items = kayan_hp_get_group_items( 'kayan_hp_hero_chips' );
		$html  = '';
		foreach ( $items as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$text = trim( (string) ( $row['text'] ?? '' ) );
			if ( $text === '' ) {
				continue;
			}
			$icon = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-circle-check';
			$html .= '<span class="chip"><i class="' . esc_attr( $icon ) . '"></i> ' . esc_html( kayan_homepage_expand_inline_tokens( $text ) ) . '</span>';
		}
		if ( $html === '' ) {
			$company = kayan_homepage_get_company_name();
			$defaults = array(
				array( 'fas fa-shield-halved', 'ضمان مكتوب على أعمال ' . $company ),
				array( 'fas fa-headset', 'دعم وطوارئ على مدار الساعة' ),
				array( 'fas fa-map-location-dot', kayan_homepage_expand_inline_tokens( '{{all_regions}}' ) ),
			);
			foreach ( $defaults as $d ) {
				$html .= '<span class="chip"><i class="' . esc_attr( $d[0] ) . '"></i> ' . esc_html( $d[1] ) . '</span>';
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_hero_dashboard_html' ) ) {
	function kayan_homepage_build_hero_dashboard_html() {
		$posts = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		$icons = array( 'fas fa-droplet', 'fas fa-layer-group', 'fas fa-snowflake', 'fas fa-spray-can-sparkles', 'fas fa-wrench', 'fas fa-bug-slash' );
		$mini  = '';
		$i     = 0;
		foreach ( $posts as $post ) {
			$icon = $icons[ $i % count( $icons ) ];
			$mini .= '<div class="mini"><i class="' . esc_attr( $icon ) . '"></i><span>' . esc_html( get_the_title( $post ) ) . '</span></div>';
			$i++;
		}
		if ( $mini === '' ) {
			return '';
		}

		$stats = kayan_hp_get_group_items( 'kayan_hp_stats_items' );
		$dstat = '';
		$shown = 0;
		foreach ( $stats as $row ) {
			if ( $shown >= 3 || ! is_array( $row ) ) {
				continue;
			}
			$label = trim( (string) ( $row['label'] ?? '' ) );
			$count = trim( (string) ( $row['count'] ?? '' ) );
			if ( $label === '' || $count === '' ) {
				continue;
			}
			$suffix = isset( $row['suffix'] ) ? $row['suffix'] : '';
			$dec    = isset( $row['dec'] ) ? (int) $row['dec'] : 0;
			$dstat .= '<div class="dstat"><b data-count="' . esc_attr( $count ) . '"';
			if ( $suffix !== '' ) {
				$dstat .= ' data-suffix="' . esc_attr( $suffix ) . '"';
			}
			if ( $dec > 0 ) {
				$dstat .= ' data-dec="' . (int) $dec . '"';
			}
			$dstat .= '>0</b><small>' . esc_html( $label ) . '</small></div>';
			$shown++;
		}
		if ( $dstat === '' ) {
			$dstat = '<div class="dstat"><b data-count="100" data-suffix="+">0</b><small>عميل</small></div>';
			$dstat .= '<div class="dstat"><b data-count="200" data-suffix="+">0</b><small>خدمة</small></div>';
			$dstat .= '<div class="dstat"><b data-count="5" data-dec="1">0</b><small>تقييم</small></div>';
		}

		$warranty = kayan_hp_get_section_text(
			'hero',
			'warranty_title',
			'ضمان مكتوب على أعمالنا',
			'Written warranty on our work'
		);
		$warranty_sub = kayan_hp_get_section_text(
			'hero',
			'warranty_sub',
			'على أعمال العزل والصيانة المعتمدة',
			'On approved insulation and maintenance work'
		);

		ob_start();
		?>
    <div class="dash rv-l">
      <div class="dash-top">
        <span class="ttl"><i class="fas fa-chart-line" style="color:var(--aqua)"></i> {{dashboard_title}}</span>
        <span class="live"><b></b> <?php echo esc_html( kayan_hp_get_section_text( 'hero', 'live_label', 'مباشر', 'Live' ) ); ?></span>
      </div>
      <div class="dash-mini"><?php echo $mini; // phpcs:ignore ?></div>
      <div class="dash-stats"><?php echo $dstat; // phpcs:ignore ?></div>
      <div class="warranty">
        <i class="fas fa-shield-halved"></i>
        <div><b><?php echo esc_html( kayan_homepage_expand_inline_tokens( $warranty ) ); ?></b><small><?php echo esc_html( kayan_homepage_expand_inline_tokens( $warranty_sub ) ); ?></small></div>
      </div>
      <div class="dash-trust"><?php echo kayan_homepage_build_trust_badges_html( 'dt' ); // phpcs:ignore ?></div>
    </div>
		<?php
		$html = ob_get_clean();
		return kayan_homepage_expand_inline_tokens( str_replace( '{{dashboard_title}}', esc_html( kayan_homepage_get_option_text( 'kayan_homepage_dashboard_title', kayan_homepage_get_company_name() ) ), $html ) );
	}
}

if ( ! function_exists( 'kayan_homepage_build_trust_badges_html' ) ) {
	function kayan_homepage_build_trust_badges_html( $class = 'dt' ) {
		$items = kayan_hp_get_group_items( 'kayan_hp_trust_badges' );
		$html  = '';
		foreach ( $items as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$text = trim( (string) ( $row['text'] ?? '' ) );
			if ( $text === '' ) {
				continue;
			}
			$icon = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-circle-check';
			$html .= '<span class="' . esc_attr( $class ) . '"><i class="' . esc_attr( $icon ) . '"></i> ' . esc_html( kayan_homepage_expand_inline_tokens( $text ) ) . '</span>';
		}
		if ( $html === '' ) {
			$html .= '<span class="' . esc_attr( $class ) . '"><i class="fas fa-circle-check"></i> ' . esc_html( kayan_homepage_get_company_name() ) . '</span>';
			$html .= '<span class="' . esc_attr( $class ) . '"><i class="fas fa-user-shield"></i> ' . esc_html( kayan_hp_get_section_text( 'hero', 'badge_crew', 'فنيون معتمدون', 'Certified technicians' ) ) . '</span>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_trustbar_html' ) ) {
	function kayan_homepage_build_trustbar_html() {
		$items = kayan_hp_get_group_items( 'kayan_hp_trust_items' );
		$html  = '';
		foreach ( $items as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$text = trim( (string) ( $row['text'] ?? '' ) );
			if ( $text === '' ) {
				continue;
			}
			$icon = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-circle-check';
			$html .= '<div class="tb"><i class="' . esc_attr( $icon ) . '"></i> ' . esc_html( kayan_homepage_expand_inline_tokens( $text ) ) . '</div>';
		}
		if ( $html === '' ) {
			$fallback = array(
				array( 'fas fa-shield-halved', 'ضمان مكتوب' ),
				array( 'fas fa-clock', 'خدمة طوارئ' ),
				array( 'fas fa-map-location-dot', '{{all_regions}}' ),
				array( 'fas fa-user-shield', 'فنيون معتمدون' ),
			);
			foreach ( $fallback as $f ) {
				$html .= '<div class="tb"><i class="' . esc_attr( $f[0] ) . '"></i> ' . esc_html( kayan_homepage_expand_inline_tokens( $f[1] ) ) . '</div>';
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_atb_html' ) ) {
	function kayan_homepage_build_atb_html() {
		if ( ! empty( yc_get_option( 'kayan_hp_atb_disable' ) ) ) {
			return '';
		}
		$items = kayan_hp_get_group_items( 'kayan_hp_stats_items' );
		$html  = '';
		foreach ( $items as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = trim( (string) ( $row['label'] ?? '' ) );
			$count = trim( (string) ( $row['count'] ?? '' ) );
			$icon  = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-chart-line';
			if ( $label === '' ) {
				continue;
			}
			$html .= '<div class="atb-item"><i class="' . esc_attr( $icon ) . '"></i><div>';
			if ( $count !== '' ) {
				$suffix = isset( $row['suffix'] ) ? $row['suffix'] : '';
				$dec    = isset( $row['dec'] ) ? (int) $row['dec'] : 0;
				$html  .= '<b data-count="' . esc_attr( $count ) . '"';
				if ( $suffix !== '' ) {
					$html .= ' data-suffix="' . esc_attr( $suffix ) . '"';
				}
				if ( $dec > 0 ) {
					$html .= ' data-dec="' . (int) $dec . '"';
				}
				$html .= '>0</b>';
			} else {
				$html .= '<b>' . esc_html( kayan_homepage_expand_inline_tokens( $label ) ) . '</b>';
				$label = trim( (string) ( $row['sublabel'] ?? '' ) );
			}
			$sublabel = trim( (string) ( $row['sublabel'] ?? $label ) );
			if ( $sublabel !== '' && $count !== '' ) {
				$html .= '<small>' . esc_html( kayan_homepage_expand_inline_tokens( $sublabel ) ) . '</small>';
			}
			$html .= '</div></div>';
		}
		if ( $html === '' ) {
			return '';
		}
		return '<div class="wrap atb-wrap"><div class="atb rv">' . $html . '</div></div>';
	}
}

if ( ! function_exists( 'kayan_homepage_build_finder_html' ) ) {
	function kayan_homepage_build_finder_html() {
		if ( ! empty( yc_get_option( 'kayan_hp_finder_disable' ) ) ) {
			return '';
		}
		$services = get_posts(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 12,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		$cities = get_terms(
			array(
				'taxonomy'   => 'city',
				'hide_empty' => false,
			)
		);
		if ( empty( $services ) || is_wp_error( $cities ) || empty( $cities ) ) {
			return '';
		}

		$svc_opts = '';
		foreach ( $services as $post ) {
			$svc_opts .= '<option value="' . esc_attr( get_the_title( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</option>';
		}
		$city_opts = '';
		foreach ( $cities as $term ) {
			$time = get_term_meta( $term->term_id, 'response_time', true );
			if ( $time === '' ) {
				$time = kayan_hp_get_section_text( 'finder', 'default_time', 'خلال ساعتين', 'Within 2 hours' );
			}
			$city_opts .= '<option value="' . esc_attr( $term->name ) . '" data-time="' . esc_attr( $time ) . '">' . esc_html( $term->name ) . '</option>';
		}

		$head = kayan_homepage_build_section_head_html(
			'finder',
			'ابدأ الآن',
			'ما الخدمة التي <span>تحتاجها؟</span>',
			'اختر الخدمة والمدينة واحصل على عرض سعر فوري.',
			'Get started',
			'Which <span>service</span> do you need?',
			'Pick service and city for a quick quote.'
		);

		ob_start();
		?>
<!-- kayan-section:finder -->
<section class="sec finder-sec" id="finder">
  <div class="wrap">
    <?php echo $head; // phpcs:ignore ?>
    <div class="finder rv">
      <div class="finder-step">
        <label><span class="snum">1</span> <?php echo esc_html( kayan_hp_get_section_text( 'finder', 'step1', 'اختر الخدمة', 'Choose service' ) ); ?></label>
        <div class="sel"><i class="fas fa-screwdriver-wrench"></i>
          <select id="fnSvc" aria-label="<?php echo esc_attr( kayan_hp_get_section_text( 'finder', 'step1', 'اختر الخدمة', 'Choose service' ) ); ?>"><?php echo $svc_opts; // phpcs:ignore ?></select>
        </div>
      </div>
      <div class="finder-step">
        <label><span class="snum">2</span> <?php echo esc_html( kayan_hp_get_section_text( 'finder', 'step2', 'اختر المدينة', 'Choose city' ) ); ?></label>
        <div class="sel"><i class="fas fa-location-dot"></i>
          <select id="fnCity" aria-label="<?php echo esc_attr( kayan_hp_get_section_text( 'finder', 'step2', 'اختر المدينة', 'Choose city' ) ); ?>"><?php echo $city_opts; // phpcs:ignore ?></select>
        </div>
      </div>
      <div class="finder-step finder-go">
        <button class="btn btn-quote" id="fnBtn"><i class="fas fa-bolt"></i> <?php echo esc_html( kayan_hp_get_section_text( 'finder', 'btn', 'احصل على عرض سعر فوري', 'Get instant quote' ) ); ?></button>
      </div>
    </div>
    <div class="finder-result" id="fnResult" hidden>
      <div class="fr-lead"><b id="frTitle"></b><small id="frSub"></small></div>
      <div class="fr-stat"><i class="fas fa-clock"></i><b id="frTime"></b><small><?php echo esc_html( kayan_hp_get_section_text( 'finder', 'time_lbl', 'وقت الاستجابة', 'Response time' ) ); ?></small></div>
      <div class="fr-stat"><i class="fas fa-circle-check"></i><b class="fr-ok"><?php echo esc_html( kayan_hp_get_section_text( 'finder', 'avail', 'متوفرة الآن', 'Available now' ) ); ?></b><small><?php echo esc_html( kayan_hp_get_section_text( 'finder', 'status', 'حالة الخدمة', 'Status' ) ); ?></small></div>
    </div>
    <div style="text-align:center;margin-top:18px" class="rv">
      <a href="<?php echo esc_url( kayan_hp_resolve_whatsapp_url() ); ?>" class="btn btn-wa"><i class="fab fa-whatsapp"></i> <?php echo esc_html( function_exists( 'kayan_i18n_t' ) ? kayan_i18n_t( 'btn_whatsapp_full' ) : 'واتساب' ); ?></a>
    </div>
  </div>
</section>
<!-- /kayan-section:finder -->
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'kayan_homepage_build_why_body_html' ) ) {
	function kayan_homepage_build_why_body_html() {
		$steps = kayan_hp_get_group_items( 'kayan_hp_why_steps' );
		$feats = kayan_hp_get_group_items( 'kayan_hp_why_features' );
		$tl    = '';
		foreach ( $steps as $i => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = trim( (string) ( $row['title'] ?? '' ) );
			$desc  = trim( (string) ( $row['desc'] ?? '' ) );
			if ( $title === '' ) {
				continue;
			}
			$num = isset( $row['num'] ) ? $row['num'] : ( $i + 1 );
			$tl .= '<div class="tl"><span class="dot">' . esc_html( $num ) . '</span><div><b>' . esc_html( $title ) . '</b><small>' . esc_html( $desc ) . '</small></div></div>';
		}
		$cards = '';
		foreach ( $feats as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = trim( (string) ( $row['title'] ?? '' ) );
			$desc  = trim( (string) ( $row['desc'] ?? '' ) );
			if ( $title === '' ) {
				continue;
			}
			$icon = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-circle-check';
			$cards .= '<div class="feat rv"><div class="fic"><i class="' . esc_attr( $icon ) . '"></i></div><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $desc ) . '</p></div>';
		}
		if ( $tl === '' && $cards === '' ) {
			return '';
		}
		$timeline_title = kayan_hp_get_section_text( 'why', 'timeline_title', 'رحلتك معنا بسيطة وواضحة', 'A simple clear journey' );
		$timeline_sub   = kayan_hp_get_section_text( 'why', 'timeline_sub', 'من أول اتصال إلى تسليم العمل بضمان مكتوب.', 'From first call to completion with warranty.' );
		$why_sub        = kayan_hp_get_section_text( 'why', 'subtitle', 'نجمع بين الخبرة والضمان لراحة بالك.', 'Experience and warranty you can trust.' );

		ob_start();
		?>
    <div class="shead rv">
      <span class="tag"><?php echo esc_html( kayan_hp_get_section_text( 'why', 'tag', 'لماذا نحن', 'Why us' ) ); ?></span>
      <h2>{{why_heading_html}}</h2>
      <p><?php echo esc_html( kayan_homepage_expand_inline_tokens( $why_sub ) ); ?></p>
    </div>
    <div class="why">
      <?php if ( $tl !== '' ) : ?>
      <div class="why-time rv">
        <div class="inner">
          <h2><?php echo esc_html( $timeline_title ); ?></h2>
          <p><?php echo esc_html( $timeline_sub ); ?></p>
          <div class="tline"><?php echo $tl; // phpcs:ignore ?></div>
        </div>
      </div>
      <?php endif; ?>
      <?php if ( $cards !== '' ) : ?>
      <div class="why-cards"><?php echo $cards; // phpcs:ignore ?></div>
      <?php endif; ?>
    </div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'kayan_homepage_build_team_grid_html' ) ) {
	function kayan_homepage_build_team_grid_html() {
		$members = kayan_hp_get_group_items( 'kayan_hp_team_members' );
		$html    = '';
		foreach ( $members as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$name = trim( (string) ( $row['name'] ?? '' ) );
			if ( $name === '' ) {
				continue;
			}
			$initials = trim( (string) ( $row['initials'] ?? '' ) );
			if ( $initials === '' ) {
				$parts = preg_split( '/\s+/u', $name, 2 );
				$initials = mb_substr( $parts[0], 0, 1, 'UTF-8' );
				if ( isset( $parts[1] ) ) {
					$initials .= '.' . mb_substr( $parts[1], 0, 1, 'UTF-8' );
				}
			}
			$role  = trim( (string) ( $row['role'] ?? '' ) );
			$spec  = trim( (string) ( $row['spec'] ?? '' ) );
			$badge = trim( (string) ( $row['badge1'] ?? '' ) );
			$badge2 = trim( (string) ( $row['badge2'] ?? '' ) );
			$html .= '<div class="tcard rv"><div class="tav">' . esc_html( $initials ) . '</div>';
			$html .= '<h3>' . esc_html( $name ) . '</h3>';
			if ( $role !== '' ) {
				$html .= '<div class="role">' . esc_html( $role ) . '</div>';
			}
			if ( $spec !== '' ) {
				$html .= '<p class="spec">' . esc_html( $spec ) . '</p>';
			}
			$html .= '<div class="tbadges">';
			if ( $badge !== '' ) {
				$html .= '<span class="tbadge"><i class="fas fa-award"></i> ' . esc_html( $badge ) . '</span>';
			}
			if ( $badge2 !== '' ) {
				$html .= '<span class="tbadge"><i class="fas fa-circle-check"></i> ' . esc_html( $badge2 ) . '</span>';
			}
			$html .= '</div></div>';
		}
		return $html;
	}
}

if ( ! function_exists( 'kayan_homepage_build_stats_section_html' ) ) {
	function kayan_homepage_build_stats_section_html() {
		$items = kayan_hp_get_group_items( 'kayan_hp_stats_items' );
		$grid  = '';
		foreach ( $items as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = trim( (string) ( $row['label'] ?? '' ) );
			$count = trim( (string) ( $row['count'] ?? '' ) );
			$icon  = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-chart-line';
			if ( $label === '' ) {
				continue;
			}
			$grid .= '<div class="stat rv"><i class="' . esc_attr( $icon ) . '"></i>';
			if ( $count !== '' ) {
				$suffix = isset( $row['suffix'] ) ? $row['suffix'] : '';
				$dec    = isset( $row['dec'] ) ? (int) $row['dec'] : 0;
				$grid  .= '<div class="num" data-count="' . esc_attr( $count ) . '"';
				if ( $suffix !== '' ) {
					$grid .= ' data-suffix="' . esc_attr( $suffix ) . '"';
				}
				if ( $dec > 0 ) {
					$grid .= ' data-dec="' . (int) $dec . '"';
				}
				$grid .= '>0</div>';
			} else {
				$grid .= '<div class="num">' . esc_html( kayan_homepage_expand_inline_tokens( $label ) ) . '</div>';
				$label = trim( (string) ( $row['sublabel'] ?? '' ) );
			}
			$sublabel = trim( (string) ( $row['sublabel'] ?? $label ) );
			$grid    .= '<div class="lbl">' . esc_html( kayan_homepage_expand_inline_tokens( $sublabel ) ) . '</div></div>';
		}
		if ( $grid === '' ) {
			return '';
		}
		$head = kayan_homepage_build_section_head_html(
			'stats',
			'أرقامنا',
			'أرقام تتحدث عن جودتنا',
			'ثقة عملائنا في ' . kayan_homepage_get_company_name() . '.',
			'Our numbers',
			'Numbers that matter',
			'Trusted by our clients.'
		);
		return $head . '<div class="stats-grid">' . $grid . '</div>';
	}
}

if ( ! function_exists( 'kayan_homepage_build_compare_body_html' ) ) {
	function kayan_homepage_build_compare_body_html() {
		$rows = kayan_hp_get_group_items( 'kayan_hp_compare_rows' );
		$body = '';
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = trim( (string) ( $row['label'] ?? '' ) );
			if ( $label === '' ) {
				continue;
			}
			$us   = ! empty( $row['us'] );
			$them = ! empty( $row['them'] );
			$body .= '<div class="cmp-row"><div class="cc lbl">' . esc_html( $label ) . '</div>';
			$body .= '<div class="cc val rk"><i class="fas fa-circle-' . ( $us ? 'check' : 'xmark' ) . '"></i></div>';
			$body .= '<div class="cc val ot"><i class="fas fa-circle-' . ( $them ? 'check' : 'xmark' ) . '"></i></div></div>';
		}
		if ( $body === '' ) {
			return '';
		}
		$sub = kayan_hp_get_section_text( 'compare', 'subtitle', 'مقارنة واضحة بين خدماتنا والخدمات التقليدية.', 'A clear comparison.' );
		ob_start();
		?>
    <div class="shead rv">
      <span class="tag"><?php echo esc_html( kayan_hp_get_section_text( 'compare', 'tag', 'مقارنة', 'Compare' ) ); ?></span>
      <h2>{{compare_heading_html}}</h2>
      <p><?php echo esc_html( $sub ); ?></p>
    </div>
    <div class="cmp rv">
      <div class="cmp-row cmp-head">
        <div class="ch"><?php echo esc_html( kayan_hp_get_section_text( 'compare', 'col1', 'المعيار', 'Criteria' ) ); ?></div>
        <div class="ch rk">{{company_name}}</div>
        <div class="ch"><?php echo esc_html( kayan_hp_get_section_text( 'compare', 'col3', 'شركات أخرى', 'Others' ) ); ?></div>
      </div>
      <?php echo $body; // phpcs:ignore ?>
    </div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'kayan_homepage_build_reviews_html' ) ) {
	function kayan_homepage_build_reviews_html() {
		$reviews = function_exists( 'kayan_seo_get_home_reviews_data' ) ? kayan_seo_get_home_reviews_data() : array();
		if ( empty( $reviews ) ) {
			return '';
		}
		$cards = '';
		foreach ( $reviews as $review ) {
			if ( empty( $review['text'] ) ) {
				continue;
			}
			$author = isset( $review['author'] ) ? $review['author'] : '';
			$city   = isset( $review['city'] ) ? $review['city'] : '';
			$rating = isset( $review['rating'] ) ? (int) $review['rating'] : 5;
			$stars  = str_repeat( '★', max( 1, min( 5, $rating ) ) );
			$initial = mb_substr( trim( $author ), 0, 1, 'UTF-8' );
			$cards  .= '<div class="rcard"><div class="gtop"><span class="gver"><i class="fab fa-google"></i> ' . esc_html( kayan_hp_get_section_text( 'reviews', 'verified', 'موثّق عبر Google', 'Verified on Google' ) ) . '</span><span class="rstars">' . esc_html( $stars ) . '</span></div>';
			$cards  .= '<p class="txt">"' . esc_html( $review['text'] ) . '"</p>';
			$cards  .= '<div class="rclient"><span class="rav">' . esc_html( $initial ) . '</span><div><b>' . esc_html( $author ) . '</b>';
			if ( $city !== '' ) {
				$cards .= '<small>' . esc_html( $city ) . '</small>';
			}
			$cards .= '</div></div></div>';
		}
		if ( $cards === '' ) {
			return '';
		}
		$head = kayan_homepage_build_section_head_html(
			'reviews',
			'آراء العملاء',
			'آراء عملائنا',
			'تقييمات من عملاء ' . kayan_homepage_get_company_name() . '.',
			'Reviews',
			'Client reviews',
			'Feedback from our clients.'
		);
		ob_start();
		?>
    <?php echo $head; // phpcs:ignore ?>
    <div class="rv-slider rv">
      <div class="rv-track" id="rvTrack"><?php echo $cards; // phpcs:ignore ?></div>
    </div>
    <div class="rv-nav">
      <button onclick="rvMove(-1)" aria-label="<?php echo esc_attr( kayan_hp_get_section_text( 'reviews', 'prev', 'السابق', 'Previous' ) ); ?>"><i class="fas fa-chevron-right"></i></button>
      <button onclick="rvMove(1)" aria-label="<?php echo esc_attr( kayan_hp_get_section_text( 'reviews', 'next', 'التالي', 'Next' ) ); ?>"><i class="fas fa-chevron-left"></i></button>
    </div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'kayan_homepage_build_faq_html' ) ) {
	function kayan_homepage_build_faq_html() {
		$posts = get_posts(
			array(
				'post_type'      => 'faq',
				'post_status'    => 'publish',
				'posts_per_page' => (int) kayan_homepage_get_option_text( 'kayan_hp_faq_count', '10' ),
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		if ( empty( $posts ) ) {
			return '';
		}
		$items = '';
		foreach ( $posts as $post ) {
			$answer = apply_filters( 'the_content', $post->post_content );
			$items .= '<div class="faq-item rv" data-cat="all"><div class="faq-q" onclick="faqT(this)"><span>' . esc_html( get_the_title( $post ) ) . '</span><i class="fas fa-chevron-down"></i></div>';
			$items .= '<div class="faq-a"><p>' . wp_kses_post( $answer ) . '</p></div></div>';
		}
		$head = kayan_homepage_build_section_head_html(
			'faq',
			'الأسئلة الشائعة',
			'الأسئلة <span>الشائعة</span>',
			'إجابات واضحة لأكثر ما يسأل عنه عملاؤنا.',
			'FAQ',
			'Frequently <span>asked</span>',
			'Clear answers to common questions.'
		);
		return $head . '<div class="faq-wrap">' . $items . '</div>';
	}
}

if ( ! function_exists( 'kayan_homepage_build_pricing_html' ) ) {
	function kayan_homepage_build_pricing_html() {
		if ( ! empty( yc_get_option( 'kayan_hp_pricing_disable' ) ) ) {
			return '';
		}
		$posts = get_posts(
			array(
				'post_type'      => 'price',
				'post_status'    => 'publish',
				'posts_per_page' => (int) kayan_homepage_get_option_text( 'kayan_hp_pricing_count', '6' ),
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);
		if ( empty( $posts ) ) {
			return '';
		}
		$icons = array( 'fas fa-droplet', 'fas fa-layer-group', 'fas fa-water', 'fas fa-spray-can-sparkles', 'fas fa-bug-slash', 'fas fa-house-chimney' );
		$grid  = '';
		$i     = 0;
		foreach ( $posts as $post ) {
			$icon  = $icons[ $i % count( $icons ) ];
			$price = get_post_meta( $post->ID, 'price_text', true );
			$url   = kayan_homepage_get_post_url( $post );
			$grid .= '<div class="pcard rv"><div class="pic"><i class="' . esc_attr( $icon ) . '"></i></div>';
			$grid .= '<h3>' . esc_html( get_the_title( $post ) ) . '</h3>';
			$grid .= '<p>' . esc_html( wp_trim_words( get_the_excerpt( $post ), 14, '…' ) ) . '</p>';
			if ( $price !== '' ) {
				$grid .= '<div class="range"><b>' . esc_html( $price ) . '</b></div>';
			}
			$grid .= '<a class="read" href="' . esc_url( $url ) . '">' . esc_html( kayan_hp_get_section_text( 'pricing', 'read', 'اقرأ الدليل', 'Read guide' ) ) . ' <i class="fas fa-arrow-left"></i></a></div>';
			$i++;
		}
		$head = kayan_homepage_build_section_head_html(
			'pricing',
			'الأسعار',
			'أدلة الأسعار <span>والتكاليف</span>',
			'تقديرات شفافة من خطط الأسعار في موقعك.',
			'Pricing',
			'Pricing <span>guides</span>',
			'Transparent estimates from your price plans.'
		);
		return $head . '<div class="price-grid">' . $grid . '</div>';
	}
}

if ( ! function_exists( 'kayan_homepage_build_cta_html' ) ) {
	function kayan_homepage_build_cta_html() {
		$title = kayan_hp_get_section_text(
			'cta',
			'title',
			'جاهزون لخدمتك — تواصل مع ' . kayan_homepage_get_company_name(),
			'Ready to help — contact ' . kayan_homepage_get_company_name()
		);
		$sub = kayan_hp_get_section_text(
			'cta',
			'subtitle',
			'تواصل الآن واحصل على معاينة مجانية وعرض سعر شفاف.',
			'Contact us for a free inspection and clear quote.'
		);
		$chips = kayan_hp_get_group_items( 'kayan_hp_cta_chips' );
		$chip_html = '';
		foreach ( $chips as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$text = trim( (string) ( $row['text'] ?? '' ) );
			if ( $text === '' ) {
				continue;
			}
			$icon = ! empty( $row['icon'] ) ? $row['icon'] : 'fas fa-circle-check';
			$chip_html .= '<span><i class="' . esc_attr( $icon ) . '"></i> ' . esc_html( kayan_homepage_expand_inline_tokens( $text ) ) . '</span>';
		}
		if ( $chip_html === '' ) {
			$chip_html = '<span><i class="fas fa-shield-halved"></i> ضمان مكتوب</span><span><i class="fas fa-magnifying-glass"></i> معاينة مجانية</span><span><i class="fas fa-headset"></i> دعم مستمر</span>';
		}
		ob_start();
		?>
    <h2 class="rv"><?php echo esc_html( kayan_homepage_expand_inline_tokens( $title ) ); ?></h2>
    <p class="rv"><?php echo esc_html( kayan_homepage_expand_inline_tokens( $sub ) ); ?></p>
    <div class="fcta-btns rv">
      <a href="{{whatsapp_url}}" class="btn btn-wa"><i class="fab fa-whatsapp"></i> {{ui_btn_whatsapp_full}}</a>
      <a href="{{tel_url}}" class="btn btn-call"><i class="fas fa-phone"></i> {{ui_btn_call}}</a>
      <a href="{{whatsapp_url}}" class="btn btn-quote"><i class="fas fa-file-invoice-dollar"></i> {{ui_btn_quote}}</a>
    </div>
    <div class="fcta-trust rv"><?php echo $chip_html; // phpcs:ignore ?></div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'kayan_homepage_build_ba_html' ) ) {
	function kayan_homepage_build_ba_html() {
		$posts = get_posts(
			array(
				'post_type'      => 'works',
				'post_status'    => 'publish',
				'posts_per_page' => 2,
				'meta_query'     => array(
					array(
						'key'     => 'works_gallery',
						'compare' => 'EXISTS',
					),
				),
				'no_found_rows'  => true,
			)
		);
		if ( empty( $posts ) ) {
			return '';
		}
		$blocks = '';
		foreach ( $posts as $post ) {
			$gallery = get_post_meta( $post->ID, 'works_gallery', true );
			$gallery = is_array( $gallery ) ? $gallery : array();
			$before  = isset( $gallery[0] ) ? wp_get_attachment_image_url( $gallery[0], 'large' ) : '';
			$after   = isset( $gallery[1] ) ? wp_get_attachment_image_url( $gallery[1], 'large' ) : $before;
			if ( $before === '' ) {
				$before = get_the_post_thumbnail_url( $post, 'large' );
			}
			if ( $after === '' ) {
				$after = $before;
			}
			if ( $before === '' ) {
				continue;
			}
			$url = kayan_homepage_get_post_url( $post );
			$blocks .= '<div class="ba rv"><div class="ba-stage" data-ba>';
			$blocks .= '<div class="ba-img ba-after" style="background-image:url(' . esc_url( $after ) . ');background-size:cover;background-position:center"><span><i class="fas fa-circle-check"></i> بعد</span></div>';
			$blocks .= '<div class="ba-img ba-before" style="background-image:url(' . esc_url( $before ) . ');background-size:cover;background-position:center"><span><i class="fas fa-triangle-exclamation"></i> قبل</span></div>';
			$blocks .= '<span class="ba-label b">قبل</span><span class="ba-label a">بعد</span><div class="ba-handle"><span class="grip"><i class="fas fa-arrows-left-right"></i></span></div></div>';
			$blocks .= '<div class="ba-info"><div><b>' . esc_html( get_the_title( $post ) ) . '</b><br><small>' . esc_html( wp_trim_words( get_the_excerpt( $post ), 10, '…' ) ) . '</small></div>';
			$blocks .= '<a href="' . esc_url( $url ) . '" class="btn btn-soft">' . esc_html( kayan_hp_get_section_text( 'ba', 'link', 'شاهد التفاصيل', 'View details' ) ) . '</a></div></div>';
		}
		if ( $blocks === '' ) {
			return '';
		}
		$head = kayan_homepage_build_section_head_html(
			'ba',
			'قبل وبعد',
			'قبل وبعد — <span>نتائج حقيقية</span>',
			'نماذج من أعمالنا المنشورة.',
			'Before & after',
			'Real <span>results</span>',
			'From our published portfolio.'
		);
		return $head . '<div class="ba-wrap">' . $blocks . '</div>';
	}
}

if ( ! function_exists( 'kayan_hp_auto_hide_empty_sections' ) ) {
	function kayan_hp_auto_hide_empty_sections( $html ) {
		$map = array(
			'finder'   => 'id="finder"',
			'why'      => 'class="why-cards"',
			'team'     => 'class="team-grid"',
			'stats'    => 'class="stats-grid"',
			'compare'  => 'class="cmp rv"',
			'ba'       => 'class="ba-wrap"',
			'pricing'  => 'class="price-grid"',
			'reviews'  => 'id="rvTrack"',
			'projects' => 'class="proj-grid"',
			'services' => 'class="services-grid"',
			'blog'     => 'class="blog-grid"',
			'faq'      => 'class="faq-wrap"',
		);
		return preg_replace_callback(
			'/<!--\s*kayan-section:([a-z0-9_-]+)\s*-->(.*?)<!--\s*\/kayan-section(?::\1)?\s*-->/s',
			function ( $matches ) use ( $map ) {
				$section = $matches[1];
				$body    = $matches[2];
				if ( kayan_hp_section_disabled( $section ) ) {
					return '';
				}
				if ( trim( wp_strip_all_tags( $body ) ) === '' ) {
					return '';
				}
				if ( $section === 'team' && strpos( $body, 'class="tcard' ) === false ) {
					return '';
				}
				if ( $section === 'stats' && strpos( $body, 'class="stat' ) === false ) {
					return '';
				}
				if ( $section === 'compare' && strpos( $body, 'class="cmp-row"' ) === false ) {
					return '';
				}
				if ( $section === 'why' && strpos( $body, 'class="feat' ) === false && strpos( $body, 'class="tl"' ) === false ) {
					return '';
				}
				if ( $section === 'finder' && strpos( $body, 'id="fnSvc"' ) === false ) {
					return '';
				}
				if ( isset( $map[ $section ] ) ) {
					$needle = $map[ $section ];
					if ( preg_match( '/class="proj-grid">\s*<\/div>/', $body ) || preg_match( '/class="services-grid">\s*<\/div>/', $body ) || preg_match( '/class="blog-grid">\s*<\/div>/', $body ) ) {
						return '';
					}
					if ( preg_match( '/id="rvTrack">\s*<\/div>/', $body ) ) {
						return '';
					}
					if ( preg_match( '/class="faq-wrap">\s*<\/div>/', $body ) ) {
						return '';
					}
					if ( preg_match( '/class="ba-wrap">\s*<\/div>/', $body ) ) {
						return '';
					}
					if ( preg_match( '/class="price-grid">\s*<\/div>/', $body ) ) {
						return '';
					}
				}
				return $body;
			},
			$html
		);
	}
}
