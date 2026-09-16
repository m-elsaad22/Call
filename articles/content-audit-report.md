# تقرير فحص الموقع والمحتوى — مقالات الدفعة المعاد كتابتها

## الخلاصة
تم فحص **121 مقالاً** كُتبت بقالب موحّد مستوحى من مقالات كشف التسربات. النتيجة:

| المشكلة | العدد |
|---|---:|
| تكرار فقرات متطابقة | 121 |
| حشو قبل الخاتمة | 121 |
| تكرار مفرط لـ [post_call] | 121 |
| قالب تسربات/أعطال غير مناسب للخدمة | 76 |

**الحكم:** المحتوى الحالي فيه حشو وتكرار بنيوي بسبب تمديد الفقرات للوصول لـ ~3500 كلمة وضبط كثافة الكلمة المفتاحية، والقالب يتحدث بلغة «عطل/إهمال/تشخيص/ترقيع» حتى في خدمات مثل زراعة النخيل والتنظيف والتركيب.

## 1) مثال واضح: شركة زرع نخيل وأشجار في أبوظبي
- الرابط: https://www.rukn-eltatawer.com/palm-tree-planting-abu-dhabi/
- يستخدم عناوين مثل: «علامات ومخاطر إهمال…»، «الأسباب الجذرية والتشخيص الاحترافي»، «تفاقم المشكلة»، «ترقيع»، «تكرار العطل» — وهذا قالب تسربات/صيانة وليس زراعة نخيل.
- قبل الخاتمة تتكرر نفس الفقرات عشرات المرات، أشهرها:
  - «تظل المتابعة بعد التسليم…» (~20 مرة)
  - «في أحياء أبوظبي المختلفة يساعد التوثيق الواضح…» (~17 مرة)
  - «يعتمد نجاح … على التشخيص الصحيح…» (~12 مرة)
- `[post_call]` مكرر **17** مرة داخل المقال.

## 2) حشو نهاية المقال (قبل الخاتمة)
**كل الـ 121 مقالاً** فيها حشو فقرات متشابهة قبل الخاتمة. الأسوأ (~45–46 فقرة حشو):
- (46) نجار أثاث في رأس الخيمة — https://www.rukn-eltatawer.com/furniture-carpenter-ras-al-khaimah/
- (46) نجار أثاث في أم القيوين — https://www.rukn-eltatawer.com/furniture-carpenter-umm-al-quwain/
- (46) غسيل سيارات بالبخار في دبي — https://www.rukn-eltatawer.com/steam-car-wash-dubai/
- (46) شركة قص عشب طبيعي في دبي — https://www.rukn-eltatawer.com/natural-grass-cutting-dubai/
- (46) شركة عزل خزانات في دبي — https://www.rukn-eltatawer.com/tank-insulation-dubai/
- (46) شركة عزل خزانات في العين — https://www.rukn-eltatawer.com/tank-insulation-al-ain/
- (46) شركة تنظيف فلل الشارقة — https://www.rukn-eltatawer.com/sharjah-villas-cleaning-company/
- (46) شركة ترميم مباني أبوظبي — https://www.rukn-eltatawer.com/a-restoration-company-in-abu-dhabi/
- (46) شركة ترميم حمامات في دبي — https://www.rukn-eltatawer.com/bathroom-renovation-dubai/
- (46) شركة تركيب جبس بورد في دبي — https://www.rukn-eltatawer.com/gypsum-board-installation-in-dubai/
- (46) شركة تركيب انترلوك في دبي — https://www.rukn-eltatawer.com/interlock-installation-dubai/
- (46) سباك منازل في رأس الخيمة — https://www.rukn-eltatawer.com/home-plumber-ras-al-khaimah/
- (46) حداد أبواب ونوافذ في العين — https://www.rukn-eltatawer.com/door-window-blacksmith-al-ain/
- (45) شركة مكافحة الوزغ (البرص) في دبي — https://www.rukn-eltatawer.com/lizard-control-dubai/
- (45) شركة مكافحة الأفاعي في عجمان — https://www.rukn-eltatawer.com/snake-control-ajman/
- (45) شركة قص عشب طبيعي في الشارقة — https://www.rukn-eltatawer.com/natural-grass-cutting-sharjah/
- (45) شركة صيانة ميكروويف في عجمان — https://www.rukn-eltatawer.com/microwave-repair-ajman/
- (45) شركة صيانة ميكروويف في العين — https://www.rukn-eltatawer.com/microwave-repair-al-ain/
- (45) شركة صيانة مصاعد في الفجيرة — https://www.rukn-eltatawer.com/elevator-maintenance-fujairah/
- (45) شركة صيانة مصاعد في العين — https://www.rukn-eltatawer.com/elevator-maintenance-al-ain/
- (45) شركة صيانة مبردات مياه في عجمان — https://www.rukn-eltatawer.com/water-cooler-repair-ajman/
- (45) شركة صيانة مبردات مياه في العين — https://www.rukn-eltatawer.com/water-cooler-repair-al-ain/
- (45) شركة صيانة غسالات في الفجيرة — https://www.rukn-eltatawer.com/washing-machine-repair-fujairah/
- (45) شركة صيانة غسالات في العين — https://www.rukn-eltatawer.com/washing-machine-repair-al-ain/
- (45) شركة صيانة ثلاجات في الفجيرة — https://www.rukn-eltatawer.com/refrigerator-repair-fujairah/

## 3) مقالات بقالب تسربات غير مناسب لنوع الخدمة (76)
خدمات زراعة/حدائق/تنظيف/تركيب/نقل/مكافحة… مكتوبة بنفس هيكل كشف التسربات.

### install (32)
- شركة تركيب جبس بورد في أبوظبي — https://www.rukn-eltatawer.com/installing-gypsum-boards-in-abu-dhabi/
- شركة تركيب جبس بورد في دبي — https://www.rukn-eltatawer.com/gypsum-board-installation-in-dubai/
- حداد أبواب ونوافذ في العين — https://www.rukn-eltatawer.com/door-window-blacksmith-al-ain/
- شركة تركيب طابوق في أبوظبي — https://www.rukn-eltatawer.com/brick-installation-abu-dhabi/
- شركة تركيب بلاستر في أبوظبي — https://www.rukn-eltatawer.com/plaster-installation-abu-dhabi/
- شركة تركيب انترلوك في دبي — https://www.rukn-eltatawer.com/interlock-installation-dubai/
- شركة تركيب بلاستر في عجمان — https://www.rukn-eltatawer.com/plaster-installation-ajman/
- شركة تركيب بلاستر في الفجيرة — https://www.rukn-eltatawer.com/plaster-installation-fujairah/
- شركة تركيب أرضيات فينيل في أبوظبي — https://www.rukn-eltatawer.com/vinyl-flooring-abu-dhabi/
- شركة تركيب حجر طبيعي وصناعي في أبوظبي — https://www.rukn-eltatawer.com/stone-installation-abu-dhabi/
- شركة تركيب أسقف جبسية / فورسيلنج في أبوظبي — https://www.rukn-eltatawer.com/false-ceiling-installation-abu-dhabi/
- شركة تركيب سجاد أرضيات في أبوظبي — https://www.rukn-eltatawer.com/floor-carpet-installation-abu-dhabi/
- شركة تركيب حجر طبيعي وصناعي في عجمان — https://www.rukn-eltatawer.com/stone-installation-ajman/
- شركة تركيب أرضيات فينيل في رأس الخيمة — https://www.rukn-eltatawer.com/vinyl-flooring-ras-al-khaimah/
- شركة تركيب مطابخ في الشارقة — https://www.rukn-eltatawer.com/kitchen-installation-sharjah/
- شركة تركيب أبواب في الشارقة — https://www.rukn-eltatawer.com/door-installation-sharjah/
- شركة تركيب حجر طبيعي وصناعي في الفجيرة — https://www.rukn-eltatawer.com/stone-installation-fujairah/
- شركة تركيب أسقف مستعارة في عجمان — https://www.rukn-eltatawer.com/suspended-ceiling-installation-ajman/
- شركة تركيب أرضيات فينيل في أم القيوين — https://www.rukn-eltatawer.com/vinyl-flooring-umm-al-quwain/
- شركة تركيب حجر طبيعي وصناعي في أم القيوين — https://www.rukn-eltatawer.com/stone-installation-umm-al-quwain/
- شركة تركيب حجر طبيعي وصناعي في العين — https://www.rukn-eltatawer.com/stone-installation-al-ain/
- شركة تركيب شبابيك في العين — https://www.rukn-eltatawer.com/window-installation-al-ain/
- شركة تركيب مظلات وسواتر في العين — https://www.rukn-eltatawer.com/shade-installation-al-ain/
- شركة تركيب عرائش خشبية في دبي — https://www.rukn-eltatawer.com/wooden-arbor-installation-dubai/
- شركة تركيب أنظمة طاقة شمسية في أبوظبي — https://www.rukn-eltatawer.com/solar-systems-installation-abu-dhabi/
- شركة تركيب سخانات طاقة شمسية في أبوظبي — https://www.rukn-eltatawer.com/solar-water-heaters-abu-dhabi/
- شركة تركيب أنظمة طاقة شمسية في الشارقة — https://www.rukn-eltatawer.com/solar-systems-installation-sharjah/
- شركة تركيب أنظمة طاقة شمسية في عجمان — https://www.rukn-eltatawer.com/solar-systems-installation-ajman/
- شركة تركيب سخانات طاقة شمسية في عجمان — https://www.rukn-eltatawer.com/solar-water-heaters-ajman/
- شركة تركيب أنظمة طاقة شمسية في رأس الخيمة — https://www.rukn-eltatawer.com/solar-systems-installation-ras-al-khaimah/
- شركة تركيب سخانات طاقة شمسية في رأس الخيمة — https://www.rukn-eltatawer.com/solar-water-heaters-ras-al-khaimah/
- شركة تركيب أنظمة طاقة شمسية في الفجيرة — https://www.rukn-eltatawer.com/solar-systems-installation-fujairah/

### landscaping (28)
- شركة تنظيف واجهات حجرية في أبوظبي — https://www.rukn-eltatawer.com/stone-facade-cleaning-abu-dhabi/
- شركة تنظيف محلات تجارية ومولات في دبي — https://www.rukn-eltatawer.com/mall-cleaning-dubai/
- شركة تنظيف واجهات حجرية في العين — https://www.rukn-eltatawer.com/stone-facade-cleaning-al-ain/
- شركة تركيب عشب صناعي في الشارقة — https://www.rukn-eltatawer.com/artificial-grass-installation-sharjah/
- شركة زرع نخيل وأشجار في أبوظبي — https://www.rukn-eltatawer.com/palm-tree-planting-abu-dhabi/
- شركة تركيب شبكات ري أوتوماتيكية في أبوظبي — https://www.rukn-eltatawer.com/automatic-irrigation-abu-dhabi/
- شركة تركيب نوافير جدارية في أبوظبي — https://www.rukn-eltatawer.com/wall-fountain-abu-dhabi/
- شركة قص عشب طبيعي في دبي — https://www.rukn-eltatawer.com/natural-grass-cutting-dubai/
- شركة تركيب شبكات ري حديثة في دبي — https://www.rukn-eltatawer.com/modern-irrigation-dubai/
- شركة تركيب شبكات ري أوتوماتيكية في دبي — https://www.rukn-eltatawer.com/automatic-irrigation-dubai/
- شركة تركيب شلالات اصطناعية في دبي — https://www.rukn-eltatawer.com/artificial-waterfall-dubai/
- شركة تركيب نوافير جدارية في دبي — https://www.rukn-eltatawer.com/wall-fountain-dubai/
- شركة قص عشب طبيعي في الشارقة — https://www.rukn-eltatawer.com/natural-grass-cutting-sharjah/
- شركة تركيب نوافير جدارية في الشارقة — https://www.rukn-eltatawer.com/wall-fountain-sharjah/
- شركة زرع نخيل وأشجار في عجمان — https://www.rukn-eltatawer.com/palm-tree-planting-ajman/
- شركة تركيب شبكات ري أوتوماتيكية في عجمان — https://www.rukn-eltatawer.com/automatic-irrigation-ajman/
- شركة تنسيق حدائق في رأس الخيمة — https://www.rukn-eltatawer.com/landscaping-ras-al-khaimah/
- شركة تركيب شبكات ري حديثة في رأس الخيمة — https://www.rukn-eltatawer.com/modern-irrigation-ras-al-khaimah/
- شركة تركيب شبكات ري أوتوماتيكية في رأس الخيمة — https://www.rukn-eltatawer.com/automatic-irrigation-ras-al-khaimah/
- شركة تركيب برجولات في رأس الخيمة — https://www.rukn-eltatawer.com/pergola-installation-ras-al-khaimah/
- شركة تنسيق حدائق في أم القيوين — https://www.rukn-eltatawer.com/landscaping-umm-al-quwain/
- شركة زرع نخيل وأشجار في العين — https://www.rukn-eltatawer.com/palm-tree-planting-al-ain/
- شركة تنظيف وتزيين الحدائق في العين — https://www.rukn-eltatawer.com/garden-cleaning-al-ain/
- شركة تركيب شبكات ري حديثة في العين — https://www.rukn-eltatawer.com/modern-irrigation-al-ain/
- شركة تركيب شلالات اصطناعية في العين — https://www.rukn-eltatawer.com/artificial-waterfall-al-ain/
- شركة عزل حراري في رأس الخيمة — https://www.rukn-eltatawer.com/thermal-insulation-ras-al-khaimah/
- شركة تركيب وصيانة أجهزة تبريد مياه الخزانات في الفجيرة — https://www.rukn-eltatawer.com/water-tank-cooling-fujairah/
- شركة أنظمة السلامة ومكافحة الحريق في دبي — https://www.rukn-eltatawer.com/shrkh-anzmh-alslamh-wmkafhh-alhryq-fy-dby/

### cleaning (9)
- شركة تنظيف منازل في عجمان — https://www.rukn-eltatawer.com/house-cleaning-company-in-ajman/
- شركة تنظيف فلل الشارقة — https://www.rukn-eltatawer.com/sharjah-villas-cleaning-company/
- شركة تنظيف المداخن والشفاطات في أبوظبي — https://www.rukn-eltatawer.com/chimney-cleaning-abu-dhabi/
- شركة تنظيف كراجات ومواقف سيارات في الفجيرة — https://www.rukn-eltatawer.com/garage-cleaning-fujairah/
- شركة تنظيف المداخن والشفاطات في الفجيرة — https://www.rukn-eltatawer.com/chimney-cleaning-fujairah/
- شركة تنظيف خزانات ديزل في العين — https://www.rukn-eltatawer.com/diesel-tank-cleaning-al-ain/
- شركة تنظيف المداخن والشفاطات في العين — https://www.rukn-eltatawer.com/chimney-cleaning-al-ain/
- شركة غسيل سيارات بالبخار في أبوظبي – ركن التطور — https://www.rukn-eltatawer.com/steam-car-wash-abu-dhabi/
- غسيل سيارات بالبخار في دبي — https://www.rukn-eltatawer.com/steam-car-wash-dubai/

### pest (4)
- شركة مكافحة الوزغ (البرص) في دبي — https://www.rukn-eltatawer.com/lizard-control-dubai/
- شركة مكافحة الأفاعي في عجمان — https://www.rukn-eltatawer.com/snake-control-ajman/
- شركة مكافحة الحشرات الزاحفة في رأس الخيمة — https://www.rukn-eltatawer.com/crawling-pest-control-ras-al-khaimah/
- شركة مكافحة الأفاعي في رأس الخيمة — https://www.rukn-eltatawer.com/snake-control-ras-al-khaimah/

### moving (3)
- شركة تخزين أثاث في رأس الخيمة — https://www.rukn-eltatawer.com/furniture-storage-ras-al-khaimah/
- شركة نقل أثاث داخلي في الفجيرة — https://www.rukn-eltatawer.com/indoor-moving-fujairah/
- شركة تخزين أثاث في الفجيرة — https://www.rukn-eltatawer.com/furniture-storage-fujairah/

## 4) ملاحظات إضافية متكررة
- تكرار فقرات متطابقة حرفياً: في **كل** المقالات المفحوصة.
- `[post_call]` مفرط (≥12، وغالباً 17): في **كل** المقالات.
- صياغة العناوين أحياناً غير طبيعية لأن الكلمة المفتاحية = اسم المقال كاملاً (مثل: «نصائح وقائية للحفاظ على نتائج شركة زرع نخيل وأشجار في أبوظبي»).

## 5) التوصية
1. إزالة فقرات الحشو المكررة قبل الخاتمة فوراً من كل المقالات المعاد كتابتها.
2. إعادة كتابة القالب حسب نوع الخدمة (زراعة/تنظيف/تركيب/صيانة/مكافحة) بدل قالب التسربات العام.
3. تقليل `[post_call]` إلى 3–5 كحد أقصى.
4. الإبقاء على الشورت كودات والميتات، مع محتوى حقيقي خاص بكل خدمة.
