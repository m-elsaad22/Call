# تدقيق الروابط الداخلية في محتوى المقالات

فحصنا محتوى **1349 مقالاً منشوراً** و**10 صفحات** على [rukn-eltatawer.com](https://www.rukn-eltatawer.com/). استخرجنا **1367 رابطاً داخلياً** شبيهاً بمقالات (بدون `tel` / واتساب / تصنيفات / مرفقات).

**النتيجة:** لا توجد روابط ترجع **404**. الروابط المعطوبة كلها تُحوَّل إلى الصفحة الرئيسية لأن الـ slug مكتوب خطأ أو لأن المقال الإنجليزي غير منشور.

| النوع | العدد |
|---|---|
| روابط خاطئة تصل للرئيسية (ولها مقال بديل منشور) | 11 |
| روابط تشير إلى مقالات غير موجودة أصلاً | 2 |
| روابط عربية قديمة تعمل عبر 301 إلى مقال منشور | 5 |

---

## 1) روابط تشير إلى مقالات أخرى لكنها مكتوبة خطأ → الصفحة الرئيسية

هذه الروابط مقصودة كمقالات داخلية، لكن المسار غير مطابق لأي مقال منشور، فيحوّلها ووردبريس إلى `/` أو `/en`.

### 1. شركة تصميم مطابخ في دبي (رابط عربي مُرمَّز)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/%D8%B4%D8%B1%D9%83%D8%A9-%D8%AA%D8%B5%D9%85%D9%8A%D9%85-%D9%85%D8%B7%D8%A7%D8%A8%D8%AE-%D9%81%D9%8A-%D8%AF%D8%A8%D9%8A/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة تصميم مطابخ في دبي](https://www.rukn-eltatawer.com/shrkh-tsmym-mtabkh-fy-dby/)
- يظهر داخل: [شركة تركيب مطابخ في دبي](https://www.rukn-eltatawer.com/shrkh-trkyb-mtabkh-fy-dby/)

### 2. شركة مكيفات في دبي (slug قديم)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/air-conditioner-company-in-dubai/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة تركيب مكيفات في دبي](https://www.rukn-eltatawer.com/ac-companies-in-dubai/)
- يظهر داخل:
  - [تركيب وصيانة التكييف المركزي في دبي](https://www.rukn-eltatawer.com/central-air-conditioning-installation-and-maintenance-in-dubai/)
  - [شركة تنظيف مكيفات في الشارقة](https://www.rukn-eltatawer.com/air-conditioner-cleaning-in-sharjah/)
  - [شركة تنظيف مكيفات في دبي](https://www.rukn-eltatawer.com/shrkh-tnzyf-mkyfat-fy-dby/)

### 3. تركيب زجاج أبوظبي (`abudhabi` بدل `abu-dhabi`)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/glass-installation-abudhabi/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة تركيب زجاج في أبوظبي](https://www.rukn-eltatawer.com/glass-installation-abu-dhabi/)
- يظهر داخل: [تركيب ألوميتال أبوظبي](https://www.rukn-eltatawer.com/aluminium-installation-abu-dhabi/)

### 4. عزل أسطح أبوظبي (`abudhabi` بدل المسار الصحيح)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/roof-insulation-abudhabi/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة عزل أسطح في أبوظبي](https://www.rukn-eltatawer.com/roof-insulation-in-abu-dhabi/)
- النسخة الإنجليزية إن لزم: [Roof Insulation Abu Dhabi](https://www.rukn-eltatawer.com/en/roof-insulation-abu-dhabi/)
- يظهر داخل: [تركيب ألوميتال أبوظبي](https://www.rukn-eltatawer.com/aluminium-installation-abu-dhabi/)

### 5. عزل أسطح رأس الخيمة (`rak` مختصر غير مستخدم)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/roof-insulation-rak/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة عزل أسطح رأس الخيمة](https://www.rukn-eltatawer.com/ras-al-khaimah-roof-insulation-company/)
- يظهر داخل: [كشف تسربات رأس الخيمة](https://www.rukn-eltatawer.com/water-leak-detection-ras-al-khaimah/)

### 6. تسليك مجاري رأس الخيمة (`sewage-cleaning-rak`)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/sewage-cleaning-rak/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة تسليك المجاري في رأس الخيمة](https://www.rukn-eltatawer.com/ras-al-khaimah-sewage-wiring-company/)
- يظهر داخل: [كشف تسربات رأس الخيمة](https://www.rukn-eltatawer.com/water-leak-detection-ras-al-khaimah/)

### 7. تسليك مجاري الشارقة (`sewage-cleaning` بدل `sewerage-plumbing`)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/sewage-cleaning-sharjah/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة تسليك المجاري في الشارقة](https://www.rukn-eltatawer.com/sewerage-plumbing-sharjah/)
- يظهر داخل: [كشف تسربات الشارقة](https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-sharjah/)

### 8. كشف تسربات أبوظبي (`abudhabi` بدل `abu-dhabi`)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/water-leak-detection-abudhabi/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة كشف تسربات المياه في أبوظبي](https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-abu-dhabi/)
- يظهر داخل: [تركيب ألوميتال أبوظبي](https://www.rukn-eltatawer.com/aluminium-installation-abu-dhabi/)

### 9. كشف تسربات دبي (اسم slug مختلف)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/water-leak-detection-company-dubai/`
- الحالة: `302` → الصفحة الرئيسية
- المقال الصحيح المنشور: [شركة كشف تسربات المياه في دبي](https://www.rukn-eltatawer.com/leak-detection-company-in-dubai/)
- يظهر داخل: نفس مقال دبي أعلاه (رابط داخلي خاطئ داخل المقال نفسه)

### 10. كشف تسربات الإمارات (ناقص كلمة `company`)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/water-leak-detection-uae/`
- الحالة: `302` → الصفحة الرئيسية
- أقرب مقال منشور (إنجليزي): [Water Leak Detection in the UAE](https://www.rukn-eltatawer.com/en/water-leak-detection-company-uae/)
- ملاحظة: لا يوجد مقال عربي بنفس عنوان «كشف تسربات الإمارات». المقال العربي العام القريب: [كل ما تحتاج لمعرفته عن شركات كشف التسربات](https://www.rukn-eltatawer.com/information-about-leak-detection-companies/)
- يظهر داخل: [كشف تسربات العين](https://www.rukn-eltatawer.com/water-leak-detection-company-in-al-ain/)

### 11. شركة ديكورات في دبي (رابط عربي خام)

- الرابط المكتوب: `https://www.rukn-eltatawer.com/شركة-ديكورات-في-دبي/`
- الحالة: بعد ترميز الرابط `302` → الصفحة الرئيسية (الطلب غير المرمَّز يفشل تقنياً)
- المقال الصحيح المنشور: [شركة ديكورات في دبي](https://www.rukn-eltatawer.com/shrkh-dykwrat-fy-dby/)
- يظهر داخل: [شلالات ونوافير أبوظبي](https://www.rukn-eltatawer.com/waterfalls-and-fountains-in-abu-dhabi/)

---

## 2) روابط تشير إلى مقالات غير موجودة في الموقع

هذان المساران ليسا خطأ إملائياً لمقال منشور: **لا توجد صفحة إنجليزية منشورة** بهذا الاسم، لذلك التحويل يذهب إلى الرئيسية الإنجليزية `/en`.

### 1. سباكة دبي — النسخة الإنجليزية غير منشورة

- الرابط المكتوب: `https://www.rukn-eltatawer.com/en/plumbing-services-dubai`
- الحالة: `302` → `/en`
- لا يوجد مقال إنجليزي `plumbing-services-dubai`
- أقرب مقال عربي منشور: [شركة صيانة سباكة في دبي](https://www.rukn-eltatawer.com/plumbing-maintenance-dubai/)
- يظهر داخل: [Water Leak Detection Dubai](https://www.rukn-eltatawer.com/en/water-leak-detection-dubai/)

### 2. تسليك مجاري دبي — النسخة الإنجليزية غير منشورة

- الرابط المكتوب: `https://www.rukn-eltatawer.com/en/sewer-cleaning-dubai`
- الحالة: `302` → `/en`
- لا يوجد مقال إنجليزي `sewer-cleaning-dubai`
- أقرب مقال عربي منشور: [شركة تسليك المجاري في دبي](https://www.rukn-eltatawer.com/sewerage-plumbing-dubai/)
- يظهر داخل: [Water Leak Detection Dubai](https://www.rukn-eltatawer.com/en/water-leak-detection-dubai/)

---

## روابط عربية قديمة تعمل (ليست معطلة)

هذه كُتبت بمسار عربي قديم، لكن الموقع يحوّلها **301** إلى مقال إنجليزي-slug منشور. الزائر يصل للمقال، لذلك **لا تُحسب ضمن الروابط المعطلة**. يمكن استبدالها لاحقاً بالمسار النهائي لتحسين السيو.

| الرابط المكتوب (عربي قديم) | يصل إلى |
|---|---|
| أهمية الكشف المبكر عن تسربات المياه… | [importance-of-detecting-water-leaks](https://www.rukn-eltatawer.com/importance-of-detecting-water-leaks/) |
| أبرز الأضرار التي تسببها تسربات المياه… | [damage-caused-by-water-leaks](https://www.rukn-eltatawer.com/damage-caused-by-water-leaks/) |
| شركة مقاولات في دبي | [contracting-company-in-dubai](https://www.rukn-eltatawer.com/contracting-company-in-dubai/) |
| كل ما تحتاج لمعرفته عن شركات كشف التسربات… | [information-about-leak-detection-companies](https://www.rukn-eltatawer.com/information-about-leak-detection-companies/) |
| شركة معالجة الرطوبة في دبي | [humidity-treatment-in-dubai](https://www.rukn-eltatawer.com/humidity-treatment-in-dubai/) |

تظهر داخل: كاميرات دبي، صيانة غرف التبريد، النمل الأبيض دبي، تركيب مطابخ دبي.

---

## جدول التصحيح السريع

| الرابط الخاطئ | الرابط الصحيح |
|---|---|
| `/شركة-تصميم-مطابخ-في-دبي/` | `/shrkh-tsmym-mtabkh-fy-dby/` |
| `/air-conditioner-company-in-dubai/` | `/ac-companies-in-dubai/` |
| `/glass-installation-abudhabi/` | `/glass-installation-abu-dhabi/` |
| `/roof-insulation-abudhabi/` | `/roof-insulation-in-abu-dhabi/` |
| `/roof-insulation-rak/` | `/ras-al-khaimah-roof-insulation-company/` |
| `/sewage-cleaning-rak/` | `/ras-al-khaimah-sewage-wiring-company/` |
| `/sewage-cleaning-sharjah/` | `/sewerage-plumbing-sharjah/` |
| `/water-leak-detection-abudhabi/` | `/a-water-leak-detection-company-in-abu-dhabi/` |
| `/water-leak-detection-company-dubai/` | `/leak-detection-company-in-dubai/` |
| `/water-leak-detection-uae/` | `/en/water-leak-detection-company-uae/` أو المقال العربي العام |
| `/شركة-ديكورات-في-دبي/` | `/shrkh-dykwrat-fy-dby/` |
| `/en/plumbing-services-dubai` | غير موجود — أقرب: `/plumbing-maintenance-dubai/` |
| `/en/sewer-cleaning-dubai` | غير موجود — أقرب: `/sewerage-plumbing-dubai/` |

لم نعدّل المحتوى الحي. إذا أردت، يمكن تصحيح هذه الروابط داخل المقالات في خطوة لاحقة.
