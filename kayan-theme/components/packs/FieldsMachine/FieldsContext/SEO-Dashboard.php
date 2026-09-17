<?php
require_once get_template_directory() . '/components/packs/kayan-seo/dashboard.php';

$data = kayan_seo_get_dashboard_data();
$stats = $data['stats'];

echo '<div class="-fix-inputs-area kayan-seo-dashboard-field">';
	echo '<div class="kayan-seo-dashboard">';

		echo '<div class="kayan-seo-dashboard__score">';
			echo '<div class="kayan-seo-dashboard__score-ring" style="--kayan-score:' . (int) $data['score_percent'] . '">';
				echo '<strong>' . (int) $data['score_percent'] . '%</strong>';
				echo '<span>جاهزية SEO</span>';
			echo '</div>';
			echo '<div class="kayan-seo-dashboard__score-meta">';
				echo '<h3>لوحة SEO — KAYAN</h3>';
				echo '<p>' . (int) $data['score_passed'] . ' من ' . (int) $data['score_total'] . ' عناصر أساسية مكتملة</p>';
			echo '</div>';
		echo '</div>';

		echo '<div class="kayan-seo-dashboard__grid">';

			echo '<div class="kayan-seo-dashboard__card">';
				echo '<h4><i class="fa-solid fa-list-check"></i> قائمة التحقق</h4>';
				echo '<ul class="kayan-seo-dashboard__checks">';
				foreach ( $data['checks'] as $check ) {
					$class = ! empty( $check['ok'] ) ? 'is-ok' : 'is-missing';
					$icon = ! empty( $check['ok'] ) ? 'fa-circle-check' : 'fa-circle-xmark';
					echo '<li class="' . esc_attr( $class ) . '">';
						echo '<i class="fa-solid ' . esc_attr( $icon ) . '"></i>';
						echo '<div><strong>' . esc_html( $check['label'] ) . '</strong>';
						if ( ! empty( $check['hint'] ) ) {
							echo '<small>' . esc_html( $check['hint'] ) . '</small>';
						}
						echo '</div>';
					echo '</li>';
				}
				echo '</ul>';
			echo '</div>';

			echo '<div class="kayan-seo-dashboard__card">';
				echo '<h4><i class="fa-solid fa-chart-simple"></i> إحصائيات المحتوى</h4>';
				echo '<dl class="kayan-seo-dashboard__stats">';
					echo '<div><dt>مقالات منشورة</dt><dd>' . (int) $stats['posts'] . '</dd></div>';
					echo '<div><dt>مقالات بوصف SEO</dt><dd>' . (int) $stats['posts_with_meta'] . ' / ' . (int) $stats['posts'] . '</dd></div>';
					echo '<div><dt>مقالات مربوطة بمدينة</dt><dd>' . (int) $stats['posts_with_city'] . ' / ' . (int) $stats['posts'] . '</dd></div>';
					echo '<div><dt>مدن</dt><dd>' . (int) $stats['cities'] . '</dd></div>';
					echo '<div><dt>مدن بوصف SEO</dt><dd>' . (int) $stats['cities_with_seo'] . ' / ' . (int) $stats['cities'] . '</dd></div>';
					echo '<div><dt>مدن بصورة</dt><dd>' . (int) $stats['cities_with_image'] . ' / ' . (int) $stats['cities'] . '</dd></div>';
				echo '</dl>';
			echo '</div>';

			echo '<div class="kayan-seo-dashboard__card kayan-seo-dashboard__card--links">';
				echo '<h4><i class="fa-solid fa-link"></i> روابط سريعة</h4>';
				echo '<p class="kayan-seo-dashboard__url"><strong>Sitemap</strong><br><code id="kayan-seo-sitemap-url">' . esc_html( $data['sitemap_url'] ) . '</code> <button type="button" class="button kayan-seo-copy-url" data-copy-target="kayan-seo-sitemap-url">نسخ</button></p>';
				echo '<p class="kayan-seo-dashboard__url"><strong>الموقع</strong><br><a href="' . esc_url( $data['home_url'] ) . '" target="_blank" rel="noopener">' . esc_html( $data['home_url'] ) . '</a></p>';
				echo '<p><a class="button button-primary" href="' . esc_url( $data['gsc_url'] ) . '" target="_blank" rel="noopener"><i class="fa-brands fa-google"></i> فتح Search Console</a></p>';
				echo '<p><a class="button" href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=city&post_type=post' ) ) . '">إدارة المدن</a> <a class="button" href="' . esc_url( admin_url( 'admin.php?page=yts-kayan_seo' ) ) . '">إعدادات KAYAN SEO</a></p>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<script>
document.addEventListener("click", function (event) {
	var btn = event.target.closest(".kayan-seo-copy-url");
	if (!btn) return;
	var id = btn.getAttribute("data-copy-target");
	var el = document.getElementById(id);
	if (!el) return;
	var text = el.textContent || el.innerText;
	if (navigator.clipboard && navigator.clipboard.writeText) {
		navigator.clipboard.writeText(text);
	} else {
		var ta = document.createElement("textarea");
		ta.value = text;
		document.body.appendChild(ta);
		ta.select();
		document.execCommand("copy");
		document.body.removeChild(ta);
	}
	btn.textContent = "تم النسخ";
	setTimeout(function () { btn.textContent = "نسخ"; }, 1500);
});
</script>';
