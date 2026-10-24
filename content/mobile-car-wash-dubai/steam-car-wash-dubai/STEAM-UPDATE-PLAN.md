# Steam 8564 — safest update plan (no production write yet)

**التاريخ:** 22 سبتمبر 2026  
**الحالة:** قراءة فقط. المنشور الحي **8564 لم يُعدَّل.** Hub **12716 ما زال Draft.**

WordPress REST أعاد **0 مراجعات** لـ 8564. لا تعتمد على شاشة Revisions في الووردبريس. مصدر الرجوع هو النسخة المحفوظة في Git.

## Backup already taken (Git, not WordPress)

| ملف | المحتوى |
|---|---|
| `steam-car-wash-dubai/LIVE-BACKUP-8564.html` | الجسم الخام الحالي (فيه JSON-LD وقالب المنازل) |
| `steam-car-wash-dubai/LIVE-BACKUP-8564.json` | slug، الحالة، الصورة 3143، التصنيف، الوسوم، `<title>` الحي، canonical |

الاستعادة بعد تحديث مرفوض: `PUT /wp-json/wp/v2/posts/8564` بنفس الـ HTML + `featured_media: 3143` + Rank Math title القديم.

## Why some methods are unsafe

| طريقة | لماذا تُرفض |
|---|---|
| تحويل 8564 إلى `draft` | `/steam-car-wash-dubai/` يصبح 404 فوراً |
| Autosave فقط | غير ثابت؛ Rank Math غير مضمون؛ يُستبدل عند فتح المحرر |
| مقال جديد بنفس الـ slug | يتطلب تغيير 8564 أو ينتج URL ثانياً |
| تعديل القالب العام / Rank Math Titles العامة | يلمس عشرات المقالات |

## Recommended sequence (only after your explicit go-ahead)

**1. انشر Hub 12716 أولاً** (خطوة منفصلة بموافقتك).  
محتوى البخار الجديد يربط 5 مرات إلى `/mobile-car-wash-dubai/`. إن حُدّث البخار والـ Hub ما زال Draft، تلك الروابط 404.

**2. حدّث نفس المقال 8564 — لا تغيّر الرابط.**  
`POST/PUT /wp-json/wp/v2/posts/8564` مع تجميد الحقول أدناه، ثم `POST /wp-json/rankmath/v1/updateMeta`.

**3. تحقق فوراً من view-source للرابط الحي نفسه.**

**4. إن لزم الرجوع:** أعد `LIVE-BACKUP-8564.html` + الصورة 3143 + عنوان Rank Math القديم.

لا تُنشأ صفحة `/services/…`. لا Redirect. لا دمج مع الـ Hub.

---

## Locked (must not change)

| الحقل | القيمة الحالية = تبقى |
|---|---|
| ID | 8564 |
| post type | `post` |
| status | `publish` |
| slug | `steam-car-wash-dubai` |
| URL | `https://www.rukn-eltatawer.com/steam-car-wash-dubai/` |
| canonical | نفس URL |
| عنوان المقال / H1 | `غسيل سيارات بالبخار في دبي` |
| robots | index, follow |
| cities | `[]` — **لا** تُسند `dubai` |
| category | الخدمات `2457` |
| تاريخ النشر | `2026-03-16` (لا يُمس) |

---

## What will change on approval

| الحقل | الآن (حي) | بعد الموافقة |
|---|---|---|
| `<title>` / og:title | `غسيل سيارات بالبخار في دبي 📞 01151481000 📢 الإعلان للإيجار` | `غسيل سيارات بالبخار في دبي \| متنقل عند موقعك \| ركن التطور` |
| Meta description | «أفضل… ضمان مكتوب وتغطية أحياء دبي» | `غسيل سيارات بالبخار في دبي للمقاعد والمقصورة. الأسعار المعتمدة: 60 / 90 / 349 درهماً حسب باقة الخدمة المتنقلة. احجز عبر واتساب.` |
| Focus keyword | (غير مقروء من REST) | `غسيل سيارات بالبخار في دبي` |
| الجسم | قالب تنظيف منازل + JSON-LD مكرر (~12.6k) | `steam-car-wash-dubai/content.html` (سيارة/بخار فقط، روابط إلى الـ Hub، أسعار 60/90/349) |
| JSON-LD داخل المحرر | موجود (Article + LocalBusiness + Service) | **محذوف** — يبقى Rank Math فقط |
| FAQ | قالب منازل | 6 أسئلة ظاهرة + بلوك Rank Math FAQ مخفي للـ schema (نفس أسلوب الـ Hub) |
| الصورة البارزة | media **3143** (غسالات/صيانة مباني) | **لا صورة** (`featured_media: 0`). الملف يبقى في المكتبة؛ الإرجاع يعيد ربط 3143 |
| excerpt | ضمان مكتوب / تغطية أحياء | يُستبدل بنص متوافق مع المسودة (بدون ضمان/أحياء غير مؤكدة) أو يُترك إن طلبت إبقاؤه |
| الوسوم | 2406, 2380, 2377, 2405, 2407 | تُترك كما هي ما لم تطلب غير ذلك |

لن يتغيّر: الهيدر، الفوتر، الأزرار العائمة، robots.txt، permalinks، CPT، `/city/dubai/`، Hub 12716 (حتى توافق على نشره).

---

## Optional preview (still not touching 8564)

يمكن إنشاء **مسودة شقيقة جديدة** (slug مختلف، status draft) لمعاينة الجسم. هذا لا يغيّر URL البخار الحي. لم يُنفَّذ الآن.

---

## Go-live payload (for the future approval message)

لن يُرسل إلا بعد نص صريح منك:

1. `status` يبقى `publish` — ليس draft.
2. `slug` يبقى `steam-car-wash-dubai`.
3. `featured_media`: 0.
4. الجسم من `content.html` داخل `<!-- wp:html -->` + بلوك FAQ Rank Math.
5. Rank Math meta من `rank-math.json`.
6. لا `cities=dubai`.
7. فوراً بعدها: GET للـ URL الحي — canonical ذاتي، `<title>` بلا «الإعلان للإيجار»، لا JSON-LD يدوي في الجسم.

**في انتظار موافقتك. لا تحديث لـ 8564 في هذه الجولة.**
