# المراجعة النهائية — Sitemap 7463 (`/service/`) — بدون تنفيذ على المباشر

**التاريخ:** 2026-10-18  
**مصدر Rank Math:** wordpress.org plugin zip **1.0.280** (مطابق لإصدار الموقع)  
**الموقع:** لم يُجرَ أي Snippet أو تعديل إعدادات/DB/محتوى.

---

## أ) هل Snippet `build_type` + `sitemap/entry` صحيح تقنيًا بالكامل؟

### الحكم: **لا — يحتاج تعديل جوهري أو استبدال بالحل البديل**

**سبب مثبت من الكود (1.0.280):**  
إدخال `/service/` في `post-sitemap1.xml` يأتي من **`get_first_links( 'post' )`** (صفحة `page_for_posts` = 7463)، وليس من حلقة `get_posts( 'post' )`.

```187:189:includes/modules/sitemap/providers/class-post-type.php
		if ( 1 === $current_page ) {
			$links = array_merge( $links, $this->get_first_links( $type ) );
		}
```

```330:336:includes/modules/sitemap/providers/class-post-type.php
		} elseif ( $this->get_page_on_front_id() && 'post' === $post_type && $this->get_page_for_posts_id() ) {
			$needs_archive = false;
			$post_id       = $this->get_page_for_posts_id();
			if ( Sitemap::is_object_indexable( $post_id ) ) {
				$links[] = $this->get_single_post_row_data( get_post( $post_id ) );
			}
```

**`rank_math/sitemap/entry` يُستدعى فقط داخل `foreach ( $posts as $post )`** بعد `get_posts()` — **لا يمرّ عليه** ما يُرجعه `get_first_links()`.

| ما تم التحقق منه (مراجعة كود) | ما يحتاج اختبار على الموقع |
|-------------------------------|----------------------------|
| مسار 7463 = `get_first_links` + `page_for_posts` | أن Snippet `entry` **لا يزيل** `/service/` من post-sitemap |
| `entry` لا يغطي `first_links` | أن فلتر **`post_sitemap_url`** يزيلها فعليًا |
| `build_type` موجود ويُستدعى مرة لكل بناء | سلوك الكاش بعد purge |
| شكل `$url['loc']` + `$object->ID` في الحلقة | — |

---

## 1) فلتر `rank_math/sitemap/build_type`

**موجود** في `includes/modules/sitemap/class-generator.php`:

```151:169:includes/modules/sitemap/class-generator.php
	public function build_sitemap( $type, $page ) {
		$this->max_entries = absint( Helper::get_settings( 'sitemap.items_per_page', 100 ) );
		$type = $this->do_filter( 'sitemap/build_type', $type );
		// ...
		foreach ( $this->providers as $provider ) {
			if ( ! $provider->handles_type( $type ) ) {
				continue;
			}
			$links = $provider->get_sitemap_links( $type, $this->max_entries, $page );
```

- **الاسم الكامل:** `rank_math/sitemap/build_type` (بادئة `rank_math/` من `do_filter`).
- **متى:** بداية `build_sitemap()` لكل طلب XML (مثلاً `post-sitemap1.xml` → `$type` = `post` من query var `sitemap` في `class-router.php`).
- **الاعتماد عليه:** مناسب **لتمييز post vs page** أثناء بناء **حلقة** `get_posts`، لكن **لا يغطي** URLs من `get_first_links` حتى لو عُرِف `$ctx->building === 'post'`.

**حدود:** طلب HTTP واحد = نوع sitemap واحد؛ `$ctx` في Snippet يعمل **per-request** (لا مشاركة بين زوار). **لم يُختبر** تنفيذ PHP على المباشر.

---

## 2) مراجعة Snippet المقترح (build_type + entry)

| العنصر | التقييم |
|--------|---------|
| `plugins_loaded` @ 20 | Rank Math `init` @ 14 — **منطقي**؛ `RANK_MATH_VERSION` متوقع أن يكون معرّفًا |
| `$ctx` anonymous class | **نطاق صحيح** لطلب واحد؛ لا مشكلة closure |
| `build_type` @ 1 | **قبل** بناء الروابط في نفس الطلب — **منطقي** |
| `entry` + `$ctx->building === 'post'` | **صحيح** لتمييز post-sitemap عن page-sitemap **في الحلقة فقط** |
| **`$object->ID` / `$url['loc']` على 7463** | **لن يُستدعى** لإدخال `/service/` الحالي لأنه من `get_first_links` |

**الخلاصة:** Snippet §3 في `SITEMAP_7463_VERIFICATION_REPORT.md` **غير كافٍ** لإزالة التكرار المثبت.

### Snippet بديل مقترح (مراجعة — غير منشور)

يستهدف **`rank_math/sitemap/post_sitemap_url`** — يُستدعى عند **output** كل `<url>` في post-sitemap فقط (`class-generator.php` ~243–245):

```php
<?php
/**
 * RUKn — حذف /service/ من post-sitemap*.xml فقط (7463 via page_for_posts).
 * Rollback: تعطيل Snippet.
 */
add_action(
	'plugins_loaded',
	function () {
		if ( ! defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		add_filter(
			'rank_math/sitemap/post_sitemap_url',
			function ( $url, $generator ) {
				if ( ! is_array( $url ) || empty( $url['loc'] ) ) {
					return $generator->sitemap_url( $url );
				}
				$loc     = untrailingslashit( (string) $url['loc'] );
				$service = untrailingslashit( home_url( '/service/' ) );
				if ( $loc === $service ) {
					return '';
				}
				return $generator->sitemap_url( $url );
			},
			10,
			2
		);
	},
	20
);
```

**ملاحظة:** عند وجود **أي** فلتر على `post_sitemap_url`، Rank Math **لا** يستدعي `sitemap_url()` تلقائيًا — يجب إعادة استدعائها للروابط الأخرى (كما في المثال).

**لم يُختبر** على المباشر.

---

## 3) post-sitemap فقط vs page-sitemap

| المسار | post-sitemap | page-sitemap |
|--------|--------------|--------------|
| Snippet `entry` + build_type | **لا يزيل** `/service/` (first_links) | 7463 تبقى عبر `get_posts('page')` |
| Snippet `post_sitemap_url` | **مقصود** حذف `<url>` لـ `/service/` | **لا** — فلتر `page_sitemap_url` منفصل |

**لم يُختبر** XML بعد تطبيق أي حل.

---

## 4) شكل بيانات `rank_math/sitemap/entry`

| المعامل | في 1.0.280 (Post_Type) | ينطبق على 7463 في post-sitemap؟ |
|---------|-------------------------|-----------------------------------|
| `$url` | مصفوفة؛ `loc`, `mod`, `images` من `get_single_post_row_data()` | **لا** — 7463 لا تمر هنا |
| `$type` | دائماً `'post'` (حتى في page-sitemap) | — |
| `$object` | كائن منشور من `get_posts` | **لا** لـ first_links |

استبعاد: `empty( $url )` بعد الفلتر — **`false` و `''`** مقبولان (مثل WPML).

---

## 5) بديل `page_for_posts` — مقارنة (قراءة فقط)

### الوضع الحالي (مثبت سابقًا)

| الخيار | القيمة |
|--------|--------|
| `show_on_front` | `posts` |
| `page_on_front` | 7460 (`/about/`) |
| `page_for_posts` | **7463** (`/service/`) |

### الصفحة 2453

- **2453** = صفحة «المدونة»، slug `blog`, `/blog/`, `publish`, `page`.
- **2453 هي صفحة مدونة منطقية** — **لم يُثبت** أنها كانت «صفحة المقالات» الرسمية في Reading (الإعداد الحالي `posts` على الرئيسية).

### تعيين `page_for_posts` → **2453**

| | |
|-|-|
| **Rank Math** | ما دام `page_on_front` ≠ 0 و `page_for_posts` ≠ 0، `get_first_links('post')` يضيف **permalink 2453** → `/blog/` إلى **post-sitemap** |
| **page-sitemap** | `/blog/` **موجود أصلًا** |
| **الحكم** | **ليس الأقل مخاطرة** — يستبدل تكرار `/service/` بتكرار محتمل لـ `/blog/`؛ **لم يُختبر** |

### تعيين `page_for_posts` → **0**

| | |
|-|-|
| **Rank Math** | فرع `page_for_posts` في `get_first_links` **لا يعمل**؛ قد يُضاف **archive** للـ post type (عند `show_on_front=posts` غالبًا رابط أرشيف/رئيسية — **سلوك WP**) |
| **الرئيسية** | `show_on_front=posts` **لا يتغير** — عرض المقالات على `/` كما هو |
| **الحكم** | **أقرب لإصلاح السبب** من Snippet أو 2453؛ **يتطلب موافقة** (إعداد Reading) + **اختبار** post-sitemap بعد التغيير |

**لم يُغيَّر** أي خيار على المباشر.

---

## 6) الحل الأقل مخاطرة (توصية)

| الترتيب | الحل | لماذا |
|---------|------|--------|
| **1** | **`page_for_posts` = 0** (بعد موافقة) | يزيل سبب Rank Math مباشرة؛ لا كود؛ `/service/` تبقى في page-sitemap |
| **2** | Snippet **`post_sitemap_url`** | إذا رفض تغيير Reading؛ **يجب** اختبار على المباشر |
| **3** | Snippet **`entry` + build_type** | **غير موصى به** — ثغرة `get_first_links` |
| **4** | **`page_for_posts` = 2453** | **غير موصى به** — `/blog/` في post-sitemap + page-sitemap |

---

## 7) اختبارات مطلوبة **بعد** التنفيذ (على المباشر)

1. `post-sitemap1.xml` — **لا** `<loc>…/service/</loc>`.
2. `page-sitemap.xml` — **نعم** `/service/` **مرة واحدة**.
3. `curl -I https://www.rukn-eltatawer.com/service/` → **200**؛ canonical/robots دون تغيير غير مقصود.
4. عينة من post-sitemap (أول/آخر 5 URLs) + `page-sitemap` كامل — **لا** حذف عشوائي.
5. purge Rank Math sitemap cache + LiteSpeed؛ إعادة فحص XML (تأكيد `x-litespeed-cache` / credits).
6. (اختياري) GSC — إعادة إرسال `sitemap_index.xml`.

---

## 8) التراجع

| الحل | التراجع |
|------|---------|
| Snippet | تعطيل/حذف في WPCode → purge كاش → التحقق من عودة `/service/` في post-sitemap |
| `page_for_posts` | استعادة القيمة **7463** من نسخة احتياطية للخيار → purge → فحص XML |

---

## 9) ملخص: مُحقق vs يحتاج اختبار

| مُحقق (كود + قراءة موقع سابقًا) | يحتاج اختبار مباشر |
|--------------------------------|---------------------|
| سبب التكرار = `page_for_posts=7463` + `get_first_links` | فعالية Snippet `post_sitemap_url` |
| `entry` لا يغطي first_links | `page_for_posts=0` وشكل أول URL في post-sitemap |
| `build_type` موجود ومعناه | عدم كسر PHP / HTML sitemap |
| 2453 = `/blog/` | تأثير تغيير Reading على breadcrumbs/روابط أخرى |
| Snippet entry **ناقص** | الكاش بعد purge |

**لا يُعتبر أي إصلاح «مؤكد النجاح» قبل الاختبارات §7.**
