#!/usr/bin/env python3
"""Read-only audit: Rank Math titles + phone inventory. Does not write to WordPress."""

from __future__ import annotations

import json
import os
import re
import sys
from collections import Counter, defaultdict

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Standalone WP client bits so this runs even if wp_client.py is absent.
import base64
import urllib.request

WP = "https://www.rukn-eltatawer.com"


def auth_header() -> dict:
    user = os.environ["WP_USER"]
    pw = os.environ["WP_APP_PASS"].replace(" ", "")
    token = base64.b64encode(f"{user}:{pw}".encode()).decode()
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json",
        "User-Agent": "CursorAgent/1.0",
    }


def api_post(path: str, payload: dict, timeout: int = 180):
    req = urllib.request.Request(
        f"{WP}/wp-json/{path.lstrip('/')}",
        data=json.dumps(payload).encode(),
        headers=auth_header(),
        method="POST",
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def cli(command: str) -> dict:
    return api_post("wpvibe/v1/cli/run", {"command": command, "confirm_write": False})


def db_query(sql: str) -> list[dict]:
    # Escape inner double quotes for the WP-CLI wrapper
    wrapped = sql.replace('"', '\\"')
    r = cli(f'db query "{wrapped}" --skip-column-names')
    raw = r.get("stdout") or r.get("output") or ""
    if isinstance(raw, str):
        try:
            data = json.loads(raw)
        except json.JSONDecodeError:
            print("non-json cli", r)
            return []
    else:
        data = raw
    if isinstance(data, dict):
        return data.get("results") or []
    if isinstance(data, list):
        return data
    return []


CITY_PATTERNS = [
    ("أبوظبي", re.compile(r"أبوظبي|ابوظبي|abu-?dhabi|abudhabi", re.I)),
    ("العين", re.compile(r"العين|al-?ain|alain", re.I)),
    ("دبي", re.compile(r"دبي|dubai", re.I)),
    ("الشارقة", re.compile(r"الشارقة|sharjah", re.I)),
    ("عجمان", re.compile(r"عجمان|ajman", re.I)),
    ("رأس الخيمة", re.compile(r"رأس الخيمة|راس الخيمة|ras-?al-?khaimah|\brak\b", re.I)),
    ("الفجيرة", re.compile(r"الفجيرة|fujairah", re.I)),
    ("أم القيوين", re.compile(r"أم القيوين|ام القيوين|umm-?al-?quwain", re.I)),
    ("خورفكان", re.compile(r"خورفكان|khor-?fakkan", re.I)),
    ("الإمارات", re.compile(r"الإمارات|الامارات|\buae\b", re.I)),
]

HOUR_RE = re.compile(r"بالساع[هة]|hourly|maid|عاملات|خادمة|خادمات", re.I)
GARDEN_RE = re.compile(r"تنسيق.?الحدائق|تنسيق حدائق|landscap|حدائق|garden", re.I)
LEAK_RE = re.compile(r"تسرب|leak-?detection|كشف تسرب", re.I)
INSUL_RE = re.compile(r"عزل|insulation|waterproof", re.I)
POOL_RE = re.compile(r"مسبح|مسابح|pool", re.I)
PEST_RE = re.compile(
    r"مكافح|حشر|pest|termite|lizard|snake|pigeon|bird|insect|طارد|حمام|طيور|رمة|وزغ|برص|ثعبان|صراصير",
    re.I,
)
CLEAN_RE = re.compile(r"تنظيف|cleaning", re.I)
RENT_RE = re.compile(r"للإيجار|للايجار|للأيجار")

# Canonical site numbers we already know + extras we may find
KNOWN = {
    "01151481000": "واتساب مصر / الإعلان (المطلوب في عنوان السيو الجديد)",
    "201151481000": "واتساب مصر في الفوتر (نفس الخط)",
    "01556644443": "رقم مصري غريب ظهر في بعض عناوين السيو",
    "0586634710": "واتساب الموقع العام — ممنوع على زر الاتصال",
    "+971586634710": "واتساب الموقع العام",
    "0524314370": "اتصال + واتساب كشف تسربات وعزل",
    "+971524314370": "اتصال + واتساب كشف تسربات وعزل",
    "0521300019": "اتصال + واتساب المسابح",
    "+971521300019": "اتصال + واتساب المسابح",
    "0556190406": "تكييف / كهرباء",
    "+971556190406": "تكييف / كهرباء",
    "0522901095": "تنظيف أبوظبي (ما عدا مسابح وخادمة بالساعة) + مكافحة العين",
    "+971522901095": "تنظيف أبوظبي + مكافحة غير أبوظبي بعد التصحيح",
    "0541673020": "تنظيف إمارات أخرى + خادمة بالساعة أبوظبي",
    "+971541673020": "تنظيف إمارات أخرى + خادمة بالساعة أبوظبي",
    "0524221011": "مكافحة حشرات أبوظبي (20 مقال)",
    "+971524221011": "مكافحة حشرات أبوظبي",
    "0544438542": "عاملات بالساعة دبي — لا يُمس",
    "+971544438542": "عاملات بالساعة دبي",
    "0567925243": "عاملات بالساعة الشارقة — لا يُمس",
    "+971567925243": "عاملات بالساعة الشارقة",
}


def blob_of(row: dict) -> str:
    return " ".join(
        [
            str(row.get("post_title") or ""),
            str(row.get("post_name") or ""),
            str(row.get("seo") or ""),
        ]
    )


def cities_of(blob: str) -> list[str]:
    return [name for name, pat in CITY_PATTERNS if pat.search(blob)]


def services_of(blob: str) -> list[str]:
    out = []
    if LEAK_RE.search(blob):
        out.append("كشف_تسربات")
    if INSUL_RE.search(blob):
        out.append("عزل")
    if POOL_RE.search(blob):
        out.append("مسابح")
    if PEST_RE.search(blob):
        out.append("مكافحة")
    if HOUR_RE.search(blob):
        out.append("بالساعة")
    if GARDEN_RE.search(blob) and not PEST_RE.search(blob):
        out.append("تنسيق_حدائق")
    if CLEAN_RE.search(blob):
        out.append("تنظيف")
    return out


def exclude_reason(row: dict) -> str | None:
    seo = row.get("seo") or ""
    if RENT_RE.search(seo):
        return "عنوان السيو فيه للإيجار أصلًا"
    blob = blob_of(row)
    cities = set(cities_of(blob))
    svcs = set(services_of(blob))
    ad = "أبوظبي" in cities
    ain = "العين" in cities
    dubai = "دبي" in cities
    shj = "الشارقة" in cities

    if "كشف_تسربات" in svcs:
        return "كشف تسربات — كل الإمارات مستثناة"
    if "عزل" in svcs:
        return "عزل — كل الإمارات مستثناة"
    if "مسابح" in svcs and ad:
        return "مسابح في أبوظبي"
    if "مكافحة" in svcs and ad:
        return "مكافحة حشرات في أبوظبي"
    if "بالساعة" in svcs and dubai:
        return "عاملات بالساعة في دبي"
    if "بالساعة" in svcs and shj:
        return "عاملات بالساعة في الشارقة"
    if "تنسيق_حدائق" in svcs and (ad or ain):
        return "تنسيق حدائق في أبوظبي أو العين"
    if "تنظيف" in svcs and ad and "مسابح" not in svcs:
        # pool cleaning AD already caught; remaining AD cleaning excluded
        return "تنظيف في أبوظبي"
    return None


def parse_cli_rows(sql: str) -> list[dict]:
    return db_query(sql)


def main() -> int:
    print("loading posts…")
    posts = parse_cli_rows(
        "SELECT ID AS id, post_title, post_name, post_type FROM wp3mdn_posts "
        "WHERE post_status='publish' AND post_type IN ('post','page')"
    )
    print("posts+pages", len(posts))
    print("loading rank_math_title…")
    titles = parse_cli_rows(
        "SELECT post_id, meta_value AS seo FROM wp3mdn_postmeta WHERE meta_key='rank_math_title'"
    )
    seo_map = {str(r.get("post_id") or r.get("ID")): r.get("seo") or "" for r in titles}
    print("seo metas", len(seo_map))

    rows = []
    for p in posts:
        pid = str(p.get("id") or p.get("ID"))
        row = {
            "id": int(pid),
            "post_title": p.get("post_title") or "",
            "post_name": p.get("post_name") or "",
            "post_type": p.get("post_type") or "post",
            "seo": seo_map.get(pid, ""),
        }
        row["cities"] = cities_of(blob_of(row))
        row["services"] = services_of(blob_of(row))
        row["exclude"] = exclude_reason(row)
        row["would_change"] = row["exclude"] is None and row["post_type"] == "post"
        rows.append(row)

    # Phone inventory via SQL counts
    print("scanning phones…")
    variants = [
        "01151481000",
        "201151481000",
        "01556644443",
        "0586634710",
        "586634710",
        "0524314370",
        "524314370",
        "0521300019",
        "521300019",
        "0556190406",
        "556190406",
        "0522901095",
        "522901095",
        "0541673020",
        "541673020",
        "0524221011",
        "524221011",
        "0544438542",
        "544438542",
        "0567925243",
        "567925243",
    ]
    phone_hits = {}
    for num in variants:
        content = parse_cli_rows(
            "SELECT ID AS id, post_name FROM wp3mdn_posts "
            f"WHERE post_status='publish' AND post_content LIKE '%{num}%'"
        )
        seo = parse_cli_rows(
            "SELECT post_id AS id FROM wp3mdn_postmeta "
            f"WHERE meta_key='rank_math_title' AND meta_value LIKE '%{num}%'"
        )
        meta = parse_cli_rows(
            "SELECT post_id AS id, meta_key FROM wp3mdn_postmeta "
            f"WHERE meta_value LIKE '%{num}%' AND meta_key NOT LIKE '\\_%' "
            "LIMIT 500"
        )
        opt = parse_cli_rows(
            "SELECT option_name FROM wp3mdn_options "
            f"WHERE option_value LIKE '%{num}%' LIMIT 80"
        )
        phone_hits[num] = {
            "content_posts": [{"id": r.get("id"), "slug": r.get("post_name")} for r in content],
            "seo_title_posts": [r.get("id") for r in seo],
            "meta_keys": Counter((r.get("meta_key") or "") for r in meta),
            "options": [r.get("option_name") for r in opt],
            "content_count": len(content),
            "seo_count": len(seo),
        }
        print(f"  {num}: content={len(content)} seo={len(seo)} opt={len(opt)}")

    # SEO title pattern stats
    seo_patterns = Counter()
    for row in rows:
        seo = row["seo"] or ""
        if not seo:
            seo_patterns["(فارغ / Rank Math افتراضي)"] += 1
        elif RENT_RE.search(seo):
            seo_patterns["فيه للإيجار"] += 1
        elif "%title%" in seo:
            seo_patterns["قالب %title%"] += 1
        else:
            seo_patterns["عنوان مكتوب يدوي"] += 1

    would = [r for r in rows if r["would_change"]]
    excluded = [r for r in rows if r["post_type"] == "post" and r["exclude"]]
    pages = [r for r in rows if r["post_type"] == "page"]

    ex_reasons = Counter(r["exclude"] for r in excluded)

    out = {
        "new_seo_template": "%title% 📞 01151481000 📢 الإعلان للإيجار",
        "counts": {
            "published_posts": sum(1 for r in rows if r["post_type"] == "post"),
            "published_pages": len(pages),
            "would_change": len(would),
            "excluded": len(excluded),
            "no_seo_meta": sum(1 for r in rows if r["post_type"] == "post" and not r["seo"]),
        },
        "exclude_reasons": ex_reasons,
        "seo_patterns": seo_patterns,
        "would_change_sample": would[:40],
        "would_change": [
            {
                "id": r["id"],
                "slug": r["post_name"],
                "title": r["post_title"],
                "seo": r["seo"],
                "cities": r["cities"],
                "services": r["services"],
            }
            for r in would
        ],
        "excluded": [
            {
                "id": r["id"],
                "slug": r["post_name"],
                "title": r["post_title"],
                "seo": r["seo"],
                "reason": r["exclude"],
                "cities": r["cities"],
                "services": r["services"],
            }
            for r in excluded
        ],
        "phone_hits": {
            k: {
                "content_count": v["content_count"],
                "seo_count": v["seo_count"],
                "content_slugs": [x["slug"] for x in v["content_posts"][:80]],
                "seo_ids": v["seo_title_posts"][:80],
                "meta_keys": dict(v["meta_keys"]),
                "options": v["options"],
            }
            for k, v in phone_hits.items()
        },
    }

    os.makedirs("/workspace/articles", exist_ok=True)
    with open("/workspace/articles/seo-title-rental-audit.json", "w", encoding="utf-8") as f:
        json.dump(out, f, ensure_ascii=False, indent=2)
    print("wrote json")
    print("counts", out["counts"])
    print("exclude_reasons", dict(ex_reasons))
    print("seo_patterns", dict(seo_patterns))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
