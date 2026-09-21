# ملاحظات النشر — Batch 1 (مسودة فقط)

**لا تنشر على الإنتاج من هذا الفرع.** الصق المحتوى في ووردبريس كـ **Draft** بعد المراجعة.

## الصفحة الأم (جديد)

| الحقل | القيمة |
|---|---|
| نوع المحتوى | `post` (مقال) — ليس CPT `services` |
| الحالة المقترحة | `draft` |
| Slug | `mobile-car-wash-dubai` |
| الرابط النهائي | `https://www.rukn-eltatawer.com/mobile-car-wash-dubai/` |
| العنوان الداخلي | غسيل سيارات متنقل في دبي |
| التصنيف | الخدمات (`service`) |
| الوسوم | `غسيل سيارات متنقل` (الوسم موجود، count كان 0)، `سيارات`، `دبي`، `خدمة متنقلة` |
| تصنيف المدن | `dubai` حتى تظهر البطاقة في `/city/dubai/` دون تعديل قالب الأرشيف |
| الصورة البارزة | لا ترفع صورة عشوائية — `TODO: ADD_REAL_MOBILE_CAR_WASH_IMAGES` |
| القالب | الافتراضي (قالب المقالات الحالي) |

الصق `hub/content.html` في المحرر (وضع HTML / Custom HTML).  
الـ CSS داخل الغلاف `.rukn-mcw` فقط.

## صفحة البخار (تحديث محتوى — نفس المقال)

| الحقل | القيمة |
|---|---|
| ID الحالي | 8564 |
| Slug | `steam-car-wash-dubai` — **لا تغيير** |
| URL | `/steam-car-wash-dubai/` — **لا تغيير / لا Redirect / لا دمج** |
| Canonical | `https://www.rukn-eltatawer.com/steam-car-wash-dubai/` — يبقى ذاتياً |
| العنوان الداخلي | غسيل سيارات بالبخار في دبي |
| Rank Math Title | من `steam-car-wash-dubai/rank-math.json` (إزالة الإعلان للإيجار والرقم المصري) |
| الصورة البارزة | لا تُبقَ صورة صيانة المباني/الغسالات — `TODO: ADD_REAL_STEAM_CAR_WASH_IMAGE` |
| Schema المخصص داخل المحتوى | **احذف** كتلة JSON-LD الثانية (Article + LocalBusiness + Service) من المقال الحي. Rank Math يبقى. |

## ما لا يُفعل عند اللصق

- لا تغيّر permalinks أو robots.txt أو إعدادات Rank Math العامة.
- لا تضف JSON-LD Organization / LocalBusiness / AggregateRating داخل المحتوى.
- فعّل FAQ Schema من بلوك Rank Math FAQ **فقط** لأن الأسئلة ظاهرة في HTML (تفاصيل في ملفات rank-math).
- لا تستخدم `/services/mobile-car-wash-dubai/`.
- لا تلمس تحويل `/services/mobile-car-wash/` → أبوظبي.
- لا تعدّل الهيدر أو الفوتر أو الأزرار العائمة.
