# Batch 1 Implementation — غسيل سيارات متنقل في دبي

**التاريخ:** 21 سبتمبر 2026  
**الفرع:** `cursor/mobile-car-wash-dubai-batch-1-e30e`  
**القاعدة:** `reports/mobile-car-wash-dubai/AUDIT-REPORT.md`  
**النشر على الإنتاج:** **لم يتم.** لا Merge، لا Deploy، لا تعديل ووردبريس الحي.

هذا الفرع يحتوي مسودات لصق في المحرر كـ Draft بعد مراجعتك.

---

## إعادة الفحص قبل التنفيذ

| المسار | HTTP | قرار Batch 1 |
|---|---|---|
| `/mobile-car-wash-dubai/` | 404 | إنشاء Hub كـ `post` |
| `/services/mobile-car-wash-dubai/` | 404 | **لا يُستخدم** |
| `/services/mobile-car-wash/` | 301 → أبوظبي | **لا يُلمس** |
| `/steam-car-wash-dubai/` | 200، canonical ذاتي، index,follow | إعادة كتابة محتوى + Title Rank Math لهذه الصفحة فقط |
| `/mobile-car-wash-in-abu-dhabi/` | 200 | جملة رابط اختيارية فقط |
| Cannibalization بخار | صفحة البخار موجودة | تبقى Supporting، بلا Redirect وبلا دمج |

لم يُعثر على `/mobile-car-wash-in-dubai/` أو `/car-wash-dubai/`. لا حاجة لـ merge/redirect.

---

## 1. Created

| العنصر | المسار في الريبو |
|---|---|
| هيكل النشر + ملاحظات ووردبريس | `content/mobile-car-wash-dubai/README.md` ، `wordpress-publish-notes.md` |
| جسم Hub | `content/mobile-car-wash-dubai/hub/content.html` |
| حقول Rank Math للـ Hub | `content/mobile-car-wash-dubai/hub/rank-math.json` |
| إعادة كتابة البخار | `content/mobile-car-wash-dubai/steam-car-wash-dubai/content.html` |
| حقول Rank Math للبخار | `content/mobile-car-wash-dubai/steam-car-wash-dubai/rank-math.json` |
| قصاصة روابط داخلية | `content/mobile-car-wash-dubai/internal-links/*` |
| هذا التقرير | `reports/mobile-car-wash-dubai/BATCH-1-IMPLEMENTATION.md` |

لم يُنشأ CPT، ولا قالب PHP، ولا صفحة Location، ولا مسار `/services/`.

---

## 2. Modified

**على الإنتاج:** لا شيء.

**في Git (مسودات لاستبدال/إدراج لاحقاً):**

| الهدف الحي | نوع التعديل المقترح |
|---|---|
| مقال جديد `mobile-car-wash-dubai` | إنشاء Draft عند الموافقة على اللصق |
| post 8564 `/steam-car-wash-dubai/` | استبدال الجسم + Rank Math Title/Description لهذه الصفحة + حذف JSON-LD المخصص + استبدال الصورة البارزة لاحقاً |
| `/hourly-cleaning-maids-dubai/` | فقرة واحدة |
| `/office-cleaning-dubai/` | فقرة واحدة |
| `/garage-cleaning-dubai/` | فقرة واحدة (تمييز الكراج عن غسيل المركبة) |
| `/mobile-car-wash-in-abu-dhabi/` | جملة اختيارية |
| تصنيف `cities` → دبي | إسناد المنشور الجديد + وصف اختياري. **لا تعديل قالب الأرشيف** |

صفحات أبوظبي الأخرى، `/garage-cleaning-dubai/` (ما عدا الفقرة)، CPT، الهيدر/الفوتر، robots، permalinks: بلا تغيير.

---

## 3. URLs

| الدور | URL | الحالة بعد هذا الفرع |
|---|---|---|
| Hub | `https://www.rukn-eltatawer.com/mobile-car-wash-dubai/` | مسودة في Git — 404 على الحي حتى اللصق/النشر |
| Supporting | `https://www.rukn-eltatawer.com/steam-car-wash-dubai/` | URL كما هو؛ المحتوى البديل في Git |
| مرفوض | `/services/mobile-car-wash-dubai/` | غير مستخدم |
| قائم لا يُلمس | `/services/mobile-car-wash/` | 301 أبوظبي كما هو |

لا صفحات مناطق في Batch 1.

---

## 4. Keywords — Keyword → URL

| الكلمة | URL |
|---|---|
| غسيل سيارات متنقل في دبي (رئيسي) | `/mobile-car-wash-dubai/` |
| غسيل سيارات متنقل دبي | Hub |
| غسيل سيارات بالمنزل / عند المنزل دبي | Hub (H2 المنزل) |
| تنظيف سيارات متنقل دبي / غسيل سيارات في دبي | Hub |
| Mobile Car Wash Dubai / Car Wash at Home Dubai / Mobile Car Cleaning Dubai | مذكورة طبيعياً في السياق العربي للـ Hub — لا صفحة EN في Batch 1 |
| غسيل سيارات بالبخار في دبي | `/steam-car-wash-dubai/` |
| تلميع / داخلي / خارجي (كباقات) | H3 داخل Hub، ليست صفحات مستقلة في Batch 1 |
| بدون ماء / اشتراك / أساطيل | غير مستهدفة — TODO |

---

## 5. Internal Links — Source → Target → Anchor

| Source | Target | Anchor |
|---|---|---|
| Hub | `/steam-car-wash-dubai/` | غسيل السيارات بالبخار في دبي / غسيل سيارات بالبخار في دبي |
| Hub | `/hourly-cleaning-maids-dubai/` | عاملات التنظيف بالساعة في دبي |
| Hub | `/office-cleaning-dubai/` | تنظيف المكاتب في دبي |
| Hub | `/garage-cleaning-dubai/` | تنظيف الكراجات ومواقف السيارات في دبي |
| Hub | `/city/dubai/` | خدمات ركن التطور في دبي |
| Hub | `/mobile-car-wash-in-abu-dhabi/` | غسيل سيارات متنقل في أبوظبي |
| Steam rewrite | `/mobile-car-wash-dubai/` | غسيل سيارات متنقل في دبي / غسيل السيارات المتنقل في دبي |
| Hourly insert | Hub | غسيل السيارات عند المنزل |
| Office insert | Hub | غسيل سيارات متنقل في دبي |
| Garage insert | Hub | تنظيف السيارات في دبي |
| Abu Dhabi insert (اختياري) | Hub | غسيل سيارات متنقل في دبي |
| City term description (اختياري) | Hub | غسيل سيارات متنقل في دبي |

لا روابط من العزل / التسربات / الحشرات / الصيانة.

---

## 6. Schema

| المصدر | الاستخدام |
|---|---|
| Rank Math Organization / Breadcrumb / WebPage | يُبقى كما يولّده القالب بعد نشر الـ post |
| Service عبر Rank Math | موصوف في `hub/rank-math.json` — `areaServed: Dubai`، المزود Organization الحالي (MBZ). لا LocalBusiness دبي |
| FAQPage | الأسئلة ظاهرة في HTML؛ تُفعَّل من بلوك Rank Math FAQ بنفس النص — **ليس** JSON-LD ثانياً داخل المحتوى |
| Offer / AggregateRating / Review | غير مستخدم |
| JSON-LD المخصص الحالي في صفحة البخار | يُحذف عند اللصق (تكرار LocalBusiness+Service) |

لا CSS/JS عالمي. الأنماط داخل `.rukn-mcw` و `.rukn-steam` فقط.

---

## 7. SEO Checks

تنطبق **بعد** لصق المسودات كـ Draft ثم نشر بموافقتك. على الحي الآن الـ Hub ما زال 404.

| الفحص | Hub (المخطط) | Steam (بعد اللصق) |
|---|---|---|
| Canonical | `https://www.rukn-eltatawer.com/mobile-car-wash-dubai/` | يبقى `.../steam-car-wash-dubai/` |
| Indexability | index, follow | index, follow (كما هو) |
| Robots.txt | بلا تعديل | بلا تعديل |
| Sitemap | يدخل `post-sitemap` تلقائياً بعد النشر | موجود أصلاً في `post-sitemap3.xml` |
| H1 | عنوان المقال: غسيل سيارات متنقل في دبي — **غير مكرر داخل الجسم** حتى لا يظهر H1 ثانٍ من القالب | يبقى H1 القالب: غسيل سيارات بالبخار في دبي |
| Title | غسيل سيارات متنقل في دبي \| عند المنزل والمكتب \| ركن التطور | غسيل سيارات بالبخار في دبي \| متنقل عند موقعك \| ركن التطور |
| Meta | في `hub/rank-math.json` | في `steam-car-wash-dubai/rank-math.json` — بلا إعلان للإيجار وبلا رقم مصري |
| Breadcrumb | الرئيسية > الخدمات > الصفحة | كما هو مسار الخدمات |
| Schema | Rank Math فقط + FAQ عبر Rank Math | حذف الكتلة المخصصة المكررة |

CTA: واتساب + اتصال `+971586634710` داخل المحتوى. لا نظام حجز جديد. لا تعديل Floating Buttons.

---

## 8. Content TODOs

- `TODO: CONFIRM_REAL_CAR_WASH_PRICING`
- `TODO: CONFIRM_DUBAI_SERVICE_AREAS`
- `TODO: ADD_REAL_MOBILE_CAR_WASH_IMAGES`
- `TODO: ADD_REAL_STEAM_CAR_WASH_IMAGE` (استبدال media 3143)
- `TODO: CONFIRM_WATERLESS_SERVICE` / `CONFIRM_WATERLESS_SUBSCRIPTIONS_FLEETS`
- `TODO: CONFIRM_MONTHLY_PLANS`
- `TODO: CONFIRM_FLEET_SERVICE`
- `TODO: CONFIRM_WATER_POWER_REQUIREMENTS`
- `TODO: CONFIRM_TOWER_PARKING_ACCESS_RULES`

---

## 9. Risks

1. المسودات غير مرئية لجوجل حتى اللصق والنشر اليدوي — هذا مقصود.  
2. إن وُرث قالب Rank Math الجماعي «الإعلان للإيجار» عند إنشاء البوست، يجب إدخال Title يدوياً من JSON.  
3. H1 مكرر إذا أُلصق `<h1>` إضافي في المحرر؛ الجسم الحالي بدون H1 عمداً.  
4. فقرة أبوظبي والكراج تعدّل صفحات قائمة — جراحية وموافقة اللصق منفصلة إن رغبت بتأجيلها.  
5. `/city/dubai/` أرشيف؛ الربط الآمن = إسناد taxonomy لا تعديل الثيم.  
6. Cannibalization البخار تُدار بالنية لا بالدمج؛ إن بقي المحتوى القديم على الحي فستبقى مشكلة قالب المنازل.  
7. لا أسعار = ضعف أمام صفحات المنافسين ذات «من 15 درهم» حتى يتوفر `CONFIRM_REAL_CAR_WASH_PRICING`.

---

## 10. Files Changed

```
content/mobile-car-wash-dubai/README.md
content/mobile-car-wash-dubai/wordpress-publish-notes.md
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
```

لم يُغيَّر أي ملف HTML/CSV للخدمات القائمة في جذر المستودع.

---

## 11. Git Commit IDs

| Commit | الرسالة |
|---|---|
| `17fef6a` | feat: add mobile car wash Dubai Batch 1 structure |
| `f4b6c3c` | feat: add Dubai mobile car wash hub SEO content |
| `347c789` | feat: rewrite steam-car-wash-dubai as supporting car-wash page |
| `b747a08` | feat: add contextual internal links for the car-wash hub |
| *(هذا التقرير)* | docs: add Batch 1 implementation report |

---

## الخطوة التالية

بعد مراجعتك: لصق Hub كـ Draft في ووردبريس، ثم استبدال جسم البخار، ثم الفقرات الداخلية. لا Batch 2 مواقع حتى `CONFIRM_DUBAI_SERVICE_AREAS`.
