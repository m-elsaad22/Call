# تقرير مرحلة التحقق — ركن التطور (قبل الإصلاح)

**الموقع:** https://www.rukn-eltatawer.com/  
**التاريخ:** 2026-10-17  
**المرجع:** PR #56 + ملفات `audit/output/*`  
**الحالة:** **تحقق فقط** — لم يُ changed أي كود أو محتوى أو إعدادات.

---

## 1. ملخص تنفيذي للتحقق

| الادعاء السابق | بعد التحقق | الثقة |
|----------------|------------|--------|
| «Canonical العربي → EN **عام**» | **مقتصر على 61 مقالاً** (نفس مجموعة permalink `/en/` في REST) | **عالية** |
| «62 مقال EN = off-topic» | **مرتبطة بالنشاط** — تحتاج **سياسة لغة** لا حذف | **عالية** |
| «52 مقال off-topic (سعودية)» | **~47 false positive** — boilerplate | **عالية** |
| «kayan-dbg على الإنتاج» | **مؤكد نشط** — محتوى الملف **غير متاح** (WPVibe path guard) | **متوسطة** |
| «GTM مزدوج» | **gtag.js (G-RGGQTPW3KE)** مؤكد؛ **GTM container ID** غير ظاهر في HTML الرئيسية | **متوسطة** |

**لا تتوفر:** بيانات GSC/Analytics (نقرات، انطباعات، ترتيب).

---

## 2. Canonical — أدلة ومصدر الكود

### 2.1 النطاق (مهم)

- **1,303** مقال permalink عربي (`/{slug}/`) → **canonical ذاتي** + H1 عربي (عينة مؤكدة).
- **61** مقال permalink في REST = `/en/{slug}/` لكن **`/{slug}/` أيضاً HTTP 200**:
  - **61** `ar_url_canonical_points_en`
  - **59** `ar_url_serves_en_h1`
  - **59** الاثنان معاً

**الاستنتاج:** المشكلة **ليست موقعاً كاملاً** بل **مجموعة EN-primary** مع **مسار عربي موازٍ** يخدم غالباً **نفس المحتوى الإنجليزي**.

### 2.2 مثال مؤكد (Post 12185)

| الحقل | القيمة |
|--------|--------|
| AR URL | `https://www.rukn-eltatawer.com/water-leak-detection-dubai/` |
| Canonical | `https://www.rukn-eltatawer.com/en/water-leak-detection-dubai/` |
| H1 على AR URL | English |
| EN URL | HTTP 200 |
| REST `link` | `/en/...` |

### 2.3 مصدر Canonical (كود — قراءة فقط)

1. **`kayan-seo/helpers.php`** — `kayan_seo_canonical_url()` = `kayan_seo_get_current_url()` (مسار الطلب بعد i18n).
2. **`kayan-seo/setup.php`** — طباعة `<link rel="canonical">` + filter `rank_math/frontend/canonical`.
3. **`kayan-i18n/helpers.php`** — `kayan_i18n_detect_lang_from_path()` يعتبر `/en/` = English؛ `kayan_i18n_build_url()` يبني مسارات EN/AR.

**Polylang:** الحقل `lang` في REST غالباً **فارغ** على العينة؛ الاعتماد الفعلي على **مسار URL** (KAYAN i18n) + Rank Math.

**ترجمة:** `kayan_i18n_get_localized_url()` تربط نفس `post_name` بـ `/en/{slug}/` — **علاقة slug واحد / لغتين**، وليس بالضرورة `pll_get_post` كزوج ترجمة.

### 2.4 Canonical المتوقع (سياسة — للموافقة)

| الحالة | المتوقع |
|--------|---------|
| AR URL يعرض **محتوى عربي** | canonical = **AR URL** |
| AR URL يعرض **محتوى EN** (الوضع الحالي للـ61) | إما **301 AR→EN** أو **استرجاع محتوى عربي** على AR + canonical=self |
| صفحة `/en/` | canonical = **EN URL** + hreflang `ar`/`en` |

### 2.5 الفهرسة (ما يمكن قياسه)

- **robots:** `index, follow` على العينات — **لا noindex**.
- **GSC:** غير متصل — لا تأكيد «Crawled not indexed» per URL.

**ملف:** `audit/output/canonical_issues_verified.csv` (1364 صف) + `canonical_verification_summary.json`

---

## 3. المحتوى «غير المناسب» — إعادة تصنيف

**منهجية:** قراءة REST **كامل** لـ 66 من `off_topic_refined.csv` + **47** false positive (`confidence=منخفض`).

| التصنيف | العدد |
|---------|-------|
| مرتبط بالنشاط ويجب الاحتفاظ به | **108** |
| يحتاج مراجعة بشرية | **2** |
| مرتبط ويحتاج تحسينًا | **2** |
| غير مرتبط بالنشاط فعلًا | **1** |

### حالات خاصة

| ID | العنوان | التصنيف | السبب |
|----|---------|---------|--------|
| **9287** | كشف تسربات المياه في مكة | **غير مرتبط** | استهداف **مكة** — نشاط إماراتي (~1042 كلمة) |
| **12716** | غسيل سيارات متنقل في دبي | **مراجعة بشرية** | خدمة **غسيل سيارات** — قريبة من التنظيف، غير ضمن CPT الـ12 |
| **10300** | شركة فاير ألarm في دبي | **مرتبط — تحسين** | إنذار/سلامة — تحقق offering |
| **10299** | شركة فاير فايتنج دبي | **مرتبط — تحسين** | مكافحة حريق — slug مشوّه |
| **/en/** (61+) | EN service pages | **احتفاظ** | ليست off-topic — **canonical/hreflang** |

**ملف:** `audit/output/off_topic_verified.csv`

---

## 4. مجموعات التكرار

**53** صفاً في `duplicate_groups_verified.csv`:

| النوع | العدد | دليل الدمج |
|--------|-------|------------|
| **exact_content_hash** (13 مجموعة) | 13 | SequenceMatcher **>0.95** — **دمج مرشح** بعد اختيار keeper |
| **template_skeleton** (TPL-*) | 40 | تشابه **هيكل** — **لا دمج تلقائي** (اختلاف مدينة/خدمة) |

**TPL-1 (58 URL):** مسابح EN/UAE — **template + keyword overlap**، نية بحث **متقاربة** — تنافس داخلي **محتمل**، دمج **غير كافٍ** دون GSC.

**wall-grass / artificial-grass clusters:** **template_only** — توصية **تمييز محلي** لا 301 جماعي.

---

## 5. الإضافات

| المكوّن | الدليل | المخاطر | تعطيل آمن؟ |
|---------|--------|---------|------------|
| **kayan-dbg** | في `plugins[]` (WPVibe site-info) | debug/production | **مرجح** بعد staging — **لم يُثبت** اعتماد القالب (ملف غير مقروء) |
| **download-plugin** | نشط | أمن admin | **نعم** إن لم يُستخدم |
| **GTM/gtag** | `gtag/js?id=G-RGGQTPW3KE` في homepage | تتبع مزدوج **محتمل** | يحتاج مراجعة `google-tag-manager` + `duracelltomi-google-tag-manager` + WPCode |

**ملف:** `audit/output/plugin_risk_review.csv`

---

## 6. SEO أخرى (مؤكد / محتمل)

| الم issue | الحالة | الدليل |
|-----------|--------|--------|
| `/services/` title «Archive» | **مؤكد** | HTML |
| `/category/villa-cleaning/` meta فارغ | **مؤكد** | HTML |
| `/service/` في sitemap مرتين | **مؤكد** | technical_seo.json |
| روابط `/en/...` نسبية في محتوى عربي | **محتمل** | deep_analysis (29×) — يحتاج crawl كامل |
| 160/648 بدون Alt | **مؤكد** (REST) | عينة كاملة media |
| Post 9287 indexable | **مؤكد** | robots index |

---

## 7. ما لم يُفحص

- محتوى `/sa/` `/qa/` … `/eg/` بالتفصيل  
- Rank Math per-post focus keyword bulk  
- كود `kayan-dbg.php`  
- Core Web Vitals ميداني  

---

## 8. الملفات المُنتَجة

- `audit/VERIFICATION_REPORT.md` (هذا الملف)
- `audit/output/canonical_issues_verified.csv`
- `audit/output/off_topic_verified.csv`
- `audit/output/duplicate_groups_verified.csv`
- `audit/output/plugin_risk_review.csv`
- `audit/output/final_action_plan.csv`

---

## 9. قائمة الإصلاحات ذات الأولوية (انتظر موافقتك)

راجع **`final_action_plan.csv`** — أهم ما **ثبت**:

1. **Canonical/لغة — 61 URL** (حرجة)  
2. **Post 9287 مكة** (حرجة — محتوى)  
3. **kayan-dbg** (حرجة — تشغيل)  
4. **أرشيف services + category meta** (عالية)  
5. **13 hash duplicate groups** (متوسطة — بعد اختيار keeper)  
6. **download-plugin** (عالية — أمن)

**اختبار بعد أي إصلاح:** 61 صف canonical CSV + 5 URLs عربية «سليمة» + GSC URL inspection + عدم لمس GCC حتى اختبار منفصل.
