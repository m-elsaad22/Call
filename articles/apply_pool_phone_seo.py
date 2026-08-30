#!/usr/bin/env python3
"""Pool Call+WhatsApp +971521300019, SEO with 0521300019, confirm leak/insulation 0524314370."""

from __future__ import annotations

import json
from urllib.error import HTTPError

from rukn_rewrite_pipeline import api_get, api_patch, cli, cli_approved

LEAK_CALL = "+971524314370"
POOL_TEL = "+971521300019"
POOL_LOCAL = "0521300019"
POOL_WA = "971521300019"

PHONE_KEYS = (
    "phone_number",
    "contact_number",
    "phone",
    "memo-meta-phone",
    "whatsapp",
    "whatsapp_number",
)

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


def esc(value: str) -> str:
    return value.replace("\\", "\\\\").replace("'", "''")


def replace_metas(ids: list[int], mapping: dict[str, str]) -> None:
    if not ids:
        return
    id_list = ",".join(str(int(i)) for i in ids)
    keys = ",".join("'" + k + "'" for k in mapping)
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key IN ({keys})")
    rows = []
    for pid in ids:
        for k, v in mapping.items():
            rows.append(f"({int(pid)},'{esc(k)}','{esc(v)}')")
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(rows))


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


CTA = (
    '<div class="rukn-service-phone" style="background:#0A1F4E;color:#fff;padding:14px 18px;'
    'border-radius:10px;margin:16px 0;text-align:center;"><p style="margin:0;font-size:18px;">'
    f'<strong>اطلب خدمة المسابح الآن</strong> — اتصل أو واتساب '
    f'<a href="tel:{POOL_TEL}" style="color:#fff;font-weight:700;">{POOL_LOCAL}</a> | '
    f'<a href="https://wa.me/{POOL_WA}" style="color:#fff;font-weight:700;" target="_blank" rel="noopener">{POOL_TEL}</a>'
    "</p></div>\n"
)


def rewrite_pool_content(raw: str) -> str:
    out = raw
    out = out.replace("{PHONE_UAE}", POOL_TEL)
    out = out.replace("{WHATSAPP_UAE}", POOL_WA)
    for old, new in [
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
    ]:
        out = out.replace(old, new)
    if "tel:+971521300019" not in out and f"tel:{POOL_TEL}" not in out:
        out = CTA + out
    return out


def leak_insulation_ids() -> list[int]:
    rows = q(
        "SELECT ID FROM wp3mdn_posts WHERE post_type='post' AND post_status='publish' "
        "AND (post_title LIKE '%كشف تسربات المياه%' OR post_title LIKE '%عزل أسطح%' "
        "OR post_title LIKE '%عزل الاسطح%' OR post_name LIKE '%water-leak-detection%' "
        "OR post_name LIKE '%roof-insulation%') "
        "AND post_name NOT LIKE '%qatar%' AND post_name NOT LIKE '%makkah%' "
        "AND post_title NOT LIKE '%غاز%' AND post_title NOT LIKE '%تكييف%'"
    ).get("results") or []
    return sorted({int(r["ID"]) for r in rows} | set(EN_LEAK_INSUL))


def confirm_leak_insulation() -> None:
    ids = leak_insulation_ids()
    print("leak/insulation posts", len(ids))
    phones = {k: LEAK_CALL for k in PHONE_KEYS}
    phones["post__call_section__data"] = json.dumps(
        {
            "call_section_phone": LEAK_CALL,
            "call_section_whatsapp": "971524314370",
            "call_section_title": "تواصل الآن",
            "call_section_subtitle": "كشف تسربات وعزل أسطح — اتصال وواتساب على 0524314370",
        },
        ensure_ascii=False,
    )
    replace_metas(ids, phones)
    print("leak/insulation metas written")


def apply_pools() -> None:
    ids = [int(p["id"]) for p in POOL_POSTS]
    phones = {k: POOL_TEL for k in PHONE_KEYS}
    replace_metas(ids, phones)
    print("pool phone metas written", len(ids))

    seo_rows = []
    for item in POOL_POSTS:
        pid = int(item["id"])
        mapping = {
            "rank_math_title": f"%title% | {POOL_LOCAL}",
            "rank_math_description": seo_desc(item["kind"], item["city"])[:320],
            "rank_math_focus_keyword": item["kw"],
            "rank_math_robots": "index,follow",
            "post__call_section__data": json.dumps(
                {
                    "call_section_phone": POOL_TEL,
                    "call_section_whatsapp": POOL_WA,
                    "call_section_title": f"مسابح {item['city']}",
                    "call_section_subtitle": f"إنشاء وصيانة وتنظيف — اتصال وواتساب {POOL_LOCAL}",
                },
                ensure_ascii=False,
            ),
        }
        seo_rows.append((pid, mapping))
    all_ids = [pid for pid, _ in seo_rows]
    keys = (
        "rank_math_title",
        "rank_math_description",
        "rank_math_focus_keyword",
        "rank_math_robots",
        "post__call_section__data",
    )
    id_list = ",".join(str(i) for i in all_ids)
    key_list = ",".join("'" + k + "'" for k in keys)
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key IN ({key_list})")
    values = []
    for pid, mapping in seo_rows:
        for k, v in mapping.items():
            values.append(f"({pid},'{esc(k)}','{esc(v)}')")
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(values))
    print("pool seo written")

    for item in POOL_POSTS:
        pid = int(item["id"])
        raw = api_get(f"wp/v2/posts/{pid}?context=edit")["content"]["raw"]
        new = rewrite_pool_content(raw)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid, item["city"], item["kind"])
        else:
            print(" content unchanged", pid)


def verify() -> None:
    checks = [237, 181, 12185, 12186, 97, 795, 6469, 5960]
    id_list = ",".join(str(i) for i in checks)
    r = q(
        f"SELECT post_id, meta_key, LEFT(meta_value,80) v FROM wp3mdn_postmeta "
        f"WHERE post_id IN ({id_list}) AND meta_key IN "
        f"('phone_number','whatsapp','rank_math_title')"
    )
    by: dict[int, dict[str, str]] = {}
    for m in r.get("results") or []:
        by.setdefault(int(m["post_id"]), {})[m["meta_key"]] = m["v"]
    for pid in checks:
        print(pid, by.get(pid))
    raw = api_get("wp/v2/posts/5960?context=edit")["content"]["raw"]
    print("ajman placeholder", "{PHONE_UAE}" in raw, "pool tel", POOL_TEL in raw)
    raw = api_get("wp/v2/posts/795?context=edit")["content"]["raw"]
    print("ad clean old", "522901095" in raw, "pool", POOL_LOCAL in raw)


def main() -> None:
    print("== leak/insulation Call+WA ==")
    confirm_leak_insulation()
    print("== pool Call+WA + SEO ==")
    apply_pools()
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    print("== verify ==")
    verify()
    print("done")


if __name__ == "__main__":
    main()
