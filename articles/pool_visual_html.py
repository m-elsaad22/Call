"""Original pool landing-page HTML. Design tokens from Rukn leak/insulation pages."""

from __future__ import annotations

TEL = "+971521300019"
TEL_LOCAL = "0521300019"
WA = "971521300019"

IMG = {
    "clean1": (
        3266,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Prof-Pool-Cleaner-rukn-eltatawer.com_-1.webp",
        "تنظيف حوض سباحة بمعدات احترافية",
    ),
    "clean2": (
        3260,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Pool-Cleaning-rukn-eltatawer.com_.webp",
        "تنظيف مياه المسبح وإزالة الشوائب",
    ),
    "clean3": (
        2738,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2021/12/Swimming-pool-cleaning-2-e1721137044406.webp",
        "مكنسة يدوية لتنظيف قاع الإسبلاش",
    ),
    "clean4": (
        2901,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2021/12/Swimming-pool-cleaning-4-7.webp",
        "سطح مسبح بعد التنظيف",
    ),
    "maint1": (
        3255,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2024/05/pool-maintenance-rukn-eltatawer.com_.webp",
        "أعمال صيانة مسبح",
    ),
    "equip": (
        3261,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Pool-equipment-scale-rukn-eltatawer.com_.webp",
        "مضخة وفلتر مسبح",
    ),
    "scale": (
        3263,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Pool-scale-rukn-eltatawer.com_.webp",
        "مقياس جودة مياه المسبح",
    ),
    "build": (
        1527,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2020/06/3.jpg",
        "تصميم وإنشاء مسبح",
    ),
}

CITY = {
    "عجمان": {
        "climate": "رطوبة ساحلية وحرارة مرتفعة تجعل الطحالب والأملاح تظهر أسرع مما يتوقع كثير من أصحاب الفلل",
        "areas": "الراشدية والنعيمية والجرف",
        "note": "المسابح هنا غالبًا ضمن فلل متلاصقة؛ أي تسريب أو مضخة ضعيفة يظهر أثره على الجار وعلى فاتورة المياه بسرعة",
    },
    "رأس الخيمة": {
        "climate": "هواء بحري وملوحة أعلى من الداخل، مع غبار في الأيام العاصفة",
        "areas": "النخيل والرمس والمعيريض",
        "note": "الفلاتر والمبادلات المعدنية تحتاج متابعة أوضح بسبب الملوحة، خاصة في المسابح القريبة من الساحل",
    },
    "الفجيرة": {
        "climate": "الساحل الشرقي أشد رطوبة، مع تباين حراري بين الجبل والبحر",
        "areas": "المرحلة والقدفع ومربح",
        "note": "المياه الراكدة بعد الإجازات شائعة في البيوت الموسمية؛ التنظيف المتأخر يرفع جهد المعالجة لاحقًا",
    },
    "أم القيوين": {
        "climate": "بيئة هادئة لكن الحرارة والرطوبة كافيتان لتعكير الماء خلال أيام قليلة بدون circulating منتظم",
        "areas": "المدينة القديمة وفلج المعلا",
        "note": "كثير من المسابح هنا صغيرة أو متوسطة؛ العطل في مضخة واحدة يوقف الدورة كلها",
    },
    "العين": {
        "climate": "حرارة داخلية أعلى وغبار أكثر، مع ماء أملاحه أعلى من الساحل في بعض الأحياء",
        "areas": "المقام والجاهلي والهيلي",
        "note": "الترسبات الكلسية على البلاط والسلال تظهر أسرع؛ جدول الفلترة أهم من زيادة الكلور عشوائيًا",
    },
    "أبوظبي": {
        "climate": "حرارة ورطوبة ساحلية مع استخدام كثيف في الفلل والمجتمعات السكنية",
        "areas": "الخليفة والشاطئ ومحمد بن زايد",
        "note": "المعاينة توضح إن كان الاحتياج تنظيفًا دوريًا أم إصلاح مضخة وفلتر",
    },
    "دبي": {
        "climate": "استخدام عالٍ على مدار السنة وملوحة في بعض المناطق الساحلية",
        "areas": "المارينا وجميرا والبرشاء",
        "note": "المسابح المشتركة في العمائر تختلف خطتها عن مسبح الفيلا الخاصة",
    },
    "الشارقة": {
        "climate": "رطوبة خليجية وغبار موسمي يرفع حمل الفلتر",
        "areas": "الخان والبحيرة والنهدة",
        "note": "وضوح خطة الصيانة أهم من الوعود العامة قبل بدء أي عمل",
    },
}

LINKS = {
    "build_ad": ("/swimming-pool-company-in-abu-dhabi/", "إنشاء وصيانة مسابح أبوظبي"),
    "clean_ad": ("/abu-dhabi-swimming-pool-cleaning-company/", "تنظيف مسابح أبوظبي"),
    "maint_ad": ("/pool-maintenance-abu-dhabi/", "صيانة مسابح أبوظبي"),
    "clean_dxb": ("/dubai-pool-cleaning/", "تنظيف مسابح دبي"),
    "maint_dxb": ("/pool-maintenance-dubai/", "صيانة مسابح دبي"),
}


def css() -> str:
    return """<style>
.rp{max-width:100%;color:#1a2332;line-height:1.85}
.rp h2{display:flex;align-items:center;gap:10px;color:#003366;margin:36px 0 16px;font-size:1.45rem}
.rp h3{display:flex;align-items:center;gap:8px;color:#003366;margin:0 0 10px;font-size:1.08rem}
.rp .rp-hero{background:linear-gradient(135deg,#f0f7ff,#fff);border-right:6px solid #0056b3;padding:28px 26px;border-radius:16px;margin:18px 0 28px;box-shadow:0 4px 18px rgba(0,0,0,.06)}
.rp .rp-hero h2{margin-top:0}
.rp .rp-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px;margin:22px 0}
.rp .rp-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 16px;box-shadow:0 4px 12px rgba(0,0,0,.05)}
.rp .rp-step{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;position:relative}
.rp .rp-num{width:32px;height:32px;border-radius:50%;background:#0056b3;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;margin-left:8px}
.rp .rp-img{max-width:100%;height:auto;border-radius:12px;margin:12px 0 8px}
.rp .rp-cap{font-size:.9rem;color:#4a5568;margin:0 0 20px}
.rp .rp-cta{background:linear-gradient(135deg,#003366,#0056b3);color:#fff;padding:26px 22px;border-radius:16px;margin:32px 0;text-align:center}
.rp .rp-cta h2,.rp .rp-cta p{color:#fff}
.rp .rp-cta a{display:inline-block;margin:8px 6px 0;padding:10px 18px;border-radius:10px;font-weight:700;text-decoration:none}
.rp .rp-cta .rp-call{background:#fff;color:#003366}
.rp .rp-cta .rp-wa{background:#25d366;color:#fff}
.rp .rp-faq{margin:18px 0}
.rp .faq-item{border:1px solid #e2e8f0;border-radius:12px;margin:10px 0;overflow:hidden;background:#fff}
.rp .faq-q{background:#f0f7ff;padding:14px 16px;font-weight:700;color:#003366;display:flex;gap:8px;align-items:flex-start}
.rp .faq-a{padding:14px 16px}
.rp .rp-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin:20px 0}
.rp .rp-links a{display:block;padding:14px 16px;border:1px solid #dce8f8;border-radius:12px;background:#f8fbff;color:#0056b3;text-decoration:none;font-weight:600}
.rp-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;margin:20px 0;border:1px solid #e5e7eb;border-radius:12px}
.rp-table-wrap table{width:100%;min-width:520px;border-collapse:collapse;text-align:right}
.rp-table-wrap th{background:#f0f6ff;padding:10px 12px}
.rp-table-wrap td{padding:10px 12px;border-top:1px solid #eef2f7}
@media(max-width:640px){.rp h2{font-size:1.22rem}.rp .rp-hero{padding:20px 16px}}
</style>"""


def fa(name: str) -> str:
    return f'<i class="fa-solid fa-{name}"></i>'


def img(key: str, alt: str | None = None, eager: bool = False) -> str:
    mid, url, default_alt = IMG[key]
    a = alt or default_alt
    load = "eager" if eager else "lazy"
    return (
        f'<img class="rp-img aligncenter wp-image-{mid} size-full" src="{url}" alt="{a}" '
        f'width="800" height="450" loading="{load}" decoding="async" />'
        f'<p class="rp-cap">{a}</p>'
    )


def cta(title: str, body: str) -> str:
    return f"""<div class="rp-cta">
<h2>{fa("phone")} {title}</h2>
<p>{body}</p>
<p><a class="rp-call" href="tel:{TEL}">{fa("phone")} اتصال {TEL_LOCAL}</a>
<a class="rp-wa" href="https://wa.me/{WA}" target="_blank" rel="noopener">{fa("comment")} واتساب {TEL}</a></p>
</div>"""


def faq(items: list[tuple[str, str]]) -> str:
    out = ['<div class="rp-faq">']
    for q, a in items:
        out.append(
            f'<div class="faq-item"><div class="faq-q">{fa("circle-question")} {q}</div>'
            f'<div class="faq-a">{a}</div></div>'
        )
    out.append("</div>")
    return "\n".join(out)


def cards(items: list[tuple[str, str, str]]) -> str:
    bits = ['<div class="rp-cards">']
    for icon, title, text in items:
        bits.append(
            f'<div class="rp-card"><h3>{fa(icon)} {title}</h3><p>{text}</p></div>'
        )
    bits.append("</div>")
    return "\n".join(bits)


def steps(items: list[tuple[str, str]]) -> str:
    bits = ['<div class="rp-cards">']
    for i, (title, text) in enumerate(items, 1):
        bits.append(
            f'<div class="rp-step"><h3><span class="rp-num">{i}</span> {title}</h3><p>{text}</p></div>'
        )
    bits.append("</div>")
    return "\n".join(bits)


def table(headers: list[str], rows: list[list[str]]) -> str:
    th = "".join(f"<th>{h}</th>" for h in headers)
    body = "".join("<tr>" + "".join(f"<td>{c}</td>" for c in row) + "</tr>" for row in rows)
    return f'<div class="rp-table-wrap"><table><tr>{th}</tr>{body}</table></div>'


def callout(kind: str, title: str, text: str) -> str:
    styles = {
        "warn": ("#fff3cd", "#ffc107", "triangle-exclamation"),
        "tip": ("#e3f2fd", "#1976d2", "lightbulb"),
        "info": ("#f0f7ff", "#0056b3", "circle-info"),
    }
    bg, br, icon = styles[kind]
    return (
        f'<blockquote style="background:{bg};border-right:5px solid {br};padding:16px 18px;'
        f'border-radius:10px;margin:22px 0;">{fa(icon)} <strong>{title}:</strong> {text}</blockquote>'
    )


def related(pairs: list[tuple[str, str]]) -> str:
    bits = ['<div class="rp-links">']
    for href, label in pairs:
        bits.append(f'<a href="{href}">{fa("arrow-left")} {label}</a>')
    bits.append("</div>")
    return "\n".join(bits)


def build_clean(city: str) -> str:
    c = CITY[city]
    title = f"تنظيف مسابح في {city}"
    return f"""
<div class="rp">
<section class="rp-hero">
<h2>{fa("person-swimming")} شركة تنظيف مسابح في {city}</h2>
<p>خدمة تنظيف مسابح في {city} من ركن التطور تركز على إعادة صفاء الماء وإزالة الرواسب من الجدران والقاع والسلال، ثم ضبط الفلترة بما يناسب حجم الحوض ونمط الاستخدام. {c["climate"]}. المعاينة توضح إن كان يكفي تنظيف واحد أم يلزم برنامج دوري.</p>
</section>
{img("clean1", f"تنظيف مسبح في {city}", True)}
<h2>{fa("pump-soap")} ماذا يشمل التنظيف</h2>
<p>التنظيف الجيد لا يقتصر على كشط السطح. في {c["areas"]} وغيرها من {city} نتعامل مع القاع، خط الماء، السلال، والفلتر لأن إهمال جزء واحد يعيد العكارة خلال أيام.</p>
{cards([
    ("broom", "كنس القاع والجدران", "إزالة الطمي والطحالب الملتصقة قبل أن تسد السلات أو تخدش البلاط."),
    ("filter", "غسيل الفلتر والسلال", "خفض ضغط المضخة وتحسين دوران الماء بعد تجمع الشعر والأوراق."),
    ("droplet", "موازنة المياه", "قياس الكلور وpH والقلوية الظاهرة على الجهاز، دون وعود بنسبة ثابتة لكل حوض."),
    ("spray-can-sparkles", "خط الماء والسلالم", "الدهون والغبار يلتصقان عند سطح الماء؛ تنظيفهما يغيّر شكل الحوض فورًا."),
])}
{callout("warn", "تنبيه", "زيادة الكلور بدون كنس وفلترة قد تبيّض الماء ساعات ثم تعود الخضرة. عالج المصدر أولًا.")}
<h2>{fa("list-check")} خطوات التنفيذ</h2>
{steps([
    ("معاينة الحوض", f"ننظر إلى لون الماء، رائحته، صوت المضخة، وحالة البلاط في موقعك بـ{city}."),
    ("تحديد السبب", "طحالب، فلتر مسدود، مضخة ضعيفة، أو توقف الدورة لأيام."),
    ("اختيار نطاق العمل", "تنظيف سطحي، تنظيف عميق، أو تنظيف مع خدمة فلتر. التكلفة تتضح بعد المعاينة."),
    ("التنفيذ", "كنس، شفط، غسل سلال، ومراجعة الدورة."),
    ("القياس والتسليم", "نراجع صفاء الماء وتشغيل المعدات، ونشرح ما يحتاجه المسبح بعد الزيارة."),
])}
{img("clean3", f"كنس قاع المسبح أثناء التنظيف في {city}")}
<h2>{fa("calendar-days")} متى تحتاج تنظيفًا عميقًا</h2>
{table(
    ["العلامة", "المعنى الشائع", "ما يُراجع"],
    [
        ["ماء أخضر أو زيتوني", "طحالب مع دوران ضعيف", "فلتر، ساعات التشغيل، الظل والأوراق"],
        ["ماء غائم أبيض", "فلتر متسخ أو pH خارج المدى", "غسيل عكسي وقياس الماء"],
        ["جدار لزج", "فيلم عضوي على البلاط", "فرك يدوي وليس كلورًا فقط"],
        ["ضغط مرتفع على المقياس", "وسيط الفلتر مشبع", "غسيل أو استبدال الرمل/الخرطوشة"],
    ],
)}
{callout("tip", "نصيحة", f"{c['note']}.")}
<h2>{fa("circle-question")} أسئلة متكررة عن تنظيف المسابح في {city}</h2>
{faq([
    (f"كم مرة يُنظَّف المسبح في {city}؟",
     f"يختلف حسب الاستخدام والغبار والظل. بعض الفلل تحتاج زيارة أسبوعية في الصيف، وأخرى أقل في الشتاء. الجدول يُحدد بعد أول معاينة وليس برقم ثابت لكل {city}."),
    ("هل التنظيف يشمل إصلاح المضخة؟",
     "التنظيف يعالج الماء والأسطح والفلتر الظاهر. صوت المعدات أو التسريب يدخل في الصيانة، ويُذكر في المعاينة إن لزم فصل العمل."),
    ("هل تستخدمون موادًا معيّنة؟",
     "نستخدم ما يناسب قراءة الماء وحجم الحوض. لا نعلن علامة تجارية إلزامية، والهدف أمان الاستخدام بعد انتهاء العمل."),
])}
{cta(f"هل تحتاج تنظيف مسبح في {city}؟", f"صف حالة الماء أو أرسل صورة على واتساب {TEL_LOCAL} لنحدد إن كانت الزيارة تنظيفًا أم صيانة.")}
<h2>{fa("link")} خدمات مسابح مرتبطة</h2>
{related([
    LINKS["maint_ad"] if city != "أبوظبي" else LINKS["clean_dxb"],
    LINKS["build_ad"],
    LINKS["clean_dxb"] if city != "دبي" else LINKS["maint_dxb"],
])}
</div>
"""


def build_maint(city: str) -> str:
    c = CITY[city]
    return f"""
<div class="rp">
<section class="rp-hero">
<h2>{fa("screwdriver-wrench")} شركة صيانة مسابح في {city}</h2>
<p>صيانة المسابح في {city} تعني تشخيص العطل قبل استبدال القطع: مضخة لا تسحب، فلتر يضغط، تسريب حول الكور، أو كلور لا يثبت. {c["climate"]}. ركن التطور تبدأ بالمعاينة وتوضح نطاق الإصلاح قبل التنفيذ.</p>
</section>
{img("maint1", f"صيانة معدات مسبح في {city}", True)}
<h2>{fa("gears")} أعطال شائعة</h2>
{cards([
    ("filter", "فلتر مرتفع الضغط", "الرمل أو الخرطوشة مشبعة، أو صمام التحويل في وضع خاطئ."),
    ("droplet", "ضعف الدوران", "هواء في المضخة، سلة مسدودة، أو مروحة تالفة."),
    ("triangle-exclamation", "تسريب ظاهر أو خفي", "فواصل البلاط، الإنارة، أو خطوط الإرجاع. التحديد يحتاج فحصًا وليس تخمينًا."),
    ("flask", "ماء لا يستقر", "حقن كيماوي بلا دورة كافية، أو مبادل تالف إن وُجد تدفئة."),
])}
{callout("info", "معلومة مهمة", "لا نحدد سعر قطعة أو مدة ضمان قبل رؤية المسبح. المعاينة تكتب ما سيُستبدل وما سيُنظَّف.")}
<h2>{fa("list-check")} مسار الصيانة</h2>
{steps([
    ("الاستماع للعَرَض", "متى بدأ الصوت أو التسريب أو اخضرار الماء."),
    ("فحص المعدات", f"غرفة المضخة، العدادات، والصمامات في موقع {city}."),
    ("توضيح الخيارات", "إصلاح، استبدال جزئي، أو تنظيف مرافق إن كان العطل بسيطًا."),
    ("التنفيذ", "بعد موافقتك على النطاق المكتوب."),
    ("تشغيل تجريبي", "نتأكد أن الدورة تعود وأن القراءة الأولية للماء منطقية."),
])}
{img("equip", f"مضخة وفلتر مسبح أثناء الفحص في {city}")}
<h2>{fa("table")} ماذا تراجع بنفسك قبل الزيارة</h2>
{table(
    ["ملاحظة", "ماذا تجرب", "متى تتصل"],
    [
        ["المضخة تطن ولا تدفع ماء", "سلة السكimmer وغطاء المضخة", "إذا استمر الصوت بعد تنظيف السلة"],
        ["ماء ينخفض يوميًا", "علامات حول الكور والإنارة", "انخفاض أسرع من التبخر المعتاد"],
        ["كلور يختفي خلال ساعات", "هل الدورة تعمل 8 ساعات على الأقل", "مع طحالب متكررة رغم الإضافة"],
        ["قاطع يفصل", "لا تعِد التشغيل مرارًا", "فورًا حتى لا تحترق الملفات"],
    ],
)}
{callout("tip", "نصيحة", f"{c['note']}.")}
<h2>{fa("circle-question")} أسئلة عن صيانة المسابح في {city}</h2>
{faq([
    (f"هل صيانة المسبح في {city} تشمل التنظيف؟",
     "إذا كان العطل مرتبطًا بالفلتر أو السلال نعم ضمن النطاق المتفق عليه. التنظيف الكامل للحوض يُطلب كخدمة منفصلة أو يُدمج بعد المعاينة."),
    ("هل تستبدلون المضخة دائمًا؟",
     "لا. كثير من الأعطال سلّة أو موانع تسريب أو كابل. الاستبدال يُقترح فقط إذا كان الإصلاح غير مجدي."),
    ("كم تستغرق الزيارة؟",
     "زيارة الفحص أقصر من زيارة الاستبدال. المدة تعتمد على توفر القطعة وحجم غرفة المعدات، وتُذكر بعد المعاينة."),
])}
{cta(f"تحتاج صيانة مسبح في {city}؟", f"اذكر العَرَض: صوت، تسريب، أو ماء متغير. واتساب أو اتصال {TEL_LOCAL}.")}
<h2>{fa("link")} مقالات مسابح ذات صلة</h2>
{related([LINKS["clean_ad"], LINKS["build_ad"], LINKS["maint_dxb"]])}
</div>
"""


def build_construct(city: str) -> str:
    c = CITY[city]
    return f"""
<div class="rp">
<section class="rp-hero">
<h2>{fa("person-swimming")} شركة إنشاء وصيانة مسابح في {city}</h2>
<p>إنشاء مسبح في {city} يبدأ من اختيار النوع والموقع والعزل، لا من حفر الأرض فقط. {c["climate"]}. ركن التطور ترتب المعاينة لتوضيح إمكانيات المساحة ومتطلبات الدورة والفلترة قبل أي تنفيذ.</p>
</section>
{img("build", f"أعمال إنشاء مسبح تناسب مناخ {city}", True)}
<h2>{fa("layer-group")} أنواع الأحواض الشائعة</h2>
{cards([
    ("house", "مسبح فيلا خرساني", "حرية الشكل والعمق، ويحتاج عزلًا وتمديدات واضحة قبل البلاط."),
    ("border-all", "مسبح جاهز أو فايبر", "أسرع في بعض المساحات الصغيرة، وقيوده في التعديل لاحقًا."),
    ("children", "مسبح ضحل أو أطفال", "دورة أقصر وحماية حواف، مع فلترة تناسب الحجم الصغير."),
    ("hot-tub", "ملحق جاكوزي", "إن طُلب، يُدرس منفصلًا لأن السخان والأنابيب تختلف."),
])}
<h2>{fa("list-check")} مراحل الإنشاء</h2>
{steps([
    ("معاينة الموقع", f"المساحة، الظل، الجيران، ومسار غرفة المعدات في {c['areas']} أو موقعك."),
    ("التصميم المبدئي", "الشكل، العمق، الدرج، والإنارة. لا نثبت مقاسات نهائية قبل موافقتك."),
    ("العزل والتمديدات", "الخطوط والرجوع والمفيض تُحدد قبل التشطيب."),
    ("التشطيب والمعدات", "البلاط، المضخة، الفلتر، ولوحة التشغيل."),
    ("التشغيل والتسليم", "ملء وتجربة الدورة، مع شرح الاستخدام اليومي."),
])}
{callout("info", "معلومة مهمة", "المدة والتكلفة تختلف حسب الحفر، نوع التربة، والعزل المختار. أي رقم قبل المعاينة تخمين.")}
<h2>{fa("table")} ما الذي يؤثر على القرار</h2>
{table(
    ["عامل", "لماذا يهم", "سؤال للمعاينة"],
    [
        ["المساحة والارتداد", "يحدد الشكل وغرفة المعدات", "هل يوجد منفذ صيانة لاحقًا؟"],
        ["الظل والأشجار", "أوراق = حمل فلتر أعلى", "هل يلزم غطاء أو شبكة؟"],
        ["نوع الاستخدام", "سباحة يومية أم مناسبات", "كم ساعة تشغيل متوقعة؟"],
        ["الصيانة بعد التسليم", "الإنشاء بلا خطة تنظيف يتعب سريعًا", "هل تريد عقد تنظيف منفصل؟"],
    ],
)}
{img("equip", f"معدات فلترة تُختار حسب حجم المسبح في {city}")}
{callout("tip", "نصيحة", f"{c['note']}.")}
<h2>{fa("circle-question")} أسئلة إنشاء المسابح في {city}</h2>
{faq([
    (f"هل تنشئون مسابح في كل {city}؟",
     f"نعم ضمن المواقع التي يمكن معاينتها والوصول إليها بمعدات الحفر. بعض الأزقة الضيقة تحتاج تقييم وصول أولًا."),
    ("هل السعر يشمل العزل والبلاط؟",
     "يُكتب في العرض بعد المعاينة. لا يوجد باقة واحدة لكل فيلا."),
    ("هل الصيانة بعد الإنشاء إلزامية معنا؟",
     "ليست شرطًا. يمكن طلب التنظيف أو الصيانة لاحقًا كخدمة مستقلة."),
])}
{cta(f"تخطط لمسبح في {city}؟", f"أرسل مساحة الفناء أو مخططًا تقريبيًا على واتساب {TEL_LOCAL}.")}
<h2>{fa("link")} تنظيف وصيانة بعد الإنشاء</h2>
{related([LINKS["clean_ad"], LINKS["maint_ad"], LINKS["build_ad"]])}
</div>
"""


def render(kind: str, city: str) -> str:
    body = {"clean": build_clean, "maint": build_maint, "build": build_construct}[kind](city)
    return css() + body
