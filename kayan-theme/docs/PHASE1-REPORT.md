# تقرير Phase 1 — KAYAN Theme

**الإصدار:** `2027.3.0`  
**الفرع:** `cursor/phase1-seo-perf-cda3`  
**التاريخ:** 2026-06-07

---

## 1. الملفات المعدّلة

### ملفات جديدة
| الملف | الغرض |
|-------|--------|
| `components/packs/kayan-seo/frontend.php` | طبقة SEO موحّدة عبر `wp_head` |
| `components/packs/kayan-seo/schema-extensions.php` | Reviews، Team/Person، ساعات العمل |
| `components/packs/#footer/js/setup-carousel.js` | Owl Carousel (مفصول من setup.js) |
| `components/packs/#footer/js/setup-lazy.js` | Lazy loading للوسائط (مفصول من setup.js) |
| `docs/PHASE1-REPORT.md` | هذا التقرير |

### ملفات معدّلة
| الملف | التغيير الرئيسي |
|-------|-----------------|
| `kayan-seo/setup.php` | تحميل frontend + schema-extensions |
| `kayan-seo/helpers.php` | canonical للأرشيفات، hreflang حقيقي فقط |
| `kayan-seo/schema-output.php` | reviews/team على الرئيسية، Person للمؤلف |
| `kayan-seo/dashboard.php` | استبدال `get_posts(-1)` بـ COUNT SQL |
| `kayan-seo/frontend.php` | تعطيل Rank Math/Yoast frontend، og:type |
| `theme-seo/setup.php` | إيقاف Title عند تفعيل KAYAN SEO |
| `#header/part.php` | لا title/meta مكرر؛ تخطي CSS/FA على v3 |
| `Breadcrumb/setup.php` | إزالة microdata عند modern schema |
| `Enqueues/setup.php` | whitelist للسكربتات، تحميل شرطي، إصلاح `disable_all_scripts` |
| `#footer/js/setup.js` | تقليص الحجم (فصل carousel/lazy) |
| `#Posts/part.php` | إعادة استخدام `WP_Query` بدل `get_posts` مكرر |
| `functions.php` | cache static لـ `yc_get_option()` |
| `kayan-homepage/setup.php` | preconnect، تقليل أوزان الخطوط |
| `kayan-homepage/assets/kayan-home.js` | loader أسرع، particles أقل |
| `kayan-performance/helpers.php` | preconnect للخطوط |
| `kayan-performance/setup.php` | defer لسكربتات غير حرجة |
| `schema/setup.php` + `LocalBusiness.php` | `https://schema.org` |
| `style.css` | إصدار `2027.3.0` |

---

## 2. المشاكل التي تم حلها

### 2.1 توحيد SEO
- **مصدر واحد** لـ Title / Meta / Canonical / OG / Twitter عند تفعيل KAYAN SEO (`frontend.php` + `helpers.php`).
- **Rank Math:** التخزين يبقى؛ إخراج الـ frontend يُعطَّل عبر `rank_math/frontend/disable`.
- **Theme SEO:** `ThemeSeo->Title()` لا يُخرج `<title>` عند تفويض KAYAN SEO.
- **#header:** لا يُكرّر meta/title يدوياً عند `$kayan_seo_active`.
- **منع الازدواجية** مع Yoast عند وجوده.

### 2.2 Schema
| المشكلة | الحل |
|---------|------|
| Breadcrumb مزدوج | microdata يُزال من HTML عند modern schema؛ JSON-LD فقط من `kayan-seo` |
| `http://schema.org` | تحويل إلى `https://` في legacy + JSON-LD الحديث |
| `og:type` خاطئ | `article` للمقالات، `website` للصفحات، `profile` للمؤلف |
| canonical الأرشيفات | `get_pagenum_link()` + term links في `kayan_seo_get_canonical_url()` |
| hreflang وهمي | يُخرج فقط عند ≥2 بدائل في `kayan_seo_hreflang_alternates` |
| مراجعات | `Review` + `AggregateRating` على الرئيسية |
| Team/Person | `Person` من CPT `team` إن وُجد؛ مؤلف المقال كـ Person |

### 2.3 الأداء
- إزالة تحميل CSS/FA/خطوط legacy على **Homepage v3**.
- Font Awesome **مرة واحدة** على v3 عبر `kayan-home-fa`.
- تقسيم `setup.js` → `setup-carousel.js` + `setup-lazy.js` + نواة أصغر.
- `disable_all_scripts` لا يحذف سكربتات القالب ولا يعمل على v3.
- Owl Carousel يُحمَّل فقط خارج وضع `IsSpeed()`.

### 2.4 Core Web Vitals (الأسباب والإصلاح)

| المقياس | السبب المحدد | الإصلاح |
|---------|--------------|---------|
| **LCP** | Loader 900ms يؤخر ظهور المحتوى؛ خطوط ثقيلة؛ عدم preconnect | Loader → 350ms؛ أوزان خط أقل؛ preconnect لـ Google Fonts و CDN |
| **CLS** | صور بدون أبعاد ثابتة في بعض القوالب القديمة | `fetchpriority=high` + `loading=eager` للصورة البارزة؛ preload LCP عبر `kayan_perf` |
| **INP** | `setup.js` ضخم (~60KB) يُنفَّذ دفعة واحدة؛ particles كثيرة | فصل carousel/lazy؛ defer للسكربتات غير الحرجة؛ تقليل particles 26→12 |

### 2.5 الاستعلامات
- `yc_get_option()` / `yc_update_option()` / `yc_delete_option()` — **cache static**.
- `dashboard.php` — `wp_count_posts()` + COUNT SQL بدل `get_posts(-1)`.
- `#Posts/part.php` — استعلام `WP_Query` واحد بدل count + `get_posts`.

---

## 3. مقارنة قبل / بعد (تقديرية)

| المؤشر | قبل Phase 1 | بعد Phase 1 (تقدير) |
|--------|-------------|---------------------|
| مصادر SEO في `<head>` | 2–3 (Theme + KAYAN + Rank Math) | **1** (KAYAN SEO) |
| Breadcrumb Schema | HTML microdata + JSON-LD | **JSON-LD فقط** |
| `get_posts(-1)` في لوحة SEO | 4+ استعلامات كاملة | **0** (COUNT) |
| `#Posts` لكل قائمة | 2 استعلامات | **1** استعلام |
| حجم JS الصفحة الرئيسية v3 | jQuery + Owl + setup كامل | **kayan-home.js فقط** (~5KB) |
| Loader الرئيسية | 900ms | **350ms** |
| Particles Hero | 26 عنصر | **12** عنصر |

### تقدير Core Web Vitals (Mobile — Lighthouse)

| المقياس | قبل (تقدير) | بعد (تقدير) | ملاحظة |
|---------|-------------|-------------|--------|
| LCP | 3.2–4.5s | **2.0–2.8s** | يعتمد على الاستضافة والصور |
| CLS | 0.12–0.25 | **0.05–0.10** | يحتاج قياس حي |
| INP | 250–400ms | **150–220ms** | تحسين JS defer/split |
| Performance Score | 55–70 | **75–88** | بدون CDN إضافي |

> **ملاحظة:** الأرقام تقديرية بناءً على التحليل الثابت للكود. القياس الفعلي يتطلب PageSpeed Insights على بيئة الإنتاج.

---

## 4. المشاكل المتبقية (خارج نطاق Phase 1)

| # | المشكلة | الأولوية |
|---|---------|----------|
| 1 | `export-import/extract.php` ما زال يستخدم `posts_per_page => -1` | متوسطة (أداة admin) |
| 2 | CPT `team` غير مُسجَّل على `main` — schema الفريق يعمل عند إضافة CPT لاحقاً | منخفضة |
| 3 | `setup.js` النواة ما زالت ~58KB (minified) — يحتاج إعادة هيكلة تدريجية | متوسطة |
| 4 | Homepage v3 HTML ثابت (`body.html.php`) — لا يقرأ من إعدادات القالب | مقصود حالياً |
| 5 | تعارض محتمل إذا عُطّل KAYAN SEO وترك Rank Math frontend مفعّلاً بدون تنسيق | منخفضة |
| 6 | لا يوجد اختبار PHPUnit/E2E آلي للقالب | للمرحلة القادمة |
| 7 | Modules (Track, CRM, AI, Programmatic SEO) — **لم تُنفَّذ** حسب الطلب | Phase 2+ |

---

## 5. ما لم يُنفَّذ (حسب تعليماتك)

- ❌ Modules جديدة  
- ❌ CRM  
- ❌ AI Features  
- ❌ Programmatic SEO  

---

## 6. خطوات التحقق الموصى بها

1. تفعيل KAYAN SEO → View Source: عنوان واحد، canonical واحد، لا Rank Math meta.
2. صفحة مقال: `og:type=article`، breadcrumb JSON-LD بدون `itemscope` في HTML.
3. الرئيسية v3: لا jQuery/Owl في Network؛ `kayan-home.js` + CSS فقط.
4. Query Monitor: لا `get_posts` بـ `-1` في frontend/dashboard.
5. Rich Results Test على الرئيسية: LocalBusiness + Reviews + WebSite.

---

*تم إعداد التقرير تلقائياً ضمن Phase 1 — KAYAN Theme v2027.3.0*
