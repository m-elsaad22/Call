# تقرير التدقيق الشامل — موقع ركن التطور (قراءة فقط)

**النطاق:** https://www.rukn-eltatawer.com/ (و www)  
**تاريخ التدقيق:** 2026-10-17  
**الحالة:** تحليل فقط — **لم يُنفَّذ أي تعديل** على الملفات أو قاعدة البيانات أو الإعدادات.

---

## 1. ملخص تنفيذي

موقع **ركن التطور** منصة WordPress كبيرة (أكثر من **1,360** مقال خدمة/مدينة) مبنية على **قالب KAYAN** مع **Rank Math** و **Polylang** و **LiteSpeed Cache** و **Google Site Kit**. المحتوى المنشور **مرتبط بشكل عام** بخدمات الصيانة والمقاولات في الإمارات، لكن هناك **نمط إنتاج واضح** (صفحات مدينة × خدمة) يسبب **تشابهاً هيكلياً عالياً** وتنافساً داخلياً — وهذا يتوافق مع تقارير Search Console («تم الزحف ولم تُفهرس»).

**أهم المخاطر المؤكدة (بدون تنفيذ إصلاح):**

| الأولوية | الم issue | الدليل |
|----------|-----------|--------|
| حرجة | **Canonical العربي → `/en/`** على عينة مقالات | `water-leak-detection-dubai/` → canonical `/en/...` |
| حرجة | **صفحة مكة** خارج النطاق الجغرافي | Post **9287** — مفهرسة |
| حرجة | **kayan-dbg** (debug) نشط على الإنتاج | قائمة الإضافات |
| عالية | **62** مقال `/en/` + **40** cluster تشابه قالب (حتى **58** URL/cluster) | CSV + deep_analysis |
| عالية | أرشيفات/تصنيفات **meta ضعيف** | `/services/`, `/category/villa-cleaning/` |
| متوسطة | **~25%** وسائط بدون Alt | 160/648 |
| متوسطة | **13** مجموعة **تطابق hash** للمحتوى | article_audit_full.csv |

**لا تتوفر في هذا التدقيق:** بيانات GSC/Analytics (نقرات، انطباعات، ترتيب). أي ربط بالأداء الفعلي يحتاج تصديراً من Site Kit أو Search Console.

---

## 2. ما تم فحصه فعلياً وما تعذر

### تم فحصه

- **REST API** (Application Password): جميع المقالات المنشورة (**1,364**) + **10** صفحات + CPTs (**services, faqs, reviews, pricing, portfolio, before-after**) — **محتوى HTML كامل** لكل عنصر.
- **التصنيفات:** categories (36), tags (100), cities (8), service_categories (1).
- **الوسائط:** 648 ملف — Alt + حجم الملف.
- **Sitemap Rank Math:** 10 sub-sitemaps، **1,412** URL فريد.
- **robots.txt** + عينة **SEO front-end** (title, meta, canonical, H1, schema).
- **قالب KAYAN (قراءة):** `functions.php`, `components/packs/kayan-cpt/setup.php`, `components/packs/kayan-seo/setup.php` عبر WPVibe (POST `/file/read`).
- **حالة HTTP** لصفحات كانت **5xx** سابقاً: **200** وقت الفحص.

### تعذر أو محدود

| البند | السبب |
|--------|--------|
| **MySQL مباشرة** | غير متاح — الاعتماد على REST |
| **Rank Math meta لكل مقال** | لم يُجلب bulk (meta fields) — يحتاج تمريرة API إضافية |
| **كود كل الإضافات** | WPVibe يقيّد المسارات خارج القالب (`path_traversal` لـ plugins) |
| **Core Web Vitals قياس حقلي** | لم يُشغَّل Lighthouse/PageSpeed API — ملاحظات نظرية + أحجام صور |
| **مراجعة تحريرية بشرية لـ 1,364 مقال** | تم **قراءة المحتوى آلياً** + قواعد — الثقة **متوسطة** للحالات الحدية |

---

## 3. إحصائيات الموقع (أعداد حقيقية من المصدر)

| النوع | العدد |
|--------|-------|
| مقالات `post` — publish | **1,364** |
| مقالات — draft | **1** (ID 12640) |
| صفحات — publish | **10** |
| CPT `services` | **12** |
| CPT `faqs` | **14** |
| CPT `reviews` | **1** |
| CPT `pricing` | **1** |
| CPT `portfolio` | **1** |
| CPT `before_after` | **1** |
| وسائط `media` | **648** |
| تصنيفات `category` | **36** |
| وسوم `tag` | **100** (حد REST) |
| taxonomy `cities` | **8** |
| taxonomy `service_categories` | **1** |
| عناصر قائمة (menu-items) | **35** |
| URLs في Sitemap | **1,412** فريد (+1 مكرر `/service/`) |

**خدمات CPT (12 عنواناً — من REST):** كشف تسربات، عزل أسطح/خزانات، تسليك مجاري، مكيفات، تنظيف/تعقيم، مكافحة حشرات، مسابح، حدائق، باركيه، جبس، عازل صوت، صيانة مباني.

**Sitemap دول (robots):** `/sa/`, `/qa/`, `/kw/`, `/om/`, `/bh/`, `/eg/` — **لم يُفحص محتوى كل locale بالتفصيل** في هذا التقرير (التركيز على النطاق الرئيسي www).

---

## 4. أهم المشكلات الحرجة والعالية

1. **Canonical متضارب (AR vs EN)** — مؤكد على `water-leak-detection-dubai/`: الصفحة العربية تعرض canonical للنسخة الإنجليزية. يفسر «Crawled — currently not indexed» لكثير من URLs العربية.
2. **Post 9287 — كشف تسربات مكة** — نشاط الشركة الإماراتي؛ الصفحة **index** و schema كاملة.
3. **kayan-dbg** — إضافة debug على الإنتاج (مرتبطة سابقاً بـ TEMP KAYAN Debug Fatal).
4. **تشابه قوالب المدينة** — أكبر cluster **58** URL (غالباً محتوى مسابح EN/UAE).
5. **أرشيف `/services/`** — title/description غير تسويقية («Archive»).
6. **Download Plugin** — إضافة عالية الخطورة إن بقيت مفعّلة دون حاجة.

---

## 5. تدقيق القالب والبرمجة

**القالب:** KAYAN Theme **1.4.28** — بنية **components/packs** بدلاً من `single.php` / `page.php` الكلاسيكية (WPVibe يبلغ عن «missing» — **تصميم مقصود** وليس بالضرورة خطأ).

**CPT (`kayan-cpt/setup.php`):**  
`services`, `reviews`, `faqs`, `pricing`, `portfolio`, `before_after` — **public + has_archive + REST** — متوافق مع Rank Math indexing.

**SEO (`kayan-seo/setup.php`):**  
- `kayan_seo_print_canonical` في `wp_head`  
- Filters على `get_canonical_url` و **`rank_math/frontend/canonical`**  
→ **تداخل مباشر** مع Rank Math + Polylang — **مرشح رئيسي** لمشاكل الفهرسة.

**الإضافات النشطة (من site-info):**  
TinyMCE Advanced, Classic Editor/Widgets, Easy TOC, **Rank Math**, **Polylang**, **LiteSpeed Cache**, **Site Kit**, GTM + GTM4WP, WPCode, **vibe-ai**, **kayan-dbg**, **download-plugin**.

**ملاحظات أمان (تحقق فقط):**  
- عدم تنفيذ اختراق — **Download Plugin** + debug plugin يستحقان المراجعة.  
- `robots.txt` يحتوي `Disallow: /*.php$` — قد يؤثر على بعض crawlers (ملاحظة — ليست 5xx).

---

## 6. تدقيق SEO التقني (عينة + sitemap)

| URL | status | ملاحظة |
|-----|--------|--------|
| `/` | 200 | index, schema, OG — جيد |
| `/services/` | 200 | Title «الخدمات Archive» — ضعيف |
| `/pricing/` | 200 | index — title emoji-heavy |
| `/water-leak-detection-dubai/` | 200 | **canonical → /en/** |
| `/flea-control-dubai/` | 200 | canonical self — جيد |
| `/water-leak-detection-makkah-center/` | 200 | **محتوى جغرافي خاطئ** |
| `/category/villa-cleaning/` | 200 | **meta description فارغ** |
| `/pricing/`, `/services/` (سابق 5xx) | **200** | لا 5xx وقت الفحص |

**Sitemap:** تطابق تقريبي مع عدد المنشورات + صفحات + CPTs؛ **تكرار واحد** لـ `/service/`.

**robots.txt:** Allow `/` + sitemaps متعددة اللغات/الدول.

---

## 7. تحليل المقالات «غير المناسبة للنشاط» (مرحلة ثانية + ثالثة)

### منهجية

- **قراءة محتوى كل** post/page/CPT منشور (**1,404** صف audit row).
- **تصنيف آلي** + **تصفية ثانية** (`off_topic_refined.csv`) لتقليل الإ positives.

### نتائج التصفية الثانية (66 URL — **مراجعة بشرية مطلوبة**)

| السبب | تقريباً |
|--------|---------|
| مسارات **`/en/`** (Polylang/EN) | **~62** |
| **مكة** | **1** (9287) |
| **غسيل سيارات متنقل** | **1** (12716) |
| **فاير/إنذار** (slugs transliterated) | **2** |

**تحذير:** التصنيف الآلي الأول أ flagged **52** URL كـ «خارج الإمارات» بسبب ذكر «السعودية» في **نصوص boilerplate** — **47** على الأرجح **false positive** (انظر `article_audit_refined.csv` — confidence منخفض). **لا تعتمد على العمود الأول دون `refined_review_reason`.**

### محتوى «مناسب لكن ضعيف/مكرر»

- **61** مقال **< 250 كلمة**.
- **Median ~1,382** كلمة للمقالات — الإنتاج **طويل** لكن **متشابه الهيكل**.
- **992** صف flagged «مناسب لكنه مكرر» في التصنيف الآلي — **معظمه** بسبب **تكرار كلمات العنوان** وليس تطابقاً حرفياً؛ **13** مجموعة **hash متطابق** (26 مقال) — **أولوية دمج/301**.

---

## 8. جدول المقالات — أين تجد التفاصيل

| الملف | المحتوى |
|--------|---------|
| `audit/output/article_audit_full.csv` | **1,404** صف — **20** عموداً + fingerprint |
| `audit/output/article_audit_refined.csv` | + `refined_review_flag`, `template_cluster` |
| `audit/output/off_topic_refined.csv` | **66** URL للمراجعة |
| `audit/output/action_plan.csv` | خطة معالجة مرتبة |

**أعمدة الجدول** (في CSV): Post ID, URL, نوع المحتوى, الخدمة/المدينة المستهدفة, التوافق, الجودة, SEO issues, الإجراء المقترح, الأولوية, الثقة, …

---

## 9. جدول التكرار والتداخل

| النوع | العدد | ملف |
|--------|-------|-----|
| تطابق hash كامل | **13** مجموعة (**26** posts) | `duplicate_group` في CSV |
| تشابه قالب (≥4 URLs) | **40** cluster | `duplicate_template_clusters.csv` |
| أكبر cluster | **TPL-1: 58** (مسابح EN/UAE) | نفس الملف |
| cluster عشب/جدران | **TPL-2: 15**, **TPL-3: 12** | wall-grass / artificial-grass |

**مرشح احتفاظ:** الأقوى في GSC (يحتاج بيانات) أو الأقدم + الأكثر روابط داخلية (`deep_analysis.json` — روابط uploads مكررة أكثر من روابط خدمات).

---

## 10. صفحات المدن والخدمات

- **نمط slug:** `{service}-{city}` — يغطي **7–8** emirates + **Al Ain**.
- **مشكلة مؤكدة:** استبدال اسم المدينة مع **نفس الهيكل** (clusters أعلاه) — **ليس خطأً تلقائياً** لكن يحتاج **تمييز محلي** (أسعار، أحياء، زمن استجابة) لتجنب soft duplicate.
- **صفحة مكة:** استثناء جغرافي — **إجراء فوري مقترح** بعد موافقتك.

---

## 11. الصور والوسائط

| المؤشر | القيمة |
|---------|--------|
| إجمالي | **648** |
| بدون `alt_text` | **160** (**24.7%**) |
| filesize > 500KB | **52** |

**روابط داخلية شائعة:** صور uploads مكررة في مئات المقالات (نفس `20250420_203319` ×94) — **ليس خطأ** لكن يدل على **قالب صور موحد**.

---

## 12. الروابط الداخلية و UX (عينة)

- **روابط `/en/...` نسبية** داخل محتوى عربي (مثال: `/en/pool-company-uae/` ×29) — خطر locale/404.
- **الصفحة الرئيسية** واضحة: H1 + schema + CTA (عينة HTML).
- **فحص شامل لكل أزرار واتساب/اتصال** — لم يُ automatize بالكامل؛ يُوصى بفحص يدوي على الجوال.

---

## 13. بيانات خارجية مطلوبة

1. **Google Search Console** — تصدير: Coverage + URLs لـ 195 صفحة من ملف `Problem` + impressions/clicks.
2. **Analytics** — landing pages + bounce.
3. **Rank Math** — تصدير focus keyword لكل post (CSV).
4. **موافقة على سياسة EN/AR** — هل `/en/` للفهرسة أم consolidation؟

---

## 14. خطة المعالجة (ملخص)

راجع **`audit/output/action_plan.csv`** — **17** بنداً مع: evidence, risks, verification, **needs_approval=نعم** لكل إجراء تغيير.

**ترتيب مقترح بعد الموافقة:**  
1) Canonical/hreflang (KAYAN SEO + Rank Math)  
2) مكة + thin outliers  
3) تعطيل debug/download plugin  
4) taxonomy/archive meta  
5) دمج clusters duplicate hash  
6) تمييز محلي city pages  
7) Alt + ضغط صور  

---

## 15. إجراءات تحتاج موافقتك قبل التنفيذ

- أي تغيير **canonical / hreflang / Polylang**  
- **حذف / 301 / 410 / noindex** لأي URL  
- **دمج** مقالات city/service  
- **تعطيل** plugins (kayan-dbg, download-plugin)  
- **تعديل** قالب KAYAN أو Rank Math globals  
- **لمس** مسارات `/sa`, `/qa`, `/kw`, `/om`, `/bh`, `/eg`  

---

## ملحق: مقارنة REST vs Sitemap

| المصدر | Count |
|--------|-------|
| Posts publish | 1,364 |
| Pages + CPTs publish | ~40 |
| Sitemap URLs | 1,412 |

الفارق الطبيعي: تصنيفات، أرشيفات، صفحات Polylang، pagination sitemap.

---

*تم إنتاج هذا التقرير آلياً مع فحص REST + WPVibe + crawlers — **لا يُعتبر بديلاً عن مراجعة تحريرية و GSC** للقرارات الحساسة.*
