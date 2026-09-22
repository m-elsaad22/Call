# FINAL PRE-PUBLISH AUDIT — Batch 1

**التاريخ:** 21 سبتمبر 2026  
**الفرع:** `cursor/mobile-car-wash-dubai-batch-1-e30e`  
**الحيّ:** لم يُلصق شيء. الـ Hub ما زال 404.  
**هذه الجولة:** تدقيق فقط. لا Merge، لا Deploy، لا Publish، لا تعديل إنتاج.

التحقق من المسودات في Git + رؤوس الصفحات الحيّة (GET) في 21 سبتمبر 2026.

---

## PASS

### Hub (مسودة)

| الفحص | النتيجة |
|---|---|
| URL النهائي | `/mobile-car-wash-dubai/` — `permalink` = `https://www.rukn-eltatawer.com/mobile-car-wash-dubai/` |
| نوع المحتوى | `post` — ليس CPT. لا مسار `/services/mobile-car-wash-dubai/` (404 حيّ، وممنوع في الملاحظات) |
| H1 | `غسيل سيارات متنقل في دبي` عبر عنوان المقال (`wordpress_post_title` / `h1`). الجسم بلا `<h1>` حتى لا يتكرر H1 القالب |
| SEO Title | `غسيل سيارات متنقل في دبي \| عند المنزل والمكتب \| ركن التطور` — بلا «الإعلان للإيجار» وبلا رقم مصري |
| Canonical | `https://www.rukn-eltatawer.com/mobile-car-wash-dubai/` |
| Robots | `index, follow` |
| الأسعار | الثلاث فقط: **60** (`<del>` 90) / **90** (`<del>` 120) / **349** (`<del>` 500). لا باقة رابعة. لا Offer schema |

الحيّ: `/mobile-car-wash-dubai/` = **404** (متوقع قبل اللصق).

### Steam (مسودة Git — post 8564)

| الفحص | النتيجة |
|---|---|
| URL | `/steam-car-wash-dubai/` دون تغيير / دون redirect / canonical ذاتي |
| JSON-LD في المسودة | لا `application/ld+json` في `steam-car-wash-dubai/content.html` |
| SEO Title المسودة | `غسيل سيارات بالبخار في دبي \| متنقل عند موقعك \| ركن التطور` — بلا اللاحقة |
| النية | H1 + focus + H2 عن البخار/المقصورة. الغسيل العام يُحوَّل إلى الـ Hub. البخار «ليس باقة سعر رابعة» |

### روابط داخلية (href في المسودات)

| الرابط | الحيّ | الاستخدام |
|---|---|---|
| `/steam-car-wash-dubai/` | 200 ذاتي | Hub → Supporting |
| `/hourly-cleaning-maids-dubai/` | 200 ذاتي | Hub + فقرة إدراج |
| `/office-cleaning-dubai/` | 200 ذاتي | Hub + فقرة إدراج |
| `/garage-cleaning-dubai/` | 200 ذاتي | Hub + فقرة إدراج |
| `/city/dubai/` | 200، canonical ذاتي | Hub فقط |
| `/mobile-car-wash-in-abu-dhabi/` | 200 ذاتي | Hub + جملة اختيارية |
| `/mobile-car-wash-dubai/` | 404 حتى النشر | Steam + فقرات الإدراج |
| `/services/mobile-car-wash-dubai/` | 404 | **غير مستخدم** في أي `href` |
| `/services/mobile-car-wash/` | ما زال يفتح صفحة أبوظبي | **غير مستخدم**؛ لا يُلمس التحويل |

لا روابط مكسورة إلى CPT. فقرات الإدراج جملة واحدة لكل صفحة. Steam يشير إلى الـ Hub فقط (5 مرات، كلها نفس الصفحة الأم).

### Schema (المسودات)

- Rank Math هو المصدر الوحيد المخطط له (Organization / Breadcrumb / WebPage).
- FAQ ظاهر في HTML للصفحتين؛ التعليمات: بلوك Rank Math FAQ بنفس النص — بلا `FAQPage` يدوي.
- لا LocalBusiness دبي، لا Rating/Review/Offer/AggregateRating، لا بيانات شركة مخترعة.
- لا JSON-LD Article/Service داخل الجسم.

### أمان SEO القائم (هذا الفرع مقابل `main`)

14 ملفاً فقط تحت `content/mobile-car-wash-dubai/` و`reports/mobile-car-wash-dubai/`.  
لا `robots.txt`، لا قوالب، لا permalinks، لا تحويلات، لا هيدر/فوتر/أزرار عائمة، لا CPT `/services/`، لا مقالات غير ذات صلة.

`robots.txt` الحيّ لم يُمس (Allow `/` + Disallow المعتاد + sitemaps).

### Taxonomy

- `cities=dubai` (term 2874) للـ **Hub فقط**.
- `/city/dubai/` يبقى نفس URL وcanonical (`https://www.rukn-eltatawer.com/city/dubai/`). القالب لا يُعدَّل. بعد النشر تُضاف بطاقة `.svc` ثانية فقط.
- Steam **غير** مسند: `GET /wp-json/wp/v2/cities?post=8564` = `[]`.
- وصف التصنيف اختياري ومنفصل؛ ليس منشوراً ثانياً.

### صور

- لا `<img>` في Hub أو Steam.
- لا صور مولَّدة/مخزن.
- TODOs موثّقة. إزالة media 3143 تخص صفحة البخار فقط عند اللصق — ليس محتوى غير ذي صلة.

### Git

لا Merge / Deploy / Publish / إنتاج. نفس الفرع `cursor/mobile-car-wash-dubai-batch-1-e30e`.

---

## WARNINGS

1. **الحيّ ≠ المسودة (Steam).** الصفحة المنشورة ما زالت تحمل `<title>` الملوّث و**كتلتي** JSON-LD. المسودة نظيفة؛ التنظيف يتم فقط عند اللصق اليدوي (Overwrite SEO Title + حذف JSON-LD الداخلي + إزالة الصورة 3143).
2. **H1 يعتمد على حقل عنوان المقال** لا على الجسم. إن تغيّر عنوان الووردبريس أو أُلصق `<h1>` إضافي يظهر H1 خاطئ/مكرر.
3. **SEO Title فارغ عند أول حفظ** يسقط على قالب Posts العام. يجب لصق `rank_math_title` قبل الحفظ.
4. **جدول الأسعار نفسه على صفحة البخار** (بطلب الاتساق). النية معلنة أنها ليست SKU رابعاً، لكن التداخل التجاري قائم. العنوان يحتوي أيضاً «متنقل عند موقعك».
5. **تغطية الإمارة:** «الخدمة متاحة داخل إمارة دبي» و«نغطي إمارة دبي كمنطقة خدمة» بينما `TODO: CONFIRM_DUBAI_SERVICE_AREAS` ما زال مفتوحاً.
6. **مزيج الطلبات (غير موثّق):** «كثير من طلبات دبي تأتي من مواقف المكاتب والأبراج لا من الفلل فقط» — لا رقم/مصدر. لم يُستبدل النص.
7. **لغة طريقة البخار عامة** (حرارة/رطوبة/ماء أقل/تقليل روائح) بلا معدات مؤكدة. لا مواصفات جهاز.
8. **«بالمعدات والمواد اللازمة»** في مقدمة الـ Hub — عامة، بلا قائمة معدات.
9. **فقرات الإدراج** (hourly / office / garage، وأبوظبي اختياري) تعدّل مقالات قائمة **عند اللصق فقط**. ليست في الإنتاج الآن. وصف `/city/dubai/` اختياري.
10. **FAQ schema** لن يصدر من `<dl>` وحده. يحتاج بلوك Rank Math FAQ بعد اللصق.

لا ساعات عمل معلنة. لا عدد عملاء. لا تقييمات/نجوم. لا جوائز/شهادات. لا ضمان لهذه الخدمة. بدون ماء / اشتراك / أساطيل غير مدرجة + TODO.

---

## TODO BEFORE PUBLISH

تنفيذ يدوي في ووردبريس بعد موافقتك — ليس من هذا الفرع:

1. إنشاء Hub كـ **Draft** `post`، slug `mobile-car-wash-dubai`، عنوان المقال = H1 حرفياً.
2. ملء Rank Math Title / Description / Canonical / robots من `hub/rank-math.json` **قبل أول حفظ**.
3. لصق `hub/content.html` (HTML) بلا `<h1>` وبلا JSON-LD.
4. إسناد تصنيف الخدمات + `cities=dubai` (2874). لا تُسند البخار.
5. استبدال جسم post **8564** بالمسودة. **لا تغيّر** slug/URL/canonical.
6. استبدال Rank Math Title للبخار (إزالة «الإعلان للإيجار» والرقم المصري). لا تفرّغ الحقل.
7. حذف JSON-LD المخصص من محرر البخار الحيّ.
8. إزالة الصورة البارزة 3143 من البخار. لا ترفع مخزن. لا تلمس ميديا صفحات أخرى.
9. نسخ أسئلة FAQ الظاهرة إلى بلوك Rank Math FAQ في الصفحتين.
10. view-source: `<title>` وcanonical وrobots قبل أي Publish.
11. فقرات الإدراج الثلاث (والجمل الاختيارية) موافقة لصق منفصلة إن رغبت بتأجيلها.
12. لا Batch 2 مواقع حتى `CONFIRM_DUBAI_SERVICE_AREAS`.

---

## EXACT FILES CHANGED

مقابل `main` — إضافات مسودة/تقرير فقط:

```
content/mobile-car-wash-dubai/README.md
content/mobile-car-wash-dubai/wordpress-publish-notes.md
content/mobile-car-wash-dubai/pricing.json
content/mobile-car-wash-dubai/hub/content.html
content/mobile-car-wash-dubai/hub/rank-math.json
content/mobile-car-wash-dubai/steam-car-wash-dubai/content.html
content/mobile-car-wash-dubai/steam-car-wash-dubai/rank-math.json
content/mobile-car-wash-dubai/internal-links/README.md
content/mobile-car-wash-dubai/internal-links/hourly-cleaning-maids-dubai.html
content/mobile-car-wash-dubai/internal-links/office-cleaning-dubai.html
content/mobile-car-wash-dubai/internal-links/garage-cleaning-dubai.html
content/mobile-car-wash-dubai/internal-links/mobile-car-wash-in-abu-dhabi.html
content/mobile-car-wash-dubai/internal-links/city-dubai-term-description.html
reports/mobile-car-wash-dubai/BATCH-1-IMPLEMENTATION.md
reports/mobile-car-wash-dubai/FINAL-PRE-PUBLISH-AUDIT.md
```

لا ملفات قالب أو إنتاج أخرى.

---

## EXACT CONTENT STILL REQUIRING MY CONFIRMATION

| البند | الحالة في المسودة | مطلوب منك |
|---|---|---|
| أسعار 60 / 90 / 349 (مقارنة 90 / 120 / 500) | مطبّقة | إقرار نهائي أنها للتنفيذ |
| `CONFIRM_DUBAI_SERVICE_AREAS` | TODO ظاهر؛ النص يقول «إمارة دبي» دون أحياء | هل تُنشر التغطية العامة أم تُضيَّق؟ |
| جملة «كثير من طلبات دبي تأتي من مواقف المكاتب والأبراج…» | موجودة بلا مصدر | إبقاؤها / حذفها / استبدالها بواقع تشغيلي |
| `CONFIRM_TOWER_PARKING_ACCESS_RULES` | TODO؛ التنفيذ «بعد تأكيد الدخول» | قواعد الأبراج إن وُجدت |
| `CONFIRM_WATER_POWER_REQUIREMENTS` | TODO؛ بلا ادعاء equip | ماء/كهرباء/معدات |
| `CONFIRM_WATERLESS_SERVICE` | غير مدرج | نعم/لا |
| `CONFIRM_MONTHLY_PLANS` | غير مدرج | نعم/لا |
| `CONFIRM_FLEET_SERVICE` | غير مدرج | نعم/لا |
| `ADD_REAL_MOBILE_CAR_WASH_IMAGES` | لا صورة | صور تشغيل حقيقية أو نشر بلا صورة بارزة |
| `ADD_REAL_STEAM_CAR_WASH_IMAGE` | إزالة 3143 بلا بديل | صورة بخار حقيقية أو بلا صورة |
| فقرات hourly / office / garage | مسودة جملة واحدة | لصق الآن أو تأجيل |
| جملة أبوظبي | اختيارية | لصق أو ترك |
| وصف مصطلح `/city/dubai/` | اختياري | لصق أو ترك |
| عنوان/وصف Rank Math المقترحان | في JSON | اعتماد النص أو تعديله قبل اللصق |

لا اختراع بدائل. بعد إقرارك: اللصق كـ Draft فقط، ثم موافقة نشر منفصلة.
