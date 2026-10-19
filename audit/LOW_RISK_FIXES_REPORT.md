# تقرير الإصلاحات منخفضة المخاطر — rukn-eltatawer.com

**التاريخ (UTC):** 2026-10-17  
**النطاق:** Sitemap `/service/`، Alt للصور، Meta للتصنيفات وأرشيف `/services/`  
**لم يُمس:** الـ61 URL، المقال 9287، القالب، الإضافات، Slugs، Canonical، Redirects، إعدادات الفهرسة.

---

## 1. تكرار `/service/` في Sitemap

### التشخيص (مؤكد)

| العنصر | القيمة |
|--------|--------|
| URL | `https://www.rukn-eltatawer.com/service/` |
| كيان WordPress | **صفحة واحدة** ID **7463**، `post_type=page`، slug `service` |
| في قاعدة البيانات | صف واحد فقط (`wp3mdn_posts`) — **لا** يوجد مقال slug `service` |
| `page-sitemap.xml` | يتضمن `/service/` (صحيح) |
| `post-sitemap1.xml` | يتضمن **نفس** `/service/` (خطأ — الصفحة ليست `post`) |
| صفحة `/services/` | أرشيف CPT `services` — **URL مختلف** وغير مكرر مع `/service/` في Sitemap |

**السبب الجذري:** Rank Math يضع الصفحة 7463 في مزود sitemap منشورات (`post-sitemap`) رغم أن نوعها `page`. النتيجة: **نفس URL مرتين** (post + page sitemap)، وليس صفحتين مختلفتين.

**ما لم يُنفَّذ (حماية + قيود):**

- لا استبعاد الصفحة من Sitemap بالكامل (يُخفى URL مهم من `page-sitemap`).
- إصلاح PHP عبر WPCode يتطلب **موافقة WPVibe** (`code-snippet/dormant` → 403 `wpvibe_op_proof_missing`).
- تعديل القالب **محظور** بشروطك.

**الإصلاح المقترح (بعد موافقتك على Snippet):** فلتر `rank_math/sitemap/entry` يستبعد ID 7463 **فقط** من sitemap نوع `post` ويبقيها في `page-sitemap`.

**التراجع:** حذف/تعطيل الـ Snippet.

---

## 2. Alt text للصور

### النسخ الاحتياطي

- `audit/backups/pre_low_risk_20261017T120530Z.json` (قبل SEO + عينة 10)
- `audit/backups/pre_low_risk_20261017T120811Z.json` (قبل دفعة Alt الكاملة)

### التنفيذ

| المؤشر | قبل | بعد |
|--------|-----|-----|
| وسائط بدون `alt_text` | 160 | **0** |
| تحديثات ناجحة | — | **160** (عينة 10 + 150) |
| فشل | — | **0** |

**المنهج:** REST `POST /wp/v2/media/{id}` مع `alt_text` — وصف من عنوان الملف، سياق المقال الأب، أو تسمية عربية قصيرة (بدون حشو كلمات مفتاحية).

**سجل التغييرات:** `audit/output/low_risk_changes.jsonl` (164 سطراً يشمل 4 تغييرات Rank Math + 160 وسيطة).

**التراجع (مثال):**

```bash
wp post meta update <MEDIA_ID> _wp_attachment_image_alt '<القيمة_القديمة_من_النسخة_الاحتياطية>'
```

---

## 3. Meta Title & Description — التصنيفات و `/services/`

### ما تم تطبيقه (Rank Math — خيار `rank-math-options-titles`)

| المفتاح | قبل | بعد |
|---------|-----|-----|
| `tax_category_title` | `%term% %sep% { شركة ركن التطور }` | `%term% %sep% %sitename%` |
| `tax_category_description` | `%seo_description%` | `%term_description%` |
| `pt_services_title` | `%title% %page% %sep% %sitename%` | `خدماتنا في الإمارات %sep% %sitename%` |
| `pt_services_description` | `%excerpt%` | نص عربي ثابت يصف خدمات الشركة (≈155 حرفاً) |

**تم:** `wp cache purge` + LiteSpeed purge.

### التحقق من الواجهة

| URL | العنوان / الوصف | الحالة |
|-----|-----------------|--------|
| `/category/villa-cleaning/` | `تنظيف فلل \| ركن التطور` + وصف من نص التصنيف | **محسّن** |
| `/services/` (أرشيف CPT) | لا يزال `الخدمات Archive \| ركن التطور` | **لم يتغير على الواجهة** |

**سبب `/services/`:** KAYAN SEO يعطل واجهة Rank Math ويقرأ **`rank_math_title` / `rank_math_description` على منشور/تصنيف فقط** — أرشيف CPT لا يمر عبر قوالب `pt_services_*` في `<title>`. القوالب محفوظة في Rank Math للاستخدام لاحقاً، لكن **العنوان الحي** يأتي من WordPress (`Archive`).

**بديل منخفض المخاطر للزوار:** صفحة **`/service/`** (7463) لديها عنوان ووصف SEO قويان — لا تغيير عليها في هذه الجولة.

**لإصلاح `/services/` لاحقاً (يتطلب قراراً):** توسيع جسر KAYAN لأرشيف `services` (تعديل قالب — خارج النطاق الحالي)، أو Snippet معتمد، أو تفعيل `kayan_seo_disable` (خطر تكرار وسوم — يحتاج GSC).

---

## 4. ملخص الصفحات/الكيانات المتأثرة

- **160** مرفق وسائط (Alt فقط).
- **إعدادات Rank Math العامة** (4 مفاتيح — لا slugs/canonical/indexing).
- **36** تصنيف — استفادوا من قالب العنوان/الوصف (الوصف من حقل «وصف التصنيف» حيث وُجد).
- **لم يُعدَّل:** مقالات، slugs، canonical، redirects، 61 URL، 9287، kayan-dbg، download-plugin.

---

## 5. ما يحتاج Google Search Console

1. **الـ61 URL ثنائية اللغة** — أي URL يُفهرس فعلياً (AR vs `/en/`) قبل أي canonical.
2. **`/service/` vs `/services/` vs `/category/service/`** — الانطباعات والنقرات لكل URL.
3. **أرشيف `/services/`** — هل يجلب زيارات؟ إن كان ضعيفاً مقابل `/service/` قد لا يستحق تغييراً تقنياً عالي المخاطر.
4. **بعد إصلاح Sitemap (Snippet):** إعادة إرسال `sitemap_index.xml` ومراقبة «تم اكتشافه - حالياً غير مفهرس» / تكرار URL.
5. **تصنيفات رئيسية** (تنظيف، صيانة، …) — CTR بعد إصلاح `{ شركة ركن التطور }`.

---

## 6. الأولوية: حماية التصدر

- لم تُجرَ تغييرات على محتوى المقالات المتصدرة أو canonical/redirect.
- Alt وقوالب SEO للتصنيفات **تحسينات مساعدة**؛ مراقبة GSC 2–4 أسابيع قبل أي خطوة تالية على الـ61 أو 9287.

---

## 7. ملفات المرجع

| الملف | الغرض |
|-------|--------|
| `audit/backups/pre_low_risk_*.json` | استعادة Alt + خيارات Rank Math |
| `audit/output/low_risk_changes.jsonl` | سجل تفصيلي + أوامر rollback |
| `audit/apply_low_risk_fixes.py` | سكربت التنفيذ (قابل لإعادة التشغيل مع `SKIP_SEO=1`) |
