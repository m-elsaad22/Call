# تقرير التحقق الفني — Sitemap الصفحة 7463 (`/service/`)

**التاريخ:** 2026-10-17  
**Rank Math على الموقع:** 1.0.280  
**حالة النشر:** **لم يُفعَّل أي Snippet على المباشر** — لم تُغيَّر إعدادات WordPress أو المحتوى.

---

## 1. السبب الجذري (مثبت — ليس افتراض GUID)

### الدليل من قاعدة البيانات / الخيارات (قراءة فقط)

| الخيار | القيمة | المعنى |
|--------|--------|--------|
| `show_on_front` | `posts` | الرئيسية تعرض آخر المقالات |
| `page_on_front` | **7460** | صفحة «من نحن» (`/about/`) — **مُخزَّنة لكن غير مستخدمة** كرئيسية ثابتة |
| `page_for_posts` | **7463** | صفحة «خدماتنا» (`/service/`) مُعيَّنة كـ **«صفحة المقالات»** في إعدادات القراءة |

- `wp3mdn_posts`: ID **7463** → `post_type=page` فقط (لا صف `post` بنفس الـ slug).
- **7463 لا يظهر** في استعلام Rank Math لعدّ المقالات (`post_type=post` → **1364** منشوراً).
- **`post-sitemap1.xml`**: **201** `<url>` (200 مقالة + **إدخال واحد إضافي** في بداية الملف).
- أول `<loc>` في `post-sitemap1.xml` = `/service/`، يليه مباشرة أول مقال (`gypsum-board-installation-in-dubai/`).

### مسار الكود في Rank Math 1.0.280 (مصدر الإضافة الرسمي)

ملف: `includes/modules/sitemap/providers/class-post-type.php`

عند بناء **`post-sitemap*.xml`** (نوع WordPress = `post`)، الصفحة **1** تستدعي `get_first_links( 'post' )`:

```330:336:includes/modules/sitemap/providers/class-post-type.php
		} elseif ( $this->get_page_on_front_id() && 'post' === $post_type && $this->get_page_for_posts_id() ) {
			$needs_archive = false;
			$post_id       = $this->get_page_for_posts_id();

			if ( Sitemap::is_object_indexable( $post_id ) ) {
				$links[] = $this->get_single_post_row_data( get_post( $post_id ) );
			}
```

- بما أن **`page_on_front` = 7460** (غير صفر) **و** **`page_for_posts` = 7463**، Rank Math **يُضيف عمداً** permalink الصفحة 7463 إلى **post-sitemap** كتمثيل لـ «صفحة المقالات» — حتى لو `show_on_front` = `posts`.
- `get_single_post_row_data()` يستخدم `get_permalink( $post )` → `https://www.rukn-eltatawer.com/service/`.
- **`page-sitemap.xml`** يُ built بنوع `page` عبر `get_posts( 'page' )` → 7463 تظهر **مرة** هناك (سليم).

**الاستنتاج:** التكرار = **سلوك Rank Math + إعداد WordPress `page_for_posts` = 7463**، وليس إدراجاً خاطئاً لـ `post_type=page` داخل SQL المقالات.  
**GUID `/services/`** و**أرشيف CPT `/services/`** قد تكون **سياقاً مرتبطاً** (خطأ إعداد تاريخي) لكن **ليسا السبب المباشر** المثبت في الكود.

---

## 2. التحقق من فلتر `rank_math/sitemap/entry` (1.0.280)

### أين يُستدعى؟

```215:225:includes/modules/sitemap/providers/class-post-type.php
				$url = $this->do_filter( 'sitemap/entry', $url, 'post', $post );
				if ( empty( $url ) ) {
					continue;
				}
```

- الاسم الكامل للفلتر: **`rank_math/sitemap/entry`** (بادئة Rank Math).
- المعاملات: **`$url`** (مصفوفة، مفتاح `loc`)، **`$type`** (سلسلة)، **`$object`** (كائن منشور WordPress).

### ⚠️ تنبيه مهم عن المعامل `$type`

في مزود `Post_Type`، القيمة المُمرَّرة للمعامل الثالث **دائماً `'post'`** — حتى عند بناء **`page-sitemap.xml`** (نوع WP = `page`).  
هذا **لا** يعني «sitemap المقالات فقط»؛ legacy من Yoast.

**لذلك:** Snippet يعتمد **فقط** على `if ( 'post' === $type )` **قد يستبعد 7463 من page-sitemap أيضاً** إذا رُكّز على `ID === 7463 && post_type === page`.

### استبعاد URL

بعد الفلتر: `if ( empty( $url ) ) { continue; }`  
Rank Math / WPML يستبعدان بإرجاع **`false`** أو **`''`** (انظر `exclude_hidden_language_posts` في `class-sitemap.php`).

**`return false` مقبول** ويعادل «لا تُضف هذا السطر».

### فصل post-sitemap عن page-sitemap في Snippet

يجب معرفة **نوع Sitemap الجاري بناؤه** (`post` vs `page`):

- فلتر **`rank_math/sitemap/build_type`** يُستدعى في `class-generator.php` قبل `get_sitemap_links( $type, ... )` حيث `$type` = `post` | `page` | …

**الاسترategia الآمنة:** تخزين `$type` الحالي في متغير/static أثناء `build_type`، ثم في `sitemap/entry` استبعد 7463 **فقط** عندما `$building_type === 'post'`.

---

## 3. Snippet نهائي مقترح (مراجعة — غير منشور)

```php
<?php
/**
 * RUKn — إزالة /service/ (7463) من post-sitemap فقط.
 * السبب: page_for_posts=7463 يفرضها Rank Math في get_first_links('post').
 * Rollback: تعطيل/حذف Snippet.
 */
add_action(
	'plugins_loaded',
	function () {
		if ( ! defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		$ctx = new class() {
			public $building = '';
		};

		add_filter(
			'rank_math/sitemap/build_type',
			function ( $type ) use ( $ctx ) {
				$ctx->building = (string) $type;
				return $type;
			},
			1
		);

		add_filter(
			'rank_math/sitemap/entry',
			function ( $url, $type, $object ) use ( $ctx ) {
				// استبعاد فقط أثناء بناء post-sitemap (نوع WP post).
				if ( 'post' !== $ctx->building ) {
					return $url;
				}

				$page_id = 7463;

				if ( is_object( $object ) && isset( $object->ID ) && (int) $object->ID === $page_id ) {
					return false;
				}

				if ( is_array( $url ) && ! empty( $url['loc'] ) ) {
					$loc     = untrailingslashit( (string) $url['loc'] );
					$service = untrailingslashit( home_url( '/service/' ) );
					if ( $loc === $service ) {
						return false;
					}
				}

				return $url;
			},
			10,
			3
		);
	},
	20
);
```

---

## 4. بديل بدون Snippet (للمراجعة لاحقاً — **لم يُنفَّذ**)

| الإجراء | التأثير | المخاطر |
|---------|---------|---------|
| تصحيح **`page_for_posts`** إلى `0` أو إلى **2453** (`/blog/`) | Rank Math يتوقف عن prepend 7463 في post-sitemap | **تغيير إعدادات القراءة** — قد يؤثر على مسار «المدونة» إذا كان الموقع يعتمد عليه؛ **لا يحذف محتوى** |

**لم يُطبَّق** بانتظار موافقتك (ممنوع تغيير إعدادات دون موافقة).

---

## 5. ما تم اختباره وما لم يُختبر

### تم (قراءة / تحليل / مقارنة)

| # | الاختبار | النتيجة |
|---|----------|---------|
| 1 | تنزيل Rank Math **1.0.280** من wordpress.org ومطابقة مسارات Sitemap | ✓ |
| 2 | قراءة `page_for_posts` / `page_on_front` / `show_on_front` على المباشر | ✓ `page_for_posts=7463` |
| 3 | عد المقالات vs عدد URLs في `post-sitemap1` (+1) | ✓ |
| 4 | ترتيب URLs ( `/service/` أولاً ) | ✓ يطابق `get_first_links` |
| 5 | التأكد أن 7463 `post_type=page` في DB | ✓ |
| 6 | مراجعة سلوك `empty( $url )` بعد الفلتر | ✓ `false` يستبعد |

### لم يُختبر (تعذّر أو خارج النطاق)

| # | البند | السبب |
|---|--------|--------|
| 1 | **تفعيل Snippet على مباشر** | طلبك: لا نشر قبل المراجعة |
| 2 | **بيئة staging / نسخة** | لا staging م attached لهذا Agent؛ لا clone محلي لـ WordPress كامل |
| 3 | **اختبار PHP runtime للSnippet** | يتطلب تفعيل WPCode أو mu-plugin |
| 4 | **purge كاش + إعادة crawl XML بعد الإصلاح** | لم يُطبَّق إصلاح |
| 5 | **تصحيح `page_for_posts` تجريبياً** | ممنوع دون موافقة |

---

## 6. مخاطر متبقية بعد التفعيل (عند الموافقة)

| المخاطرة | الاحتمال | التخفيف |
|----------|----------|---------|
| كاش Rank Math / LiteSpeed يعرض XML قديم | متوسط | `wp cache purge` + Rank Math sitemap cache clear |
| Snippet قديم يستبعد 7463 من page-sitemap إن نُسخ بدون `build_type` | منخفض (إن استُخدم الكود §3) | استخدام النسخة §3 فقط |
| Google يعيد معالجة sitemap | طبيعي | GSC → إعادة إرسال index |

---

## 7. `/services/` (عنوان Archive)

**بدون تعديل** — التشخيص السابق (KAYAN SEO + `pre_get_document_title` + عدم تطبيق `pt_services_*` على الأرشيف) ما زال صالحاً. مراجعة منفصلة لاحقاً.

---

## 8. الالتزام بالقيود

- لا حذف/دمج/redirect/slug/canonical/GUID/قالب/إضافات.
- لا Snippet على المباشر في هذه الجولة.

**الخطوة التالية بعد موافقتك:** تفعيل Snippet §3 **أو** اختيار تصحيح `page_for_posts`، ثم purge كاش، ثم التحقق من XML و`/service/` HTTP 200.
