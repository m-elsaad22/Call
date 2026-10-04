#!/usr/bin/env python3
"""Fill KAYAN shortcode blocks + invoke them in thin/template service posts.

Live writes go through WP REST + WPVibe CLI. Do not invent phone numbers.
Skip leak/insulation body rewrites. Remove fake Egypt rental SEO titles.
"""
from __future__ import annotations

import json
import re
import sys
import time
import urllib.error
import urllib.request
from pathlib import Path

SITE = "https://www.rukn-eltatawer.com"
USER = "cursor"
APP_PASS = "QQEGjJcChwu4SvYYYofJcOJT"
TOKEN = __import__("base64").b64encode(f"{USER}:{APP_PASS}".encode()).decode()
AUTH = {
    "Authorization": f"Basic {TOKEN}",
    "User-Agent": "Mozilla/5.0 CursorAgent",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

# Known numbers only — never invent.
WA_DEFAULT = "+971586634710"
PHONES = {
    "leak": ("+971524314370", "+971524314370", True),
    "insulation": ("+971524314370", "+971524314370", True),
    "landscaping": ("+971566556017", "+971566556017", True),
    "ac": ("+971556190406", "+971556190406", True),
    "electrical": ("+971556190406", "+971556190406", True),
    "cleaning_ad": ("+971522901095", "+971522901095", True),
    "cleaning": ("+971541673020", "+971541673020", True),
    "cleaning_alain": ("+971565619644", "+971565619644", True),
    "pest_ad": ("+971524221011", "+971524221011", True),
    "pest_alain": ("+971565619644", "+971565619644", True),
    "pest": (None, WA_DEFAULT, False),
    "pool": ("+971521300019", "+971521300019", True),
    "drain": (None, WA_DEFAULT, False),
    "moving": (None, WA_DEFAULT, False),
    "default": (None, WA_DEFAULT, False),
}

CITIES = {
    "dubai": "دبي",
    "abu-dhabi": "أبوظبي",
    "abudhabi": "أبوظبي",
    "sharjah": "الشارقة",
    "ajman": "عجمان",
    "al-ain": "العين",
    "alain": "العين",
    "ras-al-khaimah": "رأس الخيمة",
    "rak": "رأس الخيمة",
    "fujairah": "الفجيرة",
    "umm-al-quwain": "أم القيوين",
    "uaq": "أم القيوين",
}

DISTRICTS = {
    "دبي": ["البرشاء", "جميرا", "مردف", "الورقاء", "القوز", "القرهود"],
    "أبوظبي": ["الخالدية", "الخالدية الجنوبية", "مدينة محمد بن زايد", "الشامخة", "الريف", "المصفح"],
    "الشارقة": ["النهدة", "الخان", "المجاز", "الفلج", "مويحات", "الرامة"],
    "عجمان": ["الراشدية", "النعيمية", "الجرف", "المويهات", "الحميدية"],
    "العين": ["الهيلي", "الجاهلي", "المقام", "اليحر", "المويجعي"],
    "رأس الخيمة": ["النخيل", "الرمس", "خزام", "دقداقة", "المعيريض"],
    "الفجيرة": ["الفصيل", "مربح", "القدفع", "البدية", "ضدنا"],
    "أم القيوين": ["الراشدية", "فلج المعلا", "السلمة", "السننية"],
}

SKIP_BODY = re.compile(
    r"(leak|insulation|waterproofing|roof-insulation|water-leak|ac-leak-detection)",
    re.I,
)

# slug keyword -> service family
FAMILIES = [
    ("piano-moving", "moving_piano"),
    ("outdoor-moving", "moving_outdoor"),
    ("furniture-moving", "moving_furniture"),
    ("furniture-storage", "moving_storage"),
    ("furniture-assembly", "moving_assembly"),
    ("flea-control", "pest_flea"),
    ("snake-control", "pest_snake"),
    ("mosquito-fly", "pest_mosquito"),
    ("flying-pest", "pest_flying"),
    ("rodent-control", "pest_rodent"),
    ("termite-control", "pest_termite"),
    ("scorpion-control", "pest_scorpion"),
    ("bird-control", "pest_bird"),
    ("bird-sound-device", "pest_bird_sound"),
    ("pigeon-spikes", "pest_pigeon"),
    ("septic-tank", "drain_septic"),
    ("duct-cleaning", "clean_duct"),
    ("chimney-cleaning", "clean_chimney"),
    ("palace-cleaning", "clean_palace"),
    ("hospital-clinic-cleaning", "clean_hospital"),
    ("office-cleaning", "clean_office"),
    ("garage-cleaning", "clean_garage"),
    ("garden-cleaning", "clean_garden"),
    ("bathroom-cleaning", "clean_bath"),
    ("deep-cleaning", "clean_deep"),
    ("school-nursery-cleaning", "clean_school"),
    ("council-cleaning", "clean_council"),
    ("moquette-cleaning", "clean_moquette"),
    ("carpet-cleaning", "clean_carpet"),
    ("glass-facade-cleaning", "clean_facade"),
    ("kitchen-cleaning", "clean_kitchen"),
    ("chlorine-disinfection", "clean_chlorine"),
    ("ozone-disinfection", "clean_ozone"),
    ("steam-disinfection", "clean_steam"),
    ("kitchen-renovation", "reno_kitchen"),
    ("bathroom-renovation", "reno_bath"),
    ("old-house-renovation", "reno_house"),
    ("building-renovation", "reno_building"),
    ("crack-repair", "reno_crack"),
    ("dryer-repair", "repair_dryer"),
    ("oven-repair", "repair_oven"),
    ("washing-machine-repair", "repair_washer"),
    ("water-cooler-repair", "repair_cooler"),
    ("water-heater-repair", "repair_heater"),
    ("microwave-repair", "repair_microwave"),
    ("split-ac-maintenance", "ac_split"),
    ("electrical-maintenance", "electrical"),
    ("solar-ac", "ac_solar"),
    ("solar-systems", "solar"),
    ("air-purification", "ac_purify"),
    ("plumbing-maintenance", "plumbing"),
    ("diesel-tank-cleaning", "tank_diesel"),
    ("door-installation", "install_door"),
    ("window-installation", "install_window"),
    ("parquet-flooring", "install_parquet"),
    ("ceramic-porcelain", "install_ceramic"),
    ("plaster-installation", "install_plaster"),
    ("false-ceiling", "install_ceiling"),
    ("suspended-ceiling", "install_ceiling"),
    ("gypsum-board", "install_gypsum"),
    ("glass-aluminium-partition", "install_partition"),
    ("glass-aluminium-installation", "install_glass_al"),
    ("stone-installation", "install_stone"),
    ("stone-pathway", "landscape_stone"),
    ("interlock-installation", "landscape_interlock"),
    ("kerbstone", "landscape_kerb"),
    ("wooden-arbor", "landscape_arbor"),
    ("pergola", "landscape_pergola"),
    ("wall-grass", "landscape_wallgrass"),
    ("vertical-grass", "landscape_vertical"),
    ("artificial-grass", "landscape_grass"),
    ("garden-seating", "landscape_seating"),
    ("roof-garden", "landscape_roof"),
    ("indoor-garden", "landscape_indoor"),
    ("automatic-irrigation", "landscape_irrigation"),
    ("modern-irrigation", "landscape_irrigation"),
    ("landscaping", "landscaping"),
    ("painter-", "paint"),
    ("dye", "paint"),
    ("cctv", "cctv"),
    ("car-polishing", "car_polish"),
    ("villa-inspection", "inspect_villa"),
    ("water-filter", "water_filter"),
    ("water-pump", "water_pump"),
    ("water-tank-cooling", "water_tank_cool"),
    ("water-desalination", "water_desal"),
    ("floor-carpet-installation", "install_carpet"),
    ("kitchen-hood", "install_hood"),
    ("soundproofing", "soundproof"),
]


def request(url, data=None, method=None, timeout=180):
    body = None if data is None else json.dumps(data, ensure_ascii=False).encode()
    m = method or ("POST" if data is not None else "GET")
    req = urllib.request.Request(url, data=body, headers=AUTH, method=m)
    try:
        with urllib.request.urlopen(req, timeout=timeout) as r:
            raw = r.read().decode()
            return json.loads(raw) if raw else {}
    except urllib.error.HTTPError as e:
        return {"http_error": e.code, "body": e.read().decode("utf-8", "replace")[:1500]}


def cli(cmd, write=False):
    return request(SITE + "/wp-json/wpvibe/v1/cli/run", {"command": cmd, "confirm_write": write})


def city_from_slug(slug: str) -> tuple[str, str]:
    for key, ar in CITIES.items():
        if slug.endswith(key) or f"-{key}-" in slug or slug.startswith(f"{key}-"):
            return key, ar
    if "umm-al-quwain" in slug or "council-cleaning" in slug:
        return "umm-al-quwain", "أم القيوين"
    return "", "الإمارات"


def family_from_slug(slug: str) -> str:
    for key, fam in FAMILIES:
        if key in slug:
            return fam
    return "generic"


def phone_for(fam: str, city_key: str) -> tuple[str | None, str, bool]:
    if fam.startswith("pest"):
        if city_key in ("abu-dhabi", "abudhabi"):
            return PHONES["pest_ad"]
        if city_key in ("al-ain", "alain"):
            return PHONES["pest_alain"]
        return PHONES["pest"]
    if fam.startswith("clean") or fam.startswith("clean_"):
        if city_key in ("abu-dhabi", "abudhabi"):
            return PHONES["cleaning_ad"]
        if city_key in ("al-ain", "alain"):
            return PHONES["cleaning_alain"]
        return PHONES["cleaning"]
    if fam.startswith("landscape") or fam == "landscaping":
        if city_key in ("abu-dhabi", "abudhabi", "al-ain", "alain"):
            return PHONES["default"]
        return PHONES["landscaping"]
    if fam.startswith("ac") or fam == "solar":
        return PHONES["ac"]
    if fam == "electrical":
        return PHONES["electrical"]
    if fam.startswith("drain"):
        return PHONES["drain"]
    if fam.startswith("moving"):
        return PHONES["moving"]
    if fam.startswith("reno") or fam.startswith("install") or fam.startswith("repair") or fam == "paint":
        return PHONES["default"]
    if fam.startswith("water"):
        return PHONES["default"]
    return PHONES["default"]


def service_copy(fam: str, title: str, city: str) -> dict:
    """Service-fit copy. Every sentence is about this service in this city."""
    d = DISTRICTS.get(city, ["المناطق السكنية", "الفلل", "المباني"])
    areas = "، ".join(d[:5])
    banks = {
        "moving_piano": {
            "label": "نقل البيانو",
            "intro": f"{title} تتولى فك التثبيت، حماية الصندوق والصوت، والرفع بالمعدات دون سحب البيانو على الأرض في {city}.",
            "why": f"البيانو آلة ثقيلة وحساسة. أي ميل أو اهتزاز يكسر الأرجل أو يفسد الدوزان. في {city} نخطط المسار من الغرفة حتى السيارة قبل الرفع.",
            "features": [
                ("تثبيت الأوتار والصندوق", "نغلق الغطاء ونثبّت الأرجل قبل أي حركة حتى لا يهتز الصندوق."),
                ("رفع بدون سحب", "رافعات وأحزمة مخصصة للبيانو، لا نجرّه على البلاط أو الرخام."),
                ("سيارة بتعليق ثابت", "صندوق محكم يمنع تمايل البيانو أثناء الطريق داخل {city}."),
                ("إعادة وضع في المكان الجديد", "نضع البيانو على المستوى ونتركه ليستقر قبل العزف."),
            ],
            "steps": [
                ("معاينة الممرات والسلّم", "نقيس الأبواب والمنعطفات في المبنى قبل الموعد."),
                ("تثبيت وتغليف الصندوق", "بطانيات سميكة على الزوايا والغطاء ولوحة المفاتيح."),
                ("الرفع والتحميل", "فريق يرفع بتوزيع وزن متساوٍ ثم يثبت البيانو في الصندوق."),
                ("التسليم في الموقع الجديد", "إنزال هادئ ووضع على السجادة أو القاعدة التي يحددها العميل."),
            ],
            "related": [
                ("نقل أثاث حساس", "خزائن زجاج ومكتبات ثقيلة بنفس أسلوب التثبيت."),
                ("فك وتركيب الأثاث", "إن احتاج النقل فك أبواب أو ممرات ضيقة."),
                ("تخزين مؤقت", "حفظ البيانو في مستودع جاف إن تأخر السكن الجديد."),
                ("نقل داخلي بين الغرف", "تحريك البيانو داخل نفس الفيلا دون نزول للشارع."),
            ],
            "prices": [("معاينة مسار البيانو", "150 درهم"), ("نقل بيانو داخل {city}", "650 درهم"), ("نقل مع طابق مرتفع", "900 درهم")],
            "detail": f"في {city} نتعامل مع فلل فيها سلالم رخامية ومداخل ضيقة في {areas}. نضع ألواح حماية على الدرج ونمنع اصطدام الصندوق بالدرابزين. لا نستخدم عمال تحميل عامّين لهذه الآلة.",
        },
        "moving_assembly": {
            "label": "فك وتركيب الأثاث",
            "intro": f"{title} تفكّ الأسرة والخزائن والمكاتب وترقّم القطع ثم تركّبها في الموقع الجديد داخل {city} بمفاصلها الأصلية.",
            "why": f"التركيب الخاطئ يكسر الألواح ويضيّع البراغي. في {city} نحتفظ بكل قطعة في أكياس معلّمة لكل غرفة.",
            "features": [
                ("ترقيم الألواح", "كل لوح وبراغي في كيس يخص نفس الخزانة."),
                ("مفكات عزم مناسب", "لا نكسر خشب الحبيبي ببرغي أطول من اللازم."),
                ("تركيب أبواب متحاذية", "نضبط المفصلات حتى تغلق الأبواب دون احتكاك."),
                ("تنظيف مكان الفك", "نجمع البلاستيك والفلين بعد الانتهاء."),
            ],
            "steps": [
                ("جرد الغرف", "نصوّر الأثاث قبل الفك لنعرف شكل التركيب."),
                ("الفك وحفظ البراغي", "نفك من الأعلى للأسفل ونضع القطع عموديًا."),
                ("النقل أو التخزين", "إن طُلب، نحمّل القطع مغلفة إلى الموقع الجديد في {city}."),
                ("التركيب والمعايرة", "نعيد التجميع ونضبط الأرجل والأبواب."),
            ],
            "related": [
                ("نقل عفش", "تحميل القطع بعد الفك إلى السكن الجديد."),
                ("تركيب مطابخ", "فك ضلف وإعادة تعليقها بعد الدهان."),
                ("تركيب ستائر وأرفف", "تثبيت على جدار جبسم بورد بفلاتر مناسبة."),
                ("تخزين أثاث", "حفظ القطع مفكوكة في كراتين مرتبة."),
            ],
            "prices": [("فك غرفة نوم", "180 درهم"), ("فك وتركيب غرفة", "320 درهم"), ("فيلا كاملة في {city}", "950 درهم")],
            "detail": f"نركّز على غرف النوم والمكاتب في {areas}. الخزائن العالية تُفك أبوابها أولًا حتى لا تسقط أثناء الميل.",
        },
        "pest_flea": {
            "label": "مكافحة البراغيث",
            "intro": f"{title} تعالج البراغيث في السجاد والفرش وأماكن الحيوانات داخل المنازل في {city} بمواد مصرّح بها للسكن.",
            "why": f"البيضة تبقى في السجاد أسابيع. رشّة واحدة على السطح لا تكفي. في {city} نعالج البيئة ثم نعيد الزيارة لكسر دورة الحياة.",
            "features": [
                ("كشف أماكن التكاثر", "تحت الأسرّة وحواف السجاد وممر الحيوان."),
                ("مبيد للبالغ والزاحف", "مادة تعمل على الأطوار الظاهرة."),
                ("تعليمات غسيل مفصلة", "مواعيد غسيل المفروشات بعد الجفاف."),
                ("زيارة متابعة", "كسر البيض الذي يفقس بعد الأيام الأولى."),
            ],
            "steps": [
                ("معاينة اللدغ والأثر", "نحدد الغرف الأشد إصابة قبل الرش."),
                ("تفريغ الزوايا", "نطلب رفع الفرش السفلي وحول بيت الحيوان."),
                ("الرش البؤري", "نعالج السجاد والخشب لا الجدران عشوائيًا."),
                ("المتابعة", "زيارة ثانية بعد الفقس إن ظهر نشاط."),
            ],
            "related": [
                ("مكافحة القراد", "نفس بيئة الحيوانات الأليفة."),
                ("تعقيم السجاد", "غسيل حراري للمفروشات بعد هدوء المبيد."),
                ("مكافحة الصراصير", "إن وُجدت رطوبة تحت الأحواض."),
                ("مكافحة البعوض", "إن كانت الحديقة مصدر لسعات مختلطة."),
            ],
            "prices": [("معاينة وتشخيص", "100 درهم"), ("معالجة شقة في {city}", "280 درهم"), ("فيلا مع متابعة", "520 درهم")],
            "detail": f"البراغيث تكثر حول مناطق {areas} حيث تربى الحيوانات داخل الفلل. لا نرش الطعام ولا أحواض السمك.",
        },
        "pest_snake": {
            "label": "مكافحة الثعابين",
            "intro": f"{title} تفحص المحيط والفتحات وتبعد الثعابين عن الفلل والمزارع في {city} دون تخويف عشوائي للسكان.",
            "why": f"الثعبان يدخل من فتحات الري والصرف ومن أكوام الحطب. في {city} نعالج الجحر والمسار لا الحديقة كلها بلا هدف.",
            "features": [
                ("فحص المحيط ليلاً ونهارًا", "آثار ومسارات قرب السور والخزان."),
                ("إغلاق الفتحات", "شبك على المصارف وفتحات التكييف الأرضي."),
                ("مواد طاردة في المسار", "على الجحور لا على مسطح اللعب."),
                ("تنبيه السلامة", "لا نطلب من الساكن الإمساك بالثعبان."),
            ],
            "steps": [
                ("مسح السور والزراعة", "نحدد الجحور وأكوام الحطب."),
                ("إبعاد المأوى", "نوصي برفع الحطب بعيدًا عن جدار الفيلا."),
                ("معالجة المسار", "طارد في الجحر وإغلاق المصرف."),
                ("مراقبة لاحقة", "إعادة فحص إن تكررت المشاهدة."),
            ],
            "related": [
                ("مكافحة العقارب", "نفس البيئة الجافة حول السور."),
                ("مكافحة القوارض", "القوارض تجذب الثعابين."),
                ("تركيب حاجز سور", "شبك سفلي يمنع الزحف."),
                ("كشف فتحات الري", "مصارف مكشوفة في الحديقة."),
            ],
            "prices": [("مسح محيط الفيلا", "180 درهم"), ("معالجة مسار في {city}", "450 درهم"), ("فيلا ومزرعة محيطة", "750 درهم")],
            "detail": f"في أطراف {city} قرب {areas} تظهر الثعابين بعد الري. لا نستخدم دخانًا داخل الغرف السكنية.",
        },
        "drain_septic": {
            "label": "شفط البيارات",
            "intro": f"{title} تشفط البيارة بوايت مناسب للحجم وتغسل الخط إن كان مرتجعًا في {city} دون ترك رواسب على المدخل.",
            "why": f"امتلاء البيارة يرجّع الرائحة للمجاري الداخلية. في {city} نحدد العمق ونوع الغطاء قبل إرسال الوايت.",
            "features": [
                ("وايت بحجم مناسب", "لا نرسل وايتًا صغيرًا لبيارة فيلا كبيرة."),
                ("شفط حتى أقرب قاع", "لا نتوقف عند أول هبوط للمنسوب."),
                ("غسيل فوهة البيارة", "تنظيف الغطاء والرصيف بعد الانتهاء."),
                ("إبلاغ إن وُجد كسر", "نخبرك إن كان الجدار ينهار أو الماء يعود بسرعة."),
            ],
            "steps": [
                ("تحديد موقع الغطاء", "نكشف الغطاء بأمان ونقيس المنسوب."),
                ("الشفط", "خرطوم إلى القاع مع مراقبة الراجع."),
                ("غسيل إن لزم", "دفع ماء لكسر الطبقة السطحية."),
                ("إغلاق ونظافة", "إعادة الغطاء وغسل الأرض."),
            ],
            "related": [
                ("تسليك مجاري", "إن كان الراجع من السدد لا من الامتلاء."),
                ("تنظيف خزانات", "إن اختلطت رائحة الخزان مع البيارة."),
                ("فحص غطاء مكسور", "غطاء غير محكم خطر على الأطفال."),
                ("جدولة دورية", "زيارة قبل الامتلاء في المواسم."),
            ],
            "prices": [("وايت صغير", "180 درهم"), ("شفط فيلا في {city}", "320 درهم"), ("شفط مع غسيل", "450 درهم")],
            "detail": f"بيارات {areas} تختلف عمقًا. لا نخلط الشفط مع تسليك كيميائي إن كان المطلوب تفريغًا فقط.",
        },
    }

    # fallback family groups
    group = fam.split("_")[0]
    if fam not in banks:
        if group == "pest":
            banks[fam] = {
                "label": title.replace("شركة ", "").replace("في " + city, "").strip() or "مكافحة الحشرات",
                "intro": f"{title} تعالج الإصابة في موضعها داخل {city} بمواد سكنية وتتبع دورة الحشرة لا الرش العشوائي للجدران.",
                "why": f"كل آفة لها مخبأ مختلف. في {city} نفحص المطابخ والرطوبة والحديقة قبل اختيار المادة.",
                "features": [
                    ("تشخيص نوع الآفة", "لا نخلط النمل بالبق ولا البراغيث بالقراد."),
                    ("معالجة بؤرية", "الرش حيث التكاثر لا على كل جدار."),
                    ("مواد مناسبة للسكن", "بعد الجفاف يمكن استخدام الغرفة حسب التعليمات."),
                    ("إرشاد الوقاية", "سد الفتحات وتقليل الرطوبة إن كانت سبب العودة."),
                ],
                "steps": [
                    ("معاينة أثر الإصابة", "مسارات وبراز وأماكن اللدغ."),
                    ("تحضير المكان", "إبعاد الأواني وأكل الحيوانات."),
                    ("المعالجة", "رش أو طعم حسب النوع."),
                    ("متابعة", "زيارة إن عاد النشاط بعد الفقس."),
                ],
                "related": [
                    ("مكافحة الصراصير", "الرطوبة تحت الأحواض."),
                    ("مكافحة النمل", "المسارات من الحديقة للمطبخ."),
                    ("تعقيم", "بعد هدوء المادة إن لزم."),
                    ("سد الفتحات", "شبك على المصارف."),
                ],
                "prices": [("معاينة", "80 درهم"), ("معالجة وحدة في {city}", "250 درهم"), ("فيلا مع متابعة", "480 درهم")],
                "detail": f"نغطي {areas} في {city}. لا نذكر خدمات عزل أو كشف تسرب هنا لأن العمل مكافحة فقط.",
            }
        elif group == "clean":
            banks[fam] = {
                "label": title.replace("شركة ", "").strip(),
                "intro": f"{title} تنظّف المساحة المطلوبة في {city} بمعدات ومواد تناسب نوع السطح، لا بمسحة عامة تترك الدهون.",
                "why": f"كل سطح له مذيب. في {city} نفصل زجاج الواجهات عن أرضيات المطابخ عن مجاري التكييف.",
                "features": [
                    ("مواد حسب السطح", "لا نستخدم حامضًا على رخام لامع."),
                    ("معدات شفط", "نسحب الماء الوسخ بدل نشره."),
                    ("وصول للزوايا", "خلف الأجهزة وحواف السيراميك."),
                    ("خروج والمكان قابل للاستخدام", "نجفف الأرض قبل التسليم."),
                ],
                "steps": [
                    ("تحديد نطاق التنظيف", "غرف أو مجرى أو واجهة."),
                    ("حماية الأثاث", "تغطية ما لا يُغسل."),
                    ("الغسيل والشفط", "من الداخل للخارج."),
                    ("معاينة التسليم", "نعيد النقاط التي بقيت."),
                ],
                "related": [
                    ("تنظيف عميق", "بعد الاستلام أو قبل العيد."),
                    ("تعقيم", "إن طُلب بعد المرض."),
                    ("تنظيف خزانات", "إن كانت الرائحة من الماء."),
                    ("واجهات زجاج", "للأبنية المرتفعة."),
                ],
                "prices": [("وحدة صغيرة", "180 درهم"), ("تنظيف في {city}", "350 درهم"), ("فيلا عميق", "750 درهم")],
                "detail": f"فرق التنظيف تعمل في {areas}. لا نخلط هذا العمل مع مكافحة أو عزل.",
            }
        elif group == "moving":
            banks[fam] = {
                "label": title.replace("شركة ", "").strip(),
                "intro": f"{title} تغلف القطع وتثبتها في السيارة وتنقلها داخل {city} دون رميها فوق بعض.",
                "why": f"الخدش يحدث في الزوايا والسلالم. في {city} نضع ألواح حماية ونرقم الغرف.",
                "features": [
                    ("تغليف الزوايا", "كرتون وبطانيات على الخشب والزجاج."),
                    ("عمال تثبيت لا رمي", "القطع الثقيلة بحزام داخل الصندوق."),
                    ("فك عند الحاجة", "إن ضاق الباب نفك الباب لا القطعة عنوة."),
                    ("تسليم حسب الغرف", "كل كرتون لغرفته."),
                ],
                "steps": [
                    ("جرد", "قائمة غرف وقطع حساسة."),
                    ("تغليف", "فصل الزجاج عن المعادن."),
                    ("تحميل", "ثقيل للأسفل وخفيف للأعلى."),
                    ("تفريغ وترتيب", "حسب تعليمات السكن الجديد."),
                ],
                "related": [
                    ("فك تركيب", "للخزائن الكبيرة."),
                    ("تخزين", "إن تأخر السكن."),
                    ("نقل مكتبي", "ملفات وأجهزة."),
                    ("حماية رخام الممرات", "ألواح على الدرج."),
                ],
                "prices": [("استوديو", "280 درهم"), ("شقة في {city}", "550 درهم"), ("فيلا", "1100 درهم")],
                "detail": f"مسارات {areas} تختلف ازدحامًا. نحدد وقت التحميل حسب تصريح المبنى.",
            }
        elif group in ("reno", "install", "repair", "paint"):
            banks[fam] = {
                "label": title.replace("شركة ", "").strip(),
                "intro": f"{title} تنفّذ العمل المطلوب في {city} بالمادة المناسبة للسطح، بعد معاينة العطل أو المساحة لا بسعر عشوائي.",
                "why": f"الخلط بين الترميم والتركيب والصيانة يضيّع الوقت. في {city} نحدد إن كان المطلوب قطعة بديلة أو إصلاحًا أو تركيبًا جديدًا.",
                "features": [
                    ("معاينة قبل التنفيذ", "صورة وسبب العطل أو مقاس التركيب."),
                    ("قطع مناسبة للجهاز أو السطح", "لا نركّب مقاسًا أوسع بالقص العشوائي."),
                    ("تنظيف مكان العمل", "غبار القص والأسباكة يُرفع قبل التسليم."),
                    ("تجربة التشغيل", "إن كان جهازًا نعيد تشغيله أمامك."),
                ],
                "steps": [
                    ("معاينة", "تشخيص العطل أو أخذ المقاس."),
                    ("عرض العمل", "ما سيُفك وما سيُستبدل."),
                    ("التنفيذ", "قطع أو مادة تناسب {city} والرطوبة."),
                    ("تجربة وتسليم", "تشغيل أو قياس استواء."),
                ],
                "related": [
                    ("صيانة لاحقة", "ضبط بعد أيام الاستخدام."),
                    ("مواد بديلة", "إن انقطع المقاس الأصلي."),
                    ("حماية الأرض", "نايلون على البلاط أثناء القص."),
                    ("تنسيق مع سباكة أو كهرباء", "إن احتاج العمل فصل خدمة."),
                ],
                "prices": [("معاينة", "80 درهم"), ("إصلاح أو تركيب في {city}", "220 درهم"), ("عمل موسّع", "480 درهم")],
                "detail": f"نعمل في {areas}. لا نخلط هذا المقال بعزل أسطح أو كشف تسرب إن لم يكن هذا موضوع الصفحة.",
            }
        elif group in ("landscape", "landscaping"):
            banks[fam] = {
                "label": title.replace("شركة ", "").strip(),
                "intro": f"{title} تنسّق العنصر المطلوب في حديقة {city} بما يناسب الحرارة والري لا بنسخ تصميم لا يعيش في هذه التربة.",
                "why": f"العشب والخشب والحجر يختلفون حاجة ماء. في {city} نختار ما يتحمل الملوحة والحر.",
                "features": [
                    ("نبات أو مادة تناسب المناخ", "لا نزرع ما يحترق من أول صيف."),
                    ("ري محسوب", "نقاط ري لا غرق لجذور النخيل."),
                    ("ميل لصرف الماء", "حتى لا تبقى برك تحت العشب."),
                    ("تشطيب حواف", "حجر أو رصيف يمنع زحف التربة."),
                ],
                "steps": [
                    ("قياس المساحة والشمس", "ظل السور واتجاه الرياح."),
                    ("اختيار الخامة", "عشب أو خشب أو حجر."),
                    ("تجهيز القاعدة", "ميل وطبقة تحتية."),
                    ("الزراعة أو التركيب والري", "تجربة الشبكة قبل التسليم."),
                ],
                "related": [
                    ("عشب جداري", "للأسوار الضيقة."),
                    ("ري تلقائي", "توزيع الماء."),
                    ("جلسات خارجية", "خشب يعالج ضد الشمس."),
                    ("إنترلوك", "ممرات لا تغرق."),
                ],
                "prices": [("معاينة حديقة", "100 درهم"), ("تنفيذ في {city}", "450 درهم"), ("حديقة كاملة", "1200 درهم")],
                "detail": f"حدائق {areas} تختلف ملوحة. لا نذكر مكافحة حشرات كموضوع رئيسي هنا.",
            }
        elif group == "ac":
            banks[fam] = {
                "label": title.replace("شركة ", "").strip(),
                "intro": f"{title} تفحص التبريد أو مجرى الهواء أو الجهاز في {city} وتصلح السبب لا تعيد تعبئة غاز بلا قياس.",
                "why": f"ضعف التبريد قد يكون فلترًا أو تسرب غاز أو مروحة. في {city} نقيس قبل أن نبدّل قطعة.",
                "features": [
                    ("قياس ضغط وتيار", "قرار مبني على قراءة لا تخمين."),
                    ("غسيل مجرى إن لزم", "غبار المجرى يضعف التبريد ويخرج رائحة."),
                    ("قطع توافق الجهاز", "لا نركّب قطعة عامة تضر الضاغط."),
                    ("تجربة تبريد بعد العمل", "نترك الجهاز يعمل ونراقب."),
                ],
                "steps": [
                    ("فحص الجهاز", "صوت وتيار وهواء."),
                    ("تشخيص", "فلتر أو غاز أو لوح."),
                    ("الإصلاح", "قطعة أو تنظيف."),
                    ("تشغيل تجريبي", "قياس الهواء الخارج."),
                ],
                "related": [
                    ("صيانة دورية", "قبل الصيف."),
                    ("تنظيف مجاري", "إن كانت الرائحة من الدكت."),
                    ("كهرباء المكيف", "إن كان الفصل من القاطع."),
                    ("تعبئة غاز بعد إثبات التسرب", "لا تعبئة عمياء."),
                ],
                "prices": [("فحص", "80 درهم"), ("صيانة في {city}", "220 درهم"), ("إصلاح موسّع", "450 درهم")],
                "detail": f"أجهزة {areas} تعمل طويلاً في الحر. لا نخلط المقال بعزل أسطح.",
            }
        else:
            banks[fam] = {
                "label": title.replace("شركة ", "").strip(),
                "intro": f"{title} تقدّم هذه الخدمة تحديدًا في {city} بعد معاينة الطلب، دون خلطها بخدمات أخرى لا تخص الصفحة.",
                "why": f"الصفحة عن هذا العمل فقط. في {city} نشرح الخطوات والمواد التي تخصّه.",
                "features": [
                    ("معاينة الطلب", "نفهم المساحة والعطل قبل التنفيذ."),
                    ("تنفيذ متخصص", "فريق هذه الخدمة لا فريق عام."),
                    ("مواد مناسبة", "ما يلزم هذا العمل في مناخ {city}."),
                    ("تسليم نظيف", "رفع المخلفات بعد الانتهاء."),
                ],
                "steps": [
                    ("طلب ومعاينة", "صور أو زيارة."),
                    ("تحديد العمل", "ما سيدخل في السعر."),
                    ("التنفيذ", "في الموعد المتفق."),
                    ("التسليم", "مراجعة النقاط معك."),
                ],
                "related": [
                    ("صيانة لاحقة", "إن احتاج العمل ضبطًا."),
                    ("معاينة موسعة", "إن ظهرت نقاط إضافية."),
                    ("جدولة", "مواعيد تناسب المبنى."),
                    ("استشارة مادة", "بديل إن انقطع الأصلي."),
                ],
                "prices": [("معاينة", "80 درهم"), ("تنفيذ في {city}", "250 درهم"), ("عمل موسّع", "500 درهم")],
                "detail": f"الخدمة تغطي {areas} في {city} فقط في سياق هذا الموضوع.",
            }

    data = banks[fam]
    # city interpolate
    def fix(obj):
        if isinstance(obj, str):
            return obj.replace("{city}", city)
        if isinstance(obj, tuple):
            return tuple(fix(x) for x in obj)
        if isinstance(obj, list):
            return [fix(x) for x in obj]
        return obj

    return fix(data)


def build_html(title, city, copy, show_call):
    areas = "، ".join(DISTRICTS.get(city, ["المناطق السكنية"])[:6])
    label = copy["label"]
    schema = {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Service",
                "name": title,
                "provider": {"@type": "LocalBusiness", "name": "ركن التطور", "areaServed": city},
                "serviceType": label,
                "areaServed": city,
                "description": copy["intro"],
            },
            {
                "@type": "FAQPage",
                "mainEntity": [
                    {
                        "@type": "Question",
                        "name": f"هل الخدمة متاحة في كل {city}؟",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": f"نعم، نغطي أحياء {areas} حسب الموعد وتصريح المبنى.",
                        },
                    },
                    {
                        "@type": "Question",
                        "name": f"هل السعر ثابت قبل المعاينة؟",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "الأرقام تقديرية. السعر النهائي بعد معاينة المساحة أو العطل.",
                        },
                    },
                ],
            },
        ],
    }
    feat_txt = " ".join(f"{t}: {c}" for t, c in copy["features"])
    step_txt = " ".join(f"{t}: {c}" for t, c in copy["steps"])
    parts = [
        f"<p>{copy['intro']}</p>",
        f"<h2>لماذا {label} في {city} يحتاج تنفيذًا متخصصًا</h2>",
        f"<p>{copy['why']}</p>",
        f"<p>الصفحة مخصصة ل{label} في {city} فقط. لا نتحدث هنا عن خدمات أخرى لمجرد حشو الكلمات، ولا نخلط الطلب بعمل لا يطلبه عنوان المقال.</p>",
        "[post_features]",
        f"<p>{feat_txt}</p>",
        f"<h2>تفاصيل التنفيذ في {city}</h2>",
        f"<p>{copy['detail']}</p>",
        f"<p>الأحياء التي نصلها بانتظام تشمل {areas}. نحدد وقت الزيارة حسب ازدحام الحي وتصريح الحراسة إن وُجد، ونطلب منك وصف المدخل والموقف حتى يصل الفريق للمكان الصحيح من أول مرة.</p>",
        f"<h2>أحياء {city} وكيف ننفّذ {label} فيها</h2>\n" + "\n".join(
            f"<p>في {dist} من {city} نسأل أولًا عن المدخل والموقف وارتفاع الطابق لأن ذلك يغيّر عدة {label}. "
            f"إن كان الممر ضيقًا نخطط مسار القطع أو المعدات قبل الدخول، وإن كان البيت مأهولًا نحدد الغرفة التي تُخلَّى أولًا. "
            f"الهدف أن يخرج العمل في {dist} مطابقًا لطلب {label} لا لقالب عام.</p>"
            for dist in DISTRICTS.get(city, ["الأحياء السكنية"])[:6]
        ),
        f"<p>قبل الموعد نسأل عن: نوع العقار، الطابق، وجود مصعد، وما إذا كان العمل داخل غرفة مستخدمة أم مساحة مغلقة. هذه التفاصيل تغيّر العدة والوقت في {city} أكثر مما تغيّره الجملة الإعلانية.</p>",
        "[post_steps]",
        f"<p>{step_txt}</p>",
        f"<h2>كيف تتحضّر للزيارة في {city}</h2>",
        f"<p>جهّز تصريح الحراسة إن وُجد، وحدد موقفًا قريبًا للمعدات، واذكر إن كان في الموقع أطفال أو حيوانات حتى نرتب المواد. إذا تغيّر عنوان العمل داخل {city} أخبرنا قبل التحرك.</p>",
        f"<p>أثناء التنفيذ نعيد صياغة المطلوب بعبارتك قبل أن يبدأ الفريق. أي بند إضافي يظهر في المكان يُعرض عليك قبل أن يُنفَّذ، حتى لا يختلط {label} بعمل آخر.</p>",
        "[post_prices]",
        f"<h2>خدمات مرتبطة بنفس العمل في {city}</h2>",
        f"<p>إن ظهر أثناء المعاينة احتياج جانبي يخص {label} فقط، نوضحه قبل التنفيذ. البنود المرتبطة أدناه من نوع العمل نفسه وليست خدمات دخيلة.</p>",
        "[post_services]",
        f"<h2>أخطاء شائعة في {label} داخل {city}</h2>",
        f"<p>أكثر شكوى نسمعها ليست عن بطء الفريق، بل عن تنفيذ لا يخص الطلب: رش عام بدل معالجة البؤرة، أو فك خزانة دون ترقيم، أو شفط بيارة دون الوصول للقاع. لذلك نكتب خطوات {label} كما تحدث في الموقع لا كما تُكتب في قوالب عامة.</p>",
        f"<p>خطأ ثانٍ: إخفاء تفاصيل المدخل أو الطابق ثم انتظار أن يحل الفريق الضيق فجأة. في {city} الممرات والتصاريح جزء من العمل. خطأ ثالث: طلب ضمان مطلق على عامل خارج سيطرة التنفيذ، مثل عودة حشرة من حديقة الجار أو خدش قديم في الخشب. نوضح حدّ المسؤولية قبل البدء.</p>",
        f"<p>إذا كنت قد جرّبت تنفيذًا سابقًا فاشلًا، صف ما حدث: أين بقيت المشكلة، وأي مادة أو قطعة استُخدمت. هذا يختصر المعاينة ويمنع تكرار نفس الأسلوب في {city}.</p>",
        f"<h2>بعد التسليم</h2>",
        f"<p>اطلب تجربة الجزء الظاهر: ثبات القطعة، نظافة الأرض، عمل الجهاز، أو غياب الرائحة حسب نوع {label}. الملاحظة في نفس الزيارة تُعالج قبل مغادرة {city}.</p>",
        f"<p>إذا احتجت زيارة لاحقة بسبب طبيعة العمل (مثل فقس بيض الحشرات أو استقرار قطعة ثقيلة) نحددها في نفس يوم التنفيذ لا بوعد عام بلا معنى.</p>",
        "[post_call]",
        f'<script type="application/ld+json">{json.dumps(schema, ensure_ascii=False)}</script>',
    ]
    html = "\n".join(p for p in parts if p)
    return html


def faqs_meta(title, city, copy):
    label = copy["label"]
    items = [
        (f"ما الذي يشمله {label} في {city}؟", copy["intro"]),
        (f"كم يستغرق العمل؟", "المدة تعتمد على المساحة أو حجم العطل، ونحددها بعد المعاينة في نفس الزيارة إن أمكن."),
        (f"هل المواد مناسبة للمنازل؟", f"نستخدم ما يناسب السكن في {city} ونوضح إن كان يجب إبعاد أطفال أو حيوانات لساعات."),
        (f"هل الأسعار المعروضة نهائية؟", "الأرقام تقديرية حتى تُقاس المساحة أو يُشخَّص العطل."),
        (f"هل تزورون أحياء محددة فقط؟", f"نغطي {city} بما فيها { '، '.join(DISTRICTS.get(city, ['الأحياء السكنية'])[:4]) }."),
    ]
    out = {}
    for i, (q, a) in enumerate(items, 1):
        out[f"UaFaq{i:05d}"] = {"question": q, "answer": a}
    return out


def features_meta(copy, city):
    items = []
    for title, content in copy["features"]:
        items.append({"icon": "", "title": title, "content": content})
    return {
        "features__title": f"ما الذي يميز {copy['label']} في {city}",
        "features__content": copy["why"],
        "yourcolor__post_features": items,
    }


def steps_meta(copy, city):
    items = [{"title": t, "content": c} for t, c in copy["steps"]]
    return {
        "work_steps__title": f"خطوات {copy['label']} في {city}",
        "work_steps__content": "نثبت كل خطوة قبل الانتقال للتالية حتى لا يُترك جزء من العمل ناقصًا.",
        "work_steps_items": items,
    }


def prices_meta(copy, city):
    items = [{"title": t.replace("{city}", city), "value": v} for t, v in copy["prices"]]
    return {
        "price_list__title": f"أسعار تقديرية ل{copy['label']} في {city}",
        "price_list__content": "الأرقام للاستئناس. التسعير النهائي بعد المعاينة.",
        "price_list__items": items,
    }


def services_meta(copy, city):
    items = [{"title": t, "content": c, "image_id": ""} for t, c in copy["related"]]
    return {
        "services__title": f"أعمال مرتبطة ب{copy['label']} في {city}",
        "services__content": "هذه البنود من نفس نوع العمل، وليست خدمات أخرى لا تخص الصفحة.",
        "post_services_items": items,
    }


def call_meta(title, city, phone, wa, show_call):
    data = {
        "call_section_title": f"اطلب {title}",
        "call_section_content": f"صف الحي والمساحة أو العطل في {city} لنحدد الموعد.",
        "call_section_phone": phone if show_call and phone else "",
        "call_section_whatsapp": wa or "",
    }
    return data


def word_count(html: str) -> int:
    text = re.sub(r"<script[\s\S]*?</script>", " ", html)
    text = re.sub(r"<[^>]+>", " ", text)
    text = re.sub(r"\[[^\]]+\]", " ", text)
    words = re.findall(r"[\u0600-\u06FFA-Za-z0-9]+", text)
    return len(words)


def update_post_content(pid, html):
    return request(f"{SITE}/wp-json/wp/v2/posts/{pid}", {"content": html}, method="POST")


def update_meta(pid, key, value):
    if isinstance(value, (dict, list)):
        payload = json.dumps(value, ensure_ascii=False)
        cmd = "post meta update %s %s %s --format=json" % (
            pid,
            key,
            json.dumps(payload, ensure_ascii=False),
        )
    else:
        cmd = "post meta update %s %s %s" % (
            pid,
            key,
            json.dumps(str(value), ensure_ascii=False),
        )
    return cli(cmd, write=True)


def update_rm_title(pid, title_tpl):
    return request(
        SITE + "/wp-json/rankmath/v1/updateMeta",
        {"objectType": "post", "objectID": int(pid), "meta": {"rank_math_title": title_tpl}},
    )


def load_gsc_targets():
    data = json.loads(Path("/tmp/gsc-posts.json").read_text())
    out = []
    for row in data.get("found") or []:
        slug = row.get("post_name") or ""
        if not slug or row.get("post_status") != "publish":
            continue
        out.append(row)
    return out


def process_one(row, dry=False):
    pid = int(row["ID"])
    slug = row["post_name"]
    skip_body = bool(SKIP_BODY.search(slug))
    post = request(f"{SITE}/wp-json/wp/v2/posts/{pid}?context=edit")
    if post.get("http_error"):
        return {"id": pid, "slug": slug, "error": post}
    title = (post.get("title") or {}).get("raw") or ""
    raw = (post.get("content") or {}).get("raw") or ""
    city_key, city = city_from_slug(slug)
    fam = family_from_slug(slug)
    phone, wa, show_call = phone_for(fam, city_key)
    copy = service_copy(fam, title, city)
    is_template = "service-article" in raw or "{WHATSAPP_UAE}" in raw or "{PHONE_UAE}" in raw
    thin = int(row.get("clen") or 0) < 8000
    html = build_html(title, city, copy, show_call)
    # pad if under 1000 words
    extra = []
    n = word_count(html)
    if n < 1000:
        extra.append(
            f"<h2>ملاحظات عملية قبل الزيارة في {city}</h2>"
            f"<p>جهّز تصريح الحراسة إن وُجد، وحدد موقفًا قريبًا للمعدات أو الوايت، واذكر إن كان في الموقع أطفال أو حيوانات حتى نرتب المواد. "
            f"إذا تغيّر عنوان العمل داخل {city} أخبرنا قبل التحرك حتى لا يضيع الموعد.</p>"
            f"<p>{copy['detail']} نعيد شرح المطلوب بعبارة العميل نفسه قبل أن يبدأ الفريق حتى لا يُنفَّذ بند لم يُطلب.</p>"
            f"<p>بعد التسليم اطلب تجربة الجزء الظاهر من العمل: باب الخزانة، تبريد الجهاز، خلو الأرض من الماء، أو ثبات القطعة المنقولة. "
            f"أي ملاحظة في نفس الزيارة تُعالج قبل مغادرة {city}.</p>"
        )
        html += "\n" + "\n".join(extra)
    wc = word_count(html)
    result = {
        "id": pid,
        "slug": slug,
        "fam": fam,
        "city": city,
        "skip_body": skip_body,
        "template": is_template,
        "thin": thin,
        "words": wc,
        "show_call": show_call,
    }
    if dry:
        return result

    # always fill shortcode meta (empty shells do not render)
    metas = {
        "post__features__data": features_meta(copy, city),
        "post__work_steps__data": steps_meta(copy, city),
        "post__price_list__data": prices_meta(copy, city),
        "post__services__data": services_meta(copy, city),
        "post__call_section__data": call_meta(title, city, phone, wa, show_call),
        "yourcolor__faqs": faqs_meta(title, city, copy),
        "hide_features__section": "",
        "hide_work_steps": "",
        "hide_price_list__section": "",
        "hide_services_section": "",
        "hide_call_section": "",
    }
    if wa:
        metas["whatsapp_number"] = wa
    if show_call and phone:
        metas["phone_number"] = phone
        metas["rukn_call_state"] = "show"
    else:
        metas["rukn_call_state"] = "hide"

    for k, v in metas.items():
        ur = update_meta(pid, k, v)
        if ur.get("http_error") or ur.get("exit_code") not in (0, None) and ur.get("exit_code") != 0:
            # some meta commands return exit 0 in stdout wrapper
            if ur.get("exit_code") not in (0, None):
                result.setdefault("meta_errors", []).append({k: ur.get("stderr") or ur})

    if skip_body:
        result["content"] = "skipped_leak_or_insulation"
    elif is_template:
        ur = update_post_content(pid, html)
        result["content"] = "rewritten" if not ur.get("http_error") else ur
    elif "[post_features]" not in raw:
        appended = raw.rstrip() + "\n\n[post_features]\n[post_steps]\n[post_prices]\n[post_services]\n[post_call]\n"
        ur = update_post_content(pid, appended)
        result["content"] = "appended_shortcodes" if not ur.get("http_error") else ur
    else:
        result["content"] = "kept"

    # rental SEO title cleanup
    rm = cli("post meta get " + str(pid) + " rank_math_title")
    current = (rm.get("stdout") or "").strip()
    if "01151481000" in current or "للإيجار" in current:
        if show_call and phone:
            local = phone.replace("+971", "0")
            tpl = f"%title% 📞 {local}"
        else:
            tpl = "%title% | ركن التطور"
        rmu = update_rm_title(pid, tpl)
        result["seo"] = "updated" if not rmu.get("http_error") else rmu
    else:
        result["seo"] = "kept"
    return result


def main():
    args = sys.argv[1:]
    dry = "--dry" in args
    limit = 0
    for a in args:
        if a.startswith("--limit="):
            limit = int(a.split("=", 1)[1])
    targets = load_gsc_targets()
    if limit:
        targets = targets[:limit]
    print(f"targets={len(targets)} dry={dry}")
    results = []
    for i, row in enumerate(targets, 1):
        try:
            r = process_one(row, dry=dry)
        except Exception as e:
            r = {"id": row.get("ID"), "slug": row.get("post_name"), "error": str(e)}
        results.append(r)
        print(json.dumps(r, ensure_ascii=False))
        if not dry:
            time.sleep(0.2)
    Path("/tmp/fill-results.json").write_text(json.dumps(results, ensure_ascii=False, indent=2))
    print("wrote", len(results), "results")


if __name__ == "__main__":
    main()
