# ديمو أنواع صفحات ركن التطور

معاينة بصرية **قبل أي تعديل على القالب الحي**. ملفات الكيت الأصلية في جذر المشروع لم تُمس.

## الفتح

من مجلد `demos/` عبر خادم محلي (أفضل من `file://` لأن الخطوط والصور تُحمَّل بشكل أوضح):

```bash
python3 -m http.server 8765 --directory demos
```

ثم:

- معرض كل الأنواع: http://127.0.0.1:8765/index.html
- الرئيسية المقترحة: http://127.0.0.1:8765/home.html
- التصاميم المرفقة الأصلية: http://127.0.0.1:8765/originals.html

## ماذا في كل نوع

| ملف | نوع الصفحة |
|---|---|
| `home.html` | الرئيسية |
| `services.html` | أرشيف الخدمات |
| `service-category.html` | فئة (العزل) |
| `service-single.html` | خدمة مفردة |
| `service-city.html` | خدمة × مدينة |
| `cities.html` | فهرس المدن |
| `city-single.html` | مدينة |
| `portfolio.html` | معرض أعمال |
| `blog.html` | المدونة |
| `article-single.html` | مقال |
| `about.html` | من نحن |
| `reviews.html` | التقييمات |
| `offers.html` | العروض |
| `faq.html` | الأسئلة |
| `booking.html` | طلب معاينة |
| `contact.html` | اتصل بنا |
| `privacy.html` / `terms.html` | قانوني |
| `404.html` | خطأ 404 |

إعادة التوليد بعد تعديل المحتوى: `python3 demos/_build.py`
