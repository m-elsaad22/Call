# تدقيق الروابط الداخلية في محتوى المقالات (إعادة فحص كاملة)

أُعيد الفحص على **1349 مقالاً** و**10 صفحات**:

1. كل روابط `<a href>` داخل محتوى ووردبريس.
2. تتبع التحويلات حتى النهاية (وليس أول قفزة فقط).
3. فتح الصفحات الحية للتأكد من عدم وجود روابط إضافية في القوالب.

**لا توجد روابط مقالات إضافية داخل المحتوى** غير القائمة أدناه. أي رابط مقال تضغطه ويذهب للرئيسية هو واحد من هذه.

| النوع | العدد |
|---|---|
| روابط مقالات مكتوبة خطأ → الرئيسية (المقال البديل موجود) | 11 |
| مقالات غير موجودة أصلاً (لم تُنشر بهذا الاسم) | 2 |
| روابط عربية قديمة تعمل عبر 301 | 5 |

---

## 1) مكتوبة خطأ → الصفحة الرئيسية (المقال موجود باسم آخر)

### 1. شركة تصميم مطابخ في دبي
- الخاطئ: `https://www.rukn-eltatawer.com/شركة-تصميم-مطابخ-في-دبي/`
- الصحيح: [شركة تصميم مطابخ في دبي](https://www.rukn-eltatawer.com/shrkh-tsmym-mtabkh-fy-dby/)
- داخل: [تركيب مطابخ دبي](https://www.rukn-eltatawer.com/shrkh-trkyb-mtabkh-fy-dby/)

### 2. شركة مكيفات في دبي
- الخاطئ: `/air-conditioner-company-in-dubai/`
- الصحيح: [شركة تركيب مكيفات في دبي](https://www.rukn-eltatawer.com/ac-companies-in-dubai/)
- داخل: تكييف مركزي دبي، تنظيف مكيفات الشارقة، تنظيف مكيفات دبي

### 3. تركيب زجاج أبوظبي (`abudhabi` بدل `abu-dhabi`)
- الخاطئ: `/glass-installation-abudhabi/`
- الصحيح: [شركة تركيب زجاج في أبوظبي](https://www.rukn-eltatawer.com/glass-installation-abu-dhabi/)
- داخل: [ألوميتال أبوظبي](https://www.rukn-eltatawer.com/aluminium-installation-abu-dhabi/)

### 4. عزل أسطح أبوظبي
- الخاطئ: `/roof-insulation-abudhabi/`
- الصحيح: [شركة عزل أسطح في أبوظبي](https://www.rukn-eltatawer.com/roof-insulation-in-abu-dhabi/)
- داخل: ألوميتال أبوظبي

### 5. عزل أسطح رأس الخيمة (`rak`)
- الخاطئ: `/roof-insulation-rak/`
- الصحيح: [شركة عزل أسطح رأس الخيمة](https://www.rukn-eltatawer.com/ras-al-khaimah-roof-insulation-company/)
- داخل: [كشف تسربات رأس الخيمة](https://www.rukn-eltatawer.com/water-leak-detection-ras-al-khaimah/)

### 6. تسليك مجاري رأس الخيمة
- الخاطئ: `/sewage-cleaning-rak/`
- الصحيح: [شركة تسليك المجاري في رأس الخيمة](https://www.rukn-eltatawer.com/ras-al-khaimah-sewage-wiring-company/)
- داخل: كشف تسربات رأس الخيمة

### 7. تسليك مجاري الشارقة
- الخاطئ: `/sewage-cleaning-sharjah/`
- الصحيح: [شركة تسليك المجاري في الشارقة](https://www.rukn-eltatawer.com/sewerage-plumbing-sharjah/)
- داخل: [كشف تسربات الشارقة](https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-sharjah/)

### 8. كشف تسربات أبوظبي
- الخاطئ: `/water-leak-detection-abudhabi/`
- الصحيح: [شركة كشف تسربات المياه في أبوظبي](https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-abu-dhabi/)
- داخل: ألوميتال أبوظبي

### 9. كشف تسربات دبي
- الخاطئ: `/water-leak-detection-company-dubai/`
- الصحيح: [شركة كشف تسربات المياه في دبي](https://www.rukn-eltatawer.com/leak-detection-company-in-dubai/)
- داخل: نفس مقال دبي (رابط داخلي خاطئ)

### 10. كشف تسربات الإمارات
- الخاطئ: `/water-leak-detection-uae/`
- أقرب منشور (إنجليزي): [Water Leak Detection in the UAE](https://www.rukn-eltatawer.com/en/water-leak-detection-company-uae/)
- دليل عربي عام: [كل ما تحتاج لمعرفته عن شركات الكشف](https://www.rukn-eltatawer.com/information-about-leak-detection-companies/)
- داخل: [كشف تسربات العين](https://www.rukn-eltatawer.com/water-leak-detection-company-in-al-ain/)

### 11. شركة ديكورات في دبي
- الخاطئ: `/شركة-ديكورات-في-دبي/`
- الصحيح: [شركة ديكورات في دبي](https://www.rukn-eltatawer.com/shrkh-dykwrat-fy-dby/)
- داخل: [شلالات ونوافير أبوظبي](https://www.rukn-eltatawer.com/waterfalls-and-fountains-in-abu-dhabi/)

---

## 2) مقالات غير موجودة — لم يُنشر مقال بهذا الاسم

### 1. `/en/plumbing-services-dubai`
- `302` → `/en`
- لا توجد صفحة إنجليزية بهذا الاسم
- أقرب عربي: [صيانة سباكة دبي](https://www.rukn-eltatawer.com/plumbing-maintenance-dubai/)
- داخل: [Water Leak Detection Dubai](https://www.rukn-eltatawer.com/en/water-leak-detection-dubai/)

### 2. `/en/sewer-cleaning-dubai`
- `302` → `/en`
- لا توجد صفحة إنجليزية بهذا الاسم
- أقرب عربي: [تسليك مجاري دبي](https://www.rukn-eltatawer.com/sewerage-plumbing-dubai/)
- داخل: نفس مقال كشف التسربات الإنجليزي

---

## روابط عربية قديمة تعمل (301 إلى مقال منشور)

ليست معطلة. الزائر يصل للمقال.

| المكتوب | يصل إلى |
|---|---|
| أهمية الكشف المبكر عن تسربات المياه… | `/importance-of-detecting-water-leaks/` |
| أبرز الأضرار التي تسببها تسربات المياه… | `/damage-caused-by-water-leaks/` |
| شركة مقاولات في دبي | `/contracting-company-in-dubai/` |
| كل ما تحتاج لمعرفته عن شركات كشف التسربات… | `/information-about-leak-detection-companies/` |
| شركة معالجة الرطوبة في دبي | `/humidity-treatment-in-dubai/` |

---

## خارج محتوى المقالات (إن ظهر أثناء التصفح)

هذه ليست روابط مقالات داخل النص، لكنها أيضاً تحوّل للرئيسية:

- أرشيف المدن `/city/…` من صفحة [المدن](https://www.rukn-eltatawer.com/cities/): أبوظبي، الشارقة، عجمان، العين، الفجيرة، رأس الخيمة، أم القيوين — كلها `302` للرئيسية. دبي فقط تعمل.
- في JSON-LD لبعض المقالات (غير قابلة للنقر): `/alumetal-installation-abudhabi/` و `/water-leak-detection-rak/` و `/glass-installation-abudhabi/`.

---

لم يُعدَّل المحتوى الحي بعد؛ لذلك الروابط أعلاه ما زالت تذهب للرئيسية عند الضغط. يمكن تصحيحها داخل المقالات في الخطوة التالية.
