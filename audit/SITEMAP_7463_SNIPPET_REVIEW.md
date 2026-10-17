# فحص Sitemap — الصفحة 7463 (`/service/`) — للمراجعة قبل التنفيذ

**الموقع:** https://www.rukn-eltatawer.com  
**Rank Math:** 1.0.280 (نشط)  
**تاريخ الفحص:** 2026-10-17  
**حالة التنفيذ:** **لم يُنشر أي Snippet على الموقع المباشر**

---

## 1. نتيجة الفحص: لماذا `/service/` داخل `post-sitemap1.xml`؟

### 1.1 ما هو مؤكد

| التحقق | النتيجة |
|--------|---------|
| كيان واحد فقط | `wp3mdn_posts`: ID **7463**, `post_type=page`, `post_name=service`, `post_status=publish` |
| Permalink الفعلي | `https://www.rukn-eltatawer.com/service/` |
| `page-sitemap.xml` | يتضمن `/service/` مرة واحدة (سليم) |
| `post-sitemap1.xml` | يتضمن `/service/` مرة واحدة (تكرار عبر نوعي sitemap) |
| صفحات أخرى (blog, faq, pricing, …) | **لا** تظهر في `post-sitemap1.xml` |
| `/services/` (أرشيف CPT) | **ليس** داخل `post-sitemap` بسبب هذا الموضوع |

### 1.2 ما يميز الصفحة 7463 عن باقي الصفحات

1. **GUID لا يطابق الـ slug:**  
   - `guid` = `https://www.rukn-eltatawer.com/services/`  
   - الرابط الحي = `/service/`  
   - `/services/` هو أرشيف CPT `services` (منشورات من نوع `services`)، وليس permalink هذه الصفحة.

2. **تعارض اسمي مع التصنيف:**  
   - تصنيف `category/service/` (term_id **2457**, slug `service`)  
   - صفحة slug `service` على مسار `/service/`  
   - WordPress يسمح بذلك (مسارات مختلفة)، لكن Rank Math يربط `/services/` داخلياً بالصفحة 7463.

3. **سجل روابط Rank Math الداخلية:**  
   - `wp3mdn_rank_math_internal_links`: `url=/services/`, `post_id=7458` (صفحة sitemap), `target_post_id=7463`  
   - يوضح أن Rank Math يعامل مسار `/services/` كوجهة مرتبطة بالصفحة 7463 وليس فقط كأرشيف CPT.

4. **قالب KAYAN «services» + metas شبيهة بالمقالات:**  
   - `template` → `SelectedModel: services`  
   - `post__card__data`, `position__post_card`, إلخ (حقول عادةً للمقالات)  
   - **لكن** صفحات أخرى (contact-us, faqs, price) لها قوالب مخصصة و**لا** تتكرر في post-sitemap → ليس السبب الوحيد.

5. **Analytics Rank Math:**  
   - `rank_math_analytics_objects`: `object_id=7463`, `object_subtype=page`, `page=/service/`  
   - `object_type=post` هنا **مصطلح Rank Math** لكل «كائن محتوى» وليس `post_type` WordPress — **جميع** الصفحات تقريباً لها `object_type=post` + `object_subtype=page`، لذا لا يفسر التكرار وحده.

### 1.3 الاستنتاج التشخيصي (مرتفع الثقة)

**التكرار ليس «مقالاً مكرراً» ولا صفحتين منفصلتين.**  
هو **نفس URL** (`/service/`) يُولَّد من **مزودين** في Rank Math:

- **Page sitemap** ← `get_permalink(7463)` → `/service/` ✓  
- **Post sitemap** ← إدخال خاطئ لنفس الكائن 7463 (صفحة `page`) داخل سلسلة `post-sitemap` — على الأرجح بسبب **تداخل GUID/مسار `/services/`** مع أرشيف CPT + **الفهرسة الداخلية** لـ Rank Math، وليس بسبب وجود `post_type=post` في قاعدة البيانات.

**لم يُثبت** سطر PHP في القالب يضيف الصفحة إلى post-sitemap (فقط فلاتر portfolio/works في `kayan-cpt/setup.php`).

---

## 2. WPCode Snippet مقترح (مراجعة فقط — غير منشور)

### 2.1 الكود الكامل

```php
<?php
/**
 * RUKn — إزالة تكرار /service/ من post-sitemap فقط (الصفحة 7463).
 * WPCode: PHP Snippet, Auto Insert, Run Everywhere.
 * Priority: plugins_loaded @ 20 (بعد تحميل Rank Math).
 *
 * Rollback: تعطيل أو حذف هذا الـ Snippet من WPCode.
 */
add_action(
	'plugins_loaded',
	function () {
		if ( ! defined( 'RANK_MATH_VERSION' ) ) {
			return;
		}

		add_filter(
			'rank_math/sitemap/entry',
			function ( $url, $type, $object ) {
				// المزود: post-sitemap*.xml فقط (نوع post في Rank Math).
				if ( 'post' !== $type ) {
					return $url;
				}

				$page_id = 7463;

				// 1) استبعاد بالكائن إن وُجد.
				if ( is_object( $object ) && isset( $object->ID, $object->post_type ) ) {
					if ( (int) $object->ID === $page_id && 'page' === $object->post_type ) {
						return false;
					}
				}

				// 2) استبعاد بالرابط (احتياط إذا كان $object غير WP_Post).
				if ( is_array( $url ) && ! empty( $url['loc'] ) ) {
					$loc = untrailingslashit( (string) $url['loc'] );
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

### 2.2 ما الذي سيتغير عند الموافقة لاحقاً؟

| العنصر | التغيير |
|--------|---------|
| ملفات القالب | **لا** |
| إضافات (تعطيل/تحديث) | **لا** |
| Slug / canonical / redirect / robots | **لا** |
| محتوى الصفحة 7463 | **لا** |
| فهرسة الصفحة في Google | **لا** (تبقى في `page-sitemap.xml` و`index, follow`) |
| WPCode | **Snippet واحد** جديد (معطل حتى تفعّله أنت) |

**لا يُستخدم** «Exclude Posts» في Rank Math (ID 7463) — ذلك يستبعد URL من **كل** أنواع Sitemap بما فيها `page-sitemap`.

---

## 3. إجابات أسئلة المراجعة

### هل يعمل مع Rank Math 1.0.280؟

**نعم، متوقع.** الفلتر `rank_math/sitemap/entry` موثّق رسمياً في Rank Math ( `$url`, `$type`, `$object` — `$type` = `post` لمزود post-sitemap). الإصدار الحالي على الموقع **1.0.280**.

### هل يؤثر على صفحات أخرى أو sitemap دول/لغات؟

- **صفحات أخرى:** لا — الشرط `ID === 7463` و `post_type === page` داخل `$type === 'post'` فقط.
- **`page-sitemap.xml`:** لا — الفلتر يعمل عندما `$type === 'post'` فقط.
- **مقالات / CPT / تصنيفات:** لا.
- **Polylang / `/en/`:** لا يوجد `/en/service/` في post-sitemap حالياً؛ الشرط على `home_url('/service/')` فقط.
- **`/services/` (أرشيف CPT):** لا يُستهدف (URL مختلف).

### أخطاء PHP أو Sitemap قديم في الكاش؟

| المخاطرة | التقييم |
|----------|---------|
| أخطاء PHP | **منخفض** — كود بسيط، `return false` مدعوم لاستبعاد URL |
| Rank Math معطّل | Snippet يخرج مبكراً إذا `RANK_MATH_VERSION` غير معرّف |
| كاش Sitemap | **متوسط** — Rank Math يخزّن ملفات (`rank_math_sitemap_cache_files` في DB). بعد التفعيل قد تحتاج **مسح كاش Sitemap + LiteSpeed** قبل التحقق |
| كاش قديم يُخفي النجاح | إذا بقي `/service/` في post-sitemap بعد التفعيل → purge ثم إعادة فحص XML |

**لا يُنصح** بـ `rank_math/sitemap/enable_caching` = false دائماً إلا للتشخيص المؤقت (أداء).

### التحقق من النجاح

1. `curl -s https://www.rukn-eltatawer.com/post-sitemap1.xml | grep service` → **لا** نتائج لـ `/service/`.
2. `curl -s https://www.rukn-eltatawer.com/page-sitemap.xml | grep service` → **نعم** `/service/` موجود.
3. `curl -I https://www.rukn-eltatawer.com/service/` → 200، robots index، canonical دون تغيير.
4. (اختياري) Search Console → Sitemaps → إعادة جلب `sitemap_index.xml`.

### التراجع الفوري

1. **WPCode:** تعطيل أو حذف Snippet «RUKn — post-sitemap 7463».
2. **كاش:** `wp cache purge` / LiteSpeed Purge All (أو من لوحة الاستضافة).
3. إعادة فحص XML — يعود `/service/` إلى post-sitemap1 إن كان Rank Math يولّده أصلاً.

---

## 4. `/services/` — عنوان «الخدمات Archive | ركن التطور» (تشخيص فقط)

### السبب

1. **KAYAN SEO** (`kayan-seo/setup.php`) يفعّل `pre_get_document_title` ويقرأ **`rank_math_title` / `rank_math_description` من meta المنشور/التصنيف فقط** (`kayan_seo_get_queried_object_id()` لا يعيد ID لأرشيف CPT).
2. **واجهة Rank Math معطّلة** على الفرونت (`kayan-seo/compatibility.php` → `rank_math/frontend/disable`).
3. عند أرشيف `is_post_type_archive('services')` يبقى عنوان WordPress الافتراضي: **«{تسمية النوع} Archive | {اسم الموقع}»** → «الخدمات Archive | ركن التطور».
4. قوالب Rank Math `pt_services_title` / `pt_services_description` **محدّثة في الخيارات** لكن **لا تُطبَّق** على `<title>` الحي ما دام KAYAN يتحكم بالعنوان دون جسر للأرشيف.

**Canonical:** `https://www.rukn-eltatawer.com/services/` — self، `index, follow` (لم يُغيّر).

### خيارات الإصلاح (بدون تنفيذ — للقرار لاحقاً)

| الخيار | الوصف | المخاطر |
|--------|--------|---------|
| **A — لا شيء** | الإبقاء على `/service/` (7463) كوجهة SEO الرئيسية للخدمات؛ `/services/` قائمة CPT | **الأقل** — لا تغيير تقني |
| **B — WPCode `document_title_parts` / `pre_get_document_title`** | عنوان/وصف مخصّص **فقط** لـ `is_post_type_archive('services')` | **منخفض–متوسط** — snippet منفصل؛ يجب ألا يكرر `<meta description>` مع KAYAN |
| **C — توسيع جسر KAYAN** | قراءة قوالب Rank Math للأرشيف | **متوسط** — تعديل **قالب** (محظور حالياً) |
| **D — `kayan_seo_disable`** | Rank Math يخرج العنوان من `pt_services_*` | **مرتفع** — احتمال تكرار canonical/meta مع KAYAN |
| **E — تغيير GUID 7463** من `/services/` إلى `/service/` | تشخيصي/بيانات فقط؛ **ليس** slug | **منخفض** لكن **خارج النطاق الحالي**؛ لا يحل عنوان أرشيف `/services/` وحده |

**بناءً على طلبك:** لا توسيع جسر KAYAN ولا تغيير آلية Rank Math الآن.

---

## 5. خيارات إضافية لـ Sitemap (للمقارنة — غير موصى بها الآن)

| الخيار | لماذا لا الآن |
|--------|----------------|
| Rank Math → Exclude Posts → `7463` | يزيل `/service/` من **page-sitemap** أيضاً |
| تغيير slug / redirect `/service/` ↔ `/services/` | **ممنوع** بشروطك |
| تعديل `guid` فقط | قد يساعد Rank Math على المدى البعيد؛ **يحتاج موافقة منفصلة**؛ Snippet أدق وأقل مساساً |

---

## 6. التزام بالقيود

- **لم يُنفَّذ** Snippet على المباشر.
- **لم يُعدَّل** القالب، الإضافات، URLs، canonical، redirect، أو الفهرسة في هذه الجولة.
- **لم يُحذف أو يُدمج** أي محتوى.

**الخطوة التالية بعد موافقتك:** نشر Snippet §2.1 عبر WPCode (أو WPVibe `code-snippet` مع موافقة)، ثم purge كاش، ثم التحقق §3.
