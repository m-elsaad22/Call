# Phase 1.7 — Stabilization & Production Alignment

**الإصدار:** `2027.3.7`  
**الفرع:** `cursor/phase1-7-stabilization-cda3`  
**الهدف:** جعل سلوك الإنتاج مطابقاً لمعمارية Phase 1 — بدون CRM / AI / Service×City.

---

## TASK 1 — Deployment Consolidation

### حالة الفروع / PRs

| العنصر | الحالة في GitHub | على الإنتاج (rukn-eltatawer.com) |
|--------|------------------|----------------------------------|
| **PR #11** Phase 1 SEO/Perf | ❌ **غير مدمج** في `main` | ❌ غير منشور |
| **PR #10** KAYAN Track | ❌ غير مدمج | ❌ غير منشور (تتبع legacy فقط) |
| **PR #7** Homepage v3 | ✅ مدمج في `main` | ❌ لا يعمل (slider legacy ما زال يظهر) |
| **PR #12** Phase 1.5 validation | تقرير فقط | — |
| **admin-mobile** `cursor/admin-mobile-cda3` | ❌ غير مدمج | غير معروف |
| **homepage-v3** `cursor/homepage-v3-cda3` | متقدم عن main (v2027.4.x widgets) | غير منشور |

### ملفات legacy ما زالت تُحمَّل على الإنتاج

| المسار | الدليل من HTML الإنتاج |
|--------|------------------------|
| `components/packs/#footer/js/jquery-3.4.1.min.js` | ✅ محمّل |
| `components/packs/#footer/js/owl.carousel.min.js` | ✅ محمّل |
| `components/packs/#footer/js/setup.js` | ✅ محمّل (كامل) |
| `YourColorWidgets/.../slider_intro_v1.php` | ✅ `intro-model-slider_intro_v1` |
| `#header/part.php` | ✅ header legacy كامل |
| Inline `kayan-tracking-js` | ✅ (خارج المستودع — snippet) |
| Inline `rukn_track_*` | ✅ (خارج المستودع — snippet) |

### الحزم المطلوبة بعد النشر

`kayan-seo`, `kayan-homepage`, `kayan-performance`, `kayan-stabilization`, `kayan-track`, `Enqueues`

---

## TASK 2 — إزالة ازدواجية SEO (مصادر + إصلاح)

### خريطة المصادر (من تحليل HTML الإنتاج)

| المخرج | المصدر | Hook | Priority | الترتيب |
|--------|--------|------|----------|---------|
| meta/OG #1 | **KAYAN SEO** `kayan_seo_render_head_meta()` | `wp_head` | **2** | بعد title |
| meta/OG #2 | **Rank Math** `RankMath\Frontend\Head` | `wp_head` / `rank_math/head` | **1** | يتداخل |
| `<title>` | KAYAN `kayan_seo_output_document_title_tag` | `wp_head` | **0** | أولاً |
| JSON-LD #1 | Rank Math `class="rank-math-schema"` | `rank_math/json_ld` | — | — |
| JSON-LD #2 | Snippet خارجي (Organization بسيط) | plugin/snippet | — | — |
| JSON-LD #3 | **KAYAN** `kayan_seo_output_schema_graph` | `wp_head` | **2** | — |
| JSON-LD #4+ | Legacy `YourColor__Schema::insert__schema` | `wp_head` | **10** | عند تفعيل legacy |
| Breadcrumb HTML | `Breadcrumb/setup.php` | template | — | microdata |
| Breadcrumb JSON-LD | Rank Math + KAYAN | `wp_head` | — | مكرر |

### الإصلاحات في `2027.3.7`

| الملف | الإجراء |
|-------|---------|
| `kayan-seo/compatibility.php` | تعطيل Rank Math frontend بالكامل (filters + remove actions) |
| `kayan-seo/frontend.php` | طبقة موحدة (موجودة) |
| `#header/part.php` | لا meta عند `$kayan_seo_active` |
| `theme-seo/setup.php` | لا `<title>` عند KAYAN SEO |
| `schema/setup.php` | لا `insert__schema` عند modern schema |
| `Breadcrumb/setup.php` | إزالة microdata عند modern schema |
| `kayan-seo/schema-output.php` | `static $rendered` لمنع graph مزدوج |

### الهدف بعد النشر

| العنصر | العدد |
|--------|-------|
| Title | 1 |
| Description | 1 |
| Canonical | 1 |
| OG set | 1 |
| Breadcrumb schema | 1 (JSON-LD فقط) |
| JSON-LD graph | 1 |

**إجراء يدوي على الإنتاج:** إزالة snippet Organization (`sameAs:[]`) من Code Snippets / plugin.

---

## TASK 3 — توحيد Tracking

### الوضع الحالي على الإنتاج

| النظام | النوع | الحالة |
|--------|-------|--------|
| `kt_*` admin-ajax | inline script | خارج المستودع |
| `rukn_track_click` / `rukn_track_pv` | inline script | خارج المستودع |
| `_rsa_sid` pageview | inline script | خارج المستودع |
| **KAYAN Track** `tracker.js` + REST | theme pack | **مضاف في 1.7** |

### الإصلاح في `kayan-stabilization/tracking.php`

- عند تفعيل KAYAN Track: إلغاء `wp_ajax_*` للـ legacy actions
- `ob_start` لإزالة inline scripts (`kayan-tracking-js`, `rukn_track_*`, `_rsa_sid`)
- مصدر واحد: `components/packs/kayan-track/assets/tracker.js` → `/kayan/v1/track`

### التحقق المطلوب بعد النشر

- [ ] نقرة WhatsApp → صف واحد في `wp_kayan_conversions`
- [ ] نقرة Call → نفس الجدول
- [ ] Form submit → conversion
- [ ] Session → `wp_kayan_sessions`
- [ ] DNI → `kayan_dni_enabled` + قواعد في admin

---

## TASK 4 — تفعيل Homepage v3

### السبب الجذري على الإنتاج

1. ثيم قديم على السيرفر (قبل Phase 1)
2. LiteSpeed cache يخدم HTML قديم
3. `is_front_page()` فقط (لم يغطِ `is_home()` + posts)
4. `@index/shape.php` يعمل قبل takeover في بعض المسارات

### الإصلاحات

| الملف | الإجراء |
|-------|---------|
| `kayan-homepage/setup.php` | `is_front_page()` **أو** `is_home()` + posts |
| `kayan-stabilization/homepage.php` | `template_redirect` priority **0** + `exit` |
| `@index/shape.php` | `return` مبكر إذا v3 نشط |
| `Enqueues/setup.php` | لا jQuery/Owl على v3 |

### التحقق بعد النشر

HTML يجب أن يحتوي: `kayan-homepage-v3`, `kayan-home.js` — **بدون** `intro-model-slider_intro_v1`.

---

## TASK 5 — Cache Validation

`kayan-stabilization/cache.php`:

- `litespeed_purge_all()` عند تبديل الثيم
- `wp_cache_flush()`
- حذف transients `kayan_*`
- `kayan_deploy_stamp` يقارن `style.css` Version ويفرغ عند التغيير

**بعد الرفع:** LiteSpeed → Purge All → Hard refresh.

---

## TASK 6 — Production Audit (Before — 2026-06-18)

**مصدر:** `curl` + Lighthouse CLI mobile + `docs/validation-scripts/analyze-head-seo.php`  
**لقطة HTML:** `docs/validation-artifacts/html-www.rukn-eltatawer.com-2026-06-18.html`

| المقياس | الرئيسية `/` |
|---------|--------------|
| Lighthouse Perf | **75** |
| Lighthouse SEO | **92** (penalized by duplicates) |
| LCP | **5.3s** |
| CLS | **0** |
| TBT | **50ms** |
| INP | غير متاح (Lighthouse 12) |
| TTFB | **570ms** |
| meta description | **2** (سطر 5 + 21) |
| canonical | **2** (سطر 6 + 23) |
| OG title/desc/url | **2** لكل |
| JSON-LD blocks | **3** (Rank Math + snippet + KAYAN) |
| Homepage markup | **legacy** `intro-model-slider_intro_v1` |
| Tracking inline | **3** (`kayan-tracking-js`, `rukn_track_*`, `_rsa_sid`) |
| LiteSpeed | `X-LiteSpeed-Cache-Control: public,max-age=604800` |

### After (متوقع بعد نشر `2027.3.7` + purge)

| المقياس | تقدير |
|---------|-------|
| LCP | **2.0–2.8s** (v3 بدون Owl/jQuery) |
| CLS | **0** (يُحافظ عليه) |
| TTFB | يعتمد الاستضافة (~500–700ms) |
| meta/canonical/OG | **1** لكل |
| JSON-LD | **1** graph |
| Unused JS | **−118 KiB** على الرئيسية |

**إعادة القياس:** بعد النشر شغّل `docs/validation-scripts/analyze-head-seo.php` + Lighthouse.

---

## قائمة نشر (Ops)

1. دمج PR Phase 1.7 على `main`
2. رفع `kayan-theme-2027.3.7.zip` أو git pull على السيرفر
3. تفعيل KAYAN SEO + KAYAN Track من إعدادات القالب
4. حذف Code Snippets: `kayan-tracking-js`, `rukn_track_*`, `_rsa_sid`
5. LiteSpeed Purge All
6. Rich Results Test + Lighthouse mobile
7. Query Monitor: تأكيد استعلام واحد لقوائم `#Posts`

---

## ما لم يُنفَّذ (حسب الطلب)

- ❌ CRM / AI / Service×City / Master Dashboard
