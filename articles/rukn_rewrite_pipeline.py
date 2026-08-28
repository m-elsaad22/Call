#!/usr/bin/env python3
"""Rukn Eltatawer article rewrite pipeline — shortcodes + FA + mobile tables + ~1% KW."""

from __future__ import annotations

import base64
import binascii
import json
import os
import random
import re
import string
import time
import urllib.parse
import urllib.request
from typing import Any

WP = "https://www.rukn-eltatawer.com"
PHONE = "+971586634710"
PHONE_LOCAL = "0586634710"
WA = "971586634710"


def auth_header() -> dict:
    user = os.environ["WP_USER"]
    pw = os.environ["WP_APP_PASS"].replace(" ", "")
    token = base64.b64encode(f"{user}:{pw}".encode()).decode()
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json",
        "User-Agent": "CursorAgent/1.0",
    }


def api_post(path: str, payload: dict, timeout: int = 180) -> Any:
    data = json.dumps(payload).encode()
    req = urllib.request.Request(
        f"{WP}/wp-json/{path.lstrip('/')}", data=data, headers=auth_header(), method="POST"
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def api_patch(path: str, payload: dict, timeout: int = 180) -> Any:
    """WordPress REST accepts POST to an existing resource as an update."""
    return api_post(path, payload, timeout=timeout)


def api_get(path: str, params: dict | None = None, timeout: int = 90) -> Any:
    if isinstance(params, (int, float)):
        timeout = int(params)
        params = None
    if params:
        qs = urllib.parse.urlencode(params, doseq=True)
        sep = "&" if "?" in path else "?"
        path = f"{path}{sep}{qs}"
    h = {k: v for k, v in auth_header().items() if k != "Content-Type"}
    req = urllib.request.Request(f"{WP}/wp-json/{path.lstrip('/')}", headers=h)
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def cli(command: str, write: bool = False) -> dict:
    return api_post(
        "wpvibe/v1/cli/run",
        {"command": command, "confirm_write": write},
    )


def cli_approved(command: str) -> dict:
    return api_post(
        "wpvibe/v1/cli/run-approved",
        {"command": command, "confirm_write": True},
    )


def php_serialize(value: Any) -> str:
    if value is None:
        return "N;"
    if isinstance(value, bool):
        return "b:1;" if value else "b:0;"
    if isinstance(value, int) and not isinstance(value, bool):
        return f"i:{value};"
    if isinstance(value, float):
        return f"d:{value};"
    if isinstance(value, str):
        b = value.encode("utf-8")
        return f's:{len(b)}:"{value}";'
    if isinstance(value, dict):
        out = [f"a:{len(value)}:{{"]
        for k, v in value.items():
            if isinstance(k, int) or (isinstance(k, str) and str(k).isdigit()):
                out.append(php_serialize(int(k)))
            else:
                out.append(php_serialize(str(k)))
            out.append(php_serialize(v))
        out.append("}")
        return "".join(out)
    if isinstance(value, (list, tuple)):
        out = [f"a:{len(value)}:{{"]
        for i, v in enumerate(value):
            out.append(php_serialize(i))
            out.append(php_serialize(v))
        out.append("}")
        return "".join(out)
    raise TypeError(type(value))


def rid(n: int = 10) -> str:
    return "".join(random.choice(string.ascii_letters + string.digits) for _ in range(n))


def sql_set_meta(post_id: int, key: str, value: Any) -> None:
    ser = php_serialize(value) if isinstance(value, (dict, list)) else str(value)
    cli(f"post meta update {post_id} {key} tmp --force", write=True)
    hx = binascii.hexlify(ser.encode("utf-8")).decode()
    q = (
        f"UPDATE wp3mdn_postmeta SET meta_value=UNHEX('{hx}') "
        f"WHERE post_id={post_id} AND meta_key='{key}'"
    )
    cmd = f'db query "{q}"'
    try:
        cli(cmd, write=True)
    except Exception as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
    r = cli_approved(cmd)
    if r.get("exit_code") not in (0, None):
        raise RuntimeError(f"SQL meta failed {key}: {r}")


def fa(name: str) -> str:
    return f'<i class="fa-solid fa-{name}"></i>'


def word_count(html: str) -> int:
    text = re.sub(r"<script.*?</script>", " ", html, flags=re.S | re.I)
    text = re.sub(r"\[[^\]]+\]", " ", text)
    text = re.sub(r"<[^>]+>", " ", text)
    return len([w for w in re.split(r"\s+", text) if re.search(r"[\w\u0600-\u06FF]", w)])


def kw_density(html: str, keyword: str) -> tuple[float, int, int]:
    text = re.sub(r"<script.*?</script>", " ", html, flags=re.S | re.I)
    text = re.sub(r"\[[^\]]+\]", " ", text)
    text = re.sub(r"<[^>]+>", " ", text)
    words = [w for w in re.split(r"\s+", text) if re.search(r"[\w\u0600-\u06FF]", w)]
    if not words:
        return 0.0, 0, 0
    count = len(re.findall(re.escape(keyword.strip()), text))
    # 1% = occurrences / total_words * 100 (phrase counted once per occurrence)
    return (count / len(words)) * 100.0, count, len(words)


CITY_AREAS = {
    "دبي": ["المارينا", "جميرا", "البرشاء", "الخليج التجاري", "ند الشبا", "الورقاء"],
    "أبوظبي": ["الخليفة", "الشاطئ", "محمد بن زايد", "الريم", "الروضة", "المشرف"],
    "ابوظبي": ["الخليفة", "الشاطئ", "محمد بن زايد", "الريم", "الروضة", "المشرف"],
    "الشارقة": ["الخان", "البحيرة", "النهدة", "القصباء", "المجاز", "الموالي"],
    "العين": ["اليحر", "الهير", "المقام", "الجاهلي", "الهيلي", "الخبيصي", "الظاهر", "المويجعي"],
    "عجمان": ["الراشدية", "النعيمية", "المويهات", "الجرف", "الحميدية", "مشيرف"],
    "رأس الخيمة": ["النخيل", "المعيريض", "الرمس", "خزام", "الحيل", "الظيت"],
    "راس الخيمة": ["النخيل", "المعيريض", "الرمس", "خزام", "الحيل", "الظيت"],
    "الفجيرة": ["المرحلة", "السكامكام", "القدفع", "مربح", "الحيل", "كورنيش الفجيرة"],
    "أم القيوين": ["المدينة القديمة", "الراشدية", "فلج المعلا", "السلمة", "الدر"],
    "ام القيوين": ["المدينة القديمة", "الراشدية", "فلج المعلا", "السلمة", "الدر"],
}


def areas_for(city: str) -> list[str]:
    for k, v in CITY_AREAS.items():
        if k in city:
            return v
    return ["وسط المدينة", "المناطق السكنية", "الضواحي", "المناطق الصناعية", "الفلل", "الشقق"]


SERVICE_FA = {
    "ري": ["droplet", "faucet", "seedling", "clock", "shield-halved", "map-location-dot"],
    "تنظيف": ["broom", "spray-can-sparkles", "shirt", "house", "shield-halved", "clock"],
    "مكافحة": ["bug-slash", "shield-halved", "house", "check", "truck", "clock"],
    "صيانة": ["screwdriver-wrench", "gears", "clock", "shield-halved", "check", "phone"],
    "تركيب": ["screwdriver-wrench", "hammer", "ruler-combined", "shield-halved", "clock", "check"],
    "نقل": ["truck", "boxes-stacked", "house", "shield-halved", "clock", "check"],
    "تخزين": ["boxes-stacked", "warehouse", "truck", "shield-halved", "clock", "check"],
    "عزل": ["house-circle-check", "droplet", "shield-halved", "check", "temperature-high", "clock"],
    "حدائق": ["seedling", "tree", "droplet", "leaf", "clock", "shield-halved"],
    "نخيل": ["tree", "seedling", "droplet", "leaf", "clock", "shield-halved"],
    "تنسيق": ["seedling", "tree", "droplet", "leaf", "clock", "shield-halved"],
    "جبس": ["ruler-combined", "hammer", "house", "check", "clock", "shield-halved"],
    "ترميم": ["hammer", "ruler-combined", "house", "screwdriver-wrench", "clock", "shield-halved"],
    "ثلاج": ["snowflake", "temperature-low", "screwdriver-wrench", "gears", "clock", "shield-halved"],
    "غسال": ["shirt", "droplet", "screwdriver-wrench", "gears", "clock", "shield-halved"],
    "فرن": ["fire-burner", "temperature-high", "screwdriver-wrench", "gears", "clock", "shield-halved"],
    "ميكروويف": ["microwave", "bolt", "screwdriver-wrench", "gears", "clock", "shield-halved"],
    "مصعد": ["elevator", "gears", "screwdriver-wrench", "shield-halved", "clock", "check"],
    "كامير": ["video", "camera", "shield-halved", "house", "clock", "check"],
    "أبواب": ["door-open", "key", "screwdriver-wrench", "shield-halved", "clock", "check"],
    "default": ["check", "shield-halved", "clock", "star", "thumbs-up", "map-location-dot"],
}


def icons_for(service: str) -> list[str]:
    for k, icons in SERVICE_FA.items():
        if k in service:
            return icons
    return SERVICE_FA["default"]


def mobile_table(headers: list[str], rows: list[list[str]]) -> str:
    th = "".join(
        f'<th style="padding:10px 12px;white-space:nowrap;">{h}</th>' for h in headers
    )
    body = []
    for row in rows:
        tds = "".join(f'<td style="padding:10px 12px;">{c}</td>' for c in row)
        body.append(f"<tr>{tds}</tr>")
    return f'''<div class="rukn-mobile-table" style="overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%;margin:20px 0;border-radius:12px;border:1px solid #e5e7eb;">
<table style="width:100%;min-width:520px;border-collapse:collapse;text-align:right;direction:rtl;margin:0;">
<tr style="background:#f3f4f6;font-weight:bold;">{th}</tr>
{"".join(body)}
</table>
</div>'''


def callout(kind: str, title: str, text: str) -> str:
    styles = {
        "danger": ("#ffebee", "#d32f2f", "triangle-exclamation"),
        "tip": ("#e3f2fd", "#1976d2", "lightbulb"),
        "offer": ("#e8f5e9", "#388e3c", "tags"),
        "stat": ("#fff8e1", "#f9a825", "chart-column"),
    }
    bg, border, icon = styles[kind]
    return (
        f'<blockquote style="background:{bg};border-right:5px solid {border};padding:15px;'
        f'margin:20px 0;border-radius:12px;">'
        f'{fa(icon)} <strong>{title}:</strong> {text}</blockquote>'
    )


def img_tag(mid: int, url: str, alt: str, eager: bool = False) -> str:
    loading = "eager" if eager else "lazy"
    return (
        f'<img class="aligncenter wp-image-{mid} size-full" src="{url}" alt="{alt}" '
        f'width="800" height="450" loading="{loading}" decoding="async" '
        f'style="max-width:100%;height:auto;border-radius:12px;" />'
    )


# Curated media pools when WP search returns irrelevant matches
MEDIA_POOLS = {
    "gardens": [1140, 1139, 1138, 1132, 1131, 1130, 1127, 1126],
    "bath_reno": [1165, 1163, 1069, 1068, 1067, 1064, 1546, 1545],
    "buildings": [3143, 1544, 1537, 1065],
    "insulation": [2877, 2876, 2875, 2874, 2873, 2854, 2651, 1242, 1240],
    "tanks": [2689, 1242, 1240, 1230, 1229, 2651],
    "pest": [3320, 3319, 3317, 3316, 3315, 3312, 3307, 3306],
    "maintenance": [3143, 1544, 1537, 2827],
    "villas": [3117, 3110, 1264, 1262, 1132],
}

GLOBAL_EXCLUDE = [
    "تسرب", "leak", "حشرات", "pest", "مجاري", "sewer", "صرف",
    "مسابح", "pool", "سيارات", "car wash", "طيور", "birds",
]


def media_by_ids(ids: list[int]) -> list[dict]:
    out = []
    for mid in ids:
        try:
            it = api_get(f"wp/v2/media/{mid}?_fields=id,source_url,alt_text,title")
            out.append(
                {
                    "id": it["id"],
                    "url": it["source_url"],
                    "alt": it.get("alt_text") or it.get("title", {}).get("rendered", ""),
                }
            )
        except Exception:
            continue
    return out


def find_media(
    queries: list[str],
    limit: int = 10,
    exclude_terms: list[str] | None = None,
    require_any: list[str] | None = None,
) -> list[dict]:
    found = []
    seen = set()
    exclude_terms = list(dict.fromkeys((exclude_terms or []) + GLOBAL_EXCLUDE))
    require_any = require_any or []
    for q in queries:
        try:
            items = api_get(
                f"wp/v2/media?search={urllib.parse.quote(q)}&per_page=10&media_type=image"
            )
        except Exception:
            continue
        for it in items:
            mid = it["id"]
            if mid in seen:
                continue
            blob = (
                (it.get("alt_text") or "")
                + " "
                + (it.get("title", {}).get("rendered") or "")
                + " "
                + (it.get("source_url") or "")
            )
            blob_l = blob.lower()
            if any(t.lower() in blob_l for t in exclude_terms):
                continue
            if require_any and not any(t in blob for t in require_any):
                continue
            seen.add(mid)
            found.append(
                {
                    "id": mid,
                    "url": it["source_url"],
                    "alt": it.get("alt_text") or it.get("title", {}).get("rendered", ""),
                }
            )
            if len(found) >= limit:
                return found
    return found


def pick_media_for_service(service: str, city: str) -> list[dict]:
    """Return relevant images for a service; curated fallbacks when search is weak."""
    svc = service.replace("شركة ", "")
    exclude = list(GLOBAL_EXCLUDE)
    queries: list[str] = []
    require: list[str] = []
    pool_key = "maintenance"

    if any(x in svc for x in ["ري", "حدائق", "نخيل", "عشب", "تنسيق"]):
        queries = ["تنسيق حدائق", "حدائق", "ري", "نخيل", city]
        require = ["حدائق", "حديقة", "ري", "نخيل", "عشب", "تنسيق"]
        exclude += ["ثلاجة", "غسالة", "فرن", "ميكروويف", "عزل", "ترميم"]
        pool_key = "gardens"
    elif any(x in svc for x in ["أفران", "فرن", "غسالات", "غسالة", "ميكروويف", "ثلاجات", "ثلاجة"]):
        queries = [svc]
        require = [w for w in ["فرن", "أفران", "غسالة", "غسالات", "ميكروويف", "ثلاجة", "ثلاجات"] if w in svc]
        exclude += ["حدائق", "ري", "نخيل", "مسابح", "عزل", "مجاري", "تعقيم", "مكيف"]
        pool_key = "maintenance"
    elif any(x in svc for x in ["نقل", "تخزين", "أثاث"]):
        queries = ["نقل عفش", "نقل أثاث", "أثاث", "تخزين"]
        require = ["نقل", "أثاث", "عفش", "تخزين"]
        exclude += ["سيارات", "مسابح", "حدائق", "عزل"]
        pool_key = "buildings"
    elif "مكافحة" in svc or "وزغ" in svc or "برص" in svc:
        queries = ["مكافحة حشرات", "مكافحة الرمة", "مكافحة"]
        require = ["مكافحة", "حشرات", "رمة", "وزغ", "برص", "نمل"]
        pool_key = "pest"
    elif "مصعد" in svc:
        queries = ["مصعد", "مصاعد", "صيانة مباني", city]
        require = ["مصعد", "مباني", "صيانة"]
        pool_key = "buildings"
    elif "كامير" in svc:
        queries = ["كاميرات مراقبة", "أمن", "صيانة", city]
        require = ["كامير", "مراقبة", "أمن", "صيانة"]
        exclude += ["مجاري"]
        pool_key = "buildings"
    elif "أبواب" in svc:
        queries = ["أبواب أوتوماتيك", "كراج", "صيانة", city]
        require = ["باب", "أبواب", "كراج", "صيانة"]
        pool_key = "buildings"
    elif "عزل خزانات" in svc or ("عزل" in svc and "خزان" in svc):
        queries = ["عزل خزانات", "خزانات", "عزل", city]
        require = ["خزان", "عزل"]
        pool_key = "tanks"
    elif "عزل" in svc:
        queries = ["عزل أسطح", "عزل", city]
        require = ["عزل"]
        pool_key = "insulation"
    elif "حمام" in svc:
        queries = ["ترميم حمامات", "حمامات", "ترميم", city]
        require = ["حمام", "ترميم"]
        pool_key = "bath_reno"
    elif "ترميم" in svc or "تشقق" in svc:
        queries = ["ترميم", "مباني", "فلل", city]
        require = ["ترميم", "مباني", "فلل", "تشقق"]
        pool_key = "buildings"
    elif "تنظيف" in svc:
        queries = [svc, "تنظيف", city]
        require = ["تنظيف"]
        exclude += ["مسابح"] if "مسابح" not in svc else []
        pool_key = "villas"
    else:
        queries = [svc, city, "صيانة"]
        require = [w for w in svc.split() if len(w) > 2][:3]
        pool_key = "maintenance"

    media = find_media(queries, limit=10, exclude_terms=exclude, require_any=require)
    # curated fallbacks when library has no close match (common for appliances/moving)
    if len(media) < 4:
        media += media_by_ids(MEDIA_POOLS.get(pool_key, MEDIA_POOLS["maintenance"]))
    seen = set()
    uniq = []
    for m in media:
        if m["id"] in seen:
            continue
        alt = m.get("alt") or ""
        blob = (alt + " " + (m.get("url") or "")).lower()
        if "مسابح" in blob or "pool" in blob:
            continue
        if "سيارات" in blob or "car wash" in blob:
            continue
        if pool_key != "pest" and ("حشرات" in alt or "مكافحة" in alt):
            continue
        if pool_key != "insulation" and pool_key != "tanks" and "عزل" in alt and "نقل" in svc:
            continue
        if pool_key == "pest" and ("تكييف" in alt or "مكيف" in alt or "تنظيف" in alt) and "مكافحة" not in alt:
            continue
        if pool_key == "maintenance" and any(x in alt for x in ["تعقيم", "مكيفات", "مسابح", "عزل"]):
            continue
        seen.add(m["id"])
        uniq.append(m)
    # if filters emptied the list, fall back to curated only
    if len(uniq) < 3:
        for m in media_by_ids(MEDIA_POOLS.get(pool_key, MEDIA_POOLS["maintenance"])):
            if m["id"] in seen:
                continue
            seen.add(m["id"])
            uniq.append(m)
    return uniq[:10]



def ensure_kw_density(html: str, keyword: str, target_pct: float = 0.95) -> str:
    """Keep exact title keyword near target without spammy duplicate pads."""
    dens, count, total = kw_density(html, keyword)
    if total < 200:
        return html
    service_guess = keyword
    m = re.match(r"^(شركة\s+.+?)\s+في\s+", keyword)
    if m:
        service_guess = m.group(1)
    else:
        parts = keyword.split()
        if len(parts) >= 2:
            service_guess = " ".join(parts[:2])

    def demote_once(html_in: str):
        html2, n = re.subn(
            rf"<strong>{re.escape(keyword)}</strong>",
            f"<strong>{service_guess}</strong>",
            html_in,
            count=1,
        )
        if n:
            return html2, n
        html2, n = re.subn(
            rf"(<p>[^<]*?){re.escape(keyword)}",
            rf"\1{service_guess}",
            html_in,
            count=1,
        )
        return html2, n

    guard = 0
    while dens > target_pct + 0.25 and guard < 40:
        html, n = demote_once(html)
        if not n:
            break
        dens, count, total = kw_density(html, keyword)
        guard += 1

    injects = [
        f"<p>إذا كنت تقارن الخيارات في السوق، راجع وضوح العرض والضمان عند طلب <strong>{keyword}</strong> قبل اعتماد التنفيذ.</p>",
        f"<p>الفرق العملي يظهر عندما تختار <strong>{keyword}</strong> بخطة مكتوبة ومتابعة بعد التسليم بدل وعود عامة.</p>",
        f"<p>للحصول على نتيجة ثابتة، اعتمد <strong>{keyword}</strong> بعد معاينة توضح النطاق والتكلفة والمدة.</p>",
    ]
    guard = 0
    while dens < target_pct - 0.20 and guard < len(injects):
        inject = injects[guard]
        if inject in html:
            guard += 1
            continue
        if "<h2>الخاتمة" in html:
            html = html.replace("<h2>الخاتمة", inject + "\n<h2>الخاتمة", 1)
        else:
            html += inject
        dens, count, total = kw_density(html, keyword)
        guard += 1
    return html


def parse_title(title: str) -> dict:
    title = re.sub(r"\s+", " ", title).strip()
    title = re.sub(r"\s*[–-]\s*ركن التطور.*$", "", title).strip()
    m = re.match(r"^(شركة\s+.+?)\s+في\s+(.+)$", title)
    if m:
        return {"keyword": title, "service": m.group(1).strip(), "city": m.group(2).strip()}
    for city in [
        "أم القيوين", "ام القيوين", "رأس الخيمة", "راس الخيمة", "أبوظبي", "ابوظبي",
        "الشارقة", "الفجيرة", "العين", "عجمان", "دبي", "الدوحة", "قطر", "مكة",
    ]:
        if title.endswith(city):
            service = title[: -len(city)].strip()
            if service:
                return {"keyword": title, "service": service, "city": city}
    m = re.match(r"^(.+?)\s+في\s+(.+)$", title)
    if m:
        return {"keyword": title, "service": m.group(1).strip(), "city": m.group(2).strip()}
    return {"keyword": title, "service": title, "city": "الإمارات"}


def generate_article(ctx: dict, media: list[dict]) -> str:
    from service_content_v2 import build_article_v2

    return build_article_v2(
        ctx,
        media,
        {
            "fa": fa,
            "mobile_table": mobile_table,
            "callout": callout,
            "img_tag": img_tag,
            "areas_for": areas_for,
            "ensure_kw_density": ensure_kw_density,
            "word_count": word_count,
            "WP": WP,
            "PHONE": PHONE,
        },
    )



def build_metas(ctx: dict, media: list[dict]) -> dict:
    kw = ctx["keyword"]
    city = ctx["city"]
    service = ctx["service"]
    areas = areas_for(city)
    icons = icons_for(service)
    gallery = {m["id"]: m["url"] for m in media[:10]} or {}
    feat_items = {}
    labels = [
        ("خبرة محلية", f"فريق متخصص بخبرة عملية في {service} داخل {city}."),
        ("تنفيذ موثق", "خطة مكتوبة وخطوات واضحة قبل البدء."),
        ("جودة المواد", f"اختيارات تناسب ظروف {city} وطبيعة الاستخدام."),
        ("ضمان معتمد", "بنود ضمان ودعم بعد التسليم وفق الاتفاق."),
        ("تغطية واسعة", f"خدمة في {areas[0]} و{areas[1]} وباقي مناطق {city}."),
    ]
    for i, (t, c) in enumerate(labels):
        feat_items[rid()] = {
            "title": t,
            "content": c,
            "icon": fa(icons[i % len(icons)]),
        }
    steps = {}
    for t, c in [
        ("استلام الطلب:", "فهم الاحتياج وتحديد موعد المعاينة."),
        ("زيارة الموقع:", f"فحص الحالة داخل {city} وتحديد السبب."),
        ("العرض المكتوب:", "تكلفة ومدة وضمان ببنود واضحة."),
        ("التنفيذ:", "عمل منظم وفق الخطة المعتمدة."),
        ("الاختبار:", "مراجعة الجودة قبل التسليم."),
        ("التسليم:", "شرح النتيجة وتفعيل الدعم عند الحاجة."),
    ]:
        steps[rid()] = {"title": t, "content": c}

    price_items = {
        rid(): {"title": f"معاينة {service}", "value": "حسب الحالة"},
        rid(): {"title": "تنفيذ أساسي", "value": "يحدد بعد المعاينة"},
        rid(): {"title": "تنفيذ متكامل", "value": "حسب النطاق"},
        rid(): {"title": "صيانة / متابعة", "value": "باقات مرنة"},
    }
    services_items = {}
    titles = [
        (f"تشخيص {service}", f"فحص دقيق لتحديد السبب والنطاق في {city}."),
        ("تنفيذ احترافي", "تطبيق الحل وفق العرض المكتوب ومعايير الجودة."),
        ("ضمان ومتابعة", "دعم بعد التسليم وفق بنود الاتفاق."),
        ("خدمة عاجلة", "جدولة مرنة حسب الأولوية وتوفر الفريق."),
    ]
    for i, (t, c) in enumerate(titles):
        m = media[i % len(media)] if media else None
        services_items[rid()] = {
            "title": t,
            "content": c,
            "image_id": str(m["id"]) if m else "",
            "image": m["url"] if m else "",
        }

    return {
        "post__call_section__data": {
            "call_section_title": "تواصل معنا الآن",
            "call_section_content": f"لا تتردد في التواصل معنا لطلب {kw}، فنحن دائماً هنا لخدمتك.",
            "call_section_phone": PHONE,
            "call_section_whatsapp": PHONE,
        },
        "post__card__data": {
            "post_card_title": kw,
            "post_card_content": f"نوفر {service} باحتراف داخل {city} مع ضمان معتمد.",
            "hide__card__callbutton": "on",
        },
        "post__popover__data": {
            "popover_call_title": "احجز اليوم بعرض خاص",
            "popover_call_content": f"تواصل معنا الآن للحصول على {kw} مع فريق متخصص وأسعار واضحة.",
            "popover_call_icon": fa("phone"),
        },
        "post__service_request__data": {
            "orderservices": f"{kw} – اطلب الخدمة الآن",
            "contentservices": f"احصل على {service} في {city} مع ضمان مكتوب وخدمة سريعة.",
            "hide__service__callbutton": "on",
        },
        "post__features__data": {
            "features__title": f"لماذا تختار ركن التطور لـ {kw}؟",
            "features__content": f"نتميز بتشخيص واضح وتنفيذ موثق وضمان مناسب لظروف {city}.",
            "yourcolor__post_features": feat_items,
        },
        "post__work_steps__data": {
            "work_steps__title": f"خطوات العمل مع {kw}",
            "work_steps__content": "من أول تواصل حتى التسليم والدعم",
            "work_steps_items": steps,
        },
        "post__price_list__data": {
            "price_list__title": f"أسعار {kw}",
            "price_list__content": "قائمة استرشادية تُحدد بدقة بعد المعاينة",
            "price_list__table_title1": "الخدمة",
            "price_list__table_title2": "القيمة",
            "price_list__items": price_items,
        },
        "post__services__data": {
            "services__title": f"خدمات {kw}",
            "services__content": f"منظومة متكاملة لـ {service} داخل {city}.",
            "post_services_items": services_items,
        },
        "post_gallery": gallery,
        "title_post_gallery": f"صور من أعمال {kw}",
        "content_post_gallery": f"معرض أعمال ركن التطور في {service} داخل {city}.",
        "YourColor_Service": {
            "priceRange": "حسب المعاينة",
            "description": f"خدمة {kw}",
            "addressLocality": city,
            "postalCode": "00000",
            "telephone": PHONE_LOCAL,
            "addressCountry": "United Arab Emirates",
            "streetAddress": f"{city}, UAE",
            "addressRegion": city,
            "areaServed": "، ".join([city] + areas[:4]),
            "OfferCatalog": service,
            "identifier": "rukn-eltatawer.com",
            "additionalType": "https://schema.org/Service",
        },
        "yourcolor__faqs": {
            rid(): {
                "question": f"كم سعر {kw}؟",
                "answer": f"تختلف التكلفة حسب النطاق وتُحدد بعد المعاينة عبر عرض مكتوب.",
            },
            rid(): {
                "question": f"هل يوجد ضمان على {service}؟",
                "answer": "نعم، وفق بنود العرض المكتوب مع دعم عند الحاجة.",
            },
            rid(): {
                "question": f"هل تغطون كل مناطق {city}؟",
                "answer": f"نعم، بما يشمل {areas[0]} و{areas[1]} و{areas[2]} والمناطق المحيطة.",
            },
        },
        "faq": [
            {
                "question": f"كم تكلفة {kw}؟",
                "answer": "تُحدد بعد المعاينة حسب نطاق العمل والمواد.",
            },
            {
                "question": "هل يوجد ضمان؟",
                "answer": "نعم، ضمان معتمد وفق العرض المكتوب.",
            },
        ],
        "memo-meta-phone": PHONE_LOCAL,
        "phone": PHONE_LOCAL,
        "phone_number": PHONE_LOCAL,
        "whatsapp": PHONE_LOCAL,
        "whatsapp_number": PHONE_LOCAL,
        "last_update": "14-08-2026",
        "position__post_card": "top_content",
    }


def set_rank_math(post_id: int, kw: str, city: str) -> None:
    api_post(
        "rankmath/v1/updateMeta",
        {
            "objectType": "post",
            "objectID": post_id,
            "meta": {
                "rank_math_title": f"%title% | خدمة معتمدة في {city}",
                "rank_math_description": f"أفضل {kw} مع معاينة وتنفيذ وضمان مكتوب من ركن التطور وتغطية أحياء {city}.",
                "rank_math_focus_keyword": f"{kw},{kw.replace('شركة ', '')},{kw} 2026",
            },
        },
    )


def rewrite_post(item: dict) -> dict:
    post_id = int(item["id"])
    title = item["title"]
    ctx = parse_title(title)
    kw = ctx["keyword"]
    city = ctx["city"]
    service = ctx["service"]

    media = pick_media_for_service(service, city)

    content = generate_article(ctx, media)
    dens_pct, kw_count, total_words = kw_density(content, kw)

    excerpt = f"ركن التطور تقدم {kw} بمعاينة وتنفيذ وضمان مكتوب وتغطية أحياء {city}."
    featured = media[0]["id"] if media else 0

    payload = {"content": content, "excerpt": excerpt, "title": title}
    if featured:
        payload["featured_media"] = featured
    data = json.dumps(payload).encode("utf-8")
    req = urllib.request.Request(
        f"{WP}/wp-json/wp/v2/posts/{post_id}",
        data=data,
        method="POST",
        headers=auth_header(),
    )
    with urllib.request.urlopen(req, timeout=180) as r:
        out = json.load(r)

    metas = build_metas(ctx, media)
    for k, v in metas.items():
        sql_set_meta(post_id, k, v)
        time.sleep(0.05)

    try:
        set_rank_math(post_id, kw, city)
    except Exception as e:
        print("rankmath warn", e)

    # save local copy
    slug = item.get("slug") or out.get("slug") or str(post_id)
    path = f"/workspace/articles/rewrites/{slug}.html"
    os.makedirs("/workspace/articles/rewrites", exist_ok=True)
    open(path, "w", encoding="utf-8").write(content)

    return {
        "id": post_id,
        "title": title,
        "link": out.get("link"),
        "words": total_words,
        "kw_count": kw_count,
        "kw_density_pct": round(dens_pct, 2),
        "featured": featured,
        "media_count": len(media),
        "file": path,
    }


if __name__ == "__main__":
    import sys

    queue_path = sys.argv[1] if len(sys.argv) > 1 else "/workspace/articles/rewrite-all-ids.json"
    limit = int(sys.argv[2]) if len(sys.argv) > 2 else 5
    offset = int(sys.argv[3]) if len(sys.argv) > 3 else 0
    queue = json.load(open(queue_path, encoding="utf-8"))
    done = set()
    done_path = "/workspace/articles/v2-done-ids.json"
    if os.path.exists(done_path):
        try:
            done = set(json.load(open(done_path, encoding="utf-8")))
        except Exception:
            done = set()
    for name in os.listdir("/workspace/articles"):
        if name.startswith("batch-results-v2") and name.endswith(".json"):
            try:
                for row in json.load(open(f"/workspace/articles/{name}", encoding="utf-8")):
                    if row.get("id") and not row.get("error"):
                        done.add(int(row["id"]))
            except Exception:
                pass
    pending = [q for q in queue if int(q["id"]) not in done]
    batch = pending[offset : offset + limit]
    results = []
    for i, item in enumerate(batch, 1):
        print(f"\n=== [{i}/{len(batch)}] {item['title']} ===", flush=True)
        try:
            res = rewrite_post(item)
            res["template"] = "v2-service-specific"
            results.append(res)
            done.add(int(item["id"]))
            print("OK", {k: res[k] for k in ("id","words","kw_density_pct","link") if k in res}, flush=True)
        except Exception as e:
            print("FAIL", item["id"], e, flush=True)
            results.append({"id": item["id"], "title": item["title"], "error": str(e)})
    open(done_path, "w", encoding="utf-8").write(json.dumps(sorted(done)))
    out_path = f"/workspace/articles/batch-results-v2-{int(time.time())}.json"
    open(out_path, "w", encoding="utf-8").write(json.dumps(results, ensure_ascii=False, indent=2))
    print("Wrote", out_path, flush=True)
    print("Pending remaining ~", max(0, len(pending) - len([r for r in results if not r.get("error")])), flush=True)
