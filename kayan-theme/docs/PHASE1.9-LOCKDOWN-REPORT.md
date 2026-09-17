# Phase 1.9 — Production Lockdown

**الإصدار:** `2027.3.9`  
**الفرع:** `cursor/phase1-9-production-lockdown-cda3`  
**الهدف:** KAYAN Theme = المصدر الوحيد لسلوك الموقع — بدون snippets مخفية، بدون أنظمة مكررة.

---

## TASK 1 — إزالة الاعتماديات الخارجية

### ما يمكن تأكيده من HTML الإنتاج (rukn-eltatawer.com — 2026-06-18)

| الاسم | النوع | المسار | ما يحقنه | لماذا موجود | القرار |
|-------|-------|--------|----------|-------------|--------|
| **KAYAN legacy tracker** | Code Snippet | WP Admin → Snippets | `kt_*` عبر admin-ajax (إن وُجد) | تتبع قديم قبل KAYAN Track | **REMOVE** |
| **rukn_track_*** | Code Snippet | Snippet inline في `<body>` | `rukn_track_click`, `rukn_track_pv` | تتبع نقرات/زيارات مخصص | **REMOVE** |
| **_rsa_sid pageview** | Code Snippet | نفس الـ snippet | `sessionStorage` + pageview | تحليلات جلسات legacy | **REMOVE** |
| **Organization JSON-LD** | Code Snippet | `<head>` سطر ~42 | `{"@type":"Organization","sameAs":[]}` | schema يدوي قديم | **REMOVE** |
| **Rank Math SEO** | Plugin | `wp-content/plugins/seo-by-rank-math/` | meta مكررة + `rank-math-schema` | SEO plugin | **KEEP** (تخزين meta) / **تعطيل frontend** |
| **LiteSpeed Cache** | Plugin | `wp-content/plugins/litespeed-cache/` | cache HTML + lazy-load + speculationrules | أداء | **KEEP** — Purge بعد النشر |
| **KAYAN SEO** | Theme pack | `kayan-seo/` | meta + JSON-LD (مجموعة #2) | Phase 1 | **KEEP** — المصدر الوحيد بعد النشر |
| **header___codes** | Theme option | إعدادات القالب | HTML خام في `<head>` | حقن مخصص | **BLOCK** عند lockdown (`lockdown.php`) |
| **Child theme** | Theme | `wp-content/themes/*-child/` | غير مؤكد عن بُعد | — | **AUDIT على السيرفر** — REMOVE إن وُجد |
| **Mu-plugins** | mu-plugin | `wp-content/mu-plugins/` | غير مرئي في HTML | — | **AUDIT على السيرفر** |
| **WPCode / Insert Headers** | Plugin | `wp-content/plugins/wpcode*` | حقن head/footer | تداخل مع SEO/Track | **AUDIT** — REMOVE snippets التتبع/SEO |
| **Analytics (GA/GTM/Pixel)** | Plugin/Snippet | غير ظاهر في لقطة الرئيسية | — | — | **AUDIT** — أي analytics يجب أن يمر عبر KAYAN Track فقط |

### ملاحظة

لا يمكن سرد plugins النشطة من REST بدون مصادقة (`rest_cannot_view_plugins`). **مطلوب فحص يدوي:** `wp plugin list` على السيرفر.

السجل الكامل في الكود: `kayan_lockdown_get_external_dependency_registry()` — `kayan-stabilization/lockdown.php`

---

## TASK 2 — مصدر واحد للحقيقة (Single Source of Truth)

| المجال | المصدر الوحيد (بعد النشر) | الحالة على الإنتاج |
|--------|---------------------------|-------------------|
| **Title** | `kayan-seo/frontend.php` → `wp_head` priority 0 | ⚠️ مكرر مع Rank Math |
| **Meta / Canonical / OG** | `kayan_seo_render_head_meta()` priority 2 | ❌ ×2 |
| **Schema JSON-LD** | `kayan_seo_output_schema_graph()` | ❌ ×3 graphs |
| **Tracking WhatsApp/Call/Form** | `kayan-track/tracker.js` → `/kayan/v1/track` | ❌ snippets legacy |
| **Sessions** | `wp_kayan_sessions` | ❌ `_rsa_sid` snippet |
| **DNI** | `kayan-track` REST `/dni` | ❌ غير منشور |
| **Homepage** | `kayan-homepage` v3 + `homepage.php` takeover | ❌ `intro-model-slider_intro_v1` |
| **CSS/JS (home)** | `kayan-home.css` + `kayan-home.js` | ❌ jQuery + Owl + setup.js |
| **Fonts** | Google Fonts + FA 6.5.1 عبر v3 enqueue | ⚠️ legacy FA في header |

### إنفاذ Lockdown في `2027.3.9`

| الملف | الإجراء |
|-------|---------|
| `kayan-stabilization/lockdown.php` | OB sanitizer يزيل snippets + Rank Math schema + حقن head |
| `kayan-stabilization/lockdown.php` | `kayan_lockdown_filter_header_injection()` يفرغ `header___codes` |
| `kayan-seo/compatibility.php` | تعطيل Rank Math frontend |
| `kayan-stabilization/tracking.php` | إلغاء ajax legacy |
| `kayan-stabilization/homepage.php` | takeover v3 مبكر |
| `#header/part.php` | يمر عبر فلتر lockdown قبل echo |

**تعطيل lockdown (staging فقط):** `kayan_lockdown_disable = 1`

---

## TASK 3 — تدقيق الأمن والصيانة

### AJAX (`admin-ajax.php`)

| الفئة | العدد | الحماية |
|-------|-------|---------|
| KAYAN Track admin | 18 actions | `manage_options` + nonce |
| FieldsMachine | 19 actions | `wp_ajax_` فقط (مسجّل دخول) — **لا nonce مركزي** |
| Legacy `kt_*` / `rukn_*` | 0 في الثيم | من snippets — يُلغى عند Track |

### REST API

| Route | Method | Permission | ملاحظة |
|-------|--------|------------|--------|
| `kayan/v1/track` | POST | public | rate limit 20/min + IP blocklist |
| `kayan/v1/dni` | GET | public | rate limited |
| `?kayan_report={token}` | GET | token 64 hex | تقارير مشاركة |

### AjaxCenter (عام — `/ajaxcenter/`)

| قبل 1.9 | بعد 1.9 |
|---------|---------|
| `require($Action.'.php')` بدون تحقق | whitelist 11 actions + `basename()` + `file_exists()` |

### Inline scripts (frontend)

| الموقع | الخطورة |
|--------|---------|
| `#footer/part.php` globals | Medium — بيانات مستخدم بدون `esc_js` |
| `kayan-track` inline config | Low — REST URL فقط |
| Legacy snippets (إنتاج) | **Critical** — يجب حذفها |

### جداول DB (الثيم فقط)

| الجدول | الاستخدام |
|--------|-----------|
| `wp_kayan_conversions` | تحويلات |
| `wp_kayan_sessions` | جلسات |
| `wp_kayan_visitors` | زوار |
| `wp_kayan_heatmap` | خرائط حرارية |
| `wp_kayan_numbers` | أرقام |
| `wp_kayan_dni_rules` | DNI |
| `wp_kayan_blacklist` | حظر IP |
| `wp_kayan_reports` | تقارير |

**لا جداول obsolete** — كلها مستخدمة من KAYAN Track.

### Hooks legacy (ما زالت في الكود — معطّلة عند modern schema)

| Hook | الحالة |
|------|--------|
| `YourColor__Schema::insert__schema` | skipped عند modern |
| `ThemeSeo::Title()` | skipped عند KAYAN SEO |
| `rank_math/head` | removed عند lockdown |

---

## TASK 4 — تقرير صحة الإنتاج

### Health Scores

| المجال | الإنتاج (الآن) | الكود (بعد نشر 1.9) |
|--------|----------------|---------------------|
| **Code Health** | — | **82 / 100** |
| **SEO Health** | **38 / 100** | **95** (متوقع) |
| **Performance Health** | **62 / 100** | **78** (متوقع) |
| **Tracking Health** | **28 / 100** | **92** (متوقع) |
| **Architecture Health** | **45 / 100** | **74 / 100** |

**Code Health 82:** −8 AjaxCenter كان مفتوحاً (مُصلح)، −6 REST عام، −4 legacy paths ما زالت موجودة.

### Issues by severity (الإنتاج الحالي)

#### Critical
1. ثيم `2027.3.7` غير منشور — PR #13 لم يُدمج
2. meta description ×2، canonical ×2، JSON-LD ×3
3. 3 أنظمة tracking متوازية (snippets)
4. snippets قابلة للتنفيذ inline في HTML

#### High
5. Homepage legacy slider بدلاً من v3
6. Rank Math frontend ما زال يخرج schema
7. LiteSpeed cache قد يخدم HTML قديم (max-age 604800)
8. FieldsMachine ajax بدون nonce مركزي

#### Medium
9. `#footer/part.php` — PII في JS globals
10. `header___codes` كان يسمح بحقن خام (مُغلق في lockdown)
11. تكرار قمع Rank Math في ملفين

#### Low
12. مسار schema legacy احتياطي
13. Chart.js CDN في admin فقط
14. `ip-api.com` مع `sslverify => false` في Track

### أدوات التحقق

```bash
curl -sL https://www.rukn-eltatawer.com/ -o snapshot.html
php docs/validation-scripts/analyze-head-seo.php snapshot.html
php docs/validation-scripts/audit-production-lockdown.php snapshot.html
```

---

## TASK 5 — قرار GO / NO-GO لـ Phase 2

### ❌ NO-GO — لا تنتقل إلى CRM / Service×City / EEAT / Gemini AI بعد

| السبب | التفصيل |
|-------|---------|
| **الإنتاج ≠ الكود** | Phase 1.7/1.9 موجود في Git فقط — السيرفر يشغّل build قديم |
| **SEO غير موحّد** | محركات البحث ترى 2–3 مصادر meta/schema — يفسد أي programmatic SEO لاحق |
| **Tracking مكسور** | 3 أنظمة = بيانات conversion غير موثوقة — CRM يعتمد على tracking |
| **Homepage legacy** | v3 غير مفعّل — CWV وbrand experience غير مستقرة |
| **اعتماديات خارجية** | snippets لم تُحذف — سلوك مخفي خارج الثيم |
| **Architecture debt** | AjaxCenter/REST يحتاجان مراقبة قبل إضافة modules |

### شروط GO (يجب تحقيقها كلها)

- [ ] دمج PR #13 + PR #14 ونشر `2027.3.9`
- [ ] حذف كل snippets التتبع + Organization JSON-LD
- [ ] تفعيل KAYAN SEO + KAYAN Track
- [ ] LiteSpeed Purge All + تحقق HTML جديد
- [ ] `analyze-head-seo.php`: description=1, canonical=1, json_ld=1
- [ ] `audit-production-lockdown.php`: صفر legacy tracking signals
- [ ] Homepage: `kayan-homepage-v3` + `kayan-home.js`، بدون slider
- [ ] `wp plugin list` على السيرفر — لا SEO/tracking plugins مكررة
- [ ] Lighthouse mobile: Perf ≥ 80، LCP < 3s

**بعد تحقق القائمة:** يمكن البدء بـ Phase 2 — يُفضّل **Service×City** أو **EEAT** قبل CRM/AI لأنها تبني على SEO الموحّد.

---

## تغييرات الكود في 1.9

| الملف | التغيير |
|-------|---------|
| `kayan-stabilization/lockdown.php` | **جديد** — lockdown + sanitizer + registry |
| `kayan-stabilization/setup.php` | تحميل lockdown |
| `kayan-stabilization/tracking.php` | OB منقول إلى lockdown |
| `#header/part.php` | فلتر حقن head |
| `AjaxCenter/setup.php` | whitelist + path traversal fix |
| `style.css` | `2027.3.9` |
| `docs/validation-scripts/audit-production-lockdown.php` | **جديد** |

---

## ما لم يُنفَّذ (حسب الطلب)

- ❌ CRM / AI / Service×City / Master Dashboard / EEAT implementation
