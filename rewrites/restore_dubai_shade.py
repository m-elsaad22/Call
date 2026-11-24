#!/usr/bin/env python3
"""Restore Dubai shade article 6151 to the September 2026 guide body."""
from __future__ import annotations

import base64
import json
import os
import urllib.request
from pathlib import Path

from _php import faqs, features, services, steps

PID = 6151
HTML = Path("/workspace/rewrites/shade/shade-installation-dubai.html").read_text(encoding="utf-8")
URL = "https://www.rukn-eltatawer.com/wp-json/wpvibe/v1/cli/run"


def vibe(command: str, write: bool = False, timeout: int = 180) -> dict:
    user = os.environ.get("WPVIBE_USER", "cursor")
    password = os.environ.get("WPVIBE_PASS", "")
    if not password:
        raise SystemExit("Set WPVIBE_PASS")
    token = base64.b64encode(f"{user}:{password}".encode()).decode()
    body = json.dumps({"command": command, "confirm_write": write}, ensure_ascii=False).encode()
    req = urllib.request.Request(
        URL,
        data=body,
        headers={
            "Authorization": f"Basic {token}",
            "Content-Type": "application/json",
            "User-Agent": "Mozilla/5.0 CursorAgent",
        },
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.loads(r.read().decode())


def sq(value: str) -> str:
    if "'" in value:
        raise ValueError("single quote in meta value")
    return "'" + value + "'"


def main() -> None:
    html = HTML.strip() + "\n"
    if "لائحة المجمّع قبل القصّ" in html:
        raise SystemExit("restore file still has the unique rewrite")
    if "فريق المحتوى الفني" not in html:
        raise SystemExit("restore file missing original byline")
    if "[post_call]" not in html:
        raise SystemExit("missing post_call")
    if "tel:" in html:
        raise SystemExit("tel: in html")
    b64 = base64.b64encode(html.encode("utf-8")).decode("ascii")
    out = vibe(f"post update {PID} --post_content_base64={b64}", write=True)
    stdout = (out.get("stdout") or "") + (out.get("stderr") or "")
    if out.get("exit_code") not in (0, "0", None) and "updated" not in stdout.lower():
        raise RuntimeError(json.dumps(out, ensure_ascii=False)[:800])

    call = {
        "call_section_title": "تواصل معنا الآن",
        "call_section_content": "احجز معاينة مجانية لتركيب مظلات في دبي، أو تواصل واتساب لأسرع استجابة.",
        "call_section_phone": "",
        "call_section_whatsapp": "",
    }
    faq_pairs = [
        ("كم سعر تركيب مظلة سيارة في دبي؟", "السعر يبدأ من 450 درهم لموقف سيارة واحد بخامة HDPE عادية، ويرتفع حسب المساحة ونوع الخامة المختارة."),
        ("كم يستغرق تركيب مظلة حديقة كاملة؟", "المدة المتوقعة هي يوم إلى يومين لمظلة PVC عادية، بينما تحتاج البيرغولا المخصصة من 3 إلى 5 أيام حسب التصميم."),
        ("هل تحتاج المظلات في دبي لترخيص من البلدية؟", "نعم، في بعض المجمعات السكنية والفلل المملوكة ضمن جمعيات ملاك يُشترط الحصول على موافقة قبل التركيب، وفريقنا يساعد العملاء في استكمال هذه الإجراءات عند الحاجة."),
        ("متى يبدأ موسم صيانة المظلات في دبي؟", "أفضل وقت للصيانة الوقائية هو شهري سبتمبر ومارس، أي قبل بداية موسم الصيف الحار وقبل موسم الرياح الشتوية مباشرة."),
        ("لماذا تحتاج مظلة السيارة لصيانة دورية رغم أنها ثابتة؟", "السبب الرئيسي هو تراكم الأتربة والرمال التي تسرّع من تآكل القماش وتضعف مقاومته للأشعة فوق البنفسجية مع مرور الوقت."),
        ("كيف تتم صيانة قماش المظلة المتسخ بشدة؟", "إزالة الأتربة الجافة بفرشاة ناعمة، ثم غسل القماش بمحلول ماء وصابون معتدل، وتجنب المواد الكيميائية القوية."),
    ]
    updates = [
        ("yourcolor__faqs", json.dumps(faqs(faq_pairs), ensure_ascii=True)),
        (
            "post__features__data",
            json.dumps(
                features(
                    "مميزات تركيب المظلات في دبي",
                    "خامات معالجة ضد الأشعة وضمان مكتوب ومعاينة مجانية.",
                    [
                        ("ضمان 5 سنوات", "على الهيكل والتركيب."),
                        ("خامة HDPE/PVC", "معالجة ضد الأشعة فوق البنفسجية."),
                        ("معاينة مجانية", "قبل أي تركيب."),
                        ("استجابة نفس اليوم", "في معظم مناطق دبي."),
                    ],
                ),
                ensure_ascii=True,
            ),
        ),
        (
            "post__work_steps__data",
            json.dumps(
                steps(
                    "خطوات تركيب المظلات في دبي",
                    "من المعاينة المجانية إلى تسليم الضمان.",
                    [
                        ("معاينة ميدانية", "قياس المساحة ونوع الأرضية."),
                        ("اختيار الخامة", "HDPE أو PVC أو ألمنيوم."),
                        ("قواعد خرسانية", "عمق 60 إلى 80 سم."),
                        ("شد القماش والتسليم", "أنظمة شد هوائية وضمان مكتوب."),
                    ],
                ),
                ensure_ascii=True,
            ),
        ),
        (
            "post__services__data",
            json.dumps(
                services(
                    "خدمات التظليل في دبي",
                    "مظلات سيارات وحدائق ومسحوبة وبيرغولا وصيانة قماش.",
                    [
                        ("مظلة سيارات HDPE", "من 450 درهم لموقف واحد."),
                        ("مظلة حديقة PVC", "حماية من الشمس والمطر."),
                        ("بيرغولا ألمنيوم", "حسب المساحة والتصميم."),
                        ("صيانة قماش تالف", "مع الإبقاء على الهيكل."),
                    ],
                ),
                ensure_ascii=True,
            ),
        ),
        ("post__call_section__data", json.dumps(call, ensure_ascii=True)),
        ("last_update", "01-09-2026"),
    ]
    for key, val in updates:
        mout = vibe(f"post meta update {PID} {key} {sq(val)}", write=True)
        if mout.get("exit_code") not in (0, "0", None):
            raise RuntimeError(f"{key}: {json.dumps(mout, ensure_ascii=False)[:800]}")

    live = json.loads(vibe(f"post get {PID} --fields=post_content")["stdout"]).get("post_content") or ""
    print("len", len(live))
    print("byline", "فريق المحتوى الفني" in live)
    print("old_rewrite", "لائحة المجمّع قبل القصّ" in live)
    print("prices", "من 450 إلى 900 درهم" in live)
    print("post_call", "[post_call]" in live)
    if "فريق المحتوى الفني" not in live or "لائحة المجمّع قبل القصّ" in live:
        raise SystemExit("restore verify failed")
    print("RESTORED", PID)


if __name__ == "__main__":
    main()
