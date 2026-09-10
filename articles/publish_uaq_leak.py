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
        ("UaFeatFLIR", "كاميرا حرارية FLIR E96", "تميّز بين رطوبة تسرب الأنابيب الدافئة ورطوبة المياه الجوفية المالحة الباردة — تمييز حاسم في أم القيوين المحاطة بالمياه.", '<i class="fa-solid fa-satellite-dish"></i>'),
        ("UaFeatHydr", "هيدروفون AT-407", "يحدد المصدر الحقيقي للتسرب بدقة ±10 سم — في التربة الرملية يحدد نقطة الكسر بعيداً عن مظهر الرطوبة الخادع.", '<i class="fa-solid fa-headphones"></i>'),
        ("UaFeatGPR1", "رادار الأرض GPR", "يتتبع انتشار المياه المتسربة في التربة الرملية ويحدد نطاق الضرر الفعلي لتقدير حجم المشكلة الحقيقية.", '<i class="fa-solid fa-chart-column"></i>'),
        ("UaFeatNitr", "اختبار ضغط النيتروجين", "يؤكد أن المصدر من الأنابيب وليس من المياه الجوفية أو التسلل الساحلي.", '<i class="fa-solid fa-wind"></i>'),
        ("UaFeatCam1", "كاميرا الفحص الداخلي", "ترى تآكل الأنابيب الداخلي من الرطوبة المالحة في مباني أم القيوين التاريخية.", '<i class="fa-solid fa-camera"></i>'),
        ("UaFeatMois", "جهاز قياس الرطوبة الرقمي", "يفرّق بين رطوبة تسرب الأنابيب والرطوبة الجوية المالحة والرطوبة الجوفية.", '<i class="fa-solid fa-ruler"></i>'),
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


def services_meta() -> dict:
    img = "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-ae.webp"
    items = [
        ("UaSrvBath", "كشف تسربات الحمامات", "فحص الأنابيب والتوصيلات بضغط النيتروجين والجهاز الصوتي دون تكسير السيراميك.", "10517", img),
        ("UaSrvTank", "كشف تسربات الخزانات الأرضية", "فحص الهيكل الداخلي والعزل الإيبوكسي لمنع تهريب الخزان وهبوط الأساسات.", "10517", img),
        ("UaSrvRoof", "فحص الأسطح المبلطة", "كاميرا حرارية لرصد فجوات العزل وتجمعات مياه الأمطار تحت السيراميك.", "1671", "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-sharjah-1.webp"),
        ("UaSrvWall", "معالجة الرطوبة والرشح", "إيقاف المصدر ثم تجفيف الجدار وعزله وترميم الأصباغ بدل الدهان فوق العفن.", "10517", "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-ae-1.webp"),
        ("UaSrvChem", "إصلاح بدون تكسير بالحقن الكيميائي", "حقن إيبوكسي داخل المواسير لإغلاق الشقوق من الداخل أو فك بلاطة واحدة بعد التحديد الدقيق.", "1671", "https://www.rukn-eltatawer.com/wp-content/uploads/2021/08/leak-detection-sharjah-1.webp"),
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


def call_meta() -> str:
    return json.dumps(
        {
            "call_section_title": "تواصل الآن",
            "call_section_subtitle": "كشف تسربات أم القيوين — واتساب فقط 0524314370",
            "call_section_content": "احجز الكشف الإلكتروني: 500 درهم يصبح 300 درهم بعد خصم 40%",
            "call_section_phone": "",
            "call_section_whatsapp": "971524314370",
        },
        ensure_ascii=False,
    )


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


def sql_escape(s: str) -> str:
    return s.replace("\\", "\\\\").replace("'", "\\'")


def update_meta_sql(post_id: int, key: str, value: str, serialized: bool = False):
    stored = value if not serialized else value
    sql = (
        f"UPDATE wp3mdn_postmeta SET meta_value = '{sql_escape(stored)}' "
        f"WHERE post_id = {post_id} AND meta_key = '{key}'"
    )
    out = cli(f'db query "{sql}"', approved=True, confirm_write=True)
    return out


def post_meta_update(post_id: int, key: str, value: str, force: bool = True):
    force_flag = " --force" if force else ""
    # wrap value in single quotes; escape inner singles
    safe = value.replace("'", "'\\''")
    cmd = f"post meta update {post_id} {key} '{safe}'{force_flag}"
    return cli(cmd, approved=True, confirm_write=True)


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
        "post__call_section__data": call_meta(),
        "YourColor_Service": schema_service(),
        "YourColor_Article": article_object(),
        "YourColor__VideoObject": video_object(),
        "YourColor__Rating": rating_object(),
        "VideoID": VIDEO_ID,
        "phone": "+971524314370",
        "phone_number": "+971524314370",
        "contact_number": "+971524314370",
        "memo-meta-phone": "+971524314370",
        "whatsapp": "+971524314370",
        "whatsapp_number": "+971524314370",
        "rank_math_focus_keyword": "شركة كشف تسربات المياه في أم القيوين",
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


if __name__ == "__main__":
    import argparse

    p = argparse.ArgumentParser()
    p.add_argument("--skip-uaq", action="store_true")
    p.add_argument("--skip-videos", action="store_true")
    args = p.parse_args()
    if not args.skip_uaq:
        publish_uaq()
    if not args.skip_videos:
        add_videos()
    flush_cache()
    print("DONE")
