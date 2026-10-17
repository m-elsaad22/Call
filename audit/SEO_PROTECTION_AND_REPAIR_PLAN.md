# خطة حماية التصدر والإصلاح المحافظ — ركن التطور

**الموقع:** https://www.rukn-eltatawer.com/  
**التاريخ:** 2026-10-17  
**المرجع:** `VERIFICATION_REPORT.md` + CSVs في `audit/output/`  
**الحالة:** **تخطيط وتحليل فقط** — **لم يُنفَّذ أي إصلاح** على WordPress.

---

## مبدأ العمل (من توجيهكم)

الموقع **متصدر في Google** ولديه **قيمة تجارية وتاريخ**. الهدف: إصلاح أخطاء **مؤكدة** ب**أقل مخاطرة**، وليس «إعادة SEO من الصفر».

**بيانات Search Console / Analytics:** **غير متصلة** في هذه الجلسة — أي حديث عن «من يتصدر» أو «فقدان زيارات» **غير quantified** حتى تصدير GSC.

---

## المرحلة 1 — إعادة تصنيف المخاطر لكل توصية

| ID | الإجراء | التصنيف | السبب |
|----|---------|---------|--------|
| R1 | إصلاح canonical/لغة **61 URL** | **مرتفع — يحتاج GSC + staging** | زوج URL مزدوج؛ sitemap = **EN فقط**؛ inbound حتى **70**؛ equity غير معروف |
| R2 | 301 جماعي AR→EN | **مرتفع — لا يُنصح** | قد يدمّر ترتيب AR إن وُجد؛ ممنوع سياسياً |
| R3 | canonical جماعي | **مرتفع — لا يُنصح** | KAYAN SEO + Rank Math filters |
| R4 | **9287** مكة — حذف/410/301 | **يحتاج GSC** | off-topic لكن **أداء غير معروف** |
| R5 | noindex 9287 | **يحتاج GSC** | أقل من حذف؛ still ranking risk |
| R6 | دمج **13 hash duplicate** | **مرتفع — يحتاج GSC** | فقدان long-tail مدينة/خدمة |
| R7 | دمج **40 template cluster** | **مرتفع — لا يُنصح حالياً** | نية بحث جغرافية مختلفة |
| R8 | تعطيل **kayan-dbg** | **آمن نسبياً بعد staging** | لا اعتماد theme مثبت |
| R9 | إزالة **download-plugin** | **آمن نسبياً بعد staging** | أمن admin |
| R10 | meta أرشيف `/services/` | **آمن نسبياً** | أرشيف CPT |
| R11 | meta تصنيف `villa-cleaning` | **آمن نسبياً** | taxonomy |
| R12 | إصلاح `/service/` مكرر sitemap | **آمن نسبياً** | XML فقط |
| R13–R14 | Alt + ضغط صور | **آمن نسبياً** | لا slugs |
| R15 | روابط `/en/` نسبية | **متوسط — اختبار** | internal linking |
| R16 | Polylang/Rank Math globals | **مرتفع — ممنوع** | حسب سياسة المشروع |
| R17 | حذف `/en/` | **مرتفع — مرفوض** | verified: مرتبط بالنشاط |
| R18 | 12716 car wash | **يحتاج GSC** | مراجعة بشرية |

**ملف:** `audit/output/action_risk_classification.csv`

> **لا يوجد إجراء «آمن تماماً»** لتغييرات الفهرسة على الـ61 أو الدمج — بدون GSC.

---

## المرحلة 2 — تحليل الـ61 (Canonical)

### 2.1 ما ثبت (أدلة)

| المؤشر | القيمة |
|--------|--------|
| عدد المقالات | **61** |
| REST `link` | **`/en/{slug}/`** |
| `/{slug}/` | **HTTP 200** (مسار موازٍ) |
| canonical على AR | **يشير إلى `/en/`** (61) |
| H1 إنجليزي على AR | **59** |
| **Sitemap AR** | **0/61** |
| **Sitemap EN** | **61/61** |
| inbound internal (محتوى posts) | **0–70** لكل slug |

**استنتاج SEO محافظ:** Google يُغذَّى أساساً بـ **EN URL** في sitemap؛ canonical **متسق** مع EN. المسار AR **ليس في sitemap** لكنه **قابل للزحف** (روابط داخلية). **تغيير canonical دون معرفة أي URL يحصل على impressions = مخاطرة عالية.**

### 2.2 منطق الكود (مصدر السلوك)

```
REQUEST_URI
  → kayan_i18n_register_rewrites (setup.php)
      ^en/([^/]+)/?$ → kayan_lang=en & name=slug
      ^([^/]+)/?$    → name=slug (بدون kayan_lang)
  → pre_get_posts: kayan_i18n_resolve_localized_request (نفس post by name)
  → kayan_i18n_detect_lang_from_path / kayan_i18n_is_english()
      إذا المسار يبدأ /en/ → English
  → عند English: the_title / content filters + kayan_en_* post meta
  → kayan_seo_canonical_url() = kayan_seo_get_current_url()
      يبني URL من المسار الحالي بعد i18n normalize
  → setup.php: wp_head canonical + rank_math/frontend/canonical filter
```

**Polylang:** `pll_current_language()` **احتياطي**؛ REST `lang` غالباً فارغ — **KAYAN i18n path هو المتحكم الفعلي**.

**Rank Math:** meta title/description عبر `rank-math-bridge.php`؛ canonical **يُستبدل** بـ KAYAN عند التفعيل.

### 2.3 قرار مقترح (لكل URL — بدون تنفيذ)

**الافتراضي لجميع الـ61:** `defer_until_gsc`

| سيناريو بعد GSC | اتجاه محتمل (للموافقة لاحقاً) |
|-----------------|-------------------------------|
| impressions على **EN فقط** | الإبقاء على canonical EN؛ consider **تقليل crawl AR** (روابط داخلية) بدون 301 جماعي |
| impressions على **AR** | **استرجاع محتوى عربي** على `/{slug}/` + canonical self — **بدون تغيير slug** |
| impressions **م split** | hreflang + canonical دقيقة **per post** — ليس قالباً واحداً |

**ملف تفصيلي (61 صف):** `audit/output/canonical_61_detailed_analysis.csv`

---

## المرحلة 3 — حماية المقالات المتصدرة

### 3.1 ما يمكن حمايته الآن

| المجموعة | العدد | سياسة الحماية |
|----------|-------|----------------|
| مقالات permalink **عربي** بدون issue_codes | **~1303** | **no touch** لـ canonical/slug/redirect/merge |
| **61** dual-locale | 61 | **no bulk fix** |
| **1364** posts | كل المنشور | افتراض **protected** حتى GSC |

**ملف:** `audit/output/protected_pages_manifest.json`

### 3.2 GSC (مطلوب منكم)

Export لـ **28 يوماً** + فترة مقارنة:

- Page / Query / Clicks / Impressions / Position  
- لكل URL في `canonical_61_detailed_analysis.csv` (**122 URL** محتمل)

**بدون هذا:** لا يمكن تمييز «متصدر» عن «غير مهم» برمجياً.

---

## المرحلة 4 — التكرار (13 hash + 40 template)

| النوع | القرار الآن | السبب |
|-------|-------------|--------|
| 13 exact hash | **لا دمج** | لا GSC؛ لا backlink data |
| 40 TPL | **لا دمج** | مدن/خدمات مختلفة؛ موقع متصدر |

**بديل محافظ:** تحسين محتوى **فقط** على صفحة **بعد** prove she wins in GSC.

**ملف:** `duplicate_groups_verified.csv`

---

## المرحلة 5 — «غير مرتبط» (off-topic)

| ID | أقل مخاطرة (الآن) | ملاحظة |
|----|-------------------|--------|
| **9287** مكة | **لا شيء** حتى GSC + مراجعة بشرية | لا حذف/noindex |
| **12716** car wash | **لا شيء** | GSC + قرار offering |
| **10300/10299** fire | **تحسين copy** لاحقاً | مرتبط محتمل |
| **/en/** | **احتفاظ** | سياسة لغة فقط |

**ملف:** `off_topic_verified.csv`

---

## المرحلة 6 — الإضافات (بدون تعطيل)

| الإضافة | الوظيفة (المستنتجة) | اعتماد القالب | مخاطر | تعطيل |
|---------|---------------------|---------------|--------|--------|
| **kayan-dbg** | تشخيص fatals (اسم + سياق سابق) | **غير مثبت** في PHP المقروء | تسريب/أداء | **staging فقط** |
| **download-plugin** | تنزيل zip plugins | لا | admin RCE surface | staging |
| **GTM + GTM4WP** | تتبع | غير مؤكد ازدواج | **gtag G-RGGQTPW3KE** على homepage | مراجعة يدوية |

**لم يُعطَّل أي plugin.**

---

## المرحلة 7 — خطة تنفيذ محافظة (مقترحة)

### 7.1 يمكن البدء (بدون لمس URLs المتصدرين — منخفض المخاطر)

1. **R12** sitemap duplicate `/service/`  
2. **R13** Alt text (دفعات، رابط media ID)  
3. **R10/R11** meta archives (Rank Math — **بعد** موافقة نصوص)

### 7.2 يحتاج staging

1. **R8/R9** plugins  
2. **R14** ضغط صور  
3. أي تعديل **KAYAN PHP**

### 7.3 يحتاج GSC (قبل أي canonical/merge/9287)

1. **R1** الـ61  
2. **R6/R7** duplicates  
3. **R4/R5/R18**

### 7.4 لا يُنصح حالياً

1. 301/canonical/noindex **جماعي**  
2. Polylang/Rank Math globals  
3. حذف `/en/`

### 7.5 لكل إصلاح مستقبلي (قالب)

| بند | مطلوب |
|-----|--------|
| Rollback | backup DB + plugin snapshot |
| نجاح | GSC impressions/clicks stable ±10% على URLs المجاورة |
| فشل | revert + URL inspection |

**إن لم تتوفر staging/backup:** **لا** تغييرات R1/R6/R8 على production.

---

## المرحلة 8 — موافقات مطلوبة

| # | الإجراء | موافقة |
|---|---------|--------|
| A | تصدير GSC للـ61 + 9287 | **نعم — منكم** |
| B | أي canonical/slug/redirect | **نعم** |
| C | دمج/حذف | **نعم** |
| D | تعطيل plugins | **نعم + staging** |
| E | meta archives/categories | **نعم** |
| F | Alt/sitemap/صور | **نعم** (منخفض المخاطر) |

---

## الملفات المُنتَجة في هذه المرحلة

- `audit/SEO_PROTECTION_AND_REPAIR_PLAN.md` (هذا الملف)
- `audit/output/canonical_61_detailed_analysis.csv`
- `audit/output/action_risk_classification.csv`
- `audit/output/protected_pages_manifest.json`

---

**التالي (بعد موافقتكم):**  
1) رفع **GSC export**، أو  
2) الموافقة على **F فقط** (sitemap/alt/meta أرشيف)، أو  
3) إعداد **staging** لـ R8/R9.

**لم يُلمَس WordPress.**
