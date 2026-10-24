# تقرير تدقيق SEO — كلاستر غسيل السيارات المتنقل في دبي

**الموقع الحي:** https://www.rukn-eltatawer.com/  
**النطاق المطلوب:** غسيل سيارات متنقل في دبي (Topic Cluster على الدومين الحالي)  
**التاريخ:** 21 سبتمبر 2026  
**الحالة:** تدقيق فقط — **لم يُنفَّذ أي تعديل على الموقع الحي ولا على الصفحات الحالية**  
**الحكم التنفيذي:** التنفيذ ممكن، لكن **ليس** عبر URL المقترح `/services/mobile-car-wash-dubai/`، وليس قبل تأكيد أن الخدمة تُقدَّم فعلياً في دبي وبأي باقات.

هذا التقرير يستجيب لشرط المرحلة 0 والمرحلة 24: الموقع الحالي أهم من الخدمة الجديدة. أي تنفيذ بعد هذا الملف يحتاج موافقة صريحة.

---

## 0) خلاصة القرار (اقرأ هذا أولاً)

| القرار | التوصية |
|---|---|
| هل نبني كلاستراً جديداً؟ | نعم، **بعد** تأكيد التشغيل في دبي. النية التجارية موجودة في السوق بقوة والمنافسون يغطونها. |
| هل نستخدم `/services/mobile-car-wash-dubai/`؟ | **لا كخيار أول.** مسار `/services/{slug}/` لـ CPT الخدمات معطوب على الإنتاج (404 أو 301 إلى مقالات غير ذات صلة). `/services/mobile-car-wash/` يحوّل أصلاً 301 إلى مقال أبوظبي. |
| البنية الصحيحة | **مقال WordPress (post)** على النمط التشغيلي الحالي: `/{slug}/` + تصنيف `الخدمات` + وسوم + قالب KAYAN للمقالات + Rank Math. |
| الصفحة الأم المقترحة | `/mobile-car-wash-dubai/` (غير موجودة، 404 حالياً، غير موجودة في السايت ماب). |
| هل ننشئ صفحة بخار جديدة؟ | **لا.** الصفحة موجودة: `/steam-car-wash-dubai/`. تُعاد كتابتها كصفحة داعمة، لا تُستنسخ. |
| هل ننشئ 15 صفحة مواقع دفعة واحدة؟ | **لا.** شرط منع Doorway Pages. Batch 2 بعد تأكيد التغطية الفعلية لكل حي. |
| أكبر خطر SEO | تكرار قالب تنظيف المنازل على كلمات السيارات (هذا حاصل الآن في صفحة البخار)، وعناوين Rank Math «الإعلان للإيجار»، وSchema مكرر. |
| هذا المستودع GitHub | **لا يحتوي قالب ووردبريس.** يحتوي نماذج HTML وCSV. النشر يتم على ووردبريس الحي (مقالات)، لا بإعادة بناء الثيم. |

**شرط الإيقاف (المرحلة 24):** لن ألمس permalinks، robots.txt، عناوين Rank Math الجماعية، Schema العامة، الهيدر/الفوتر/الأزرار العائمة، أو أي صفحة خدمة قائمة لمجرد إدخال الكلاستر.

---

## 1) Current Architecture — كيف يعمل الموقع حالياً؟

الموقع الحي ووردبريس **7.1.1** على LiteSpeed + PHP 8.3، النطاق الأساسي `https://www.rukn-eltatawer.com` (تحويل من non-www).

### الثيم

- القالب النشط: **`kayan-theme`** تحت `/wp-content/themes/kayan-theme/` — **قالب مخصص، ليس Child Theme**.
- طبقتان ظاهرتان:
  1. واجهة رئيسية حديثة (`#hdr`، packs مثل `kayan-booking` و`kayan-price-pay` و`kayan-i18n` و`kayan-track`).
  2. قشرة مقالات/أرشيف أقدم (قوائم، تقييمات، load-more) ما زالت تُستخدم في صفحات الخدمات التجارية.
- إضافات ظاهرة من الواجهة العامة: Rank Math، Polylang (`/en/`)، LiteSpeed، WP Vibe 1.16.5، Kayan DNI (`/wp-json/kayan/v1/dni`).
- ACF: **غير معرّض** في REST (`acf: null`, `meta: []` على CPT الخدمات). المحتوى يعيش داخل جسم المنشور HTML، لا حقول مخصصة ظاهرة.

### أنواع المحتوى

| النوع | REST | المسار المعلن | الواقع على الإنتاج |
|---|---|---|---|
| `post` | `/wp/v2/posts` | `/{slug}/` | **قناة الترتيب الفعلية** (~1354 مقالاً). صفحات غسيل السيارات الحالية من هذا النوع. |
| `page` | `/wp/v2/pages` | `/{slug}/` | 10 صفحات مؤسسية (`/about/`, `/contact-us/`, `/service/`, `/cities/`, `/pricing/`…). |
| `services` CPT | `/wp/v2/services` | `/services/{slug}/` | 12 خدمة أم. **أغلب الصفحات المفردة 404 أو 301 تخميني من ووردبريس إلى مقال مدينة.** |
| `reviews`, `faqs`, `pricing`, `portfolio`, `before_after` | CPT | مسارات CPT | مخزون شبه فارغ (غالباً عنصر واحد لكل نوع). |
| taxonomy `cities` | `/city/{slug}/` | `/city/dubai/` يعمل 200. معظم الإمارات الأخرى ذُكر سابقاً أنها soft-404/تحويل. |
| taxonomy `service_categories` | — | عنصر واحد فقط («كشف تسربات المياه»). |

### نظام المدن مقابل المقالات

صفحات الإمارات/المدن **ليست** Location CPT ناضجاً. النمط التشغيلي للترتيب هو:

```
مقال post = خدمة + إمارة
مثال: /steam-car-wash-dubai/
      /mobile-car-wash-in-abu-dhabi/
      /office-cleaning-dubai/
      /garage-cleaning-dubai/
```

`/city/dubai/` أرشيف تصنيف ضعيف (H1 = «دبي») ويعرض بطاقة صيانة عامة فقط. عدد المقالات المرتبطة بـ `cities=dubai` عبر REST = **1**. بمعنى: تصنيف المدن غير مستخدم على مقالات الغسيل الحالية.

### الهيدر / الفوتر / CTA

- أزرار عائمة: واتساب + اتصال، مع DNI كيان: `phone/wa_number = 971521300019` بينما Schema والأزرار العامة تستخدم `+971586634710`.
- فوتر يحتوي `wa.me/201151481000` (رقم مصري) قبل تشغيل سكربت الهيدر.
- مقالات الخدمات تشغّل `kayan-booking.js` و`kayan-price-pay.js` — نموذج الحجز **مدعوم في نظام المقال** لا يحتاج مكتبة جديدة.
- `/contact-us/` بلا نموذج فعّال في التدقيق السابق.

### هذا المستودع (`m-elsaad22/Call`)

ملفات HTML نموذجية (`home.html`, `service-single.html`, `service-city.html`…) وCSV لمقالات عزل/تسليك. **ليست** سورس الثيم الحي. أي تنفيذ للكلاستر = إنشاء/تحديث منشورات ووردبريس + مسودات محتوى هنا، بدون إعادة بناء Architecture.

---

## 2) Existing Service System — كيف تُنشأ صفحات الخدمات؟

ثلاث طبقات، واحدة فقط ترتّب اليوم:

### أ) CPT `services` (الطبقة المعطوبة)

أمثلة معلنة في REST:

- `/services/cleaning-and-sterilization/` → **404**
- `/services/water-leak-detection/` → **301** إلى `/en/water-leak-detection-abu-dhabi/`
- `/services/pest-control/` → **301** إلى `/pest-control-company-in-ajman/`
- `/services/mobile-car-wash/` → **301** إلى `/mobile-car-wash-in-abu-dhabi/`

ووردبريس يخمن الـ slug ويعيد التوجيه. إدخال خدمة جديدة تحت `/services/` يعرّضها لنفس التصادم.

صفحة الأرشيف `/services/` تعمل 200 (عنوان: `الخدمات Archive | ركن التطور`). صفحة `/service/` تعمل 200 وتعرض بطاقات الـ 12 خدمة الأم في الواجهة.

### ب) مقالات `post` (الطبقة الحقيقية)

- قالب المقال الافتراضي (ليس `template` مخصصاً في REST).
- Rank Math يتحكم في Title / robots / canonical / JSON-LD.
- تصنيف ظاهر فيbreadcrumb: **الخدمات** → `/category/service/`.
- وسوم: `دبي`, `خدمة متنقلة`, `سيارات`, `غسيل سيارات بالبخار`… الوسم **`غسيل سيارات متنقل` موجود لكن count = 0**.
- CSS/JS: ملفات KAYAN العامة + packs الحجز/الأسعار. لا يوجد CSS خاص بغسيل السيارات اليوم.

### ج) النماذج في GitHub

`service-single.html` / `service-city.html` واجهات تصميم، وليست ما يُعرض على الدومين.

**طريقة إنشاء صفحة الخدمة الجديدة (بعد الموافقة):** منشور `post` عربي، slug إنجليزي قصير على نمط الموقع، تصنيف الخدمات، وسم سيارات + دبي، صورة بارزة حقيقية، عنوان Rank Math يدوي (لا يرث قالب الإيجار)، محتوى HTML داخل المحرر بنفس نظام المقالات الحالية. لا Child Theme جديد ولا CPT جديد في Batch 1.

---

## 3) SEO System — Rank Math / Meta / Schema

### ما يولّده Rank Math على المقال الحي (عينة `/steam-car-wash-dubai/`)

- `robots`: `follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large`
- `canonical`: self-referencing صحيح
- JSON-LD `@graph`: `Place` + `HomeAndConstructionBusiness/Organization` + `WebSite` + `ImageObject` + `BreadcrumbList` + `WebPage` + `Person`
- Organization: الاسم «شركة ركن التطور»، هاتف `+971586634710`، إيميل `m@rukn-eltatawer.com`، `sameAs` فيسبوك/تويتر، **العنوان PostalAddress فارغ**
- الإحداثيات في schema الرئيسية: **24.4539, 54.3773 (أبوظبي)** — متسقة مع عنوان MBZ، **ليست دبي**

### Schema إضافي داخل المقال (مخصص، ليس من إعداد Rank Math العام)

كتلة ثانية: `Article` + `LocalBusiness` + `Service`.  
هذا **تكرار LocalBusiness** مع جراف Rank Math. أي Schema جديد للكلاستر يجب أن يمر عبر Rank Math أو كتلة واحدة غير متعارضة. **ممنوع** إضافة JSON-LD ثالث.

لا يوجد `FAQPage` رغم وجود أسئلة في الصفحة — Rank Math FAQ غير مفعّل أو الأسئلة ليست في بلوك FAQ.

### Breadcrumb الحالي لصفحة البخار

`Home > الخدمات > غسيل سيارات بالبخار في دبي`

مناسب. الصفحة الأم الجديدة يجب أن تدخل نفس السلسلة لا سلسلة CPT `/services/`.

### Titles (خطر موروث)

عناوين Rank Math لصفحات السيارات المفهرسة حالياً:

| URL | Title الحي |
|---|---|
| `/steam-car-wash-dubai/` | `غسيل سيارات بالبخار في دبي 📞 01151481000 📢 الإعلان للإيجار` |
| `/mobile-car-wash-in-abu-dhabi/` | `شركة غسيل سيارات متنقل في أبوظبي 📞 01556644443 📢 الإعلان للإيجار` |
| `/car-polishing-abu-dhabi/` | نفس قالب الإيجار |
| `/garage-cleaning-dubai/` | نمط مختلف + رقم 0502161046 |

قالب العنوان الجماعي ملوّث. **لن نغيّره عالمياً.** الصفحات الجديدة تأخذ Title يدوي في Rank Math per-post. إصلاح العناوين القديمة للسيارات يمكن أن يكون مهمة منفصلة بموافقة، لأنه يمس CTR لصفحات قائمة.

### Sitemap / robots

- المقالات داخل `post-sitemap*.xml` (صفحة البخار في `post-sitemap3.xml`، أبوظبي المتنقل في `post-sitemap7.xml`).
- `/mobile-car-wash-dubai/` **غير موجود** في السايت ماب.
- `robots.txt` يسمح بـ `/` ويمنع `wp-admin`. خرائط SA/QA/KW/OM/BH/EG مذكورة — **لا نلمس robots.txt** لهذه المهمة.
- Permalinks: لا تغيير.

### Shortcodes

لا shortcode ظاهر خاص بغسيل السيارات. الحجز عبر packs KAYAN (`data-rukn-wa`, أزرار tel/wa، `kayan-booking`). أي نموذج يجب أن يستخدم النظام الحالي لا Contact Form 7 (غير موجود في REST).

---

## 4) Recommended Car Wash Architecture

```
[Hub]  /mobile-car-wash-dubai/          ← الصفحة الأم (post)
  ├─ Supporting commercials (posts)
  │    /steam-car-wash-dubai/           ← موجودة: إعادة كتابة فقط
  │    /car-wash-at-home-dubai/         ← Conditionally
  │    /mobile-car-interior-cleaning-dubai/
  │    /waterless-car-wash-dubai/       ← فقط إذا الخدمة حقيقية
  │    /mobile-car-polishing-dubai/
  │    /mobile-car-wash-dubai-prices/   ← فقط بأسعار حقيقية أو جدول TODO واضح
  │
  ├─ EN (لاحقاً، ليس Batch 1)
  │    /en/mobile-car-wash-dubai/       ← Polylang بعد إصلاح طبقة EN
  │
  ├─ Sibling (لا تُدمج)
  │    /mobile-car-wash-in-abu-dhabi/
  │    /steam-car-wash-abu-dhabi/
  │    /car-polishing-abu-dhabi/
  │    /garage-cleaning-dubai/          ← نية مختلفة: تنظيف كراج/موقف
  │
  └─ Location pages (Batch 2–3، صفحات فريدة فقط)
       /mobile-car-wash-dubai-marina/
       /mobile-car-wash-jbr/
       ...
```

### لماذا ليس CPT في Batch 1؟

إصلاح CPT الخدمات مشروع منفصل (HTTP 404/301 تخميني). ربط الكلاستر الجديد بنظام معطوب يخلق Orphan/Redirect risk. بعد استقرار المقالات، يمكن لاحقاً إنشاء خدمة أم في CPT **إذا** أُصلح المسار، مع canonical ثابت للمقال الأم — وليس العكس.

### لماذا Post وليس Page؟

كل صفحات الخدمات التجارية المرتبة تتبع `post` + تصنيف الخدمات. الصفحات (`page`) محجوزة للمؤسسة. الخروج عن هذا النمط يكسر breadcrumb والسايت ماب الداخلي والأرشيف.

---

## 5) URL Map

حالة المسارات اليوم وقرار كل URL. **لم يُنشأ أي مسار.**

### الصفحة الأم

| URL | الحالة الآن | القرار |
|---|---|---|
| `/mobile-car-wash-dubai/` | 404 | **Primary hub — مُوصى به** |
| `/services/mobile-car-wash-dubai/` | 404 | مرفوض في Batch 1 بسبب عطب CPT ومسار `/services/` |
| `/services/mobile-car-wash/` | 301 → مقال أبوظبي | **لا يُلمس، لا يُستخدم** |
| `/mobile-car-wash-in-dubai/` | 404 | مرادف؛ يُترك فارغاً أو يُحوّل لاحقاً إلى الـ hub بعد النشر |
| `/car-wash-dubai/` | 404 | عام جداً؛ لا يُستخدم كـ hub |

### Commercial / supporting

| URL | الحالة | القرار |
|---|---|---|
| `/steam-car-wash-dubai/` | 200، مفهرس | **Primary للبخار — إعادة كتابة، لا صفحة جديدة** |
| `/steam-car-wash-abu-dhabi/` | 200 | شقيق أبوظبي — روابط عكسية فقط |
| `/car-wash-at-home-dubai/` | 404 | Batch 1 إذا تأكد intent منفصل عن الـ hub |
| `/mobile-car-interior-cleaning-dubai/` | 404 | Batch 1 |
| `/waterless-car-wash-dubai/` | 404 | **معلق** حتى تأكيد الغسيل بدون ماء |
| `/mobile-car-polishing-dubai/` | 404 | Batch 1 إذا التلميع يُقدَّم في دبي |
| `/mobile-car-wash-dubai-prices/` | 404 | **معلق** حتى أسعار حقيقية. خلاف ذلك قسم أسعار داخل الـ hub بـ TODO |
| `/mobile-car-detailing-dubai/` أو `/en/mobile-car-detailing-dubai/` | 404 | الإنجليزية **بعد** إصلاح `/en/`. لا تُطلق EN مكسورة |

### Location (لا نشر قبل تأكيد التغطية)

مقترح slug على نمط الموقع (إنجليزي، خدمة+منطقة):

| المنطقة | URL المقترح | تنشر؟ |
|---|---|---|
| دبي مارينا | `/mobile-car-wash-dubai-marina/` | Batch 2 إذا التغطية حقيقية + محتوى أبراج/مواقف |
| JBR | `/mobile-car-wash-jbr/` | Batch 2 |
| جميرا | `/mobile-car-wash-jumeirah/` | Batch 2 |
| البرشاء | `/mobile-car-wash-al-barsha/` | Batch 2 |
| الخليج التجاري | `/mobile-car-wash-business-bay/` | Batch 2 |
| Downtown | `/mobile-car-wash-downtown-dubai/` | Batch 3 |
| JLT | `/mobile-car-wash-jlt/` | Batch 3 |
| JVC | `/mobile-car-wash-jvc/` | Batch 3 |
| دبي هيلز | `/mobile-car-wash-dubai-hills/` | Batch 3 |
| نخلة جميرا | `/mobile-car-wash-palm-jumeirah/` | Batch 3 |
| مردف | `/mobile-car-wash-mirdif/` | Batch 3 |
| ديرة | `/mobile-car-wash-deira/` | Batch 3 |
| بر دبي | `/mobile-car-wash-bur-dubai/` | Batch 3 |
| القرهود | `/mobile-car-wash-al-garhoud/` | Batch 3 |
| Arabian Ranches | `/mobile-car-wash-arabian-ranches/` | Batch 3 |

إذا لم تتوفر معلومات تشغيلية للمنطقة (نوع المواقف، قيود المبنى، جدولة، أحياء مجاورة حقيقية) **لا تُنشأ الصفحة**.

---

## 6) Keyword Map — Keyword → URL

النية أولاً، ثم الصفحة. لا حشو.

| الكلمة / النية | Intent | الصفحة المستهدفة | ملاحظة |
|---|---|---|---|
| غسيل سيارات متنقل في دبي | Commercial / Local | `/mobile-car-wash-dubai/` | Primary |
| غسيل سيارات متنقل دبي | Commercial | نفس الـ hub | لا صفحة ثانية |
| غسيل سيارات في دبي | Broad commercial | الـ hub (H1/intro) | لا صفحة broad |
| مغسلة سيارات متنقلة دبي | Synonym | الـ hub | |
| غسيل سيارات بالمنزل دبي / عند البيت | Home intent | `/car-wash-at-home-dubai/` أو H2 في الـ hub | يُقرر بعد الموافقة على فصل الصفحة |
| تنظيف سيارات متنقل دبي | Synonym | الـ hub | |
| غسيل سيارات بالبخار دبي | Service | **`/steam-car-wash-dubai/` الموجودة** | Cannibalization إذا أُنشئت صفحة جديدة |
| غسيل سيارات بدون ماء دبي | Service | `/waterless-car-wash-dubai/` | معلق على التوفر |
| تنظيف داخلي للسيارات دبي | Service | `/mobile-car-interior-cleaning-dubai/` | |
| تلميع سيارات متنقل دبي | Service | `/mobile-car-polishing-dubai/` | لا تُدمج مع `/car-polishing-abu-dhabi/` |
| أسعار غسيل السيارات المتنقل في دبي | Commercial investigate | قسم في الـ hub أو `/mobile-car-wash-dubai-prices/` | بلا أسعار مخترعة |
| Mobile Car Wash Dubai | EN commercial | `/en/mobile-car-wash-dubai/` لاحقاً | `/en/` حالياً طبقة مكسورة |
| Car Wash at Home Dubai | EN | ترجمة الصفحة المنزلية لاحقاً | |
| Mobile Car Detailing Dubai | EN premium | EN لاحقاً أو H2 عربي «تفاصيل/تلميع داخلي وخارجي» | |
| Steam / Waterless / Polishing EN | EN | ترجمات داعمة لاحقاً | |
| غسيل سيارات متنقل في دبي مارينا / JBR / … | Local | صفحات الموقع في §5 | فريدة أو لا تُنشر |
| تنظيف كراجات ومواقف سيارات دبي | Adjacent ≠ car wash | `/garage-cleaning-dubai/` تبقى primary | رابط سياقي فقط |
| غسيل سيارات متنقل أبوظبي | Other emirate | `/mobile-car-wash-in-abu-dhabi/` | شقيق، ليس هدفاً لدبي |

كلمات نية إضافية ظهرت من SERP (تُغطى كأقسام لا كصفحات في Batch 1): اشتراك شهري، أساطيل شركات، مواقف أبراج، SUV، هل أحتاج ماء/كهرباء، كم المدة، صلاحية المبنى/الجمعية.

---

## 7) Content Cluster

```
Parent
  /mobile-car-wash-dubai/
       H1: غسيل سيارات متنقل في دبي
       نية: الحجز عند المنزل/المكتب/الموقف

Supporting (Batch 1)
  /steam-car-wash-dubai/          [موجود — إعادة كتابة]
  /car-wash-at-home-dubai/        [جديد إذا فُصل عن الـ hub]
  /mobile-car-interior-cleaning-dubai/
  /mobile-car-polishing-dubai/    [إذا الخدمة حقيقية]
  /waterless-car-wash-dubai/      [معلق]
  /mobile-car-wash-dubai-prices/  [معلق]

Location (Batch 2: أفضل 5 بعد التأكيد)
  Marina, JBR, Jumeirah, Al Barsha, Business Bay

Location (Batch 3)
  Downtown, JLT, JVC, Dubai Hills, Palm, Mirdif, Deira, Bur Dubai, Garhoud, Arabian Ranches
  كل صفحة: نية محلية + قيود مواقف/ملح/غبار/أبراج + FAQ حي + روابط للأحياء المجاورة

Siblings (لا تُعدّ جزءاً من كلاستر دبي لكنها تُربط)
  /mobile-car-wash-in-abu-dhabi/
  /steam-car-wash-abu-dhabi/
  /car-polishing-abu-dhabi/
  /garage-cleaning-dubai/
```

محتوى الـ hub المقترح (بعد توفر البيانات الحقيقية فقط):

1. H1 واحد: غسيل سيارات متنقل في دبي  
2. Intro تجاري محلي + CTA واتساب/اتصال (أرقام الموقع الحالية، لا أرقام جديدة)  
3. H2 الخدمة عند المنزل أو المكتب  
4. H2 باقات الغسيل (خارجي/داخلي/كامل/بخار/بدون ماء/تلميع) — أخفِ أي باقة غير مؤكدة  
5. H2 الأسعار — جدول Placeholder: `TODO: ADD_REAL_CAR_WASH_PRICE`  
6. H2 كيف تعمل الخدمة (8 خطوات)  
7. H2 المنزل / المكتب / مواقف الأبراج / الأساطيل — أقسام قصيرة إن كانت متاحة  
8. H2 مقارنة متنقل vs مغسلة تقليدية  
9. H2 مناطق دبي — قائمة منظمة غير سبام، فقط المؤكدة  
10. H2 لماذا ركن التطور — فقط إثباتات قائمة (ترخيص دبي/غرفة، عنوان MBZ، تغطية الإمارات، واتساب) **بدون جوائز أو 15000 عميل أو 4.9 إذا لم تُوثَّق لهذه الخدمة**  
11. FAQ 10–15 ظاهر في HTML  
12. روابط داخلية عكسية للتنظيف/دبي/أبوظبي

---

## 8) Internal Linking Plan

### مصادر سلطة/صلة داخل ركن (مرشحة، كلها 200 وقت التدقيق)

| من | إلى | Anchor مقترح | سبب |
|---|---|---|---|
| `/city/dubai/` | Hub | غسيل سيارات متنقل في دبي | صفحة المدينة ضعيفة وتحتاج روابط حقيقية |
| `/service/` و`/services/` | Hub | غسيل السيارات المتنقل | إدخال الخدمة في فهرس الخدمات **كرابط تحريري** لا بتغيير بطاقات CPT الاثنتي عشرة إلا بموافقة (قد يمس القالب) |
| `/hourly-cleaning-maids-dubai/` | Hub | غسيل السيارات عند المنزل | نفس نية «عند البيت» |
| `/office-cleaning-dubai/` | Hub | غسيل سيارات أمام المكتب | نية مكتب |
| `/garage-cleaning-dubai/` | Hub | تنظيف السيارات في الموقف | مجاور لا مدمج |
| `/dubai-majlis-cleaning-company/` / `/garden-cleaning-dubai/` | Hub | خدمة غسيل السيارات المتنقلة | تنظيف دبي |
| `/steam-car-wash-dubai/` | Hub | غسيل سيارات متنقل في دبي | دعم → أم |
| `/mobile-car-wash-in-abu-dhabi/` | Hub | غسيل سيارات متنقل في دبي | ربط الإمارات |
| مقالات تنظيف دبي الأخرى ذات الصلة فقط | Hub | تنوع anchors | لا روابط عشوائية من تسليك/عزل إلا بجملة سياقية نادرة |

**لن ألمس** مقالات العزل/التسليك/المكيفات إلا إذا وُجدت فقرة تنظيف مركبة منطقية. كثافة الروابط العشوائية أخطر من نقص رابط.

### عكسياً: Hub → الموقع الحالي

- `/services/cleaning-and-sterilization/` معطوب؛ الرابط إلى `/hourly-cleaning-maids-dubai/` و`/office-cleaning-dubai/` و`/garage-cleaning-dubai/`
- `/city/dubai/`
- `/mobile-car-wash-in-abu-dhabi/`
- `/steam-car-wash-dubai/`
- `/contact-us/` وواتساب الحالي

لا إضافة الخدمة إلى الهيدر العام أو الفوتر العالمي في Batch 1 (يمس كل الموقع). إن رُغب لاحقاً: عنصر قائمة واحد بموافقة.

لا Semrush حي في هذا المستودع (الملفات حُذفت سابقاً). ترتيب أولوية الروابط الداخلية مبني على الصلة + حالة HTTP + نوع الصفحة، لا على أرقام ترافيك دقيقة. **TODO:** إن وُجد Search Console/Semrush، تُرتَّب المصادر حسب clicks/impressions قبل التنفيذ.

---

## 9) Schema Plan

| النوع | المصدر | للكلاستر الجديد |
|---|---|---|
| Organization / HomeAndConstructionBusiness | Rank Math العام | **يُترك.** لا LocalBusiness دبي مزيف. المقر MBZ/أبوظبي. |
| BreadcrumbList | Rank Math | الإبقاء؛ التأكد أن السلسلة Home > الخدمات > الصفحة |
| WebPage / Article أو Service | Rank Math | تفضيل **Service** عبر Rank Math Schema إذا كان متاحاً للـ post، وإلا Article كما المقالات الحالية |
| FAQPage | Rank Math FAQ block | يُفعَّل **فقط** لأن الأسئلة ظاهرة |
| Offer / price | — | **ممنوع** حتى سعر حقيقي |
| AggregateRating / Review | — | **ممنوع** اختراع. تقييمات الموقع العامة (4.9 / 15000) غير موثّقة لهذه الخدمة ومتناقضة أصلاً على الرئيسية |
| areaServed | داخل Service إن لزم | `Dubai` كمنطقة خدمة، ليس كـ address للمؤسسة |

لن نضيف JSON-LD يدوي بجانب Rank Math (صفحة البخار تعاني تكراراً أصلاً: جراف Rank Math + كتلة Article/LocalBusiness/Service). عند إعادة كتابة البخار: حذف الكتلة المخصصة أو توحيدها عبر Rank Math.

---

## 10) Technical Risks

1. **عطب CPT `/services/`** — استخدام URL المقترح أصلاً يعرّض الصفحة لـ 404/301 تخميني.  
2. **تصادم `/services/mobile-car-wash/` → أبوظبي** موجود الآن؛ لا نلمسه.  
3. **Cannibalization** مع `/steam-car-wash-dubai/` إذا أُنشئت صفحة بخار ثانية.  
4. **Doorway risk** إذا نُسخت 15 صفحة أحياء بتغيير اسم المنطقة (النمط موجود في صفحة البخار الحالية: فقرة واحدة تتكرر على البرشاء/جميرا/مردف…).  
5. **قالب Rank Math الإيجار** إن وُرث للصفحات الجديدة يضرب CTR والثقة.  
6. **Schema مكرر + إحداثيات أبوظبي** إذا وُسمت الخدمة كفرع دبي.  
7. **طبقة `/en/` مكسورة** (`lang=ar`) — إطلاق EN الآن يضر أكثر مما ينفع.  
8. **Soft 404** (روابط وهمية → 302 للرئيسية) مذكور في التدقيق الشامل السابق؛ صفحات جديدة غير موجودة قبل النشر قد تُفهرس كالرئيسية إذا خُمنت روابط. بعد النشر يجب التحقق HTTP 200 لا 302.  
9. **CSS/JS عالمي** في الثيم يمس الرئيسية وكل المقالات. أي تنسيق للكلاستر = classes مُصدَّرة داخل المحتوى أو بلوك محدود، لا تعديل `kayan-theme` العام.  
10. **أرقام هواتف متعارضة** (058 / 05213 DNI / 054 تنظيف / فوتر مصر). الصفحة الجديدة تستخدم قناة واتساب/اتصال المتفق عليها للموقع، لا رقم جديد دون موافقة.  
11. **صورة البخار البارزة خاطئة** (alt صيانة مباني / عنوان صيانة غسالات). إعادة الاستخدام دون تصحيح يضر.  
12. **المحتوى الحالي للبخار ليس عن السيارات** (مطابخ، أرضيات، حمامات). إبقاؤه كما هو مع رابط من الـ hub يلوّث الكلاستر.

---

## 11) Files That Will Change

**بعد الموافقة فقط، وليس الآن.**

### في ووردبريس الحي (خارج هذا الريبو)

- منشور جديد: الصفحة الأم  
- تحديث `/steam-car-wash-dubai/` (محتوى + Title Rank Math لهذه الصفحة فقط + صورة + روابط)  
- منشورات Batch 1 الداعمة  
- روابط سياقية داخل عدد محدود من مقالات دبي المذكورة في §8  
- وسوم: استخدام الوسم الموجود `غسيل سيارات متنقل`  
- ربط taxonomy `cities = dubai` على منشورات الكلاستر

### في GitHub `Call` (مسودات/توثيق)

- `reports/mobile-car-wash-dubai/**` (هذا التقرير + لاحقاً مسودات المحتوى)
- اختيارياً HTML prototype للـ hub مستند إلى `service-single.html` **بدون** استبدال ملفات الخدمات الحالية

### غير مطلوب في Batch 1

- PHP الثيم، `functions.php`، الهيدر، الفوتر، الأزرار العائمة، packs KAYAN، Rank Math global، robots، permalinks، CPT registration

---

## 12) Files That Must NOT Change

- أي URL قائم، أي slug قائم، أي canonical لصفحات موجودة  
- `robots.txt` وخرائط الدول  
- إعدادات Rank Math العامة / قوالب Title الجماعية  
- Schema المؤسسة العام  
- `/`, `/about/`, `/contact-us/`, `/service/`, صفحات CPT الاثنتي عشرة  
- `/mobile-car-wash-in-abu-dhabi/` و`/steam-car-wash-abu-dhabi/` و`/car-polishing-abu-dhabi/` (إلا رابط داخلي اختياري بموافقة)  
- `/garage-cleaning-dubai/` (نية مختلفة)  
- CSS/JS العام في `kayan-theme`  
- أزرار الهيدر/الفوتر العائمة لكل الموقع  
- Redirects الحالية بما فيها `/services/mobile-car-wash/` → أبوظبي  
- بيانات GBP الحية  

---

## 13) Missing Information — أحتاج موافقتك/بياناتك قبل التنفيذ

بدون هذه الإجابات سأخالف قاعدة «لا تخترع بيانات» أو سأنشر Doorway Pages.

1. **هل غسيل السيارات المتنقل خدمة تشغيلية في دبي اليوم؟** أم حملة SEO فقط؟ صفحة أبوظبي أقدم وأكثر تخصيصاً للسيارات؛ صفحة دبي بالبخار قالب تنظيف منازل.  
2. **الباقات الفعلية:** خارجي، داخلي، كامل، بخار، بدون ماء، تلميع، إزالة روائح، اشتراك شهري، أساطيل. ضع صح/خطأ لكل بند.  
3. **الأسعار الحقيقية** أو تأكيد استخدام `TODO: ADD_REAL_CAR_WASH_PRICE`.  
4. **التغطية:** قائمة الأحياء التي يصلها الفريق فعلياً، خصوصاً مواقف الأبراج (مارينا/JBR/JLT/Downtown) مقابل الفلل (جميرا/البرشاء/Ranches/Hills).  
5. **المعدات:** هل تحملون ماء وكهرباء؟ بخار؟ Waterless؟ ضغط؟  
6. **المدة** التقريبية لسيدان vs SUV.  
7. **رقم واتساب/اتصال** لهذه الخدمة إن كان مختلفاً عن 0586634710.  
8. **صور حقيقية** (فريق، سيارة الخدمة، قبل/بعد). الموجودة في المكتبة لغسيل السيارات ضعيفة/خاطئة الـ alt.  
9. **تراخيص/تأمين** خاصة بالعمل داخل مواقف المباني إن وُجدت.  
10. **هل نصلح Title صفحة البخار الحالية** كجزء من Batch 1 (إزالة «الإعلان للإيجار») أم نترك الترتيب القائم؟  
11. **Application Password** لووردبريس لنشر المسودات على الحي، أو تأكيد أن التنفيذ = مسودات في GitHub فقط حتى تنسخها يدوياً.  
12. **Search Console / Semrush** إن رغبت بترتيب صفحات الربط الداخلي حسب الترافيك الفعلي.

---

## 14) Implementation Plan — بعد الموافقة فقط

ترتيب التنفيذ المقترح. اسم فرع Git المطلوب في التعليمات كان `feature/mobile-car-wash-dubai-seo`. في هذه البيئة سألتزم بـ `cursor/mobile-car-wash-dubai-seo-e30e` عند التنفيذ، منفصل عن فرع التدقيق هذا.

### قبل الكود

1. استلام إجابات §13.  
2. إن كانت الخدمة غير مقدمة في دبي: **توقف.** لا كلاستر.  
3. Checkpoint Git جديد للتنفيذ فقط.

### Batch 1 — بعد الموافقة

1. `feat: add mobile car wash Dubai hub draft` — مسودة `/mobile-car-wash-dubai/` (محتوى + Title + Meta + FAQ + جدول أسعار TODO + CTA KAYAN الموجود).  
2. `feat: rewrite steam-car-wash-dubai as supporting page` — إزالة قالب المنازل، محتوى سيارات حقيقي، روابط من/إلى الـ hub، Title يدوي.  
3. `feat: add 3–4 commercial supporting drafts` حسب الباقات المؤكدة فقط (منزل، داخلي، تلميع، بدون ماء إن وُجد).  
4. `feat: add contextual internal links` من قائمة §8 المحدودة.  
5. Audit: 200، indexable، canonical ذاتي، لا noindex، لا schema مكرر، لا CSS عالمي، فحص الروابط.

**لا Batch 2 مواقع** حتى تأكيد الأحياء.

### Batch 2

أفضل 5 مواقع بمحتوى مستقل (قيود موقف، ملح بحري، أبراج، أحياء مجاورة، FAQ محلي).

### Batch 3

حتى 10 مواقع إضافية بنفس شرط القيمة المستقلة.

### ما لن يحدث في أي دفعة دون موافقة مستقلة

- تغيير GBP (انظر الملحق)  
- إصلاح CPT الخدمات  
- إصلاح `/en/`  
- تنظيف قالب Rank Math الجماعي  
- تعديل الهيدر/الفوتر

---

## ملحق A — Content Gap vs Competitors

مستخرج من SERP الحالي لـ «غسيل سيارات متنقل في دبي» و Mobile Car Wash Dubai (On My Way، Washii، Al Haddaf، Ride Revivers، SNASH، ServiceMarket، FOME، Quick Wash، MySyara، Crystal Clean). **لا نسخ.**

| فجوة المنافس | هل ركن يغطيها؟ | الصفحة المقترحة |
|---|---|---|
| Hub تجاري واضح «متنقل في دبي» | لا (404) | `/mobile-car-wash-dubai/` |
| أسعار من / باقات سيدان vs SUV | لا — ممنوع الاختراع | Hub أو prices بعد البيانات |
| تطبيق حجز 60 ثانية | لا — ركن واتساب | CTA واتساب/اتصال فقط، لا ندّعي تطبيقاً |
| Waterless / eco / توفير الماء | غير مؤكد | صفحة أو H2 بعد التأكيد |
| Steam كخدمة سيارات حقيقية | صفحة موجودة لكن المحتوى منازل | إعادة كتابة `/steam-car-wash-dubai/` |
| اشتراكات شهرية | غير مؤكد | FAQ/H2 بعد التأكيد |
| تغطية مجتمعات + قواعد مواقف الأبراج | قوائم أحياء مكررة بلا قيمة | Location pages فريدة فقط |
| Interior detailing / تلميع | تلميع أبوظبي فقط | صفحة دبي بعد التأكيد |
| أساطيل/شركات | غير مؤكد | H2 في الـ hub |
| EN landing قوي | طبقة EN مكسورة | مؤجل |
| مقارنة متنقل vs محطة بنزين/مغسلة | غير موجود | جدول في الـ hub |
| FAQ تشغيلي (ماء؟ كهرباء؟ SUV؟ مبنى؟) | عام/غير دقيق في صفحة البخار | FAQ الـ hub |

ركن لا ينافس التطبيقات على «من 15 درهم / احجز بـ60 ثانية» إلا بتشغيل حقيقي وأسعار. ميزته الممكنة: دومين خدمات منزلية قائم + واتساب + تغطية الإمارات + الربط مع التنظيف المنزلي عند البيت. هذا يجب أن يظهر في المحتوى دون ادعاء ريادة السوق.

---

## ملحق B — Cannibalization (المرحلة 19)

| صفحة موجودة | القرار | Merge/Redirect الآن؟ |
|---|---|---|
| `/steam-car-wash-dubai/` | تبقى Primary للبخار؛ تُعاد كتابتها | لا redirect |
| `/mobile-car-wash-in-abu-dhabi/` | Primary لأبوظبي | لا |
| `/steam-car-wash-abu-dhabi/` | شقيق | لا |
| `/car-polishing-abu-dhabi/` | Primary تلميع أبوظبي | لا |
| `/garage-cleaning-dubai/` | نية كراج/موقف مبنى لا غسيل سيارة | لا merge |
| `/services/mobile-car-wash/` | 301 إلى أبوظبي | **لا تغيير** |
| لا توجد `/mobile-car-wash-dubai/` | تُنشأ كـ Primary لدبي | — |

لا تنفيذ merge/redirect في هذه المرحلة.

---

## ملحق C — Google Business Profile (توصيات فقط — بلا تطبيق)

المؤسسة في Schema: خدمات منزلية/إنشاء، هاتف 0586634710، مقر مرتبط بـ MBZ/أبوظبي. **لا يُغيَّر اسم النشاط** لإضافة «غسيل سيارات دبي». **لا يُفتح GBP دبي منفصل** فقط لأننا أضفنا خدمة.

مقترح للمراجعة لاحقاً (بعد موافقتك):

| الحقل | توصية |
|---|---|
| Primary category | الإبقاء على فئة الخدمات المنزلية/الصيانة الحالية إن كانت تطابق الرخصة. لا تحويل الـ profile إلى Car Wash. |
| Additional categories | إن وُجدت الخدمة فعلياً: Car Wash أو Mobile Car Wash **كإضافة** لا كفئة أولى. |
| Services | إضافة «غسيل سيارات متنقل» كخدمة، منطقة الخدمة دبي **بعد التأكيد**. |
| Service areas | دبي كمنطقة خدمة إذا كنتم تصلون فعلياً؛ لا حذف أبوظبي. |
| Description | جملة واحدة عن الغسيل المتنقل داخل الوصف الحالي، بدون حشو كلمات. |
| Photos | صور تشغيل حقيقية للغسيل، لا stock. |
| Posts | تحديثات بعد إطلاق الصفحة الأم، لا قبل توفر الخدمة. |

---

## ملحق D — الصور المطلوبة (لا Stock كصور شركة)

المكتبة: صورة واحدة ذات صلة ضعيفة (`Un.jpg` alt = شركة غسيل سيارات متنقل في أبوظبي). صورة البخار البارزة **خاطئة الموضوع**.

| الاستخدام | Filename مقترح | ALT | الوضع |
|---|---|---|---|
| Hero | `mobile-car-wash-dubai-hero.webp` | غسيل سيارات متنقل في دبي عند المنزل | TODO صورة حقيقية |
| وحدة متنقلة | `mobile-car-wash-van-dubai.webp` | فريق غسيل سيارات متنقل في دبي | TODO |
| خارجي | `car-exterior-wash-dubai.webp` | غسيل خارجي للسيارة في دبي | TODO |
| داخلي | `car-interior-cleaning-dubai.webp` | تنظيف داخلي للسيارة في دبي | TODO |
| بخار | `steam-car-wash-dubai.webp` | غسيل سيارات بالبخار في دبي | TODO — استبدال الصورة الحالية |
| قبل/بعد | `car-wash-dubai-before-after.webp` | قبل وبعد غسيل سيارة في دبي | TODO |
| موقع دبي | لكل حي عند وجود صورة حقيقية | يتضمن اسم الحي | لا placeholder |

WebP موجود في الثيم (`logo__size`, `single__gallery`…). نستخدم أحجام النظام لا مكتبات جديدة. Lazy-load للصور تحت الـ fold.

---

## ملحق E — SEO Title / H1 / Meta (مسودة، ليست منشورة)

بعد فحص SERP: المنافسون يستخدمون «غسيل سيارات متنقل في دبي» + منفعة (عند بابك / من سعر / احجز). ركن لا يملك تطبيقاً ولا سعراً معلناً. الصياغة المقترحة للـ hub:

- **H1 (واحد):** غسيل سيارات متنقل في دبي  
- **Title:** غسيل سيارات متنقل في دبي | عند المنزل والمكتب | ركن التطور  
  (60–65 حرفاً تقريباً، بدون إيموجي وبدون «الإعلان للإيجار»)  
- **Meta:** غسيل سيارات متنقل في دبي يصل إلى المنزل أو المكتب أو الموقف. احجز الموعد عبر واتساب ركن التطور واختر الغسيل المناسب لسيارتك.  
  (خدمة + دبي + home/mobile intent + CTA، بلا حشو)

يمكن تعديل Title بعد معرفة السعر/الباقة الحقيقية (مثلاً إضافة «بخار» فقط إذا كانت الباقة الأساسية).

---

## موقف التنفيذ الحالي

- لم يُغيَّر أي ملف إنتاج.  
- لم تُنشأ صفحات.  
- لم تُمس Schema/Sitemap/robots/URLs القائمة.  

**الخطوة التالية:** موافقتك على (1) تأكيد تشغيل الخدمة في دبي، (2) الباقات الحقيقية، (3) اعتماد `/mobile-car-wash-dubai/` كـ hub بدل `/services/mobile-car-wash-dubai/`، (4) السماح بإعادة كتابة `/steam-car-wash-dubai/` ضمن Batch 1.
