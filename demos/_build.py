#!/usr/bin/env python3
"""Generate static demo HTML for each Rukn Eltatawer page type."""
from pathlib import Path

ROOT = Path(__file__).parent
WA = "https://wa.me/971586634710"
TEL = "tel:+971586634710"

PAGES = [
    ("home.html", "الرئيسية", "home"),
    ("services.html", "الخدمات", "services"),
    ("service-category.html", "فئة خدمة", "services"),
    ("service-single.html", "صفحة خدمة", "services"),
    ("service-city.html", "خدمة × مدينة", "services"),
    ("cities.html", "المدن", "cities"),
    ("city-single.html", "صفحة مدينة", "cities"),
    ("portfolio.html", "الأعمال", "portfolio"),
    ("blog.html", "المدونة", "blog"),
    ("article-single.html", "مقال", "blog"),
    ("about.html", "من نحن", "about"),
    ("reviews.html", "التقييمات", "about"),
    ("offers.html", "العروض", "offers"),
    ("faq.html", "الأسئلة", "faq"),
    ("booking.html", "طلب معاينة", "booking"),
    ("contact.html", "اتصل بنا", "contact"),
    ("privacy.html", "الخصوصية", "legal"),
    ("terms.html", "الشروط", "legal"),
    ("404.html", "صفحة 404", "404"),
]


def ico(letter="ر"):
    return f'<span class="logo-mark">{letter}</span>'


def header(active=""):
    def a(href, key, label):
        on = " is-on" if active == key else ""
        return f'<a class="{on.strip()}" href="{href}">{label}</a>'

    return f"""
<div class="demo-bar">
  معاينة تصميم مقترحة — ليست الموقع الحي
  <span>ملفات أصلية للمقارنة:</span>
  <a href="originals.html">فتح التصاميم المرفقة</a>
  <a href="index.html">كل أنواع الصفحات</a>
</div>
<a class="skip" href="#main">تخطي إلى المحتوى</a>
<header class="site-header">
  <div class="wrap nav">
    <a class="logo" href="home.html">{ico()}ركن <b>التطور</b></a>
    <nav class="menu">
      {a("services.html","services","الخدمات")}
      {a("cities.html","cities","المدن")}
      {a("portfolio.html","portfolio","الأعمال")}
      {a("blog.html","blog","المدونة")}
      {a("about.html","about","من نحن")}
    </nav>
    <div class="nav-end">
      <span class="lang"><em>ع</em> | EN</span>
      <a class="btn btn-wa" href="{WA}">واتساب</a>
      <a class="btn btn-navy" href="booking.html">اطلب معاينة</a>
      <button class="ham" aria-label="القائمة" type="button"><i></i></button>
    </div>
  </div>
</header>
<div class="drawer" aria-hidden="true">
  <nav>
    <button class="btn btn-ghost" data-close type="button">إغلاق</button>
    <a href="services.html">الخدمات</a>
    <a href="cities.html">المدن</a>
    <a href="portfolio.html">الأعمال</a>
    <a href="blog.html">المدونة</a>
    <a href="about.html">من نحن</a>
    <a href="faq.html">الأسئلة الشائعة</a>
    <a href="contact.html">اتصل بنا</a>
    <a class="btn btn-wa" href="{WA}">واتساب</a>
  </nav>
</div>
"""


FOOTER = f"""
<footer class="site-footer">
  <div class="wrap fgrid">
    <div>
      <div class="flogo">{ico()}ركن التطور</div>
      <p>شركة خدمات منزلية مرخّصة في الإمارات: كشف تسربات، عزل، تكييف، وسباكة. المقر في مدينة الشيخ محمد بن زايد، أبوظبي.</p>
      <p style="margin-top:12px"><a href="{TEL}">+971 58 663 4710</a>
      <a href="{WA}">واتساب</a>
      <a href="contact.html">Mazid Mall — MBZ، أبوظبي</a></p>
    </div>
    <div>
      <h4>الخدمات</h4>
      <a href="service-single.html">كشف تسربات المياه</a>
      <a href="service-category.html">عزل الأسطح والخزانات</a>
      <a href="services.html">صيانة التكييف</a>
      <a href="services.html">السباكة</a>
      <a href="services.html">مكافحة الحشرات</a>
    </div>
    <div>
      <h4>المدن</h4>
      <a href="city-single.html">دبي</a>
      <a href="cities.html">أبوظبي</a>
      <a href="cities.html">العين</a>
      <a href="cities.html">الشارقة</a>
      <a href="cities.html">عجمان</a>
      <a href="cities.html">رأس الخيمة</a>
      <a href="cities.html">الفجيرة</a>
      <a href="cities.html">أم القيوين</a>
    </div>
    <div>
      <h4>الشركة</h4>
      <a href="about.html">من نحن</a>
      <a href="reviews.html">التقييمات</a>
      <a href="offers.html">العروض</a>
      <a href="faq.html">الأسئلة</a>
      <a href="contact.html">اتصل بنا</a>
      <a href="privacy.html">الخصوصية</a>
      <a href="terms.html">الشروط</a>
    </div>
  </div>
  <div class="wrap copy">
    <span>© 2026 ركن التطور — ديمو تصميم قبل تطبيق الثيم</span>
    <span><a href="privacy.html">خصوصية</a> · <a href="terms.html">شروط</a></span>
  </div>
</footer>
<a class="fab" href="{WA}" aria-label="واتساب">WA</a>
<script src="js/theme.js"></script>
"""


def page(title, desc, active, body, extra_head=""):
    return f"""<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{title} | ديمو ركن التطور</title>
<meta name="description" content="{desc}">
<meta name="robots" content="noindex">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/theme.css">
{extra_head}
</head>
<body>
{header(active)}
<main id="main">
{body}
</main>
{FOOTER}
</body>
</html>
"""


SERVICES = [
    ("كشف تسربات المياه", "تحديد المصدر بالكاميرا الحرارية دون تكسير عشوائي.", "img/plumbing.jpg", "service-single.html"),
    ("عزل الأسطح", "عزل مائي وحراري وفق نظام يتناسب مع مناخ الإمارات.", "img/hero-roof.jpg", "service-category.html"),
    ("عزل وتنظيف الخزانات", "تنظيف صحي وإعادة عزل الخزان بمواد مناسبة لمياه الشرب.", "img/bathroom.jpg", "services.html"),
    ("صيانة التكييف", "غسيل، فحص، وشحن وفق حالة الوحدة لا باقة ثابتة عمياء.", "img/ac.jpg", "services.html"),
    ("أعمال السباكة", "إصلاح الشبكات والصرف مع تقرير بما تم تنفيذه.", "img/tools.jpg", "services.html"),
    ("مكافحة الحشرات", "معالجة مرخّصة مع توضيح المواد المستخدمة في المنزل.", "img/interior.jpg", "services.html"),
]

CITIES = [
    ("دبي", "نفس اليوم في أغلب الأحياء", "city-single.html"),
    ("أبوظبي", "يشمل مدينة الشيخ محمد بن زايد", "cities.html"),
    ("العين", "تغطية المدينة والأحياء الداخلية", "cities.html"),
    ("الشارقة", "فرق قريبة من المدينة", "cities.html"),
    ("عجمان", "جدولة مرنة خلال اليوم", "cities.html"),
    ("رأس الخيمة", "تنسيق موعد واضح مسبقاً", "cities.html"),
    ("الفجيرة", "خدمة بموعد محدد", "cities.html"),
    ("أم القيوين", "خدمة بموعد محدد", "cities.html"),
]


def service_cards(n=6):
    out = []
    for title, desc, img, href in SERVICES[:n]:
        out.append(f"""
        <article class="card">
          <div class="media" style="background-image:url('{img}')"></div>
          <div class="body">
            <h3>{title}</h3>
            <p>{desc}</p>
            <p style="margin-top:14px"><a class="link" href="{href}">تفاصيل الخدمة ←</a></p>
          </div>
        </article>""")
    return "\n".join(out)


def city_cards():
    out = []
    for name, sub, href in CITIES:
        out.append(f"""
        <a class="card city" href="{href}">
          <div><b>{name}</b><small>{sub}</small></div>
          <span class="link">الصفحة</span>
        </a>""")
    return "\n".join(out)


HOME = f"""
<section class="hero" style="background-image:url('img/hero-roof.jpg')">
  <div class="wrap">
    <div>
      <span class="kicker">شركة خدمات منزلية — الإمارات</span>
      <h1>كشف، عزل، وصيانة بضمان مكتوب على العمل المنفَّذ</h1>
      <p class="lead">فريق ميداني من أبوظبي يخدم الإمارات السبع والعين. المعاينة الأولى بدون التزام، والسعر بعد معاينة الموقع لا من نموذج آلي.</p>
      <div class="hero-proof">
        <span class="chip">مقر أبوظبي — Mazid Mall</span>
        <span class="chip">واتساب للجدولة</span>
        <span class="chip">ضمان على العزل وفق عرض السعر</span>
      </div>
    </div>
    <form class="quote-card" data-wa-form>
      <h2>اطلب معاينة</h2>
      <p>نرد عبر واتساب لتأكيد الموعد.</p>
      <div class="fld"><label>الخدمة</label>
        <select name="service">{"".join(f"<option>{s[0]}</option>" for s in SERVICES)}</select>
      </div>
      <div class="fld"><label>المدينة</label>
        <select name="city">{"".join(f"<option>{c[0]}</option>" for c in CITIES)}</select>
      </div>
      <div class="fld"><label>رقم الهاتف</label><input name="phone" type="tel" placeholder="05xxxxxxxx" required></div>
      <button class="btn btn-wa" type="submit" style="width:100%">إرسال عبر واتساب</button>
    </form>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="shead">
      <div class="tag">الخدمات</div>
      <h2>ست خدمات نركز عليها</h2>
      <p>لا سوبرماركت خدمات. إن احتجت عملاً خارج هذا النطاق نخبرك بوضوح.</p>
    </div>
    <div class="grid-3">{service_cards()}</div>
  </div>
</section>

<section class="sec sec-alt">
  <div class="wrap">
    <div class="shead center">
      <div class="tag">طريقة العمل</div>
      <h2>أربع خطوات حتى التسليم</h2>
    </div>
    <div class="steps">
      <div class="step"><b>1</b><h3>تواصل</h3><p>واتساب أو نموذج قصير. نحدد حيّك ونوع العقار.</p></div>
      <div class="step"><b>2</b><h3>معاينة</h3><p>كشف ميداني وتوضيح الخيار الأنسب بدون التزام بالتنفيذ.</p></div>
      <div class="step"><b>3</b><h3>عرض سعر</h3><p>بند مكتوب: نطاق العمل، المدة، والضمان إن وُجد.</p></div>
      <div class="step"><b>4</b><h3>تنفيذ ومتابعة</h3><p>فريق واحد من البداية حتى الاستلام.</p></div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="shead">
      <div class="tag">التغطية</div>
      <h2>الإمارات السبع + العين</h2>
      <p>العين مدينة ضمن أبوظبي، ولها صفحة مستقلة لأن الطلب الميداني مختلف.</p>
    </div>
    <div class="grid-4">{city_cards()}</div>
  </div>
</section>

<section class="sec sec-alt">
  <div class="wrap">
    <div class="shead">
      <div class="tag">نتائج</div>
      <h2>قبل وبعد من أعمال عزل</h2>
      <p>الصور هنا للمعاينة البصرية. تُستبدل بمشاريع موثّقة قبل الإطلاق.</p>
    </div>
    <div class="card">
      <div class="ba">
        <div style="background-image:url('img/roof-close.jpg')"><span>قبل</span></div>
        <div style="background-image:url('img/hero-roof.jpg')"><span>بعد</span></div>
      </div>
      <div class="body"><h3>عزل سطح فيلا — أبوظبي</h3><p>تجهيز السطح، نظام عزل، واختبار رش. الضمان يُذكر في عرض السعر لا كختم عام.</p></div>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="shead">
      <div class="tag">آراء</div>
      <h2>نماذج تقييم للعرض</h2>
      <p>عند الإطلاق تُستبدل بتقييمات Google Business الحقيقية دون شارة مزيّفة.</p>
    </div>
    <div class="grid-3">
      <article class="card review"><div class="body"><div class="stars">★★★★★</div><p>حددوا التسرب في الحمام دون تكسير البلاط كامل.</p><div class="who">عميل في دبي مارينا<small>كشف تسربات</small></div></div></article>
      <article class="card review"><div class="body"><div class="stars">★★★★★</div><p>عرض السعر كان مطابقاً لما نُفّذ على السطح.</p><div class="who">عميلة في الشامخة<small>عزل أسطح</small></div></div></article>
      <article class="card review"><div class="body"><div class="stars">★★★★☆</div><p>وصلوا في موعد العين المتفق عليه، والتنفيذ استغرق يومين كما قيل.</p><div class="who">عميل في العين<small>عزل</small></div></div></article>
    </div>
  </div>
</section>

<section class="sec sec-alt">
  <div class="wrap">
    <div class="shead center"><div class="tag">أسئلة</div><h2>قبل أن تتواصل</h2></div>
    <div class="faq" style="margin-inline:auto">
      <details class="faq-item" open><summary>هل المعاينة مجانية؟</summary><p>المعاينة الأولى لتقدير العمل مجانية في المدن الرئيسية. إن تطلب الأمر أجهزة كشف مطوّلة نخبرك قبل البدء.</p></details>
      <details class="faq-item"><summary>هل تعملون في كل الإمارات؟</summary><p>نعم: دبي، أبوظبي، العين، الشارقة، عجمان، رأس الخيمة، الفجيرة، وأم القيوين. زمن الوصول يختلف حسب المسافة.</p></details>
      <details class="faq-item"><summary>ما الضمان؟</summary><p>يُكتب في عرض السعر حسب نظام العزل أو نوع الإصلاح. لا نضع «10 سنوات» كختم على كل خدمة.</p></details>
    </div>
  </div>
</section>

<section class="cta">
  <div class="wrap">
    <h2>جاهزون لمعاينة موقعك</h2>
    <p>راسلنا على واتساب بالحي ونوع الخدمة.</p>
    <div class="row">
      <a class="btn btn-wa" href="{WA}">واتساب</a>
      <a class="btn btn-line" href="booking.html">نموذج المعاينة</a>
    </div>
  </div>
</section>
"""

SERVICES_PAGE = f"""
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>الخدمات</span></div>
  <h1>الخدمات المنزلية التي نقدّمها</h1>
  <p class="psub">ست خدمات أساسية. كل بطاقة تفتح قالبها الصحيح عند الربط مع ووردبريس.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="pills">
    <a class="on" href="services.html">الكل</a>
    <a href="service-category.html">المياه والعزل</a>
    <a href="services.html">التكييف</a>
    <a href="services.html">الصيانة</a>
  </div>
  <div class="grid-3">{service_cards()}</div>
  <p class="notice" style="margin-top:28px">في الكيت المرفق كانت 12 خدمة (منها حراسات ونقل أثاث) تشير كلها لصفحة تسربات واحدة. هنا الخدمات محدودة والروابط متمايزة.</p>
</div></section>
"""

CATEGORY = """
<section class="phero" style="background-image:url('img/hero-roof.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><a href="services.html">الخدمات</a><span>/</span><span>العزل</span></div>
  <h1>خدمات العزل المائي والحراري</h1>
  <p class="psub">فئة تجمع أنظمة الأسطح، الخزانات، والحمامات. الضمان يُحدد حسب النظام لا كرقم واحد لكل المشاريع.</p>
  <div class="phero-meta"><div><b>6</b><small>أنظمة فرعية</small></div><div><b>معاينة</b><small>قبل عرض السعر</small></div></div>
</div></section>
<section class="sec"><div class="wrap">
  <div class="grid-3">
    <article class="card"><div class="media" style="background-image:url('img/hero-roof.jpg')"></div><div class="body"><h3>عزل أسطح المنازل</h3><p>تجهيز، نظام مائي/حراري، واختبار.</p><p style="margin-top:12px"><a class="link" href="service-single.html">التفاصيل</a></p></div></article>
    <article class="card"><div class="media" style="background-image:url('img/bathroom.jpg')"></div><div class="body"><h3>عزل الخزانات</h3><p>مواد مناسبة لخزانات مياه الشرب بعد التنظيف.</p><p style="margin-top:12px"><a class="link" href="service-single.html">التفاصيل</a></p></div></article>
    <article class="card"><div class="media" style="background-image:url('img/roof-close.jpg')"></div><div class="body"><h3>Cool Roof</h3><p>طبقة عاكسة تُدرس حسب حالة السطح القائمة.</p><p style="margin-top:12px"><a class="link" href="service-single.html">التفاصيل</a></p></div></article>
    <article class="card"><div class="body"><h3>عزل صناعي وتجاري</h3><p>مساحات أكبر وجدول تنفيذ مرحلي.</p></div></article>
    <article class="card"><div class="body"><h3>الحمامات والمطابخ</h3><p>عزل موضعي لمناطق الرطوبة العالية.</p></div></article>
    <article class="card"><div class="body"><h3>الأساسات</h3><p>حالات تُقيَّم هندسياً قبل الالتزام.</p></div></article>
  </div>
</div></section>
<section class="sec sec-alt"><div class="wrap">
  <div class="shead"><div class="tag">التسلسل</div><h2>كيف ننفّذ العزل</h2></div>
  <div class="steps">
    <div class="step"><b>1</b><h3>مسح</h3><p>مساحة، ميل، ومصدر الرطوبة إن وُجد.</p></div>
    <div class="step"><b>2</b><h3>تجهيز</h3><p>إزالة التالف وتنظيف السطح.</p></div>
    <div class="step"><b>3</b><h3>نظام العزل</h3><p>طبقات حسب المواصفة المتفق عليها.</p></div>
    <div class="step"><b>4</b><h3>اختبار</h3><p>رش أو فحص، ثم تسليم البنود المكتوبة.</p></div>
  </div>
</div></section>
"""

SINGLE = f"""
<section class="phero" style="background-image:url('img/plumbing.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><a href="services.html">الخدمات</a><span>/</span><span>كشف تسربات</span></div>
  <h1>كشف تسربات المياه بدون تكسير عشوائي</h1>
  <p class="psub">كاميرا حرارية وأجهزة استشعار لتحديد نقطة التدخل قبل أي فتح في الجدار أو الأرضية.</p>
  <p style="margin-top:22px;display:flex;gap:10px;flex-wrap:wrap">
    <a class="btn btn-wa" href="{WA}">واتساب</a>
    <a class="btn btn-line" href="booking.html">طلب معاينة</a>
  </p>
</div></section>
<section class="sec"><div class="wrap grid-2">
  <div>
    <h2>متى تحتاج الكشف؟</h2>
    <p style="color:var(--muted);margin:12px 0 18px">ارتفاع فاتورة المياه، بقع رطوبة، أو صوت جريان دون استخدام. التأخير يوسّع الضرر في العزل والدهان.</p>
    <h2>خطوات العمل</h2>
    <ol style="padding-inline-start:18px;color:var(--muted);display:grid;gap:8px;margin-top:10px">
      <li>معاينة الأعراض وقراءة العداد إن لزم.</li>
      <li>مسح حراري/صوتي لتحديد النطاق.</li>
      <li>شرح نقطة الإصلاح قبل الموافقة على السعر.</li>
      <li>إصلاح موضعي واختبار.</li>
    </ol>
  </div>
  <div>
    <table class="table">
      <tr><th>البند</th><th>تقدير</th></tr>
      <tr><td>كشف بالكاميرا — شقة</td><td>يُحدد بعد المعاينة</td></tr>
      <tr><td>كشف — فيلا</td><td>حسب عدد النقاط</td></tr>
      <tr><td>إصلاح موضعي</td><td>بند منفصل في العرض</td></tr>
    </table>
    <p class="notice">لا نعرض «من 250 درهم» كسعر ثابت في الديمو. السعر يظهر بعد المعاينة.</p>
  </div>
</div></section>
<section class="sec sec-alt"><div class="wrap">
  <h2>متوفر في مدينتك</h2>
  <div class="pills" style="margin-top:16px">
    <a href="service-city.html">كشف تسربات في دبي</a>
    <a href="service-city.html">أبوظبي</a>
    <a href="service-city.html">العين</a>
    <a href="service-city.html">الشارقة</a>
  </div>
</div></section>
<section class="sec"><div class="wrap">
  <h2>أسئلة الخدمة</h2>
  <div class="faq" style="margin-top:16px">
    <details class="faq-item" open><summary>هل الكشف يحتاج تكسير؟</summary><p>غالباً لا في مرحلة التحديد. الفتح يكون عند نقطة الإصلاح وبعد موافقتك.</p></details>
    <details class="faq-item"><summary>كم يستغرق الكشف؟</summary><p>عادة ساعة إلى ثلاث حسب مساحة العقار وتعدد النقاط.</p></details>
  </div>
</div></section>
"""

SERVICE_CITY = f"""
<section class="phero" style="background-image:url('img/villa.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><a href="services.html">الخدمات</a><span>/</span><a href="service-single.html">كشف تسربات</a><span>/</span><span>دبي</span></div>
  <h1>كشف تسربات المياه في دبي</h1>
  <p class="psub">صفحة المال: خدمة محددة × مدينة محددة. المحتوى المحلي هنا نموذجي — يُستكمل من اللاندنجات القديمة عند الربط.</p>
  <p style="margin-top:20px;display:flex;gap:10px;flex-wrap:wrap">
    <a class="btn btn-wa" href="{WA}">واتساب — دبي</a>
    <a class="btn btn-line" href="booking.html">حجز معاينة في دبي</a>
  </p>
</div></section>
<section class="sec"><div class="wrap grid-2">
  <div>
    <h2>لماذا دبي مختلفة؟</h2>
    <p style="color:var(--muted);margin-top:10px">شبكات مدفونة، تشطيب رخام وبلاط، وأبراج مقابل فلل. الكشف البصري وحده يكلّف تكسيراً لا حاجة له إن حدّد الجهاز النقطة أولاً.</p>
    <h2 style="margin-top:28px">أحياء نغطيها</h2>
    <div class="pills" style="margin-top:12px">
      <span class="btn btn-ghost" style="cursor:default">دبي مارينا</span>
      <span class="btn btn-ghost" style="cursor:default">جميرا</span>
      <span class="btn btn-ghost" style="cursor:default">البرشاء</span>
      <span class="btn btn-ghost" style="cursor:default">مردف</span>
      <span class="btn btn-ghost" style="cursor:default">ديرة</span>
      <span class="btn btn-ghost" style="cursor:default">الخليج التجاري</span>
      <span class="btn btn-ghost" style="cursor:default">JVC</span>
      <span class="btn btn-ghost" style="cursor:default">دبي هيلز</span>
    </div>
  </div>
  <div>
    <table class="table">
      <tr><th>نوع العقار</th><th>الجدولة</th></tr>
      <tr><td>شقة</td><td>غالباً خلال نفس اليوم</td></tr>
      <tr><td>فيلا</td><td>موعد صباحي أو مسائي</td></tr>
      <tr><td>تجاري</td><td>بعد التنسيق مع الإدارة</td></tr>
    </table>
    <article class="card review" style="margin-top:16px"><div class="body"><div class="stars">★★★★★</div><p>نموذج رأي لحي مارينا — يُستبدل بتقييم حقيقي مرتبط بالمدينة.</p><div class="who">عميل — دبي مارينا</div></div></article>
  </div>
</div></section>
<section class="cta"><div class="wrap">
  <h2>تسرب في دبي؟</h2>
  <p>اكتب الحي في رسالة واتساب.</p>
  <a class="btn btn-wa" href="{WA}">واتساب</a>
</div></section>
"""

CITIES_PAGE = f"""
<section class="phero" style="background-image:url('img/villa.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>المدن</span></div>
  <h1>نخدم الإمارات السبع والعين</h1>
  <p class="psub">العين ليست إمارة ثامنة. هي مدينة خدمة لها صفحة مستقلة تحت أبوظبي.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="grid-4">{city_cards()}</div>
</div></section>
"""

CITY_SINGLE = f"""
<section class="phero" style="background-image:url('img/villa.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><a href="cities.html">المدن</a><span>/</span><span>دبي</span></div>
  <h1>خدمات ركن التطور في دبي</h1>
  <p class="psub">من مارينا إلى ديرة ومردف. الجدولة عبر واتساب حسب الحي وازدحام اليوم.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="grid-3">{service_cards(4)}</div>
  <h2 style="margin:36px 0 14px">أحياء</h2>
  <div class="pills">
    <span class="btn btn-ghost" style="cursor:default">مارينا</span>
    <span class="btn btn-ghost" style="cursor:default">جميرا</span>
    <span class="btn btn-ghost" style="cursor:default">البرشاء</span>
    <span class="btn btn-ghost" style="cursor:default">مردف</span>
    <span class="btn btn-ghost" style="cursor:default">ديرة</span>
    <span class="btn btn-ghost" style="cursor:default">الخليج التجاري</span>
  </div>
  <p style="margin-top:28px"><a class="btn btn-wa" href="{WA}">واتساب — دبي</a></p>
</div></section>
"""

ABOUT = """
<section class="phero" style="background-image:url('img/technician.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>من نحن</span></div>
  <h1>شركة خدمات ميدانية مقرها أبوظبي</h1>
  <p class="psub">ركن التطور تعمل من مدينة الشيخ محمد بن زايد، وتغطي الإمارات عبر فرق ميدانية لا عبر «منصة خليجية».</p>
</div></section>
<section class="sec"><div class="wrap grid-2">
  <div>
    <h2>المهمة</h2>
    <p style="color:var(--muted);margin-top:8px">تنفيذ كشف وعزل وصيانة ببنود مكتوبة، بعد معاينة حقيقية للموقع.</p>
    <h2 style="margin-top:28px">المقر</h2>
    <p style="color:var(--muted);margin-top:8px">Mazid Mall، مدينة الشيخ محمد بن زايد، أبوظبي. ليست دبي.</p>
  </div>
  <div class="card"><div class="media" style="height:240px;background-image:url('img/technician.jpg')"></div>
    <div class="body"><h3>الاعتمادات</h3><p>في الإطلاق توضع صورة الرخصة ورقمها هنا. هذا الديمو لا يخترع شهادات PDF.</p></div>
  </div>
</div></section>
"""

BLOG = """
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>المدونة</span></div>
  <h1>أدلة عملية للمنزل في الإمارات</h1>
  <p class="psub">مقالات مرتبطة بخدمة حقيقية، لا ستة عناوين تؤدي لنفس الصفحة.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="pills"><a class="on" href="blog.html">الكل</a><a href="blog.html">تسربات</a><a href="blog.html">عزل</a><a href="blog.html">تكييف</a></div>
  <div class="grid-3">
    <article class="card"><div class="media" style="background-image:url('img/plumbing.jpg')"></div><div class="body"><h3>خمس علامات لتسرب خفي</h3><p>فاتورة، رطوبة، وصوت جريان.</p><p style="margin-top:12px"><a class="link" href="article-single.html">اقرأ المقال</a></p></div></article>
    <article class="card"><div class="media" style="background-image:url('img/hero-roof.jpg')"></div><div class="body"><h3>العزل الحراري وصيف الإمارات</h3><p>متى يكفي الطلاء العاكس ومتى تحتاج نظاماً كاملاً.</p></div></article>
    <article class="card"><div class="media" style="background-image:url('img/ac.jpg')"></div><div class="body"><h3>تنظيف المكيف في مناخ دبي</h3><p>لماذا الدورة أقصر من المناخ المعتدل.</p></div></article>
  </div>
</div></section>
"""

ARTICLE = f"""
<section class="phero" style="background-image:url('img/plumbing.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><a href="blog.html">المدونة</a><span>/</span><span>تسرب خفي</span></div>
  <h1>خمس علامات تدل على تسرب مياه خفي</h1>
  <p class="psub">12 يونيو 2026 · 5 دقائق قراءة</p>
</div></section>
<section class="sec"><div class="wrap article-layout">
  <article class="prose">
    <p>التسرب الخفي قد يستمر أسابيع قبل أن يظهر على السطح. هذه الإشارات تستحق فحصاً قبل توسّع الضرر.</p>
    <h2 id="t1">1. ارتفاع فاتورة المياه</h2>
    <p>زيادة دون تغيّر في الاستهلاك اليومي مؤشر شائع على تسرب في الشبكة الداخلية.</p>
    <h2 id="t2">2. بقع رطوبة</h2>
    <p>اصفرار الجدران قرب الحمامات والمطابخ يستدعي كشفاً لا دهاناً فوق البقعة.</p>
    <h2 id="t3">3. صوت جريان</h2>
    <p>صوت مياه بعد إغلاق الصنابير يعني غالباً نقطة نشطة.</p>
    <h2 id="t4">4. رائحة عفن</h2>
    <p>رطوبة محتبسة خلف التشطيب تولّد رائحة قبل ظهور بقعة واضحة.</p>
    <h2 id="t5">5. انتفاخ الأرضيات</h2>
    <p>في الحالات المتقدّمة يرتفع البلاط أو يتفكك الفواصل.</p>
    <p><a class="btn btn-navy" href="booking.html">اطلب كشف تسربات</a></p>
  </article>
  <aside class="side">
    <div class="card"><h3>محتويات</h3><div class="toc">
      <a href="#t1">فاتورة المياه</a><a href="#t2">بقع الرطوبة</a><a href="#t3">صوت الجريان</a><a href="#t4">العفن</a><a href="#t5">الأرضيات</a>
    </div></div>
    <div class="card"><h3>معاينة</h3><p>فريق الكشف في مدينتك.</p><p style="margin-top:10px"><a class="btn btn-wa" href="{WA}">واتساب</a></p></div>
  </aside>
</div></section>
"""

OFFERS = f"""
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>العروض</span></div>
  <h1>عرض واحد واضح أفضل من ستة خصومات وهمية</h1>
  <p class="psub">الديمو يعرض عرضين نموذجيين بشروط. إن لم يوجد عرض حي تُخفى الصفحة من القائمة.</p>
</div></section>
<section class="sec"><div class="wrap grid-2">
  <article class="card offer">
    <div class="top"><div class="pct">صيانة + تنظيف دكت</div><h3>باقة الصيف — التكييف</h3></div>
    <div class="body">
      <p>عند جمع الغسيل والصيانة في زيارة واحدة. السعر النهائي بعد معاينة عدد الوحدات.</p>
      <p class="note">ساري حتى 30 سبتمبر 2026 · لا يجمع مع عروض أخرى</p>
      <a class="btn btn-wa" href="{WA}">اسأل عن العرض</a>
    </div>
  </article>
  <article class="card offer">
    <div class="top"><div class="pct">كشف + إصلاح</div><h3>دمج كشف التسرب مع الإصلاح</h3></div>
    <div class="body">
      <p>إن نُفّذ الإصلاح مباشرة بعد الكشف في نفس الزيارة عندما تسمح الحالة.</p>
      <p class="note">يُذكر الخصم في عرض السعر إن انطبق</p>
      <a class="btn btn-wa" href="{WA}">واتساب</a>
    </div>
  </article>
</div></section>
"""

PORTFOLIO = """
<section class="phero" style="background-image:url('img/hero-roof.jpg')"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>الأعمال</span></div>
  <h1>نماذج أعمال</h1>
  <p class="psub">صور المخزون هنا للشكل فقط. قبل الإطلاق تُستبدل بمشاريع مرخّص نشرها.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="pills"><a class="on">الكل</a><a>عزل</a><a>تسربات</a><a>تكييف</a></div>
  <div class="gal">
    <a href="portfolio.html"><img src="img/hero-roof.jpg" alt=""><span>عزل سطح — أبوظبي</span></a>
    <a href="portfolio.html"><img src="img/plumbing.jpg" alt=""><span>كشف تسرب — دبي</span></a>
    <a href="portfolio.html"><img src="img/ac.jpg" alt=""><span>صيانة تكييف — الشارقة</span></a>
    <a href="portfolio.html"><img src="img/bathroom.jpg" alt=""><span>خزان — عجمان</span></a>
    <a href="portfolio.html"><img src="img/villa.jpg" alt=""><span>فيلا — العين</span></a>
    <a href="portfolio.html"><img src="img/tools.jpg" alt=""><span>سباكة — رأس الخيمة</span></a>
  </div>
</div></section>
"""

REVIEWS = f"""
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>التقييمات</span></div>
  <h1>التقييمات</h1>
  <p class="psub">الملخص البصري جاهز. المصدر عند الإطلاق: ملف Google Business لا بطاقات «موثّق عبر Google» مكتوبة يدوياً.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="rev-sum">
    <div class="score"><div class="big">—</div><div class="stars">★★★★★</div><small>يُربط بالتقييم الحي</small></div>
    <div class="bars">
      <div class="bar">5<i><b style="width:80%"></b></i></div>
      <div class="bar">4<i><b style="width:14%"></b></i></div>
      <div class="bar">3<i><b style="width:4%"></b></i></div>
      <div class="bar">2<i><b style="width:1%"></b></i></div>
      <div class="bar">1<i><b style="width:1%"></b></i></div>
    </div>
  </div>
  <div class="grid-3">
    <article class="card review"><div class="body"><div class="stars">★★★★★</div><p>نموذج رأي للشكل — ليس اقتباساً من Google.</p><div class="who">عميل — أبوظبي</div></div></article>
    <article class="card review"><div class="body"><div class="stars">★★★★★</div><p>نموذج ثانٍ لحي مختلف.</p><div class="who">عميلة — الشارقة</div></div></article>
    <article class="card review"><div class="body"><div class="stars">★★★★☆</div><p>نموذج بتقييم غير خمسة نجوم ليبدو التوزيع صادقاً.</p><div class="who">عميل — دبي</div></div></article>
  </div>
  <p style="margin-top:22px"><a class="btn btn-navy" href="{WA}">قيّم تجربتك بعد الخدمة</a></p>
</div></section>
"""

FAQ = f"""
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>الأسئلة</span></div>
  <h1>الأسئلة الشائعة</h1>
  <p class="psub">أكورديون أصلي بدون جافاسكربت ثقيل.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="faq">
    <details class="faq-item" open><summary>ما مناطق التغطية؟</summary><p>سبع إمارات + العين. التفاصيل في <a class="link" href="cities.html">صفحات المدن</a>.</p></details>
    <details class="faq-item"><summary>كيف أحجز؟</summary><p>واتساب أو <a class="link" href="booking.html">نموذج المعاينة</a>.</p></details>
    <details class="faq-item"><summary>هل توجد رسوم خفية؟</summary><p>التنفيذ بعد موافقة مكتوبة على بنود العرض.</p></details>
    <details class="faq-item"><summary>ماذا عن الضمان؟</summary><p>يُذكر لكل خدمة في عرضها. العزل مختلف عن السباكة.</p></details>
    <details class="faq-item"><summary>هل الكشف بدون تكسير؟</summary><p>مرحلة التحديد عادة كذلك. التفاصيل في <a class="link" href="service-single.html">صفحة كشف التسربات</a>.</p></details>
  </div>
  <p style="margin-top:24px">لم تجد إجابتك؟ <a class="btn btn-wa" href="{WA}">اسأل واتساب</a></p>
</div></section>
"""

BOOKING = """
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>طلب معاينة</span></div>
  <h1>معاينة في ثلاث خطوات</h1>
  <p class="psub">البيانات تُرسل إلى واتساب. لا رسالة نجاح وهمية.</p>
</div></section>
<section class="sec"><div class="wrap" style="max-width:720px">
  <div class="card" style="padding:28px">
    <div class="stepper">
      <span class="on">1. الخدمة</span>
      <span>2. الموقع والوقت</span>
      <span>3. تواصلك</span>
    </div>
    <form data-wa-form>
      <div class="panel on">
        <div class="fld"><label>الخدمة</label><select name="service">""" + "".join(f"<option>{s[0]}</option>" for s in SERVICES) + """</select></div>
        <button class="btn btn-navy" type="button" data-next>التالي</button>
      </div>
      <div class="panel">
        <div class="fld"><label>المدينة</label><select name="city">""" + "".join(f"<option>{c[0]}</option>" for c in CITIES) + """</select></div>
        <div class="fld"><label>نوع العقار</label><select name="note"><option>شقة</option><option>فيلا</option><option>تجاري</option></select></div>
        <div class="fld"><label>الوقت المفضل</label><select><option>صباح</option><option>ظهر</option><option>مساء</option><option>طوارئ</option></select></div>
        <p style="display:flex;gap:8px"><button class="btn btn-ghost" type="button" data-prev>رجوع</button><button class="btn btn-navy" type="button" data-next>التالي</button></p>
      </div>
      <div class="panel">
        <div class="fld"><label>الاسم</label><input name="name" required></div>
        <div class="fld"><label>الهاتف</label><input name="phone" type="tel" required placeholder="05xxxxxxxx"></div>
        <label style="display:flex;gap:8px;font-size:14px;margin:8px 0 16px"><input type="checkbox" required> أوافق على التواصل بشأن هذا الطلب وفق سياسة الخصوصية.</label>
        <p style="display:flex;gap:8px"><button class="btn btn-ghost" type="button" data-prev>رجوع</button><button class="btn btn-wa" type="submit">إرسال واتساب</button></p>
      </div>
    </form>
  </div>
</div></section>
"""

CONTACT = f"""
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>اتصل بنا</span></div>
  <h1>تواصل مع فريق أبوظبي</h1>
  <p class="psub">الخريطة والمقر هنا فقط، لا في تذييل كل صفحة.</p>
</div></section>
<section class="sec"><div class="wrap contact">
  <div class="info">
    <h2>بيانات التواصل</h2>
    <ul>
      <li><div><b>واتساب</b><small>+971 58 663 4710</small></div></li>
      <li><div><b>البريد</b><small>info@rukn-eltatawer.com</small></div></li>
      <li><div><b>المقر</b><small>Mazid Mall، مدينة الشيخ محمد بن زايد، أبوظبي</small></div></li>
    </ul>
    <div class="map"></div>
    <a class="btn btn-wa" href="{WA}" style="width:100%;margin-top:16px">واتساب</a>
  </div>
  <form class="card" style="padding:28px" data-wa-form>
    <h2>رسالة</h2>
    <div class="fld"><label>الاسم</label><input name="name" required></div>
    <div class="fld"><label>الهاتف</label><input name="phone" type="tel" required></div>
    <div class="fld"><label>الخدمة</label><select name="service">""" + "".join(f"<option>{s[0]}</option>" for s in SERVICES) + """</select></div>
    <div class="fld"><label>التفاصيل</label><textarea name="note"></textarea></div>
    <button class="btn btn-navy" type="submit">إرسال واتساب</button>
  </form>
</div></section>
"""

PRIVACY = """
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>الخصوصية</span></div>
  <h1>سياسة الخصوصية</h1>
  <p class="psub">مسودة ديمو — تُراجع قانونياً قبل النشر. تشير إلى PDPL الإماراتي وواتساب كقناة تواصل.</p>
</div></section>
<section class="sec"><div class="wrap legal-layout">
  <nav class="card" style="padding:18px">
    <a class="link" href="#c1">الجمع</a><br>
    <a class="link" href="#c2">الاستخدام</a><br>
    <a class="link" href="#c3">واتساب</a><br>
    <a class="link" href="#c4">حقوقك</a>
  </nav>
  <div class="prose legal">
    <p>الكيان: ركن التطور — Mazid Mall، مدينة الشيخ محمد بن زايد، أبوظبي.</p>
    <h2 id="c1">جمع المعلومات</h2>
    <p>الاسم، الهاتف، المدينة، ونوع الخدمة عند طلب المعاينة.</p>
    <h2 id="c2">الاستخدام</h2>
    <p>لتنسيق الزيارة وتحسين الخدمة. لا بيع لبياناتك.</p>
    <h2 id="c3">واتساب</h2>
    <p>بإرسالك طلباً توافق على التواصل عبر واتساب بخصوص ذلك الطلب.</p>
    <h2 id="c4">حقوقك</h2>
    <p>طلب الاطلاع أو التصحيح أو الحذف عبر صفحة الاتصال، وفق قانون حماية البيانات الإماراتي.</p>
  </div>
</div></section>
"""

TERMS = """
<section class="phero"><div class="wrap">
  <div class="crumb"><a href="home.html">الرئيسية</a><span>/</span><span>الشروط</span></div>
  <h1>الشروط والأحكام</h1>
  <p class="psub">مسودة ديمو توضح المعاينة، العرض، والإلغاء.</p>
</div></section>
<section class="sec"><div class="wrap">
  <div class="prose legal">
    <h2>قبول الشروط</h2>
    <p>استخدام الموقع أو طلب خدمة يعني الموافقة على هذه البنود.</p>
    <h2>المعاينة والعرض</h2>
    <p>المعاينة الأولى تقديرية. التنفيذ بعد موافقتك على عرض السعر المكتوب.</p>
    <h2>الإلغاء</h2>
    <p>يمكن إعادة الجدولة بإشعار مسبق معقول. تفاصيل الرسوم تُذكر إن وُجدت في العرض.</p>
    <h2>الضمان</h2>
    <p>نطاقه ومدته جزء من عرض كل خدمة، وليسا وعداً عاماً في الموقع.</p>
  </div>
</div></section>
"""

ERR404 = f"""
<section class="err">
  <div class="wrap">
    <div class="num">404</div>
    <h1>هذه الصفحة غير موجودة</h1>
    <p>الرابط غير صحيح أو نُقلت الصفحة. ابحث أو عد للرئيسية.</p>
    <form class="search" action="home.html">
      <input name="s" placeholder="ابحث عن خدمة أو مدينة">
      <button class="btn btn-gold" type="submit">بحث</button>
    </form>
    <p style="margin-top:22px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
      <a class="btn btn-line" href="home.html">الرئيسية</a>
      <a class="btn btn-wa" href="{WA}">واتساب</a>
      <a class="btn btn-line" href="services.html">الخدمات</a>
    </p>
  </div>
</section>
"""

INDEX = """
<section class="gallery-hero">
  <div class="wrap">
    <div class="tag">ديمو قبل تعديل الثيم الحي</div>
    <h1 style="font-size:clamp(28px,4vw,42px);color:var(--navy);max-width:18ch">شكل كل نوع صفحة</h1>
    <p style="color:var(--muted);margin-top:12px;max-width:56ch">اتجاه ميداني مقترح (صور، أبوظبي، واتساب، بدون داشبورد). الملفات الأصلية المرفقة لم تُعدَّل — رابط المقارنة بالأسفل.</p>
    <p style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap">
      <a class="btn btn-navy" href="home.html">ابدأ من الرئيسية</a>
      <a class="btn btn-ghost" href="originals.html">التصاميم المرفقة الأصلية</a>
    </p>
  </div>
</section>
<section class="sec" style="padding-top:12px"><div class="wrap">
  <div class="grid-3">
"""

GALLERY_META = [
    ("home.html", "الرئيسية", "هيرو مصوَّر + نموذج معاينة", "img/hero-roof.jpg"),
    ("services.html", "أرشيف الخدمات", "ست خدمات بروابط صحيحة", "img/tools.jpg"),
    ("service-category.html", "فئة خدمة", "العزل وفروعه", "img/roof-close.jpg"),
    ("service-single.html", "صفحة خدمة", "كشف تسربات + خطوات", "img/plumbing.jpg"),
    ("service-city.html", "خدمة × مدينة", "أهم صفحة SEO", "img/villa.jpg"),
    ("cities.html", "فهرس المدن", "7 إمارات + العين", "img/villa.jpg"),
    ("city-single.html", "صفحة مدينة", "دبي والخدمات", "img/interior.jpg"),
    ("portfolio.html", "معرض الأعمال", "شبكة صور", "img/hero-roof.jpg"),
    ("blog.html", "المدونة", "قائمة مقالات", "img/ac.jpg"),
    ("article-single.html", "مقال", "محتوى + فهرس جانبي", "img/plumbing.jpg"),
    ("about.html", "من نحن", "مقر أبوظبي", "img/technician.jpg"),
    ("reviews.html", "التقييمات", "ملخص بدون شارة مزيّفة", "img/interior.jpg"),
    ("offers.html", "العروض", "عرضان بشروط", "img/ac.jpg"),
    ("faq.html", "الأسئلة", "details أصلية", "img/tools.jpg"),
    ("booking.html", "الحجز", "ثلاث خطوات → واتساب", "img/technician.jpg"),
    ("contact.html", "اتصل بنا", "NAP أبوظبي + خريطة", "img/villa.jpg"),
    ("privacy.html", "الخصوصية", "مسودة PDPL", "img/interior.jpg"),
    ("terms.html", "الشروط", "معاينة وعرض وضمان", "img/interior.jpg"),
    ("404.html", "404", "h1 وبحث", "img/roof-close.jpg"),
]

for href, title, sub, img in GALLERY_META:
    INDEX += f"""
    <a class="card gcard" href="{href}">
      <div class="media" style="background-image:url('{img}')"></div>
      <div class="body"><h3>{title}</h3><small>{sub}</small></div>
    </a>
"""

INDEX += """
  </div>
</div></section>
"""

ORIGINALS = """
<section class="sec"><div class="wrap">
  <div class="tag">الملفات المرفقة — بدون تعديل</div>
  <h1 style="color:var(--navy)">تصاميم الكيت الأصلية</h1>
  <p style="color:var(--muted);margin:12px 0 24px;max-width:60ch">هذه روابط للملفات التي رفعتها في جذر المشروع. هويتها SaaS (داشبورد، لودر، مقر دبي). استخدمها للمقارنة مع الديمو المقترح.</p>
  <div class="grid-3">
"""

ORIG_FILES = [
    ("../home.html", "home.html"),
    ("../services.html", "services.html"),
    ("../service-single.html", "service-single.html"),
    ("../service-category.html", "service-category.html"),
    ("../service-city.html", "service-city.html"),
    ("../cities.html", "cities.html"),
    ("../city-single.html", "city-single.html"),
    ("../about.html", "about.html"),
    ("../blog.html", "blog.html"),
    ("../article-single.html", "article-single.html"),
    ("../offers.html", "offers.html"),
    ("../portfolio.html", "portfolio.html"),
    ("../reviews.html", "reviews.html"),
    ("../faq.html", "faq.html"),
    ("../booking.html", "booking.html"),
    ("../contact.html", "contact.html"),
    ("../privacy.html", "privacy.html"),
    ("../terms.html", "terms.html"),
    ("../404.html", "404.html"),
]
for href, label in ORIG_FILES:
    ORIGINALS += f'<a class="card city" href="{href}"><div><b>{label}</b><small>الملف المرفق</small></div><span class="link">فتح</span></a>\n'

ORIGINALS += """
  </div>
  <p style="margin-top:24px"><a class="btn btn-navy" href="index.html">عودة لمعرض الديمو المقترح</a></p>
</div></section>
"""

BODIES = {
    "home.html": ("الرئيسية", "خدمات منزلية في الإمارات", "home", HOME),
    "services.html": ("الخدمات", "أرشيف الخدمات", "services", SERVICES_PAGE),
    "service-category.html": ("خدمات العزل", "فئة العزل", "services", CATEGORY),
    "service-single.html": ("كشف تسربات المياه", "صفحة خدمة", "services", SINGLE),
    "service-city.html": ("كشف تسربات في دبي", "خدمة × مدينة", "services", SERVICE_CITY),
    "cities.html": ("المدن", "فهرس المدن", "cities", CITIES_PAGE),
    "city-single.html": ("خدمات دبي", "صفحة مدينة", "cities", CITY_SINGLE),
    "about.html": ("من نحن", "عن الشركة", "about", ABOUT),
    "blog.html": ("المدونة", "المقالات", "blog", BLOG),
    "article-single.html": ("علامات التسرب الخفي", "مقال", "blog", ARTICLE),
    "offers.html": ("العروض", "العروض", "offers", OFFERS),
    "portfolio.html": ("الأعمال", "المعرض", "portfolio", PORTFOLIO),
    "reviews.html": ("التقييمات", "التقييمات", "about", REVIEWS),
    "faq.html": ("الأسئلة الشائعة", "FAQ", "faq", FAQ),
    "booking.html": ("طلب معاينة", "حجز", "booking", BOOKING),
    "contact.html": ("اتصل بنا", "اتصال", "contact", CONTACT),
    "privacy.html": ("سياسة الخصوصية", "خصوصية", "legal", PRIVACY),
    "terms.html": ("الشروط والأحكام", "شروط", "legal", TERMS),
    "404.html": ("الصفحة غير موجودة", "404", "404", ERR404),
    "index.html": ("أنواع الصفحات", "معرض الديمو", "", INDEX),
    "originals.html": ("التصاميم الأصلية", "مقارنة", "", ORIGINALS),
}


def main():
    for name, (title, desc, active, body) in BODIES.items():
        html = page(title, desc, active, body)
        (ROOT / name).write_text(html, encoding="utf-8")
        print("wrote", name)


if __name__ == "__main__":
    main()
