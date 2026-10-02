"""Pool landing-page HTML. Design tokens from Rukn leak/insulation pages.

Arabic + English. Do not invent prices, guarantees, reviews, or staff counts.
Pool leak / waterproofing copy must not compete with building leak-detection
or roof-insulation keywords.
"""

from __future__ import annotations

TEL = "+971521300019"
TEL_LOCAL = "0521300019"
WA = "971521300019"

IMG = {
    "clean1": (
        3266,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Prof-Pool-Cleaner-rukn-eltatawer.com_-1.webp",
        "تنظيف حوض سباحة بمعدات احترافية",
        "Professional swimming pool cleaning",
    ),
    "clean2": (
        3260,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Pool-Cleaning-rukn-eltatawer.com_.webp",
        "تنظيف مياه المسبح وإزالة الشوائب",
        "Pool water cleaning and debris removal",
    ),
    "clean3": (
        2738,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2021/12/Swimming-pool-cleaning-2-e1721137044406.webp",
        "مكنسة يدوية لتنظيف قاع الإسبلاش",
        "Manual vacuum cleaning a pool floor",
    ),
    "clean4": (
        2901,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2021/12/Swimming-pool-cleaning-4-7.webp",
        "سطح مسبح بعد التنظيف",
        "Pool surface after cleaning",
    ),
    "maint1": (
        3255,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2024/05/pool-maintenance-rukn-eltatawer.com_.webp",
        "أعمال صيانة مسبح",
        "Swimming pool maintenance work",
    ),
    "equip": (
        3261,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Pool-equipment-scale-rukn-eltatawer.com_.webp",
        "مضخة وفلتر مسبح",
        "Pool pump and filter equipment",
    ),
    "scale": (
        3263,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2025/07/Pool-scale-rukn-eltatawer.com_.webp",
        "مقياس جودة مياه المسبح",
        "Pool water test scale",
    ),
    "build": (
        1527,
        "https://www.rukn-eltatawer.com/wp-content/uploads/2020/06/3.jpg",
        "تصميم وإنشاء مسبح",
        "Swimming pool design and construction",
    ),
}

CITY = {
    "عجمان": {
        "en": "Ajman",
        "climate": "رطوبة ساحلية وحرارة مرتفعة تجعل الطحالب والأملاح تظهر أسرع مما يتوقع كثير من أصحاب الفلل",
        "climate_en": "Coastal humidity and high heat let algae and salt film appear faster than many villa owners expect",
        "areas": "الراشدية والنعيمية والجرف",
        "areas_en": "Al Rashidiya, Al Nuaimiya and Al Jurf",
        "note": "المسابح هنا غالبًا ضمن فلل متلاصقة؛ أي تسريب أو مضخة ضعيفة يظهر أثره على الجار وعلى فاتورة المياه بسرعة",
        "note_en": "Pools here often sit in attached villas; a leak or weak pump shows on the neighbour and the water bill quickly",
    },
    "رأس الخيمة": {
        "en": "Ras Al Khaimah",
        "climate": "هواء بحري وملوحة أعلى من الداخل، مع غبار في الأيام العاصفة",
        "climate_en": "Sea air and higher salinity than inland areas, plus dust on windy days",
        "areas": "النخيل والرمس والمعيريض",
        "areas_en": "Al Nakheel, Rams and Al Maerid",
        "note": "الفلاتر والمبادلات المعدنية تحتاج متابعة أوضح بسبب الملوحة، خاصة في المسابح القريبة من الساحل",
        "note_en": "Filters and metal exchangers need clearer follow-up because of salinity, especially near the coast",
    },
    "الفجيرة": {
        "en": "Fujairah",
        "climate": "الساحل الشرقي أشد رطوبة، مع تباين حراري بين الجبل والبحر",
        "climate_en": "The east coast is more humid, with temperature swings between mountain and sea",
        "areas": "المرحلة والقدفع ومربح",
        "areas_en": "Al Faseel, Qidfa and Murbeh",
        "note": "المياه الراكدة بعد الإجازات شائعة في البيوت الموسمية؛ التنظيف المتأخر يرفع جهد المعالجة لاحقًا",
        "note_en": "Stagnant water after holidays is common in seasonal homes; delayed cleaning makes later treatment harder",
    },
    "أم القيوين": {
        "en": "Umm Al Quwain",
        "climate": "بيئة هادئة لكن الحرارة والرطوبة كافيتان لتعكير الماء خلال أيام قليلة بدون circulating منتظم",
        "climate_en": "A quieter setting, but heat and humidity still cloud water within a few days without regular circulation",
        "areas": "المدينة القديمة وفلج المعلا",
        "areas_en": "Old Town and Falaj Al Mualla",
        "note": "كثير من المسابح هنا صغيرة أو متوسطة؛ العطل في مضخة واحدة يوقف الدورة كلها",
        "note_en": "Many pools here are small or mid-size; one failed pump stops the whole circuit",
    },
    "العين": {
        "en": "Al Ain",
        "climate": "حرارة داخلية أعلى وغبار أكثر، مع ماء أملاحه أعلى من الساحل في بعض الأحياء",
        "climate_en": "Higher inland heat and more dust, with harder water than the coast in some neighbourhoods",
        "areas": "المقام والجاهلي والهيلي",
        "areas_en": "Al Maqam, Al Jimi and Al Hili",
        "note": "الترسبات الكلسية على البلاط والسلال تظهر أسرع؛ جدول الفلترة أهم من زيادة الكلور عشوائيًا",
        "note_en": "Scale on tiles and baskets appears faster; a filtration schedule matters more than random extra chlorine",
    },
    "أبوظبي": {
        "en": "Abu Dhabi",
        "climate": "حرارة ورطوبة ساحلية مع استخدام كثيف في الفلل والمجتمعات السكنية",
        "climate_en": "Coastal heat and humidity with heavy use in villas and residential communities",
        "areas": "الخليفة والشاطئ ومحمد بن زايد",
        "areas_en": "Khalifa City, Al Bateen and Mohamed Bin Zayed",
        "note": "المعاينة توضح إن كان الاحتياج تنظيفًا دوريًا أم إصلاح مضخة وفلتر",
        "note_en": "A visit shows whether you need a cleaning schedule or a pump and filter repair",
    },
    "دبي": {
        "en": "Dubai",
        "climate": "استخدام عالٍ على مدار السنة وملوحة في بعض المناطق الساحلية",
        "climate_en": "Year-round use and salinity in some coastal districts",
        "areas": "المارينا وجميرا والبرشاء",
        "areas_en": "Marina, Jumeirah and Al Barsha",
        "note": "المسابح المشتركة في العمائر تختلف خطتها عن مسبح الفيلا الخاصة",
        "note_en": "Shared building pools need a different plan than a private villa pool",
    },
    "الشارقة": {
        "en": "Sharjah",
        "climate": "رطوبة خليجية وغبار موسمي يرفع حمل الفلتر",
        "climate_en": "Gulf humidity and seasonal dust raise the filter load",
        "areas": "الخان والبحيرة والنهدة",
        "areas_en": "Al Khan, Al Buhaira and Al Nahda",
        "note": "وضوح خطة الصيانة أهم من الوعود العامة قبل بدء أي عمل",
        "note_en": "A clear maintenance scope matters more than general promises before work starts",
    },
}

AR_PATH = {
    ("build", "أبوظبي"): "/swimming-pool-company-in-abu-dhabi/",
    ("build", "دبي"): "/swimming-pool-cleaning-in-dubai/",
    ("build", "العين"): "/swimming-pools-company-in-al-ain/",
    ("build", "الشارقة"): "/swimming-pools-company-in-sharjah/",
    ("build", "عجمان"): "/swimming-pool-company-in-ajman/",
    ("build", "رأس الخيمة"): "/swimming-pool-company-in-ras-al-khaimah/",
    ("build", "الفجيرة"): "/swimming-pool-company-in-fujairah/",
    ("build", "أم القيوين"): "/swimming-pool-company-in-umm-al-quwain/",
    ("clean", "أبوظبي"): "/abu-dhabi-swimming-pool-cleaning-company/",
    ("clean", "دبي"): "/dubai-pool-cleaning/",
    ("clean", "الشارقة"): "/sharjah-pool-cleaning/",
    ("clean", "عجمان"): "/pool-cleaning-ajman/",
    ("clean", "رأس الخيمة"): "/pool-cleaning-ras-al-khaimah/",
    ("clean", "الفجيرة"): "/swimming-pool-cleaning-company-in-fujairah-2/",
    ("clean", "أم القيوين"): "/cleaning-swimming-pools-in-umm-al-quwain/",
    ("clean", "العين"): "/swimming-pool-cleaning-company-in-al-ain/",
    ("maint", "أبوظبي"): "/pool-maintenance-abu-dhabi/",
    ("maint", "دبي"): "/pool-maintenance-dubai/",
    ("maint", "الشارقة"): "/pool-maintenance-sharjah/",
    ("maint", "عجمان"): "/pool-maintenance-ajman/",
    ("maint", "رأس الخيمة"): "/pool-maintenance-ras-al-khaimah/",
    ("maint", "الفجيرة"): "/pool-maintenance-fujairah/",
    ("maint", "أم القيوين"): "/pool-maintenance-umm-al-quwain/",
    ("maint", "العين"): "/pool-maintenance-al-ain/",
    ("hub", ""): "/swimming-pool-company-uae/",
    ("leak", ""): "/swimming-pool-leak-repair-uae/",
    ("waterproof", ""): "/swimming-pool-waterproofing-uae/",
    ("chlorine", ""): "/swimming-pool-chlorine-salt-uae/",
    ("heat", ""): "/swimming-pool-heating-uae/",
    ("jacuzzi", ""): "/jacuzzi-service-uae/",
}

EN_PATH = {
    ("build", "أبوظبي"): "/en/swimming-pool-construction-abu-dhabi/",
    ("build", "دبي"): "/en/swimming-pool-construction-dubai/",
    ("build", "العين"): "/en/swimming-pool-construction-al-ain/",
    ("build", "الشارقة"): "/en/swimming-pool-construction-sharjah/",
    ("build", "عجمان"): "/en/swimming-pool-construction-ajman/",
    ("build", "رأس الخيمة"): "/en/swimming-pool-construction-ras-al-khaimah/",
    ("build", "الفجيرة"): "/en/swimming-pool-construction-fujairah/",
    ("build", "أم القيوين"): "/en/swimming-pool-construction-umm-al-quwain/",
    ("clean", "أبوظبي"): "/en/swimming-pool-cleaning-abu-dhabi/",
    ("clean", "دبي"): "/en/swimming-pool-cleaning-dubai/",
    ("clean", "الشارقة"): "/en/swimming-pool-cleaning-sharjah/",
    ("clean", "عجمان"): "/en/swimming-pool-cleaning-ajman/",
    ("clean", "رأس الخيمة"): "/en/swimming-pool-cleaning-ras-al-khaimah/",
    ("clean", "الفجيرة"): "/en/swimming-pool-cleaning-fujairah/",
    ("clean", "أم القيوين"): "/en/swimming-pool-cleaning-umm-al-quwain/",
    ("clean", "العين"): "/en/swimming-pool-cleaning-al-ain/",
    ("maint", "أبوظبي"): "/en/swimming-pool-maintenance-abu-dhabi/",
    ("maint", "دبي"): "/en/swimming-pool-maintenance-dubai/",
    ("maint", "الشارقة"): "/en/swimming-pool-maintenance-sharjah/",
    ("maint", "عجمان"): "/en/swimming-pool-maintenance-ajman/",
    ("maint", "رأس الخيمة"): "/en/swimming-pool-maintenance-ras-al-khaimah/",
    ("maint", "الفجيرة"): "/en/swimming-pool-maintenance-fujairah/",
    ("maint", "أم القيوين"): "/en/swimming-pool-maintenance-umm-al-quwain/",
    ("maint", "العين"): "/en/swimming-pool-maintenance-al-ain/",
    ("hub", ""): "/en/pool-company-uae/",
    ("leak", ""): "/en/pool-leak-repair-uae/",
    ("waterproof", ""): "/en/pool-waterproofing-uae/",
    ("chlorine", ""): "/en/pool-chlorine-salt-uae/",
    ("heat", ""): "/en/pool-heating-uae/",
    ("jacuzzi", ""): "/en/spa-jacuzzi-service-uae/",
}

AR_LABEL = {
    "build": "إنشاء وصيانة مسابح {city}",
    "clean": "تنظيف مسابح {city}",
    "maint": "صيانة مسابح {city}",
    "hub": "شركة مسابح في الإمارات",
    "leak": "إصلاح تسريب المسبح",
    "waterproof": "عزل أحواض السباحة",
    "chlorine": "كلور وملح المسابح",
    "heat": "تدفئة المسابح",
    "jacuzzi": "جاكوزي وتركيب الأحواض الساخنة",
}

EN_LABEL = {
    "build": "Pool construction in {city}",
    "clean": "Pool cleaning in {city}",
    "maint": "Pool maintenance in {city}",
    "hub": "Swimming pool company in the UAE",
    "leak": "Swimming pool leak repair",
    "waterproof": "Swimming pool waterproofing",
    "chlorine": "Pool chlorine and salt systems",
    "heat": "Swimming pool heating",
    "jacuzzi": "Jacuzzi installation and service",
}


def loc(city: str, lang: str) -> tuple[str, dict]:
    c = CITY[city]
    return (c["en"] if lang == "en" else city, c)


def path_of(kind: str, city: str, lang: str) -> str:
    table = EN_PATH if lang == "en" else AR_PATH
    return table[(kind, city if kind in ("build", "clean", "maint") else "")]


def label_of(kind: str, city: str, lang: str) -> str:
    table = EN_LABEL if lang == "en" else AR_LABEL
    name = CITY[city]["en"] if lang == "en" and city else city
    return table[kind].format(city=name)


def css(lang: str = "ar") -> str:
    extra = ""
    if lang == "en":
        extra = """
.rp.rp-en{direction:ltr;text-align:left}
.rp.rp-en .rp-hero{border-right:none;border-left:6px solid #0056b3}
.rp.rp-en .rp-table-wrap table{text-align:left}
.rp.rp-en .rp-box{border-right:none}
.rp.rp-en .rp-box-warn{border-left:5px solid #ffc107}
.rp.rp-en .rp-box-tip{border-left:5px solid #1976d2}
.rp.rp-en .rp-box-info{border-left:5px solid #0056b3}
"""
    return f"""<style>
.rp{{max-width:100%;color:#1a2332;line-height:1.85}}
.rp h2{{display:flex;align-items:center;gap:10px;color:#003366;margin:36px 0 16px;font-size:1.45rem}}
.rp h3{{display:flex;align-items:center;gap:8px;color:#003366;margin:0 0 10px;font-size:1.08rem}}
.rp .rp-hero{{background:linear-gradient(135deg,#f0f7ff,#fff);border-right:6px solid #0056b3;padding:28px 26px;border-radius:16px;margin:18px 0 28px;box-shadow:0 4px 18px rgba(0,0,0,.06)}}
.rp .rp-hero h2{{margin-top:0}}
.rp .rp-cards{{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px;margin:22px 0}}
.rp .rp-card{{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px 16px;box-shadow:0 4px 12px rgba(0,0,0,.05)}}
.rp .rp-step{{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;position:relative}}
.rp .rp-num{{width:32px;height:32px;border-radius:50%;background:#0056b3;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;margin-inline-end:8px}}
.rp .rp-img{{max-width:100%;height:auto;border-radius:12px;margin:12px 0 8px}}
.rp .rp-cap{{font-size:.9rem;color:#4a5568;margin:0 0 20px}}
.rp .rp-cta{{background:linear-gradient(135deg,#003366,#0056b3);color:#fff;padding:26px 22px;border-radius:16px;margin:32px 0;text-align:center}}
.rp .rp-cta h2,.rp .rp-cta p{{color:#fff}}
.rp .rp-cta a{{display:inline-block;margin:8px 6px 0;padding:10px 18px;border-radius:10px;font-weight:700;text-decoration:none}}
.rp .rp-cta .rp-call{{background:#fff;color:#003366}}
.rp .rp-cta .rp-wa{{background:#25d366;color:#fff}}
.rp .rp-faq{{margin:18px 0}}
.rp .faq-item{{border:1px solid #e2e8f0;border-radius:12px;margin:10px 0;overflow:hidden;background:#fff}}
.rp .faq-q{{background:#f0f7ff;padding:14px 16px;font-weight:700;color:#003366;display:flex;gap:8px;align-items:flex-start}}
.rp .faq-a{{padding:14px 16px}}
.rp .rp-links{{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin:20px 0}}
.rp .rp-links a{{display:block;padding:14px 16px;border:1px solid #dce8f8;border-radius:12px;background:#f8fbff;color:#0056b3;text-decoration:none;font-weight:600}}
.rp-table-wrap{{overflow-x:auto;-webkit-overflow-scrolling:touch;margin:20px 0;border:1px solid #e5e7eb;border-radius:12px}}
.rp-table-wrap table{{width:100%;min-width:520px;border-collapse:collapse;text-align:right}}
.rp-table-wrap th{{background:#f0f6ff;padding:10px 12px}}
.rp-table-wrap td{{padding:10px 12px;border-top:1px solid #eef2f7}}
.rp-box{{padding:16px 18px;border-radius:10px;margin:22px 0}}
.rp-box-warn{{background:#fff3cd;border-right:5px solid #ffc107}}
.rp-box-tip{{background:#e3f2fd;border-right:5px solid #1976d2}}
.rp-box-info{{background:#f0f7ff;border-right:5px solid #0056b3}}
@media(max-width:640px){{.rp h2{{font-size:1.22rem}}.rp .rp-hero{{padding:20px 16px}}}}
{extra}
</style>"""


def fa(name: str) -> str:
    return f'<i class="fa-solid fa-{name}"></i>'


def wrap(lang: str, inner: str) -> str:
    cls = "rp rp-en" if lang == "en" else "rp"
    d = ' dir="ltr" lang="en"' if lang == "en" else ' lang="ar"'
    return f'<div class="{cls}"{d}>\n{inner}\n</div>'


def img(key: str, alt: str | None = None, eager: bool = False, lang: str = "ar") -> str:
    mid, url, alt_ar, alt_en = IMG[key]
    a = alt or (alt_en if lang == "en" else alt_ar)
    load = "eager" if eager else "lazy"
    return (
        f'<img class="rp-img aligncenter wp-image-{mid} size-full" src="{url}" alt="{a}" '
        f'width="800" height="450" loading="{load}" decoding="async" />'
        f'<p class="rp-cap">{a}</p>'
    )


def cta(title: str, body: str, lang: str = "ar") -> str:
    if lang == "en":
        call = f'{fa("phone")} Call {TEL_LOCAL}'
        wa = f'{fa("comment")} WhatsApp {TEL}'
    else:
        call = f'{fa("phone")} اتصال {TEL_LOCAL}'
        wa = f'{fa("comment")} واتساب {TEL}'
    return f"""<div class="rp-cta">
<h2>{fa("phone")} {title}</h2>
<p>{body}</p>
<p><a class="rp-call" href="tel:{TEL}">{call}</a>
<a class="rp-wa" href="https://wa.me/{WA}" target="_blank" rel="noopener">{wa}</a></p>
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
        bits.append(f'<div class="rp-card"><h3>{fa(icon)} {title}</h3><p>{text}</p></div>')
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
    icon = {"warn": "triangle-exclamation", "tip": "lightbulb", "info": "circle-info"}[kind]
    return (
        f'<blockquote class="rp-box rp-box-{kind}">{fa(icon)} <strong>{title}:</strong> {text}</blockquote>'
    )


def related(pairs: list[tuple[str, str]], lang: str = "ar") -> str:
    arrow = "arrow-right" if lang == "en" else "arrow-left"
    bits = ['<div class="rp-links">']
    for href, label in pairs:
        bits.append(f'<a href="{href}">{fa(arrow)} {label}</a>')
    bits.append("</div>")
    return "\n".join(bits)


def city_related(kind: str, city: str, lang: str) -> str:
    others = [k for k in ("clean", "maint", "build") if k != kind]
    pairs = [(path_of(k, city, lang), label_of(k, city, lang)) for k in others]
    pairs.append((path_of("hub", "", lang), label_of("hub", "", lang)))
    extra = "leak" if kind == "maint" else "chlorine" if kind == "clean" else "waterproof"
    pairs.append((path_of(extra, "", lang), label_of(extra, "", lang)))
    return related(pairs, lang)


def build_clean(city: str, lang: str = "ar") -> str:
    name, c = loc(city, lang)
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("person-swimming")} Swimming pool cleaning company in {name}</h2>
<p>Pool cleaning in {name} from Rukn Eltatawer focuses on water clarity, walls, floor and baskets, then filtration that matches basin size and use. {c["climate_en"]}. A visit shows whether one clean is enough or a schedule is needed.</p>
</section>
{img("clean1", f"Swimming pool cleaning in {name}", True, lang)}
<h2>{fa("pump-soap")} What cleaning includes</h2>
<p>A useful clean is more than skimming the surface. In {c["areas_en"]} and the rest of {name} we treat the floor, waterline, baskets and filter, because skipping one part brings cloudiness back within days.</p>
{cards([
    ("broom", "Floor and wall brush", "Remove silt and clinging algae before they clog baskets or scratch tiles."),
    ("filter", "Filter and basket wash", "Lower pump pressure and restore circulation after hair and leaves collect."),
    ("droplet", "Water balance", "Read chlorine, pH and alkalinity on the meter. No fixed percentage is promised for every basin."),
    ("spray-can-sparkles", "Waterline and steps", "Oils and dust stick at the waterline; cleaning them changes how the pool looks immediately."),
])}
{callout("warn", "Caution", "Extra chlorine without brushing and filtration may bleach the water for hours, then green returns. Treat the source first.")}
<h2>{fa("list-check")} How a visit runs</h2>
{steps([
    ("Inspect the basin", f"We look at colour, smell, pump sound and tile condition at your {name} site."),
    ("Name the cause", "Algae, a blocked filter, a weak pump, or a circuit left off for days."),
    ("Agree the scope", "Surface clean, deep clean, or clean plus filter work. Cost is clear after the visit."),
    ("Carry out the work", "Brush, vacuum, wash baskets and check circulation."),
    ("Measure and hand over", "We review clarity and equipment, and explain what the pool needs after the visit."),
])}
{img("clean3", f"Brushing the pool floor during cleaning in {name}", False, lang)}
<h2>{fa("calendar-days")} When a deep clean is more likely</h2>
{table(
    ["Sign", "Common meaning", "What we review"],
    [
        ["Green or olive water", "Algae with weak circulation", "Filter, run hours, shade and leaves"],
        ["Milky white water", "Dirty filter or pH out of range", "Backwash and a water reading"],
        ["Slimy wall", "Organic film on tiles", "Hand scrub, not chlorine alone"],
        ["High filter pressure", "Media is saturated", "Wash or replace sand/cartridge"],
    ],
)}
{callout("tip", "Tip", f"{c['note_en']}.")}
<h2>{fa("clipboard-check")} What you can do between visits</h2>
<p>Even with a regular visit, the skimmer basket and the deck around the basin change how fast water dirties. Emptying leaves, running the circuit daily in summer, and keeping food out of the water reduce the next clean in {name}.</p>
{cards([
    ("clock", "Run the circuit", "Longer hours in heat beat extra chemicals with no circulation."),
    ("leaf", "Leaves and dust", "A cover or net helps if the pool sits under trees."),
    ("eye", "Water colour", "A shift toward green or cloudiness is worth a WhatsApp photo before algae lock in."),
])}
<h2>{fa("circle-question")} Pool cleaning questions in {name}</h2>
{faq([
    (f"How often should a pool be cleaned in {name}?",
     f"It depends on use, dust and shade. Some villas need a weekly summer visit; others need less in winter. The schedule is set after the first visit, not as one number for all of {name}."),
    ("Does cleaning include pump repair?",
     "Cleaning treats water, surfaces and the visible filter. Noise or a leak belongs to maintenance, and we flag that during the visit if the work should be split."),
    ("Do you use a fixed chemical brand?",
     "We use what matches the water reading and basin size. No mandatory brand is advertised. The aim is safe use after work ends."),
])}
{cta(f"Need pool cleaning in {name}?", f"Describe the water or send a photo on WhatsApp {TEL_LOCAL} so we can tell if the visit is cleaning or maintenance.", lang)}
<h2>{fa("link")} Related pool services</h2>
{city_related("clean", city, lang)}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("person-swimming")} شركة تنظيف مسابح في {name}</h2>
<p>خدمة تنظيف مسابح في {name} من ركن التطور تركز على إعادة صفاء الماء وإزالة الرواسب من الجدران والقاع والسلال، ثم ضبط الفلترة بما يناسب حجم الحوض ونمط الاستخدام. {c["climate"]}. المعاينة توضح إن كان يكفي تنظيف واحد أم يلزم برنامج دوري.</p>
</section>
{img("clean1", f"تنظيف مسبح في {name}", True, lang)}
<h2>{fa("pump-soap")} ماذا يشمل التنظيف</h2>
<p>التنظيف الجيد لا يقتصر على كشط السطح. في {c["areas"]} وغيرها من {name} نتعامل مع القاع، خط الماء، السلال، والفلتر لأن إهمال جزء واحد يعيد العكارة خلال أيام.</p>
{cards([
    ("broom", "كنس القاع والجدران", "إزالة الطمي والطحالب الملتصقة قبل أن تسد السلات أو تخدش البلاط."),
    ("filter", "غسيل الفلتر والسلال", "خفض ضغط المضخة وتحسين دوران الماء بعد تجمع الشعر والأوراق."),
    ("droplet", "موازنة المياه", "قياس الكلور وpH والقلوية الظاهرة على الجهاز، دون وعود بنسبة ثابتة لكل حوض."),
    ("spray-can-sparkles", "خط الماء والسلالم", "الدهون والغبار يلتصقان عند سطح الماء؛ تنظيفهما يغيّر شكل الحوض فورًا."),
])}
{callout("warn", "تنبيه", "زيادة الكلور بدون كنس وفلترة قد تبيّض الماء ساعات ثم تعود الخضرة. عالج المصدر أولًا.")}
<h2>{fa("list-check")} خطوات التنفيذ</h2>
{steps([
    ("معاينة الحوض", f"ننظر إلى لون الماء، رائحته، صوت المضخة، وحالة البلاط في موقعك بـ{name}."),
    ("تحديد السبب", "طحالب، فلتر مسدود، مضخة ضعيفة، أو توقف الدورة لأيام."),
    ("اختيار نطاق العمل", "تنظيف سطحي، تنظيف عميق، أو تنظيف مع خدمة فلتر. التكلفة تتضح بعد المعاينة."),
    ("التنفيذ", "كنس، شفط، غسل سلال، ومراجعة الدورة."),
    ("القياس والتسليم", "نراجع صفاء الماء وتشغيل المعدات، ونشرح ما يحتاجه المسبح بعد الزيارة."),
])}
{img("clean3", f"كنس قاع المسبح أثناء التنظيف في {name}", False, lang)}
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
<h2>{fa("clipboard-check")} ما يبقى على صاحب المسبح بين الزيارات</h2>
<p>حتى مع زيارة دورية، سلة السكimmer والمسار حول الحوض يؤثران على سرعة اتساخ الماء. تفريغ السلة من الأوراق، وتشغيل الدورة يوميًا في الصيف، وتجنيب رمي الطعام في الحوض يقلّل جهد التنظيف التالي في {name}.</p>
{cards([
    ("clock", "تشغيل الدورة", "ساعات أطول في الحر أفضل من زيادة كيماويات بلا دوران."),
    ("leaf", "الأوراق والغبار", "الغطاء أو الشبكة تخفف الحمل إن كان المسبح تحت شجر."),
    ("eye", "لون الماء", "أي ميل للخضرة أو الغيوم يستحق صورة على واتساب قبل أن تتماسك الطحالب."),
])}
<h2>{fa("circle-question")} أسئلة متكررة عن تنظيف المسابح في {name}</h2>
{faq([
    (f"كم مرة يُنظَّف المسبح في {name}؟",
     f"يختلف حسب الاستخدام والغبار والظل. بعض الفلل تحتاج زيارة أسبوعية في الصيف، وأخرى أقل في الشتاء. الجدول يُحدد بعد أول معاينة وليس برقم ثابت لكل {name}."),
    ("هل التنظيف يشمل إصلاح المضخة؟",
     "التنظيف يعالج الماء والأسطح والفلتر الظاهر. صوت المعدات أو التسريب يدخل في الصيانة، ويُذكر في المعاينة إن لزم فصل العمل."),
    ("هل تستخدمون موادًا معيّنة؟",
     "نستخدم ما يناسب قراءة الماء وحجم الحوض. لا نعلن علامة تجارية إلزامية، والهدف أمان الاستخدام بعد انتهاء العمل."),
])}
{cta(f"هل تحتاج تنظيف مسبح في {name}؟", f"صف حالة الماء أو أرسل صورة على واتساب {TEL_LOCAL} لنحدد إن كانت الزيارة تنظيفًا أم صيانة.", lang)}
<h2>{fa("link")} خدمات مسابح مرتبطة</h2>
{city_related("clean", city, lang)}
"""
    return wrap(lang, inner)


def build_maint(city: str, lang: str = "ar") -> str:
    name, c = loc(city, lang)
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("screwdriver-wrench")} Swimming pool maintenance company in {name}</h2>
<p>Pool maintenance in {name} means diagnosing the fault before swapping parts: a pump that will not pull, a filter under pressure, wet tiles around the coping, or chlorine that will not hold. {c["climate_en"]}. Rukn Eltatawer starts with a visit and writes the repair scope before work.</p>
</section>
{img("maint1", f"Pool equipment maintenance in {name}", True, lang)}
<h2>{fa("gears")} Common faults</h2>
{cards([
    ("filter", "High filter pressure", "Sand or cartridge is saturated, or a valve is left in the wrong position."),
    ("droplet", "Weak circulation", "Air in the pump, a blocked basket, or a damaged impeller."),
    ("triangle-exclamation", "Visible or hidden wetness", "Tile joints, lights or return lines. Finding the path needs a check, not a guess."),
    ("flask", "Water that will not settle", "Chemical dosing without enough runtime, or a damaged exchanger if heating is installed."),
])}
{callout("info", "Important", "We do not set a part price or a warranty period before seeing the pool. The visit writes what will be replaced and what will be cleaned.")}
<h2>{fa("list-check")} Maintenance path</h2>
{steps([
    ("Listen to the symptom", "When the noise, wetness or green water started."),
    ("Inspect equipment", f"Pump room, gauges and valves at the {name} site."),
    ("Explain options", "Repair, partial replacement, or a simple clean if the fault is minor."),
    ("Carry out agreed work", "After you approve the written scope."),
    ("Test run", "We confirm circulation returns and the first water reading is sensible."),
])}
{img("equip", f"Pool pump and filter during inspection in {name}", False, lang)}
<h2>{fa("table")} What you can check before the visit</h2>
{table(
    ["Observation", "Try this", "When to call"],
    [
        ["Pump hums but pushes no water", "Skimmer basket and pump lid", "If the noise continues after the basket is clean"],
        ["Water drops every day", "Marks around coping and lights", "A drop faster than normal evaporation"],
        ["Chlorine gone in hours", "Whether the circuit runs at least 8 hours", "With repeated algae despite dosing"],
        ["Breaker trips", "Do not reset it again and again", "Immediately so windings are not burned"],
    ],
)}
{callout("tip", "Tip", f"{c['note_en']}.")}
<h2>{fa("warehouse")} The equipment room</h2>
<p>Many {name} faults start in a room with no airflow or a flooded floor. A pump sitting in standing water damages windings, and a wet cable trips the breaker. During the visit we look at drainage and ventilation before talking about a new unit.</p>
{cards([
    ("bolt", "Power", "Do not reset a tripping breaker repeatedly; that burns windings."),
    ("droplet-slash", "Puddle under the base", "It may be a seal leak or condensation. The difference is clear on inspection."),
    ("gears", "Valves", "A backwash setting left after a wash stops normal circulation."),
])}
<h2>{fa("circle-question")} Pool maintenance questions in {name}</h2>
{faq([
    (f"Does pool maintenance in {name} include cleaning?",
     "If the fault is tied to the filter or baskets, yes within the agreed scope. A full basin clean is requested separately or combined after the visit."),
    ("Do you always replace the pump?",
     "No. Many faults are a basket, a seal or a cable. Replacement is suggested only if repair is not worthwhile."),
    ("How long is a visit?",
     "An inspection visit is shorter than a replacement visit. Time depends on part availability and the room size, and is stated after the visit."),
])}
{cta(f"Need pool maintenance in {name}?", f"Describe the symptom: noise, wetness, or changing water. WhatsApp or a call on {TEL_LOCAL}.", lang)}
<h2>{fa("link")} Related pool articles</h2>
{city_related("maint", city, lang)}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("screwdriver-wrench")} شركة صيانة مسابح في {name}</h2>
<p>صيانة المسابح في {name} تعني تشخيص العطل قبل استبدال القطع: مضخة لا تسحب، فلتر يضغط، تسريب حول الكور، أو كلور لا يثبت. {c["climate"]}. ركن التطور تبدأ بالمعاينة وتوضح نطاق الإصلاح قبل التنفيذ.</p>
</section>
{img("maint1", f"صيانة معدات مسبح في {name}", True, lang)}
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
    ("فحص المعدات", f"غرفة المضخة، العدادات، والصمامات في موقع {name}."),
    ("توضيح الخيارات", "إصلاح، استبدال جزئي، أو تنظيف مرافق إن كان العطل بسيطًا."),
    ("التنفيذ", "بعد موافقتك على النطاق المكتوب."),
    ("تشغيل تجريبي", "نتأكد أن الدورة تعود وأن القراءة الأولية للماء منطقية."),
])}
{img("equip", f"مضخة وفلتر مسبح أثناء الفحص في {name}", False, lang)}
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
<h2>{fa("warehouse")} غرفة المعدات</h2>
<p>معظم أعطال {name} تبدأ من غرفة غير مهواة أو أرضية غارقة. المضخة فوق ماء راكد تفسد اللفات، والكابل المبلل يفصل القاطع. أثناء المعاينة ننظر إلى التصريف والتهوية قبل الحديث عن استبدال جهاز جديد.</p>
{cards([
    ("bolt", "الكهرباء", "لا تعِد تشغيل القاطع مرات متتالية؛ هذا يحرق الملفات."),
    ("droplet-slash", "بركة تحت القاعدة", "قد تكون تسريب ختم أو تكثف. الفرق يتضح بالفحص."),
    ("gears", "الصمامات", "وضع الغسيل العكسي إن نُسي بعد الغسيل يمنع الدوران الطبيعي."),
])}
<h2>{fa("circle-question")} أسئلة عن صيانة المسابح في {name}</h2>
{faq([
    (f"هل صيانة المسبح في {name} تشمل التنظيف؟",
     "إذا كان العطل مرتبطًا بالفلتر أو السلال نعم ضمن النطاق المتفق عليه. التنظيف الكامل للحوض يُطلب كخدمة منفصلة أو يُدمج بعد المعاينة."),
    ("هل تستبدلون المضخة دائمًا؟",
     "لا. كثير من الأعطال سلّة أو موانع تسريب أو كابل. الاستبدال يُقترح فقط إذا كان الإصلاح غير مجدي."),
    ("كم تستغرق الزيارة؟",
     "زيارة الفحص أقصر من زيارة الاستبدال. المدة تعتمد على توفر القطعة وحجم غرفة المعدات، وتُذكر بعد المعاينة."),
])}
{cta(f"تحتاج صيانة مسبح في {name}؟", f"اذكر العَرَض: صوت، تسريب، أو ماء متغير. واتساب أو اتصال {TEL_LOCAL}.", lang)}
<h2>{fa("link")} مقالات مسابح ذات صلة</h2>
{city_related("maint", city, lang)}
"""
    return wrap(lang, inner)


def build_construct(city: str, lang: str = "ar") -> str:
    name, c = loc(city, lang)
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("person-swimming")} Swimming pool construction company in {name}</h2>
<p>Building a pool in {name} starts with type, location and waterproofing — not excavation alone. {c["climate_en"]}. Rukn Eltatawer arranges a visit to clarify space, circulation and filtration before any build work.</p>
</section>
{img("build", f"Pool construction suited to the {name} climate", True, lang)}
<h2>{fa("layer-group")} Common basin types</h2>
{cards([
    ("house", "Concrete villa pool", "Freedom of shape and depth, and it needs waterproofing and pipe routes before tiling."),
    ("border-all", "Prefabricated or fibreglass", "Faster in some small yards, with limits on later changes."),
    ("children", "Shallow or children's pool", "Shorter circuit and edge protection, with filtration sized for a small volume."),
    ("hot-tub", "Jacuzzi add-on", "If requested, it is studied separately because the heater and pipework differ."),
])}
<h2>{fa("list-check")} Construction stages</h2>
{steps([
    ("Site visit", f"Space, shade, neighbours and the equipment-room path in {c['areas_en']} or your plot."),
    ("Outline design", "Shape, depth, steps and lighting. Final sizes are not locked before you approve."),
    ("Waterproofing and pipework", "Returns, overflow and lines are set before finishing."),
    ("Finishes and equipment", "Tiles, pump, filter and control board."),
    ("Commissioning", "Fill, test the circuit, and explain daily use."),
])}
{callout("info", "Important", "Time and cost change with excavation, soil and the chosen waterproofing. Any number before a visit is a guess.")}
<h2>{fa("table")} What shapes the decision</h2>
{table(
    ["Factor", "Why it matters", "Visit question"],
    [
        ["Space and setbacks", "Sets shape and the equipment room", "Is there later service access?"],
        ["Shade and trees", "Leaves raise the filter load", "Is a cover or net needed?"],
        ["How it will be used", "Daily swim or occasional events", "How many run hours are expected?"],
        ["Care after handover", "A build without a cleaning plan tires quickly", "Do you want a separate cleaning plan?"],
    ],
)}
{img("equip", f"Filtration equipment chosen for pool size in {name}", False, lang)}
{callout("tip", "Tip", f"{c['note_en']}.")}
<h2>{fa("shield-halved")} Waterproofing and finishes</h2>
<p>Waterproofing is chosen for the basin type, not the cheapest roll in the market. A mistake here shows after filling: damp, peeling tiles, or water loss. In {name} we prefer written waterproofing and pipe details before tiles so the shell is not opened later.</p>
{cards([
    ("layer-group", "Waterproof layer", "Set after basin shape and concrete or prefabricated shell type."),
    ("border-all", "Tiles and edges", "Slippery edges and steps matter more than appearance alone."),
    ("lightbulb", "Lighting", "The cable route is decided before the pour so tiles are not broken later."),
])}
<h2>{fa("circle-question")} Pool construction questions in {name}</h2>
{faq([
    (f"Do you build pools across {name}?",
     "Yes at sites that can be visited and reached by excavation equipment. Some narrow alleys need an access check first."),
    ("Does the price include waterproofing and tiles?",
     "That is written in the offer after the visit. There is no single package for every villa."),
    ("Is later maintenance mandatory with you?",
     "It is not a condition. Cleaning or maintenance can be requested later as a separate service."),
])}
{cta(f"Planning a pool in {name}?", f"Send a yard size or a rough sketch on WhatsApp {TEL_LOCAL}.", lang)}
<h2>{fa("link")} Cleaning and maintenance after construction</h2>
{city_related("build", city, lang)}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("person-swimming")} شركة إنشاء وصيانة مسابح في {name}</h2>
<p>إنشاء مسبح في {name} يبدأ من اختيار النوع والموقع والعزل، لا من حفر الأرض فقط. {c["climate"]}. ركن التطور ترتب المعاينة لتوضيح إمكانيات المساحة ومتطلبات الدورة والفلترة قبل أي تنفيذ.</p>
</section>
{img("build", f"أعمال إنشاء مسبح تناسب مناخ {name}", True, lang)}
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
{img("equip", f"معدات فلترة تُختار حسب حجم المسبح في {name}", False, lang)}
{callout("tip", "نصيحة", f"{c['note']}.")}
<h2>{fa("shield-halved")} العزل والتشطيب</h2>
<p>العزل يُختار حسب نوع الحوض لا حسب أرخص لفة في السوق. خطأ هنا يظهر بعد الملء: نمش، أو تقشير بلاط، أو فقد ماء. في {name} نفضّل تثبيت تفاصيل العزل والتمديد كتابيًا قبل البلاط حتى لا يُعاد الفتح لاحقًا.</p>
{cards([
    ("layer-group", "طبقة العزل", "تُحدد بعد شكل الحوض ونوع الخرسانة أو الجسم الجاهز."),
    ("border-all", "البلاط والحواف", "الحواف المنزلقة والدرج أهم من الشكل فقط."),
    ("lightbulb", "الإنارة", "مسار الكابل يُحسم قبل الصبة حتى لا يُكسر البلاط لاحقًا."),
])}
<h2>{fa("circle-question")} أسئلة إنشاء المسابح في {name}</h2>
{faq([
    (f"هل تنشئون مسابح في كل {name}؟",
     "نعم ضمن المواقع التي يمكن معاينتها والوصول إليها بمعدات الحفر. بعض الأزقة الضيقة تحتاج تقييم وصول أولًا."),
    ("هل السعر يشمل العزل والبلاط؟",
     "يُكتب في العرض بعد المعاينة. لا يوجد باقة واحدة لكل فيلا."),
    ("هل الصيانة بعد الإنشاء إلزامية معنا؟",
     "ليست شرطًا. يمكن طلب التنظيف أو الصيانة لاحقًا كخدمة مستقلة."),
])}
{cta(f"تخطط لمسبح في {name}؟", f"أرسل مساحة الفناء أو مخططًا تقريبيًا على واتساب {TEL_LOCAL}.", lang)}
<h2>{fa("link")} تنظيف وصيانة بعد الإنشاء</h2>
{city_related("build", city, lang)}
"""
    return wrap(lang, inner)


def _spec_related(lang: str, *kinds: str) -> str:
    pairs = []
    for k in kinds:
        city = "أبوظبي" if k in ("build", "clean", "maint") else ""
        pairs.append((path_of(k, city, lang), label_of(k, city, lang)))
    return related(pairs, lang)


def build_hub(lang: str = "ar") -> str:
    cities = list(CITY)
    if lang == "en":
        city_cards = [
            (
                "location-dot",
                CITY[c]["en"],
                f"Construction, cleaning and maintenance pages for {CITY[c]['en']}.",
            )
            for c in cities
        ]
        links = []
        for c in ("أبوظبي", "دبي", "الشارقة", "عجمان"):
            links.append((path_of("build", c, lang), label_of("build", c, lang)))
            links.append((path_of("clean", c, lang), label_of("clean", c, lang)))
        inner = f"""
<section class="rp-hero">
<h2>{fa("person-swimming")} Swimming pool company in the UAE</h2>
<p>Rukn Eltatawer handles villa and building pools across the Emirates: construction where a site can be visited, cleaning when water or surfaces need work, and maintenance when a pump, filter or circuit fails. This page is the map. City pages hold local climate notes. Specialty pages cover basin leaks, pool waterproofing, chlorine and salt, heating and jacuzzi work — not building leak detection and not roof insulation.</p>
</section>
{img("build", "Swimming pool design and construction in the UAE", True, lang)}
<h2>{fa("map")} Where the work is</h2>
{cards(city_cards)}
{callout("info", "Scope", "A site visit sets the service. We do not publish a UAE-wide price list or invent project counts.")}
<h2>{fa("layer-group")} Three service lines</h2>
{cards([
    ("helmet-safety", "Construction", "Type, waterproofing, pipe routes and equipment before tiles. Time and cost follow the visit."),
    ("broom", "Cleaning", "Floor, walls, waterline, baskets and filter. Deep clean when algae or cloudiness has set in."),
    ("screwdriver-wrench", "Maintenance", "Diagnose first. Replace a part only when repair is not worthwhile."),
])}
<h2>{fa("list-check")} How we start</h2>
{steps([
    ("Message or call", f"WhatsApp or call {TEL_LOCAL} with city, basin size if known, and the symptom."),
    ("Visit", "Colour, smell, pump room and access are checked on site."),
    ("Written scope", "Cleaning, repair or a construction outline — not a verbal guess."),
    ("Work", "After you approve the scope."),
    ("Handover", "Circuit test and a short note on daily care."),
])}
{img("clean2", "Pool water after a cleaning visit", False, lang)}
<h2>{fa("table")} Which page to open</h2>
{table(
    ["If you see", "Open", "Why"],
    [
        ["Green, cloudy or slimy water", "City cleaning page", "The basin and filter need work first"],
        ["Noise, trip or daily water drop", "City maintenance page", "Equipment or a basin wet path"],
        ["Empty yard, no basin yet", "City construction page", "Type and waterproofing come before digging"],
        ["Wet tiles around this pool only", "Pool leak repair", "This is the basin, not a building pipe search"],
    ],
)}
{callout("warn", "Caution", "Do not mix this service with building leak detection or roof insulation pages. Those jobs use a different team and number.")}
<h2>{fa("link")} City construction and cleaning</h2>
{related(links, lang)}
<h2>{fa("flask")} Specialty pool pages</h2>
{_spec_related(lang, "leak", "waterproof", "chlorine", "heat", "jacuzzi")}
{cta("Need a pool visit in the UAE?", f"Send the emirate and a photo of the water or the pump room on WhatsApp {TEL_LOCAL}.", lang)}
<h2>{fa("circle-question")} Questions about pool work in the UAE</h2>
{faq([
    ("Do you cover all seven emirates plus Al Ain?",
     "Yes where the site can be visited. Access for cleaning is simpler than access for excavation."),
    ("Is one number used for all pool pages?",
     f"Yes. Call and WhatsApp for pool articles is {TEL}."),
    ("Do you quote before seeing the pool?",
     "No fixed quote. The visit writes the scope. Any number before that is a guess."),
])}
"""
        return wrap(lang, inner)

    city_cards = [
        ("location-dot", c, f"صفحات إنشاء وتنظيف وصيانة مسابح {c}.") for c in cities
    ]
    links = []
    for c in ("أبوظبي", "دبي", "الشارقة", "عجمان"):
        links.append((path_of("build", c, lang), label_of("build", c, lang)))
        links.append((path_of("clean", c, lang), label_of("clean", c, lang)))
    inner = f"""
<section class="rp-hero">
<h2>{fa("person-swimming")} شركة مسابح في الإمارات</h2>
<p>ركن التطور تتعامل مع مسابح الفلل والعمائر في الإمارات: إنشاء حيث يمكن معاينة الموقع، تنظيف عندما يحتاج الماء أو الأسطح عملًا، وصيانة عندما تضعف المضخة أو الفلتر أو الدورة. هذه الصفحة خريطة. صفحات المدن تحمل ملاحظة المناخ المحلي. الصفحات التخصصية تغطي تسريب الحوض، عزل المسبح، الكلور والملح، التدفئة والجاكوزي — وليست كشف تسربات المباني وليست عزل الأسطح.</p>
</section>
{img("build", "تصميم وإنشاء مسبح في الإمارات", True, lang)}
<h2>{fa("map")} أين نعمل</h2>
{cards(city_cards)}
{callout("info", "نطاق العمل", "المعاينة تحدد الخدمة. لا ننشر قائمة أسعار لكل الإمارات ولا نخترع عدد مشاريع.")}
<h2>{fa("layer-group")} ثلاثة خطوط خدمة</h2>
{cards([
    ("helmet-safety", "الإنشاء", "النوع والعزل ومسار الأنابيب والمعدات قبل البلاط. المدة والتكلفة بعد المعاينة."),
    ("broom", "التنظيف", "القاع والجدران وخط الماء والسلال والفلتر. تنظيف عميق إن تماسكت الطحالب أو العكارة."),
    ("screwdriver-wrench", "الصيانة", "التشخيص أولًا. استبدال القطعة فقط إن لم يكن الإصلاح مجديًا."),
])}
<h2>{fa("list-check")} كيف نبدأ</h2>
{steps([
    ("رسالة أو اتصال", f"واتساب أو اتصال {TEL_LOCAL} مع الإمارة وحجم الحوض إن عُرف والعَرَض."),
    ("معاينة", "اللون والرائحة وغرفة المضخة ومسار الوصول تُراجع في الموقع."),
    ("نطاق مكتوب", "تنظيف أو إصلاح أو تصور إنشاء — لا تخمين شفهي."),
    ("التنفيذ", "بعد موافقتك على النطاق."),
    ("التسليم", "تجربة الدورة وملاحظة قصيرة عن الاستخدام اليومي."),
])}
{img("clean2", "ماء المسبح بعد زيارة تنظيف", False, lang)}
<h2>{fa("table")} أي صفحة تفتح</h2>
{table(
    ["إن رأيت", "افتح", "لماذا"],
    [
        ["ماء أخضر أو غائم أو لزج", "صفحة تنظيف المدينة", "الحوض والفلتر يحتاجان عملًا أولًا"],
        ["صوت أو قاطع أو انخفاض يومي", "صفحة صيانة المدينة", "معدات أو مسار بلّل في الحوض"],
        ["فناء فارغ بلا حوض", "صفحة إنشاء المدينة", "النوع والعزل قبل الحفر"],
        ["بلاط مبتل حول هذا المسبح فقط", "إصلاح تسريب المسبح", "هذا الحوض، وليس بحث أنابيب المبنى"],
    ],
)}
{callout("warn", "تنبيه", "لا تخلط هذه الخدمة مع صفحات كشف تسربات المباني أو عزل الأسطح. تلك الأعمال بفريق ورقم مختلف.")}
<h2>{fa("link")} إنشاء وتنظيف حسب المدينة</h2>
{related(links, lang)}
<h2>{fa("flask")} صفحات تخصص المسابح</h2>
{_spec_related(lang, "leak", "waterproof", "chlorine", "heat", "jacuzzi")}
{cta("تحتاج زيارة مسبح في الإمارات؟", f"أرسل الإمارة وصورة للماء أو غرفة المضخة على واتساب {TEL_LOCAL}.", lang)}
<h2>{fa("circle-question")} أسئلة عن خدمات المسابح في الإمارات</h2>
{faq([
    ("هل تغطون الإمارات السبع مع العين؟",
     "نعم حيث يمكن معاينة الموقع. وصول التنظيف أبسط من وصول الحفر."),
    ("هل رقم المسابح واحد في كل الصفحات؟",
     f"نعم. الاتصال والواتساب لمقالات المسابح {TEL}."),
    ("هل تعطون سعرًا قبل رؤية المسبح؟",
     "لا عرض ثابت. المعاينة تكتب النطاق. أي رقم قبل ذلك تخمين."),
])}
"""
    return wrap(lang, inner)


def build_leak(lang: str = "ar") -> str:
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("droplet")} Swimming pool leak repair in the UAE</h2>
<p>This page is for water leaving the swimming pool itself: liner or tile joints, skimmer throat, return fittings, lights and the basin shell. It is not a building leak-detection page and not a search for hidden pipes inside walls or under a roof. If the wet path is around this pool, start here. If the house or roof is wet away from the basin, use those separate services.</p>
</section>
{img("maint1", "Inspecting a swimming pool basin for a wet path", True, lang)}
<h2>{fa("magnifying-glass")} What we look at</h2>
{cards([
    ("border-all", "Tile joints and coping", "Cracked grout or a lifted edge can lose water without a dramatic crack."),
    ("sink", "Skimmer and returns", "Gaskets and threaded fittings often weep while the shell looks fine."),
    ("lightbulb", "Underwater lights", "A failed gland shows as a slow daily drop."),
    ("layer-group", "Shell or liner", "A liner wrinkle or a hairline in concrete needs a closer look after the visit."),
])}
{callout("warn", "Not this page", "Building leak detection and roof insulation are different jobs, with different pages and a different number. Do not treat this article as a house-pipe search.")}
<h2>{fa("list-check")} How a pool leak visit runs</h2>
{steps([
    ("Confirm it is the basin", "Daily drop versus normal evaporation, and whether wet marks sit around this pool only."),
    ("Inspect fittings", "Skimmer, returns, lights and visible joints."),
    ("Narrow the path", "Pressure or dye checks only when they help this basin, not as a building scan."),
    ("Write the repair", "Gasket, grout, fitting or a larger shell discussion — after what we see."),
    ("Repair the agreed item", "Then refill or top up enough to test the circuit."),
])}
{img("equip", "Pool pump room checked when a basin is losing water", False, lang)}
<h2>{fa("table")} Drop versus other problems</h2>
{table(
    ["Observation", "More likely", "Next page"],
    [
        ["Wet deck around this pool", "Fitting or joint on the basin", "Stay on this page"],
        ["Green water, level stable", "Cleaning / circulation", "City cleaning page"],
        ["Pump room wet, basin full", "Pump seal or union", "City maintenance page"],
        ["Ceiling or roof wet, pool unused", "Building or roof service", "Those separate pages"],
    ],
)}
{callout("tip", "Tip", "A photo of the waterline mark over two mornings helps more than a guess at litres lost.")}
<h2>{fa("circle-question")} Pool leak questions</h2>
{faq([
    ("Do you find leaks inside the house from this page?",
     "No. This page is the swimming pool basin and its fittings. House pipe or roof work is a different service."),
    ("Can you quote a liner replacement in a message?",
     "Not honestly. Liner or shell work depends on access and what the visit shows."),
    ("Will you empty the pool every time?",
     "Only if the repair needs it. Many fitting leaks are handled with a partial drop."),
])}
{cta("Pool losing water?", f"Send the emirate and two photos: waterline and the wet area. WhatsApp {TEL_LOCAL}.", lang)}
<h2>{fa("link")} Related pool pages</h2>
{_spec_related(lang, "waterproof", "maint", "hub", "clean")}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("droplet")} إصلاح تسريب المسبح في الإمارات</h2>
<p>هذه الصفحة لخروج الماء من حوض السباحة نفسه: فواصل البلاط أو اللاينر، عنق السكimmer، قطع الإرجاع، الإنارة، وجسم الحوض. ليست صفحة كشف تسربات المباني وليست بحثًا عن أنابيب مخفية داخل الجدران أو تحت السطح. إن كان مسار البلل حول هذا المسبح فابدأ هنا. إن كان البيت أو السطح مبتلاً بعيدًا عن الحوض فاستخدم تلك الخدمات المنفصلة.</p>
</section>
{img("maint1", "فحص حوض سباحة لتحديد مسار البلل", True, lang)}
<h2>{fa("magnifying-glass")} ماذا نراجع</h2>
{cards([
    ("border-all", "فواصل البلاط والكور", "ترويبة متشققة أو حافة مرفوعة قد تفقد ماء دون شق واضح."),
    ("sink", "السكimmer والإرجاع", "الجوانات والقطع الملولبة كثيرًا ما ترشح والحوض يبدو سليمًا."),
    ("lightbulb", "الإنارة تحت الماء", "فشل في الختم يظهر كانخفاض يومي بطيء."),
    ("layer-group", "الجسم أو اللاينر", "تجعد لاينر أو شعر في الخرسانة يحتاج نظرة أقرب بعد المعاينة."),
])}
{callout("warn", "ليست هذه الصفحة", "كشف تسربات المباني وعزل الأسطح أعمال مختلفة، بصفحات ورقم مختلف. لا تعامل هذا المقال كبحث أنابيب المنزل.")}
<h2>{fa("list-check")} مسار زيارة تسريب المسبح</h2>
{steps([
    ("تأكيد أن المصدر الحوض", "الانخفاض اليومي مقابل التبخر، وهل علامات البلل حول هذا المسبح فقط."),
    ("فحص القطع", "سكimmer، إرجاع، إنارة، وفواصل ظاهرة."),
    ("تضييق المسار", "فحص ضغط أو صبغة فقط إن أفاد هذا الحوض، وليس كمسح للمبنى."),
    ("كتابة الإصلاح", "جوان أو ترويبة أو قطعة أو نقاش أوسع للجسم — بعد ما نراه."),
    ("إصلاح البند المتفق عليه", "ثم إعادة الملء أو التعلية بما يكفي لتجربة الدورة."),
])}
{img("equip", "غرفة مضخة المسبح عند فقد ماء من الحوض", False, lang)}
<h2>{fa("table")} انخفاض الماء مقابل أعطال أخرى</h2>
{table(
    ["ملاحظة", "الأقرب", "الصفحة التالية"],
    [
        ["بلاط مبلل حول هذا المسبح", "قطعة أو فاصل في الحوض", "ابق في هذه الصفحة"],
        ["ماء أخضر والمستوى ثابت", "تنظيف / دوران", "صفحة تنظيف المدينة"],
        ["غرفة المضخة مبتلة والحوض ممتلئ", "ختم المضخة أو الوصلة", "صفحة صيانة المدينة"],
        ["سقف أو سطح مبتل والمسبح غير مستخدم", "خدمة المبنى أو السطح", "تلك الصفحات المنفصلة"],
    ],
)}
{callout("tip", "نصيحة", "صورتان لعلامة خط الماء على صباحين تساعدان أكثر من تخمين باللترات.")}
<h2>{fa("circle-question")} أسئلة تسريب المسبح</h2>
{faq([
    ("هل تجدون تسريبًا داخل البيت من هذه الصفحة؟",
     "لا. هذه الصفحة لحوض السباحة وقطعه. أنابيب المنزل أو السطح خدمة مختلفة."),
    ("هل تسعّرون استبدال لاينر في رسالة؟",
     "ليس بصدق. عمل اللاينر أو الجسم يعتمد على الوصول وما تظهره المعاينة."),
    ("هل تفرّغون المسبح في كل زيارة؟",
     "فقط إن احتاج الإصلاح ذلك. كثير من رشح القطع يُعالج بخفض جزئي."),
])}
{cta("المسبح يفقد ماء؟", f"أرسل الإمارة وصورتين: خط الماء ومكان البلل. واتساب {TEL_LOCAL}.", lang)}
<h2>{fa("link")} صفحات مسابح مرتبطة</h2>
{_spec_related(lang, "waterproof", "maint", "hub", "clean")}
"""
    return wrap(lang, inner)


def build_waterproof(lang: str = "ar") -> str:
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("shield-halved")} Swimming pool waterproofing in the UAE</h2>
<p>Pool waterproofing is the layer that keeps water inside the basin: membranes, coatings or detailing at steps, lights and fittings before or after tiles. It is not roof insulation and not a terrace waterproofing article. Heat, groundwater and a poor detail at the skimmer are typical reasons a shell weeps after filling.</p>
</section>
{img("build", "Pool shell waterproofing before finishing", True, lang)}
<h2>{fa("layer-group")} Where waterproofing is decided</h2>
{cards([
    ("house", "New concrete shell", "The system is chosen with the pour and pipe schedule, not after tiles are stuck."),
    ("border-all", "Existing tiled basin", "A visit decides if grout, a coating or a larger strip-out is honest."),
    ("hot-tub", "Jacuzzi pocket", "A small hot basin has more joints per litre; details matter more."),
    ("droplet", "Fittings through the shell", "Lights and returns are where many shells start to weep."),
])}
{callout("info", "Important", "We do not name a membrane brand as mandatory, and we do not quote square-metre rates before seeing the shell.")}
<h2>{fa("list-check")} Typical sequence</h2>
{steps([
    ("See the shell", "New build, empty basin, or a pool still in use."),
    ("Map wet points", "Joints, lights, steps and any previous patch."),
    ("Choose a method", "Coating, membrane or local repair — written after the visit."),
    ("Prepare the surface", "Clean, dry enough, and fittings isolated as needed."),
    ("Apply and protect", "Then a fill test before heavy tiling or handover."),
])}
{img("scale", "Checking the basin after a waterproofing discussion", False, lang)}
<h2>{fa("table")} Pool waterproofing versus roof work</h2>
{table(
    ["Job", "This page", "Other pages"],
    [
        ["Keep water in a swimming pool", "Yes", "No"],
        ["Insulate or waterproof a roof slab", "No", "Roof insulation pages"],
        ["Find hidden pipes in a villa", "No", "Building leak-detection pages"],
        ["Repair a skimmer throat leak", "Often with leak repair", "City maintenance if only a gasket"],
    ],
)}
{callout("warn", "Caution", "A cheap coating on wet or dirty concrete fails after the first fill. Surface preparation is part of the scope, not an extra slogan.")}
<h2>{fa("circle-question")} Pool waterproofing questions</h2>
{faq([
    ("Is this the same as roof insulation?",
     "No. Roof pages are building fabric. This page is the swimming pool shell only."),
    ("Can you waterproof without emptying?",
     "Sometimes for a local fitting. Full-shell work usually needs the basin empty and dry enough."),
    ("Does waterproofing include new tiles?",
     "Only if the written scope says so. Tiles are a finish, not the waterproof layer."),
])}
{cta("Need pool waterproofing reviewed?", f"Send shell photos (empty if possible) and the emirate on WhatsApp {TEL_LOCAL}.", lang)}
<h2>{fa("link")} Related pool pages</h2>
{_spec_related(lang, "leak", "build", "hub", "maint")}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("shield-halved")} عزل أحواض السباحة في الإمارات</h2>
<p>عزل المسبح هو الطبقة التي تُبقي الماء داخل الحوض: أغشية أو دهانات أو تفصيل عند الدرج والإنارة والقطع، قبل البلاط أو بعده. ليس عزل أسطح وليس مقال عزل تراس. الحرارة والمياه الجوفية وتفصيل ضعيف عند السكimmer أسباب شائعة لرشح الجسم بعد الملء.</p>
</section>
{img("build", "عزل جسم المسبح قبل التشطيب", True, lang)}
<h2>{fa("layer-group")} أين يُحسم العزل</h2>
{cards([
    ("house", "جسم خرساني جديد", "النظام يُختار مع الصبة ومسار الأنابيب، لا بعد لصق البلاط."),
    ("border-all", "حوض بلاط قائم", "المعاينة تقرر إن كانت الترويبة أو دهان أو نزع أوسع هو الخيار الصادق."),
    ("hot-tub", "جيب الجاكوزي", "حوض ساخن صغير فيه فواصل أكثر لكل لتر؛ التفصيل أهم."),
    ("droplet", "قطع تخترق الجسم", "الإنارة والإرجاع حيث يبدأ رشح كثير من الأحواض."),
])}
{callout("info", "معلومة مهمة", "لا نلزم بعلامة غشاء، ولا نسعّر بالمتر قبل رؤية الجسم.")}
<h2>{fa("list-check")} تسلسل معتاد</h2>
{steps([
    ("رؤية الجسم", "إنشاء جديد، حوض فارغ، أو مسبح ما زال قيد الاستخدام."),
    ("تحديد نقاط البلل", "فواصل، إنارة، درج، وأي رقعة سابقة."),
    ("اختيار الأسلوب", "دهان أو غشاء أو إصلاح موضع — مكتوب بعد المعاينة."),
    ("تجهيز السطح", "تنظيف وجفاف كافٍ وعزل القطع عند الحاجة."),
    ("التطبيق والحماية", "ثم اختبار ملء قبل بلاط كثيف أو التسليم."),
])}
{img("scale", "مراجعة الحوض بعد نقاش العزل", False, lang)}
<h2>{fa("table")} عزل المسبح مقابل عزل السطح</h2>
{table(
    ["العمل", "هذه الصفحة", "صفحات أخرى"],
    [
        ["إبقاء الماء داخل مسبح", "نعم", "لا"],
        ["عزل بلاطة سطح", "لا", "صفحات عزل الأسطح"],
        ["البحث عن أنابيب مخفية في فيلا", "لا", "صفحات كشف تسربات المباني"],
        ["إصلاح رشح عنق السكimmer", "غالبًا مع إصلاح تسريب المسبح", "صيانة المدينة إن كان الجوان فقط"],
    ],
)}
{callout("warn", "تنبيه", "دهان رخيص على خرسانة مبتلة أو متسخة يفشل بعد أول ملء. تجهيز السطح جزء من النطاق لا شعارًا إضافيًا.")}
<h2>{fa("circle-question")} أسئلة عزل المسابح</h2>
{faq([
    ("هل هذا نفس عزل الأسطح؟",
     "لا. صفحات الأسطح لعنصر المبنى. هذه الصفحة لجسم حوض السباحة فقط."),
    ("هل تعزلون دون تفريغ؟",
     "أحيانًا لقطعة موضع. عمل الجسم كاملًا يحتاج غالبًا حوضًا فارغًا وجافًا بما يكفي."),
    ("هل العزل يشمل بلاطًا جديدًا؟",
     "فقط إن كتبه النطاق. البلاط تشطيب وليس طبقة العزل."),
])}
{cta("تحتاج مراجعة عزل المسبح؟", f"أرسل صور الجسم (فارغًا إن أمكن) والإمارة على واتساب {TEL_LOCAL}.", lang)}
<h2>{fa("link")} صفحات مسابح مرتبطة</h2>
{_spec_related(lang, "leak", "build", "hub", "maint")}
"""
    return wrap(lang, inner)


def build_chlorine(lang: str = "ar") -> str:
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("flask")} Pool chlorine and salt systems in the UAE</h2>
<p>This page is about sanitiser for swimming pools: chlorine dosing, salt-chlorine generators, and why a reading will not hold when circulation is weak. It is not a general building disinfection or fogging page, and it does not replace those chlorine-disinfection articles. The goal is usable pool water after a visit, not a branded chemical package sold sight unseen.</p>
</section>
{img("scale", "Reading swimming pool water before dosing", True, lang)}
<h2>{fa("vial")} What we actually adjust</h2>
{cards([
    ("droplet", "Free chlorine and pH", "Read on a meter at the basin. We do not promise one ratio for every pool."),
    ("cubes", "Salt cell if fitted", "Scale on plates and a tired cell look like “no chlorine” while the pump is fine."),
    ("filter", "Runtime first", "Dosing into standing water wastes product and still leaves algae."),
    ("sun", "Heat and bather load", "Midday UAE sun and a weekend party change how fast a residual drops."),
])}
{callout("warn", "Not this page", "House or tank chlorine disinfection is a different service. This article stays with swimming pool water and pool salt systems.")}
<h2>{fa("list-check")} Visit sequence</h2>
{steps([
    ("Read the water", "Colour, smell, chlorine, pH and a look at the filter pressure."),
    ("Check the feeder or cell", "Empty floater, closed valve, or a salt cell coated in scale."),
    ("Restore circulation", "Baskets and filter often matter more than another jug of product."),
    ("Dose to the reading", "Amount follows volume and the meter, not a social-media recipe."),
    ("Explain the next days", "How long to run the pump and when a re-read is useful."),
])}
{img("clean4", "Pool surface after water is brought back into range", False, lang)}
<h2>{fa("table")} Chlorine versus salt</h2>
{table(
    ["System", "What it needs", "Common confusion"],
    [
        ["Manual or feeder chlorine", "Runtime and a correct pH", "More product will not fix a blocked filter"],
        ["Salt generator", "Clean cell, enough salt, good flow", "Low chlorine is often a dirty cell, not “bad salt”"],
        ["Stabiliser in outdoor pools", "A reading, not endless topping", "Too much stabilizer can lock chlorine"],
    ],
)}
{callout("tip", "Tip", "Send a photo of the water and of the salt-cell box if you have one. That is enough to say whether the visit is chemistry or a cell clean.")}
<h2>{fa("circle-question")} Pool chlorine questions</h2>
{faq([
    ("Do you sell a monthly chemical subscription from this page?",
     "No published package. After a visit we can say what the basin is using; we do not invent a UAE-wide kit."),
    ("Is salt safer than chlorine?",
     "A salt system still produces chlorine. The difference is how it is made, not a chlorine-free pool."),
    ("Can you shock a green pool from WhatsApp instructions only?",
     "We can suggest first checks. A locked green basin usually needs on-site brushing and filtration."),
])}
{cta("Pool chlorine will not hold?", f"WhatsApp {TEL_LOCAL} with a water photo and whether you use salt or manual chlorine.", lang)}
<h2>{fa("link")} Related pool pages</h2>
{_spec_related(lang, "clean", "maint", "heat", "hub")}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("flask")} كلور وملح المسابح في الإمارات</h2>
<p>هذه الصفحة عن تعقيم مياه حوض السباحة: جرعة الكلور، مولد الملح، ولماذا لا تثبت القراءة إن ضعف الدوران. ليست صفحة تعقيم مبانٍ أو ضبابًا عامًا، ولا تستبدل مقالات التعقيم بالكلور للمنازل. الهدف ماء مسبح قابل للاستخدام بعد الزيارة، لا باقة علامة تُباع دون رؤية الحوض.</p>
</section>
{img("scale", "قراءة مياه المسبح قبل الجرعة", True, lang)}
<h2>{fa("vial")} ماذا نضبط فعلًا</h2>
{cards([
    ("droplet", "الكلور الحر وpH", "قراءة على الجهاز عند الحوض. لا نعد بنسبة واحدة لكل مسبح."),
    ("cubes", "خلية الملح إن وُجدت", "ترسب على الألواح وخلية متعبة يبدوان كـ«لا كلور» والمضخة سليمة."),
    ("filter", "ساعات التشغيل أولًا", "الجرعة في ماء واقف تهدر المادة وتُبقي الطحالب."),
    ("sun", "الحر والحمل", "شمس الظهيرة وحفلة نهاية الأسبوع تسرّعان اختفاء المتبقي."),
])}
{callout("warn", "ليست هذه الصفحة", "تعقيم المنازل أو الخزانات بالكلور خدمة مختلفة. هذا المقال يبقى مع مياه المسبح وأنظمة الملح.")}
<h2>{fa("list-check")} تسلسل الزيارة</h2>
{steps([
    ("قراءة الماء", "اللون والرائحة والكلور وpH ونظرة إلى ضغط الفلتر."),
    ("فحص المغذي أو الخلية", "عوامة فارغة، صمام مغلق، أو خلية ملح مغطاة بالترسب."),
    ("إعادة الدوران", "السلال والفلتر أهم غالبًا من عبوة إضافية."),
    ("الجرعة حسب القراءة", "الكمية تتبع الحجم والجهاز لا وصفة من وسائل التواصل."),
    ("شرح الأيام التالية", "كم تشغّل المضخة ومتى تفيد قراءة ثانية."),
])}
{img("clean4", "سطح المسبح بعد إعادة الماء إلى مدى معقول", False, lang)}
<h2>{fa("table")} الكلور مقابل الملح</h2>
{table(
    ["النظام", "ماذا يحتاج", "خلط شائع"],
    [
        ["كلور يدوي أو مغذٍ", "تشغيل وpH صحيح", "المزيد من المادة لا يصلح فلترًا مسدودًا"],
        ["مولد ملح", "خلية نظيفة وملح كافٍ وتدفق جيد", "ضعف الكلور غالبًا خلية متسخة لا «ملح سيئ»"],
        ["مثبت في المسابح المكشوفة", "قراءة لا تعبئة بلا نهاية", "زيادة المثبت قد تقفل الكلور"],
    ],
)}
{callout("tip", "نصيحة", "أرسل صورة الماء وصورة صندوق خلية الملح إن وُجدت. هذا يكفي لنقول إن الزيارة كيمياء أم تنظيف خلية.")}
<h2>{fa("circle-question")} أسئلة كلور المسابح</h2>
{faq([
    ("هل تبيعون اشتراك كيماويات شهريًا من هذه الصفحة؟",
     "لا باقة معلنة. بعد المعاينة يمكن قول ما يستهلكه الحوض؛ لا نخترع طقمًا لكل الإمارات."),
    ("هل الملح أأمن من الكلور؟",
     "نظام الملح ما زال ينتج كلورًا. الفرق في طريقة الإنتاج لا في مسبح بلا كلور."),
    ("هل تصعقون مسبحًا أخضر بتعليمات واتساب فقط؟",
     "يمكن اقتراح فحوصات أولى. الحوض الأخضر المتماسك يحتاج عادة كنسًا وفلترة في الموقع."),
])}
{cta("كلور المسبح لا يثبت؟", f"واتساب {TEL_LOCAL} مع صورة الماء وهل تستخدم ملحًا أم كلورًا يدويًا.", lang)}
<h2>{fa("link")} صفحات مسابح مرتبطة</h2>
{_spec_related(lang, "clean", "maint", "heat", "hub")}
"""
    return wrap(lang, inner)


def build_heat(lang: str = "ar") -> str:
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("temperature-half")} Swimming pool heating in the UAE</h2>
<p>Pool heating is the exchanger, heat pump or electric heater that lifts basin temperature, plus the valves and flow that keep that unit alive. Winter evenings and shaded villas are the usual reason to ask. We do not invent a target temperature or a power bill before seeing pipe size, shelter and the existing pump.</p>
</section>
{img("equip", "Pool plant room reviewed for a heater or heat pump", True, lang)}
<h2>{fa("fire")} What a heating visit covers</h2>
{cards([
    ("temperature-half", "Existing heater", "No ignition, short cycling, or scale on an exchanger."),
    ("wind", "Heat pump airflow", "A unit boxed in with no exhaust air will fault even if the gas charge is fine."),
    ("droplet", "Flow and bypass", "A heater with starved flow overheats; valves matter as much as the unit."),
    ("plug", "Power supply", "A tripped breaker or undersized cable is a site issue, not a WhatsApp part number."),
])}
{callout("info", "Important", "We do not publish kW packages or promise a winter temperature for every emirate. The visit writes what can be repaired or added.")}
<h2>{fa("list-check")} Sequence</h2>
{steps([
    ("Ask how it is used", "Evenings only, winter months, or a jacuzzi pocket."),
    ("See the plant", "Heater, pump, valves and whether the unit can reject heat."),
    ("Test what is already there", "Repair first if the unit is serviceable."),
    ("Discuss an add-on only if needed", "Type follows space and power, not a catalogue default."),
    ("Commission", "Flow, bypass and a short run before handover."),
])}
{img("maint1", "Checking valves around pool heating equipment", False, lang)}
<h2>{fa("table")} Heat pump versus electric heater</h2>
{table(
    ["Option", "Needs", "Typical limit"],
    [
        ["Heat pump", "Airflow and a place to stand", "Works poorly if fully boxed in"],
        ["Electric heater", "Correct power and flow", "Running cost depends on hours — not quoted here"],
        ["Exchanger on existing plant", "Compatible hydraulics", "Scale and a closed bypass are common faults"],
    ],
)}
{callout("tip", "Tip", "A photo of the plant room and the nameplate on any existing heater is more useful than a target temperature in a message.")}
<h2>{fa("circle-question")} Pool heating questions</h2>
{faq([
    ("Can you guarantee 30 degrees all winter?",
     "No. Outdoor air, wind, cover use and run hours change the result. We do not invent a figure."),
    ("Do you install solar pool heating?",
     "Only if the roof or yard can take the array and the visit says so. It is not a default package."),
    ("Is heating the same as a jacuzzi heater?",
     "A spa pocket is hotter and smaller. That work is covered on the jacuzzi page if the basin is separate."),
])}
{cta("Need the pool warmer?", f"Send plant-room photos and the emirate on WhatsApp {TEL_LOCAL}.", lang)}
<h2>{fa("link")} Related pool pages</h2>
{_spec_related(lang, "jacuzzi", "maint", "chlorine", "hub")}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("temperature-half")} تدفئة المسابح في الإمارات</h2>
<p>تدفئة المسبح هي المبادل أو المضخة الحرارية أو السخان الكهربائي الذي يرفع حرارة الحوض، مع الصمامات والتدفق التي تُبقي الجهاز حيًا. أمسيات الشتاء والفلل المظللة السبب المعتاد للطلب. لا نخترع درجة مستهدفة ولا فاتورة كهرباء قبل رؤية قطر الأنابيب والمأوى والمضخة القائمة.</p>
</section>
{img("equip", "غرفة معدات المسبح عند مراجعة سخان أو مضخة حرارية", True, lang)}
<h2>{fa("fire")} ماذا تغطي زيارة التدفئة</h2>
{cards([
    ("temperature-half", "سخان قائم", "لا اشتعال، أو تشغيل قصير متكرر، أو ترسب على المبادل."),
    ("wind", "هواء المضخة الحرارية", "جهاز محبوس بلا طرد هواء يتعطل حتى لو الشحنة سليمة."),
    ("droplet", "التدفق والبايص", "سخان بجوع تدفق يسخن زيادة؛ الصمامات بأهمية الجهاز."),
    ("plug", "التغذية الكهربائية", "قاطع يفصل أو كابل أضعف من اللازم مسألة موقع لا رقم قطعة على واتساب."),
])}
{callout("info", "معلومة مهمة", "لا ننشر باقات بالكيلوواط ولا نعد بدرجة شتاء لكل إمارة. المعاينة تكتب ما يُصلح أو يُضاف.")}
<h2>{fa("list-check")} التسلسل</h2>
{steps([
    ("سؤال عن الاستخدام", "أمسيات فقط، أشهر الشتاء، أو جيب جاكوزي."),
    ("رؤية الغرفة", "السخان والمضخة والصمامات وهل يطرد الجهاز حرارته."),
    ("تجربة القائم", "الإصلاح أولًا إن كان الجهاز قابلًا للخدمة."),
    ("إضافة فقط إن لزم", "النوع يتبع المساحة والكهرباء لا افتراض كتالوج."),
    ("التشغيل", "تدفق وبايص وتشغيل قصير قبل التسليم."),
])}
{img("maint1", "مراجعة صمامات حول معدات تدفئة المسبح", False, lang)}
<h2>{fa("table")} مضخة حرارية مقابل سخان كهربائي</h2>
{table(
    ["الخيار", "يحتاج", "حد معتاد"],
    [
        ["مضخة حرارية", "هواء ومكان وقوف", "تضعف إن حُبست بالكامل"],
        ["سخان كهربائي", "قدرة وتدفق صحيحان", "تكلفة التشغيل تتبع الساعات — لا تُذكر هنا"],
        ["مبادل على شبكة قائمة", "هيدروليك متوافق", "الترسب وبايص مغلق أعطال شائعة"],
    ],
)}
{callout("tip", "نصيحة", "صورة غرفة المعدات ولوحة بيانات أي سخان قائم أنفع من درجة مستهدفة في رسالة.")}
<h2>{fa("circle-question")} أسئلة تدفئة المسابح</h2>
{faq([
    ("هل تضمنون 30 درجة طول الشتاء؟",
     "لا. الهواء والريح والغطاء وساعات التشغيل تغيّر النتيجة. لا نخترع رقمًا."),
    ("هل تركّبون تدفئة شمسية للمسبح؟",
     "فقط إن احتمل السطح أو الفناء المصفوفة وقالت المعاينة ذلك. ليست باقة افتراضية."),
    ("هل التدفئة نفس سخان الجاكوزي؟",
     "جيب السبا أسخن وأصغر. ذلك العمل في صفحة الجاكوزي إن كان الحوض منفصلًا."),
])}
{cta("تريد المسبح أدفأ؟", f"أرسل صور غرفة المعدات والإمارة على واتساب {TEL_LOCAL}.", lang)}
<h2>{fa("link")} صفحات مسابح مرتبطة</h2>
{_spec_related(lang, "jacuzzi", "maint", "chlorine", "hub")}
"""
    return wrap(lang, inner)


def build_jacuzzi(lang: str = "ar") -> str:
    if lang == "en":
        inner = f"""
<section class="rp-hero">
<h2>{fa("hot-tub")} Jacuzzi installation and service in the UAE</h2>
<p>A jacuzzi or spa pocket is a small hot basin with jets, a dedicated pump and usually its own heater. It can sit next to a swimming pool or stand alone on a villa deck. This page covers install, jet and heater faults, and cleaning of that small volume — not a full pool build unless the visit links the two.</p>
</section>
{img("build", "Spa or jacuzzi pocket next to a swimming pool", True, lang)}
<h2>{fa("hot-tub")} What usually fails</h2>
{cards([
    ("wind", "Jets weak or dead", "Air in the pump, a clogged suction, or a tired jet pump."),
    ("temperature-half", "Will not heat", "Heater, flow switch or a closed valve — checked on site."),
    ("droplet", "Slow leak at unions", "Hot water and frequent heat-up stress gaskets faster than a cold pool."),
    ("broom", "Cloudy small volume", "Oils and biofilm build faster; a pool-only clean often misses the spa."),
])}
{callout("info", "Important", "We do not advertise a model range or a seat-count package. The visit decides if repair, a swap or a new pocket is honest.")}
<h2>{fa("list-check")} Visit path</h2>
{steps([
    ("Say how it is used", "Daily soak, weekend only, or empty most of the year."),
    ("Inspect jets and plant", "Pumps, heater, unions and whether the spa shares the pool filter."),
    ("Separate the faults", "Jets, heat and water quality are three scopes if needed."),
    ("Repair or outline an install", "Written after access and power are clear."),
    ("Test heat and jets", "Short run before handover."),
])}
{img("equip", "Dedicated spa pump and heater in the plant room", False, lang)}
<h2>{fa("table")} Jacuzzi versus the main pool</h2>
{table(
    ["Topic", "Jacuzzi / spa", "Main swimming pool"],
    [
        ["Water volume", "Small, dirties faster", "Larger, slower chemistry swing"],
        ["Heat", "Higher target, shorter heat-up", "Optional — see heating page"],
        ["Pumps", "Often a second jet pump", "Circulation and filter pump"],
        ["Leak signs", "Unions and jet bodies", "Skimmer, lights, shell joints"],
    ],
)}
{callout("tip", "Tip", "If the spa shares valves with the pool, say so in the message. A closed spa valve is a common “no heat, no jets” cause.")}
<h2>{fa("circle-question")} Jacuzzi questions</h2>
{faq([
    ("Do you install a jacuzzi without a swimming pool?",
     "Yes if the deck can take the weight, power and drainage. That is confirmed on the visit."),
    ("Is a jacuzzi the same as pool heating?",
     "No. Heating the main basin is the heating page. This page is the hot spa pocket and its jets."),
    ("Can you quote a 6-seat spa in a chat?",
     "Not as a fixed kit. Access, power and whether it is in-ground or a shell change the scope."),
])}
{cta("Need jacuzzi service?", f"Send photos of the spa and the plant on WhatsApp {TEL_LOCAL}.", lang)}
<h2>{fa("link")} Related pool pages</h2>
{_spec_related(lang, "heat", "build", "leak", "hub")}
"""
        return wrap(lang, inner)

    inner = f"""
<section class="rp-hero">
<h2>{fa("hot-tub")} جاكوزي وتركيب الأحواض الساخنة في الإمارات</h2>
<p>الجاكوزي أو جيب السبا حوض ساخن صغير بنفاثات ومضخة مخصصة وغالبًا سخانه. قد يجاور مسبحًا أو يقف وحده على سطح فيلا. هذه الصفحة للتركيب وأعطال النفاثات والسخان وتنظيف هذا الحجم الصغير — وليست إنشاء مسبح كامل إلا إن ربطت المعاينة العملين.</p>
</section>
{img("build", "جيب سبا أو جاكوزي بجانب مسبح", True, lang)}
<h2>{fa("hot-tub")} ما الذي يتعطل عادة</h2>
{cards([
    ("wind", "نفاثات ضعيفة أو ميتة", "هواء في المضخة، سحب مسدود، أو مضخة نفاثات متعبة."),
    ("temperature-half", "لا يسخن", "سخان أو مفتاح تدفق أو صمام مغلق — يُفحص في الموقع."),
    ("droplet", "رشح بطيء عند الوصلات", "الماء الساخن والإقلاع المتكرر يجهدان الجوانات أسرع من مسبح بارد."),
    ("broom", "عكرة في حجم صغير", "الدهون والغشاء يتراكمان أسرع؛ تنظيف المسبح وحده كثيرًا ما يُهمل السبا."),
])}
{callout("info", "معلومة مهمة", "لا نعلن تشكيلة موديلات ولا باقة بعدد مقاعد. المعاينة تقرر إن كان الإصلاح أو الاستبدال أو جيب جديد هو الصادق.")}
<h2>{fa("list-check")} مسار الزيارة</h2>
{steps([
    ("وصف الاستخدام", "نقع يومي، نهاية أسبوع فقط، أو فارغ معظم السنة."),
    ("فحص النفاثات والغرفة", "مضخات، سخان، وصلات، وهل يشارك السبا فلتر المسبح."),
    ("فصل الأعطال", "النفاثات والحرارة وجودة الماء ثلاثة نطاقات إن لزم."),
    ("إصلاح أو تصور تركيب", "مكتوب بعد وضوح الوصول والكهرباء."),
    ("تجربة الحرارة والنفاثات", "تشغيل قصير قبل التسليم."),
])}
{img("equip", "مضخة سبا وسخان مخصصان في غرفة المعدات", False, lang)}
<h2>{fa("table")} الجاكوزي مقابل المسبح الرئيسي</h2>
{table(
    ["البند", "جاكوزي / سبا", "المسبح الرئيسي"],
    [
        ["حجم الماء", "صغير ويتسخ أسرع", "أكبر وتغيّر الكيمياء أبطأ"],
        ["الحرارة", "هدف أعلى وتسخين أقصر", "اختياري — صفحة التدفئة"],
        ["المضخات", "غالبًا مضخة نفاثات ثانية", "مضخة دوران وفلتر"],
        ["علامات التسريب", "وصلات وأجسام النفاثات", "سكimmer وإنارة وفواصل الجسم"],
    ],
)}
{callout("tip", "نصيحة", "إن شارك السبا صمامات المسبح فاذكر ذلك في الرسالة. صمام سبا مغلق سبب شائع لـ«لا حرارة ولا نفاثات».")}
<h2>{fa("circle-question")} أسئلة الجاكوزي</h2>
{faq([
    ("هل تركّبون جاكوزي بلا مسبح؟",
     "نعم إن احتمل السطح الوزن والكهرباء والتصريف. ذلك يُؤكد في المعاينة."),
    ("هل الجاكوزي نفس تدفئة المسبح؟",
     "لا. تدفئة الحوض الرئيسي صفحة التدفئة. هذه الصفحة لجيب السبا الساخن ونفاثاته."),
    ("هل تسعّرون سبا 6 مقاعد في الدردشة؟",
     "ليس كطقم ثابت. الوصول والكهرباء وإن كان مدفونًا أو جسمًا جاهزًا يغيّران النطاق."),
])}
{cta("تحتاج خدمة جاكوزي؟", f"أرسل صور السبا والغرفة على واتساب {TEL_LOCAL}.", lang)}
<h2>{fa("link")} صفحات مسابح مرتبطة</h2>
{_spec_related(lang, "heat", "build", "leak", "hub")}
"""
    return wrap(lang, inner)


CITY_KINDS = {"clean": build_clean, "maint": build_maint, "build": build_construct}
SPEC_KINDS = {
    "hub": build_hub,
    "leak": build_leak,
    "waterproof": build_waterproof,
    "chlorine": build_chlorine,
    "heat": build_heat,
    "jacuzzi": build_jacuzzi,
}


def render(kind: str, city: str = "", lang: str = "ar") -> str:
    lang = "en" if lang == "en" else "ar"
    if kind in SPEC_KINDS:
        return css(lang) + SPEC_KINDS[kind](lang)
    return css(lang) + CITY_KINDS[kind](city, lang)
