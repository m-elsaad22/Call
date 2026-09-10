#!/usr/bin/env python3
"""Publish merged UAQ leak article and add official YouTube video to other water-leak posts."""
from __future__ import annotations

import base64
import json
import os
import re
import ssl
import time
import urllib.error
import urllib.request
from pathlib import Path

SITE = "https://www.rukn-eltatawer.com"
USER = os.environ.get("WP_USER", "cursor")
APP_PASS = os.environ.get("WP_APP_PASS", "QQEG jJcC hwu4 SvYY YofJ cOJT").replace(" ", "")
TOKEN = base64.b64encode(f"{USER}:{APP_PASS}".encode()).decode()
HEADERS = {
    "Authorization": f"Basic {TOKEN}",
    "User-Agent": "Mozilla/5.0 CursorAgent",
    "Accept": "application/json",
    "Content-Type": "application/json",
}
UAQ_ID = 430
VIDEO_ID = "S6bBN3Yaq-A"
VIDEO_BLOCK = """
<!-- rukn-leak-video -->
<section class="rukn-official-leak-video" style="margin:30px 0;">
<h2>شاهد فريق ركن التطور في كشف التسربات</h2>
<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;">
<iframe src="https://www.youtube.com/embed/S6bBN3Yaq-A" title="كشف تسربات المياه – ركن التطور" style="position:absolute;top:0;right:0;width:100%;height:100%;border:0;border-radius:10px;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
</div>
</section>
"""

# Water leak detection articles (not gas/AC). UAQ is replaced fully so skipped here.
OTHER_LEAK_IDS = [
    237, 397, 402, 405, 422, 425, 427,
    2952, 2953, 2954, 2955, 2957, 9509,
    12565, 12567, 12568,
    12183, 12185, 12187, 12189, 12193,
]


def php_serialize(val) -> str:
    if val is None:
        return "N;"
    if isinstance(val, bool):
        return f"b:{1 if val else 0};"
    if isinstance(val, int) and not isinstance(val, bool):
        return f"i:{val};"
    if isinstance(val, float):
        return f"d:{val};"
    if isinstance(val, str):
        raw = val.encode("utf-8")
        return f's:{len(raw)}:"{val}";'
    if isinstance(val, list):
        inner = "".join(php_serialize(i) + php_serialize(v) for i, v in enumerate(val))
        return f"a:{len(val)}:{{{inner}}}"
    if isinstance(val, dict):
        inner = "".join(php_serialize(k) + php_serialize(v) for k, v in val.items())
        return f"a:{len(val)}:{{{inner}}}"
    raise TypeError(type(val))


def request(url: str, data=None, method: str | None = None, timeout: int = 120):
    body = None
    if data is not None:
        body = json.dumps(data, ensure_ascii=False).encode("utf-8")
        method = method or "POST"
    req = urllib.request.Request(url, data=body, headers=HEADERS, method=method)
    try:
        with urllib.request.urlopen(req, timeout=timeout) as r:
            raw = r.read().decode("utf-8")
            return json.loads(raw) if raw else {}
    except urllib.error.HTTPError as e:
        err = e.read().decode("utf-8", "replace")
        raise RuntimeError(f"{e.code} {url} {err[:1500]}") from e


def cli(cmd: str, approved: bool = False, confirm_write: bool = False):
    endpoint = "/wp-json/wpvibe/v1/cli/run-approved" if approved else "/wp-json/wpvibe/v1/cli/run"
    payload = {"command": cmd}
    if approved:
        payload["confirm_write"] = confirm_write
    return request(SITE + endpoint, payload)


def rest_update_post(post_id: int, **fields):
    return request(f"{SITE}/wp-json/wp/v2/posts/{post_id}", fields, method="POST")


def features_meta() -> dict:
    items = [
        ("UaFeatFLIR", "كاميرا حرارية FLIR E96", "تميّز بين رطوبة تسرب الأنابيب الدافئة ورطوبة المياه الجوفية المالحة الباردة — تمييز حاسم في أم القيوين المحاطة بالمياه.", '<i class="fas fa-solid fa-camera"></i>'),
        ("UaFeatHydr", "هيدروفون AT-407", "يحدد المصدر الحقيقي للتسرب بدقة ±10 سم — في التربة الرملية يحدد نقطة الكسر بعيداً عن مظهر الرطوبة الخادع.", '<i class="fas fa-solid fa-headphones"></i>'),
        ("UaFeatGPR1", "رادار الأرض GPR", "يتتبع انتشار المياه المتسربة في التربة الرملية ويحدد نطاق الضرر الفعلي لتقدير حجم المشكلة الحقيقية.", '<i class="fas fa-solid fa-search"></i>'),
        ("UaFeatNitr", "اختبار ضغط النيتروجين", "يؤكد أن المصدر من الأنابيب وليس من المياه الجوفية أو التسلل الساحلي.", '<i class="fas fa-solid fa-wind"></i>'),
        ("UaFeatCam1", "كاميرا الفحص الداخلي", "ترى تآكل الأنابيب الداخلي من الرطوبة المالحة في مباني أم القيوين التاريخية.", '<i class="fas fa-solid fa-video"></i>'),
        ("UaFeatMois", "جهاز قياس الرطوبة الرقمي", "يفرّق بين رطوبة تسرب الأنابيب والرطوبة الجوية المالحة والرطوبة الجوفية.", '<i class="fas fa-solid fa-droplet"></i>'),
    ]
    return {
        "features__title": "أجهزة كشف تسربات المياه في أم القيوين",
        "features__content": "نعتمد على أجهزة إلكترونية متخصصة لتمييز مصادر الرطوبة الثلاثة في أم القيوين دون تكسير عشوائي.",
        "yourcolor__post_features": {
            k: {"title": t, "content": c, "icon": i} for k, t, c, i in items
        },
    }


def steps_meta() -> dict:
    steps = [
        ("UaStep01", "التواصل والحجز الفوري", "راسلنا عبر واتساب على 0524314370 لتحديد موعد في أقل من ساعتين في أي وقت."),
        ("UaStep02", "الوصول والتمييز الأولي المجاني", "أول ما يصل الفريق يحدد نوع الرطوبة: تسرب أنابيب أم مياه جوفية مالحة أم رطوبة ساحلية — قبل تشغيل أي جهاز."),
        ("UaStep03", "الكشف الإلكتروني المتخصص", "GPR لتتبع الانتشار في التربة الرملية + هيدروفون لتحديد المصدر + كاميرا حرارية وجهاز رطوبة."),
        ("UaStep04", "رسم خريطة الضرر الكاملة", "في أم القيوين لا يكفي معرفة موضع التسرب — يجب معرفة نطاق انتشاره في التربة الرملية."),
        ("UaStep05", "الإصلاح المتخصص حسب النوع", "إصلاح الأنبوب، أو حاجز رطوبة للرطوبة الجوفية، أو طلاء مقاوم للملوحة — حل مختلف لكل نوع."),
        ("UaStep06", "فحص التأكيد بالأجهزة", "إعادة الفحص للتأكد من معالجة كل المصادر وليس واحداً فقط."),
        ("UaStep07", "التسليم والضمان المكتوب", "تقرير فني بخريطة الانتشار + ضمان موقع شامل، ويمكن تقديمه للاتحاد للماء والكهرباء."),
    ]
    return {
        "work_steps__title": "7 خطوات كشف التسربات مع ركن التطور في أم القيوين",
        "work_steps__content": "من أول واتساب حتى التسليم بضمان مكتوب، بمسار واضح يناسب التربة الرملية والرطوبة المالحة في أم القيوين.",
        "work_steps_items": {k: {"title": t, "content": c} for k, t, c in steps},
    }


MEDIA = {
    10516: "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-rukn-eltatawer.webp",
    1671: "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-water-1.webp",
    1676: "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-machien-1.webp",
    11293: "https://www.rukn-eltatawer.com/wp-content/uploads/2026/07/leak-detection-rukn.webp",
    11291: "https://www.rukn-eltatawer.com/wp-content/uploads/2026/07/water-leak-detection-ae.webp",
    2777: "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak.webp",
}


def services_meta() -> dict:
    items = [
        ("UaSrvBath", "كشف تسربات الحمامات", "فحص الأنابيب والتوصيلات بضغط النيتروجين والجهاز الصوتي دون تكسير السيراميك.", 10516, MEDIA[10516]),
        ("UaSrvTank", "كشف تسربات الخزانات الأرضية", "فحص الهيكل الداخلي والعزل الإيبوكسي لمنع تهريب الخزان وهبوط الأساسات.", 1676, MEDIA[1676]),
        ("UaSrvRoof", "فحص الأسطح المبلطة", "كاميرا حرارية لرصد فجوات العزل وتجمعات مياه الأمطار تحت السيراميك.", 1671, MEDIA[1671]),
        ("UaSrvWall", "معالجة الرطوبة والرشح", "إيقاف المصدر ثم تجفيف الجدار وعزله وترميم الأصباغ بدل الدهان فوق العفن.", 11293, MEDIA[11293]),
        ("UaSrvChem", "إصلاح بدون تكسير بالحقن الكيميائي", "حقن إيبوكسي داخل المواسير لإغلاق الشقوق من الداخل أو فك بلاطة واحدة بعد التحديد الدقيق.", 11291, MEDIA[11291]),
    ]
    return {
        "services__title": "خدمات كشف تسربات المياه في أم القيوين",
        "services__content": "باقة هندسية تغطي الحمامات والأسطح والخزانات ومعالجة الرطوبة بدون تكسير عشوائي.",
        "post_services_items": {
            k: {"title": t, "content": c, "image_id": iid, "image": im}
            for k, t, c, iid, im in items
        },
    }


def prices_meta() -> dict:
    return {
        "price_list__title": "أسعار كشف تسربات المياه في أم القيوين",
        "price_list__content": "الكشف الإلكتروني 500 درهم، وبعد خصم 40% يصبح 300 درهم. المعاينة لتمييز نوع الرطوبة مجانية.",
        "price_list__table_title1": "البند",
        "price_list__table_title2": "ركن التطور",
        "price_list__items": {
            "UaPrvSvc": {"title": "الخدمة", "value": "كشف تسربات المياه في أم القيوين"},
            "UaPrvFee": {"title": "سعر الكشف", "value": "500 درهم"},
            "UaPrvOff": {"title": "بعد خصم 40%", "value": "300 درهم"},
            "UaPrvWa": {"title": "واتساب", "value": "+971524314370"},
            "UaPrvHrs": {"title": "المواعيد", "value": "24 ساعة طوال أيام الأسبوع"},
            "UaPrvArea": {"title": "التغطية", "value": "جميع مناطق وجزر أم القيوين"},
        },
    }


def faq_items() -> list[dict]:
    return [
        {"question": "ما تكلفة كشف تسربات المياه في أم القيوين؟", "answer": "المعاينة الأولية لتمييز نوع الرطوبة مجانية. سعر الكشف الإلكتروني 500 درهم، وبعد خصم 40% يصبح 300 درهم. الأسعار ثابتة مسبقاً بلا مفاجآت، ويُحتسب الكشف ضمن قيمة العمل إذا تم الإصلاح معنا."},
        {"question": "لماذا لا تظهر الرطوبة في الجدران رغم وجود التسرب في أم القيوين؟", "answer": "التربة الرملية فائقة النفاذية تمتص المياه المتسربة وتوزّعها أفقياً تحت الأرضية بدلاً من أن ترتفع للجدران. لذلك ترتفع الفاتورة ويتحرك العداد أسابيع قبل ظهور أي رطوبة."},
        {"question": "ما الفرق بين تسرب الأنابيب والرطوبة الجوفية المالحة في أم القيوين؟", "answer": "تسرب الأنابيب يرفع فاتورة المياه ويحرك العداد مع الإغلاق الكامل وقد تسمع خريراً. الرطوبة الجوفية لا تؤثر على الفاتورة وتظهر في الجدران السفلية وتكون دائمة. جهاز الرطوبة الرقمي والكاميرا الحرارية يفرّقان بينهما."},
        {"question": "هل تغطون الجزر مثل جزيرة الأكعاب وجزيرة الغلة؟", "answer": "نعم نغطي الجزر السكنية في أم القيوين. وقت الوصول يعتمد على وسيلة العبور وقد يستغرق 50-60 دقيقة، ويُرجى التنسيق مسبقاً."},
        {"question": "هل الكشف ممكن في يوم الاتصال نفسه؟", "answer": "نعم، في المناطق البرية نوفر الكشف في نفس اليوم مع إمكانية الإصلاح في نفس الزيارة. الجزر تحتاج تنسيقاً مسبقاً."},
        {"question": "لماذا مباني المدينة القديمة تحتاج اهتماماً أكبر؟", "answer": "مبانٍ تزيد على 25-30 سنة بأنابيب معدنية تآكلت من الرطوبة المالحة، وقربها من الخور يعني مياهاً جوفية مالحة وشبكات صرف متقادمة. تحتاج فحصاً أكثر تكراراً وشمولاً."},
        {"question": "كم وقت الاستجابة في أم القيوين؟", "answer": "35-55 دقيقة لجميع المناطق البرية بسبب صغر المساحة الجغرافية للإمارة."},
        {"question": "هل تعملون في العطل الرسمية وأيام الجمعة؟", "answer": "نعم، الخدمة متاحة 24/7 بما في ذلك الجمعة والسبت وجميع العطل الوطنية والأعياد دون رسوم إضافية."},
        {"question": "ما الضمان المقدم على الإصلاح؟", "answer": "ضمان مكتوب موقع من سنة إلى ثلاث سنوات حسب نوع الإصلاح والمواد. في معالجة الرطوبة الجوفية يشمل الضمان فاعلية حاجز الرطوبة خلال فترته."},
        {"question": "ما أكثر أنواع التسربات في أم القيوين؟", "answer": "الأكثر شيوعاً التسرب المنتشر في التربة الرملية دون ظهور على الجدران (35%)، ثم الرطوبة التصاعدية من المياه الجوفية قرب الخور (30%)، ثم تسربات الوصلات المتآكلة بالملوحة (25%)."},
        {"question": "هل أجهزة كشف التسربات تكسر السيراميك؟", "answer": "لا. الكاميرات الحرارية والذبذبات الصوتية لا تكسر البلاط. نحدد نقطة الكسر ثم نفك بلاطة واحدة فقط في مكان العطل الدقيق عند الحاجة."},
        {"question": "ماذا أفعل عند انفجار ماسورة مياه فجأة؟", "answer": "أغلق المحبس الرئيسي فوراً لمنع الغرق، وافصل الكهرباء عن الأجهزة القريبة لتجنب الماس الكهربائي، ثم راسلنا عبر واتساب 0524314370. فريق الطوارئ متاح 24/7."},
        {"question": "هل تقدمون تقارير معتمدة لخفض فواتير المياه؟", "answer": "نعم. نقدم تقارير هندسية توضح سبب التسريب وما تم إصلاحه يمكن تقديمها للاتحاد للماء والكهرباء لتسوية الفاتورة المرتفعة بسبب عطل فني."},
        {"question": "كيف أعالج العفن والرطوبة في الجدران نهائياً؟", "answer": "أوقف مصدر التسريب أولاً، ثم يُكشط الطلاء التالف ويُعالج الجدار بمواد مضادة للفطريات. بعد الجفاف نطبق عزل مائي ثم نعيد الصبغ. الدهان فوق الرطوبة خطأ شائع يعيد المشكلة خلال أسابيع."},
        {"question": "ما أهمية عزل الأسطح في أم القيوين؟", "answer": "العزل المائي والحراري يحمي الخرسانة من التشقق وتجمع مياه الأمطار، ويمنع تسرب المياه للأسقف السفلية، ويقلل الحمل على المكيفات صيفاً."},
        {"question": "كم المدة المتوقعة لإنهاء أعمال الكشف والإصلاح؟", "answer": "الفحص الإلكتروني يستغرق نحو ساعة. الإصلاحات الموضعية البسيطة تتم في نفس اليوم خلال ساعتين. عزل الخزان أو السطح قد يستغرق يوماً إلى يومين لضمان جفاف المواد."},
    ]


def call_meta() -> dict:
    return {
        "call_section_title": "تواصل الآن",
        "call_section_subtitle": "كشف تسربات أم القيوين — واتساب 0524314370",
        "call_section_content": "احجز الكشف الإلكتروني: 500 درهم يصبح 300 درهم بعد خصم 40%",
        "call_section_phone": "",
        "call_section_whatsapp": "971524314370",
    }


def card_meta() -> dict:
    return {
        "post_card_title": "احجز كشف تسربات المياه في أم القيوين",
        "post_card_content": "الكشف الإلكتروني 500 درهم يصبح 300 درهم بعد خصم 40%. واتساب 0524314370 — بدون تكسير مع تقرير فني.",
        "hide__card__callbutton": "on",
        "hide__card__whatsapp": "",
        "whatsapp_chat_mode": "",
        "whatsapp_chat_title": "ركن التطور",
        "whatsapp_chat_message": "مرحباً! أريد حجز كشف تسربات في أم القيوين",
    }


def popover_meta() -> dict:
    return {
        "popover_call_title": "كشف تسربات المياه في أم القيوين",
        "popover_call_content": "راسلنا عبر واتساب لحجز الكشف الإلكتروني بدون تكسير. 500 درهم يصبح 300 درهم بعد خصم 40%.",
        "popover_call_icon": '<i class="fab fa-brands fa-whatsapp"></i>',
    }


def gallery_meta() -> dict:
    return {str(k): v for k, v in MEDIA.items()}


def theme_faqs() -> dict:
    keyed = {}
    for i, item in enumerate(faq_items(), start=1):
        keyed[f"UaFaq{i:05d}"] = item
    return keyed


def service_request_meta() -> dict:
    return {
        "orderservices": "اطلب كشف التسربات في أم القيوين",
        "contentservices": "واتساب 0524314370 — معاينة لتمييز نوع الرطوبة والكشف 300 درهم بعد الخصم.",
        "hide__service__callbutton": "on",
        "hide__service__whatsapp": "",
    }


def rating_default() -> dict:
    return {
        "ratingValue": "4.9",
        "ratingUsers_1": "2",
        "ratingUsers_2": "4",
        "ratingUsers_3": "8",
        "ratingUsers_4": "36",
        "ratingUsers_5": "362",
    }


def schema_service() -> str:
    return json.dumps(
        {
            "priceRange": "300 - 4000 درهم",
            "description": "كشف تسربات المياه في أم القيوين بأجهزة إلكترونية بدون تكسير. الكشف 500 درهم ويصبح 300 درهم بعد خصم 40%.",
            "addressLocality": "أم القيوين",
            "postalCode": "",
            "telephone": "+971524314370",
            "addressCountry": "AE",
            "streetAddress": "",
            "addressRegion": "أم القيوين",
            "areaServed": "أم القيوين",
            "OfferCatalog": "كشف تسربات المياه",
            "identifier": "",
            "additionalType": "",
        },
        ensure_ascii=False,
    )


def video_object() -> str:
    return json.dumps(
        {
            "title_Video": "كشف تسربات المياه – ركن التطور",
            "description": "فيديو ركن التطور الرسمي لأعمال كشف تسربات المياه بدون تكسير.",
            "duration": "",
            "Video_ID": VIDEO_ID,
        },
        ensure_ascii=False,
    )


def article_object() -> str:
    return json.dumps(
        {
            "headline": "شركة كشف تسربات المياه في أم القيوين 2026 – بدون تكسير وضمان مكتوب",
            "description": "دليل خدمة كشف تسربات المياه في أم القيوين بأحدث الأجهزة الإلكترونية بدون تكسير، مع ضمان مكتوب من ركن التطور.",
            "articleBody": "",
        },
        ensure_ascii=False,
    )


def rating_object() -> str:
    return json.dumps(
        {"RatingValue_def": "4.9", "Best_Rating_def": "5", "RatingCount_def": "412"},
        ensure_ascii=False,
    )


NEW_CALL_CSS_JS = """<style id="rukn-page-call-css">
/* Site-wide: hide Call everywhere except the floating FAB. Keep WhatsApp. */
header .nav-cta .btn-call,
#hdr .nav-cta .btn-call,
header a[data-rukn-page-call],
#hdr a[data-rukn-page-call],
#ruknMob > a.btn-call,
a.btn-call:not(.fab-call):not(.fab-btn),
a.--button-call-link-phone,
a.post-card-buttons.-callbutton--post-card,
a.order-services-phonenumber,
a.order-services-button[href^="tel:"],
.-header-call-,
.YC-wigdht-contact-minibox .phonenumber,
.-company-contact-minibox .phonenumber,
.-taxonomy--contact- a[href^="tel:"],
.yc-shortcode--section--contactus a.--button-call-link-phone,
a[data-call="Phone"]:not(.fab-call),
.yc--post--models--post-card a[href^="tel:"],
.-single-parent-flexes--content-bar a[href^="tel:"],
.-callbutton--post-card{display:none!important}
/* 0586634710 is WhatsApp-only — never a Call / tel: button, including FAB */
a.btn-call[href*="586634710"],
a.fab-call[href*="586634710"],
a.--YourColor--phone-button a[href*="586634710"],
a.--button-call-link-phone[href*="586634710"],
a[href="tel:+971586634710"],
a[href="tel:971586634710"],
a[href="tel:0586634710"],
a[href="tel:+971-58-663-4710"]{display:none!important}
/* Font Awesome 6 Free: solid glyphs require weight 900 */
.fa-solid,.fas,.yc-shortcode-features--icon>i,
i.fa-solid,i.fas{font-family:"Font Awesome 6 Free"!important;font-weight:900!important;font-style:normal}
.fa-brands,.fab,i.fa-brands,i.fab{font-family:"Font Awesome 6 Brands"!important;font-weight:400!important;font-style:normal}
</style>
<script>
(function(){
  var WA_ONLY="586634710";
  function digits(s){
    s=String(s||"");
    var out="";
    for(var i=0;i<s.length;i++){
      var c=s.charAt(i);
      if(c>="0"&&c<="9") out+=c;
    }
    return out;
  }
  function isWaOnly(s){return digits(s).indexOf(WA_ONLY)!==-1;}
  function isFab(a){
    if(!a) return false;
    if(a.classList && (a.classList.contains("fab-call")||a.classList.contains("fab-btn"))) return true;
    if(a.closest && a.closest(".--YourColor--phone-button")) return true;
    return false;
  }
  function hideNode(a){
    if(!a) return;
    a.style.setProperty("display","none","important");
    a.setAttribute("hidden","hidden");
    a.setAttribute("aria-hidden","true");
    if((a.getAttribute("href")||"").indexOf("tel:")===0) a.removeAttribute("href");
  }
  function hideCallButtons(){
    var cs=window.RuknCS;
    if(cs && isWaOnly(cs.call_number)){
      cs.call_number="";
      cs.call_show=false;
    }
    window.kayanShowCallButtons=false;
    window.kayanShowFloatingCallButton=true;
    var nodes=document.querySelectorAll("a[href^='tel:'],a.btn-call,a.fab-call,a.fab-btn.fab-call,a.--button-call-link-phone,a[data-call='Phone'],a.-callbutton--post-card,a.order-services-phonenumber");
    for(var i=0;i<nodes.length;i++){
      var a=nodes[i];
      var href=a.getAttribute("href")||"";
      if(isWaOnly(href)){
        hideNode(a);
        if(isFab(a)) window.kayanShowFloatingCallButton=false;
        continue;
      }
      if(isFab(a)) continue;
      hideNode(a);
    }
    var header=document.querySelectorAll("header .nav-cta .btn-call,#hdr .nav-cta .btn-call,header a[data-rukn-page-call],#hdr a[data-rukn-page-call],#ruknMob > a.btn-call,.-header-call-");
    for(var j=0;j<header.length;j++) hideNode(header[j]);
  }
  function run(){ hideCallButtons(); }
  if(document.readyState==="loading") document.addEventListener("DOMContentLoaded",run);
  else run();
  setTimeout(run,200);
  setTimeout(run,800);
  setTimeout(run,2000);
})();
</script>
"""


def sql_escape(s: str) -> str:
    return s.replace("\\", "\\\\").replace("'", "\\'")


def db_query(sql: str):
    return cli(f'db query "{sql}"', approved=True, confirm_write=True)


def upsert_meta(post_id: int, key: str, value: str):
    """Replace a postmeta row. Arrays must already be PHP-serialized."""
    b64 = base64.b64encode(value.encode("utf-8")).decode("ascii")
    del_sql = (
        f"DELETE FROM wp3mdn_postmeta WHERE post_id = {int(post_id)} "
        f"AND meta_key = '{sql_escape(key)}'"
    )
    ins_sql = (
        f"INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) "
        f"VALUES ({int(post_id)}, '{sql_escape(key)}', FROM_BASE64('{b64}'))"
    )
    db_query(del_sql)
    return db_query(ins_sql)


def update_meta_sql(post_id: int, key: str, value: str, serialized: bool = False):
    return upsert_meta(post_id, key, value)


def post_meta_update(post_id: int, key: str, value: str, force: bool = True):
    """Store PHP-serialized arrays via SQL so WordPress does not wrap them twice."""
    return upsert_meta(post_id, key, value)


def image_object() -> dict:
    return {
        "description": "كشف تسربات المياه في أم القيوين بأجهزة إلكترونية بدون تكسير — ركن التطور",
        "contentLocation": "أم القيوين",
    }


def references_text() -> str:
    return "\n".join(
        [
            "الاتحاد للماء والكهرباء https://www.etihadwe.ae",
            "حكومة أم القيوين https://www.uaq.ae",
            "شركة ركن التطور – كشف تسربات المياه https://www.rukn-eltatawer.com/water-leak-detection/",
        ]
    )


def wait_approval_if_needed(result: dict, label: str) -> dict:
    print(label, json.dumps(result, ensure_ascii=False)[:800])
    return result


def insert_video_into_content(content: str) -> str | None:
    if VIDEO_ID in content or "rukn-leak-video" in content:
        return None
    if "[post_call]" in content:
        return content.replace("[post_call]", VIDEO_BLOCK + "\n[post_call]", 1)
    return content.rstrip() + "\n" + VIDEO_BLOCK


def publish_uaq():
    html_path = Path("/workspace/articles/uaq-leak-merged.html")
    html = html_path.read_text(encoding="utf-8")
    # Guard: no tel links
    if re.search(r'href=["\']tel:', html, re.I):
        raise SystemExit("Refusing to publish: tel: link found in merged HTML")
    if "586634710" in html or "568060309" in html:
        raise SystemExit("Refusing to publish: wrong phone found")
    if "dQw4w9WgXcQ" in html:
        raise SystemExit("Refusing to publish: placeholder video found")

    excerpt = (
        "شركة كشف تسربات المياه في أم القيوين من ركن التطور: فحص إلكتروني وحراري بدون تكسير، "
        "تقارير معتمدة، والكشف 500 درهم يصبح 300 درهم بعد خصم 40%."
    )
    print("Updating post 430 content via REST...")
    updated = rest_update_post(
        UAQ_ID,
        content=html,
        excerpt=excerpt,
        title="شركة كشف تسربات المياه في أم القيوين",
    )
    print("REST content id", updated.get("id"), "modified", updated.get("modified"))

    metas = {
        "post__features__data": php_serialize(features_meta()),
        "post__work_steps__data": php_serialize(steps_meta()),
        "post__services__data": php_serialize(services_meta()),
        "post__price_list__data": php_serialize(prices_meta()),
        "faq": php_serialize(faq_items()),
        "yourcolor__faqs": php_serialize(theme_faqs()),
        "post__call_section__data": php_serialize(call_meta()),
        "post__card__data": php_serialize(card_meta()),
        "post__popover__data": php_serialize(popover_meta()),
        "post__service_request__data": php_serialize(service_request_meta()),
        "post_gallery": php_serialize({k: v for k, v in MEDIA.items()}),
        "defualt__rating": php_serialize(rating_default()),
        "YourColor_Service": php_serialize(json.loads(schema_service())),
        "YourColor_Article": php_serialize(json.loads(article_object())),
        "YourColor__VideoObject": php_serialize(json.loads(video_object())),
        "YourColor__Rating": php_serialize(json.loads(rating_object())),
        "VideoID": VIDEO_ID,
        "articon": '<i class="fas fa-solid fa-droplet"></i>',
        "title_post_gallery": "من ميدان العمل في أم القيوين",
        "content_post_gallery": "صور حقيقية من مكتبة وسائط ركن التطور لأعمال كشف التسربات بدون تكسير.",
        "YourColor_ImageObject": php_serialize(image_object()),
        "position__post_card": "bottom_content",
        "hide__card__callbutton": "on",
        "hide__service__callbutton": "on",
        "hide_call_section": "",
        "hide__post_card": "",
        "hide__single__popover": "",
        "hide__sidebar__service_request": "",
        "hide__floating__call": "",
        "phone": "",
        "phone_number": "",
        "phonenumber": "",
        "contact_number": "",
        "memo-meta-phone": "",
        "whatsapp": "+971524314370",
        "whatsapp_number": "+971524314370",
        "references": references_text(),
        "rank_math_title": "شركة كشف تسربات المياه في أم القيوين 2026 | ركن التطور",
        "rank_math_focus_keyword": "شركة كشف تسربات المياه في أم القيوين",
        "rank_math_description": excerpt,
    }
    for key, val in metas.items():
        print("meta", key, "bytes", len(val))
        try:
            wait_approval_if_needed(post_meta_update(UAQ_ID, key, val), f"meta-update {key}")
        except Exception as e:
            print("CLI meta failed, trying SQL for", key, e)
            wait_approval_if_needed(update_meta_sql(UAQ_ID, key, val), f"sql-meta {key}")


def add_videos():
    results = []
    for pid in OTHER_LEAK_IDS:
        post = request(f"{SITE}/wp-json/wp/v2/posts/{pid}?context=edit")
        raw = (post.get("content") or {}).get("raw") or ""
        new = insert_video_into_content(raw)
        if new is None:
            results.append((pid, "already"))
            continue
        b64 = base64.b64encode(new.encode("utf-8")).decode("ascii")
        try:
            out = cli(
                f"post update {pid} --post_content_base64={b64}",
                approved=True,
                confirm_write=True,
            )
            results.append((pid, "cli", out.get("exit_code") or out.get("status") or "ok"))
        except Exception as e:
            print("CLI post update failed", pid, e)
            rest_update_post(pid, content=new)
            results.append((pid, "rest"))
        time.sleep(0.4)
    print("VIDEO RESULTS", results)


def flush_cache():
    for cmd in ["cache flush", "litespeed-purge all"]:
        try:
            print(cmd, cli(cmd, approved=True, confirm_write=True))
        except Exception as e:
            print("cache cmd failed", cmd, e)


def fix_video_widget():
    """Replace Arabic label 'الفيديو' with a real YouTube ID so the sidebar video and theme UI work."""
    widget_val = php_serialize({"VideoID": VIDEO_ID})
    for wid in (10325, 7521):
        print("widget", wid, upsert_meta(wid, "widget_post_meta", widget_val))

    leak_ids = [UAQ_ID] + list(OTHER_LEAK_IDS)
    for pid in leak_ids:
        try:
            upsert_meta(pid, "VideoID", VIDEO_ID)
        except Exception as e:
            print("VideoID post failed", pid, e)

    # Category term 166 = water-leak-detection. Also write videoID (legacy key).
    term_ids = [166]
    try:
        found = cli(
            'db query "SELECT term_id FROM wp3mdn_term_taxonomy WHERE taxonomy=\'category\' AND term_id IN (166,2778)" --skip-column-names'
        )
        print("term ids query", found)
    except Exception as e:
        print("term query failed", e)
    for tid in term_ids:
        for key in ("VideoID", "videoID"):
            b64 = base64.b64encode(VIDEO_ID.encode("utf-8")).decode("ascii")
            try:
                db_query(
                    f"DELETE FROM wp3mdn_termmeta WHERE term_id = {int(tid)} AND meta_key = '{key}'"
                )
                db_query(
                    f"INSERT INTO wp3mdn_termmeta (term_id, meta_key, meta_value) "
                    f"VALUES ({int(tid)}, '{key}', FROM_BASE64('{b64}'))"
                )
                print("term meta", tid, key, "ok")
            except Exception as e:
                print("term meta failed", tid, key, e)


def fix_sitewide_call_and_icons():
    ih = cli("option get ihaf_insert_header").get("stdout") or ""
    start = ih.find('<style id="rukn-page-call-css">')
    end = ih.find('<style id="rukn-lc-css">')
    if start < 0 or end < 0:
        raise SystemExit(f"ihaf markers missing start={start} end={end} len={len(ih)}")
    new = ih[:start] + NEW_CALL_CSS_JS + ih[end:]
    b64 = base64.b64encode(new.encode("utf-8")).decode("ascii")
    print("ihaf new bytes", len(new), "b64", len(b64))
    out = db_query(
        f"UPDATE wp3mdn_options SET option_value = FROM_BASE64('{b64}') "
        f"WHERE option_name = 'ihaf_insert_header'"
    )
    print("ihaf update", out)
    # Theme default is already off; keep it off so card/header/popup PHP skips Call.
    try:
        print("delete kayan_show_call_buttons", cli("option delete kayan_show_call_buttons", approved=True, confirm_write=True))
    except Exception as e:
        print("option delete skipped", e)


if __name__ == "__main__":
    import argparse

    p = argparse.ArgumentParser()
    p.add_argument("--skip-uaq", action="store_true")
    p.add_argument("--skip-videos", action="store_true")
    p.add_argument("--skip-sitewide", action="store_true")
    p.add_argument("--skip-widget", action="store_true")
    args = p.parse_args()
    if not args.skip_uaq:
        publish_uaq()
    if not args.skip_widget:
        fix_video_widget()
    if not args.skip_videos:
        add_videos()
    if not args.skip_sitewide:
        fix_sitewide_call_and_icons()
    flush_cache()
    print("DONE")
