#!/usr/bin/env python3
"""Pool Call+WhatsApp +971521300019, SEO with 0521300019, confirm leak/insulation 0524314370."""

from __future__ import annotations

import json
import re
from urllib.error import HTTPError

from rukn_rewrite_pipeline import api_get, api_patch, cli, cli_approved

LEAK_CALL = "+971524314370"
POOL_TEL = "+971521300019"
POOL_LOCAL = "0521300019"
POOL_WA = "971521300019"

POOL_POSTS = [
    {"id": 38, "kind": "build", "city": "أبوظبي", "kw": "شركة إنشاء وصيانة مسابح أبوظبي"},
    {"id": 97, "kind": "build", "city": "دبي", "kw": "شركة إنشاء وصيانة مسابح دبي"},
    {"id": 133, "kind": "build", "city": "العين", "kw": "شركة إنشاء وصيانة مسابح العين"},
    {"id": 164, "kind": "build", "city": "الشارقة", "kw": "شركة إنشاء وصيانة مسابح الشارقة"},
    {"id": 795, "kind": "clean", "city": "أبوظبي", "kw": "شركة تنظيف مسابح أبوظبي"},
    {"id": 1046, "kind": "clean", "city": "الشارقة", "kw": "شركة تنظيف مسابح الشارقة"},
    {"id": 1218, "kind": "clean", "city": "دبي", "kw": "شركة تنظيف مسابح في دبي"},
    {"id": 2370, "kind": "clean", "city": "الفجيرة", "kw": "شركة تنظيف مسابح في الفجيرة"},
    {"id": 2373, "kind": "clean", "city": "أم القيوين", "kw": "شركة تنظيف مسابح في ام القيوين"},
    {"id": 2425, "kind": "clean", "city": "العين", "kw": "شركة تنظيف مسابح في العين"},
    {"id": 5960, "kind": "clean", "city": "عجمان", "kw": "شركة تنظيف مسابح في عجمان"},
    {"id": 5981, "kind": "clean", "city": "رأس الخيمة", "kw": "شركة تنظيف مسابح في رأس الخيمة"},
    {"id": 6384, "kind": "maint", "city": "أبوظبي", "kw": "شركة صيانة مسابح في أبوظبي"},
    {"id": 6469, "kind": "maint", "city": "دبي", "kw": "شركة صيانة مسابح في دبي"},
    {"id": 6580, "kind": "maint", "city": "الشارقة", "kw": "شركة صيانة مسابح في الشارقة"},
    {"id": 6663, "kind": "maint", "city": "عجمان", "kw": "شركة صيانة مسابح في عجمان"},
    {"id": 6746, "kind": "maint", "city": "رأس الخيمة", "kw": "شركة صيانة مسابح في رأس الخيمة"},
    {"id": 6824, "kind": "maint", "city": "الفجيرة", "kw": "شركة صيانة مسابح في الفجيرة"},
    {"id": 6886, "kind": "maint", "city": "أم القيوين", "kw": "شركة صيانة مسابح في أم القيوين"},
    {"id": 6949, "kind": "maint", "city": "العين", "kw": "شركة صيانة مسابح في العين"},
]

EN_LEAK_INSUL = [
    12183, 12184, 12185, 12186, 12187, 12188, 12189, 12190,
    12191, 12192, 12193, 12194, 12195, 12196, 12197, 12198,
    8996, 12200,
]


def sql(cmd_sql: str) -> dict:
    cmd = "wp db query " + json.dumps(cmd_sql)
    try:
        return cli(cmd, write=True)
    except HTTPError as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
        return cli_approved(cmd)


def q(cmd_sql: str) -> dict:
    r = cli("wp db query " + json.dumps(cmd_sql))
    raw = r.get("stdout") or ""
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        return r


def set_phone_metas(pid: int, tel: str) -> None:
    sql(
        "UPDATE wp3mdn_postmeta SET meta_value='' "
        f"WHERE post_id={int(pid)} "
        "AND meta_key IN ('phone_number','contact_number','phone','memo-meta-phone',"
        "'whatsapp','whatsapp_number')"
    )
    for k in (
        "phone_number",
        "contact_number",
        "phone",
        "memo-meta-phone",
        "whatsapp",
        "whatsapp_number",
    ):
        cli(f"wp post meta update {int(pid)} {k} {json.dumps(tel)} --force", write=True)


def set_call_section(pid: int, tel: str, wa: str, title: str, subtitle: str) -> None:
    payload = json.dumps(
        {
            "call_section_phone": tel,
            "call_section_whatsapp": wa,
            "call_section_title": title,
            "call_section_subtitle": subtitle,
        }
    )
    cli(
        f"wp post meta update {int(pid)} post__call_section__data {json.dumps(payload)} --force",
        write=True,
    )


def seo_title() -> str:
    return f"%title% | {POOL_LOCAL}"


def seo_desc(kind: str, city: str) -> str:
    if kind == "build":
        return (
            f"شركة إنشاء وصيانة مسابح في {city} من ركن التطور: تصميم، عزل، مضخات وفلاتر "
            f"وضمان مكتوب. معاينة سريعة وسعر واضح — اتصل أو واتساب {POOL_LOCAL}."
        )
    if kind == "clean":
        return (
            f"تنظيف وتعقيم مسابح في {city} بمواد آمنة ومعدات حديثة. مياه صافية وموعد مرن "
            f"وضمان مكتوب من ركن التطور — اتصل أو واتساب {POOL_LOCAL}."
        )
    return (
        f"صيانة مسابح في {city}: مضخات، فلاتر، تسريب وكلور بتقرير واضح. فريق معتمد "
        f"وضمان مكتوب — اتصل أو واتساب {POOL_LOCAL}."
    )


def set_seo(pid: int, kind: str, city: str, keyword: str) -> None:
    desc = seo_desc(kind, city)
    for k, v in {
        "rank_math_title": seo_title(),
        "rank_math_description": desc[:320],
        "rank_math_focus_keyword": keyword,
        "rank_math_robots": "index,follow",
    }.items():
        cli(f"wp post meta update {int(pid)} {k} {json.dumps(v)} --force", write=True)


def rewrite_pool_content(raw: str) -> str:
    out = raw
    out = out.replace("{PHONE_UAE}", POOL_TEL)
    out = out.replace("{WHATSAPP_UAE}", POOL_WA)
    replacements = [
        ("tel:+971522901095", f"tel:{POOL_TEL}"),
        ("tel:971522901095", f"tel:{POOL_TEL}"),
        ("https://wa.me/971522901095", f"https://wa.me/{POOL_WA}"),
        ("https://wa.me/+971522901095", f"https://wa.me/{POOL_WA}"),
        ("+971522901095", POOL_TEL),
        ("0522901095", POOL_LOCAL),
        ("tel:+971586634710", f"tel:{POOL_TEL}"),
        ("tel:971586634710", f"tel:{POOL_TEL}"),
        ("tel:0586634710", f"tel:{POOL_TEL}"),
        ("https://wa.me/971586634710", f"https://wa.me/{POOL_WA}"),
        ("https://wa.me/+971586634710", f"https://wa.me/{POOL_WA}"),
        ("+971586634710", POOL_TEL),
        ("0586634710", POOL_LOCAL),
        ("05586634710", POOL_LOCAL),
        ("01556644443", POOL_LOCAL),
        ("الإعلان للإيجار", ""),
    ]
    for old, new in replacements:
        out = out.replace(old, new)
    return out


def confirm_leak_insulation() -> None:
    print("== leak/insulation: Call + WhatsApp", LEAK_CALL, "==")
    rows = q(
        "SELECT ID FROM wp3mdn_posts WHERE post_type='post' AND post_status='publish' "
        "AND (post_title LIKE '%كشف تسربات المياه%' OR post_title LIKE '%عزل أسطح%' "
        "OR post_title LIKE '%عزل الاسطح%' OR post_name LIKE '%water-leak-detection%' "
        "OR post_name LIKE '%roof-insulation%') "
        "AND post_name NOT LIKE '%qatar%' AND post_name NOT LIKE '%makkah%' "
        "AND post_title NOT LIKE '%غاز%' AND post_title NOT LIKE '%تكييف%'"
    ).get("results") or []
    ar_ids = [int(r["ID"]) for r in rows]
    for pid in sorted(set(ar_ids + EN_LEAK_INSUL)):
        set_phone_metas(pid, LEAK_CALL)
        set_call_section(
            pid,
            LEAK_CALL,
            "971524314370",
            "تواصل الآن",
            "كشف تسربات وعزل أسطح — اتصال وواتساب على 0524314370",
        )
        print(" leak/insul", pid)


def apply_pools() -> None:
    print("== pool posts: Call + WhatsApp", POOL_TEL, "==")
    for item in POOL_POSTS:
        pid = int(item["id"])
        set_phone_metas(pid, POOL_TEL)
        set_call_section(
            pid,
            POOL_TEL,
            POOL_WA,
            f"مسابح {item['city']}",
            f"إنشاء وصيانة وتنظيف — اتصال وواتساب {POOL_LOCAL}",
        )
        set_seo(pid, item["kind"], item["city"], item["kw"])
        p = api_get(f"wp/v2/posts/{pid}?context=edit")
        raw = p["content"]["raw"]
        new = rewrite_pool_content(raw)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid, item["city"], item["kind"])
        else:
            print(" metas", pid, item["city"], item["kind"])


def verify() -> None:
    checks = [
        (237, "leak dubai AR"),
        (181, "insul dubai AR"),
        (12185, "leak dubai EN"),
        (12186, "insul dubai EN"),
        (97, "pool build dubai"),
        (795, "pool clean ad"),
        (6469, "pool maint dubai"),
        (5960, "pool clean ajman"),
    ]
    for pid, label in checks:
        r = q(
            f"SELECT meta_key, meta_value FROM wp3mdn_postmeta WHERE post_id={pid} "
            "AND meta_key IN ('phone_number','whatsapp','whatsapp_number','rank_math_title')"
        )
        print(label, pid, {m["meta_key"]: m["meta_value"][:70] for m in r.get("results") or []})
        if pid in (97, 795, 5960, 6469):
            raw = api_get(f"wp/v2/posts/{pid}?context=edit")["content"]["raw"]
            print(
                "  placeholders",
                "{PHONE_UAE}" in raw,
                "old clean",
                "522901095" in raw,
                "egypt",
                "01556644443" in raw,
                "pool tel",
                POOL_TEL in raw or POOL_LOCAL in raw,
            )


def main() -> None:
    confirm_leak_insulation()
    apply_pools()
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    print("== verify ==")
    verify()
    print("done")


if __name__ == "__main__":
    main()
