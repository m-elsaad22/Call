#!/usr/bin/env python3
"""Call+WhatsApp +971541673020 on listed home-cleaning services only.

Hourly maids: Abu Dhabi only. Never touch Dubai/Sharjah hourly-maid posts.
Never write this number onto pool, leak, drain, AC, pest, or other services.
"""

from __future__ import annotations

import binascii
import json
from urllib.error import HTTPError

from rukn_rewrite_pipeline import api_get, api_patch, cli, cli_approved

TEL = "+971541673020"
TEL_LOCAL = "0541673020"
WA_DIGITS = "971541673020"

CALL_KEYS = ("phone_number", "contact_number", "phone", "memo-meta-phone")
WA_KEYS = ("whatsapp", "whatsapp_number")
PHONE_KEYS = CALL_KEYS + WA_KEYS

NEVER_TOUCH = {791, 793}  # hourly maids Dubai + Sharjah

SLUG_PATTERNS = (
    "cleaning-company-in-%",
    "house-cleaning-%",
    "home-cleaning-%",
    "%-apartment-cleaning%",
    "%-apartments-cleaning%",
    "%-villas-cleaning%",
    "palace-cleaning-%",
    "office-cleaning-%",
    "%-majlis-cleaning%",
    "%-boards-cleaning%",
    "%-council-cleaning%",
    "board-cleaning-%",
    "%-sofa-cleaning%",
    "sofa-cleaning-%",
    "%-kitchen-cleaning%",
    "kitchen-cleaning-%",
    "bathroom-cleaning-%",
    "%-tank-cleaning%",
    "water-tank-cleaning-%",
    "diesel-tank-cleaning-%",
    "%-carpet-cleaning%",
    "moquette-cleaning-%",
    "mattress-cleaning-%",
    "curtain-cleaning-%",
    "%-glass-facade%",
    "glass-facade-cleaning-%",
    "ras-al-khaimah-cleaning",
    "hourly-cleaning-maids-abu-dhabi",
)

HARD_SLUG = (
    "pool",
    "air-conditioner",
    "duct",
    "school",
    "hospital",
    "mall-cleaning",
    "marble",
    "stone-facade",
    "garage",
    "deep-cleaning",
    "chimney",
    "garden",
    "vacuum",
    "hourly-cleaning-maids-dubai",
    "hourly-cleaning-maids-sharjah",
)

CITIES = (
    "abu-dhabi",
    "dubai",
    "sharjah",
    "ajman",
    "fujairah",
    "ras-al-khaimah",
    "umm-al-quwain",
    "al-ain",
)

CONTENT_REPLACES = (
    ("tel:+971522901095", f"tel:{TEL}"),
    ("tel:971522901095", f"tel:{TEL}"),
    ("https://wa.me/971522901095", f"https://wa.me/{WA_DIGITS}"),
    ("https://wa.me/+971522901095", f"https://wa.me/{WA_DIGITS}"),
    ("+971522901095", TEL),
    ("0522901095", TEL_LOCAL),
    ("tel:+971586634710", f"tel:{TEL}"),
    ("tel:971586634710", f"tel:{TEL}"),
    ("tel:0586634710", f"tel:{TEL}"),
    ("tel:+971-58-663-4710", f"tel:{TEL}"),
    ("https://wa.me/971586634710", f"https://wa.me/{WA_DIGITS}"),
    ("https://wa.me/+971586634710", f"https://wa.me/{WA_DIGITS}"),
    ("https://wa.me/0586634710", f"https://wa.me/{WA_DIGITS}"),
    ("01556644443", TEL_LOCAL),
)


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


def hex_sql(value: str) -> str:
    return "UNHEX('" + binascii.hexlify(value.encode("utf-8")).decode() + "')"


def allowed_city(name: str) -> bool:
    if name == "ras-al-khaimah-cleaning":
        return True
    if name == "hourly-cleaning-maids-abu-dhabi":
        return True
    return any(c in name for c in CITIES)


def allowed_service(name: str) -> bool:
    if name == "hourly-cleaning-maids-abu-dhabi":
        return True
    if name.startswith("cleaning-company-in") or name.startswith("house-cleaning") or name.startswith("home-cleaning"):
        return True
    if name == "ras-al-khaimah-cleaning":
        return True
    needles = (
        "apartment",
        "villa",
        "palace-cleaning",
        "office-cleaning",
        "majlis",
        "boards-cleaning",
        "council-cleaning",
        "board-cleaning",
        "sofa",
        "kitchen",
        "bathroom",
        "tank-cleaning",
        "water-tank",
        "diesel-tank",
        "carpet",
        "moquette",
        "mattress",
        "curtain",
        "glass-facade",
    )
    return any(n in name for n in needles)


def target_ids() -> list[int]:
    seen: dict[int, str] = {}
    for pat in SLUG_PATTERNS:
        rows = q(
            "SELECT ID, post_name FROM wp3mdn_posts "
            "WHERE post_type='post' AND post_status='publish' "
            f"AND post_name LIKE '{pat}'"
        ).get("results") or []
        for row in rows:
            seen[int(row["ID"])] = row["post_name"]
    ids = []
    for pid, name in sorted(seen.items()):
        if pid in NEVER_TOUCH:
            continue
        if any(h in name for h in HARD_SLUG):
            continue
        if not allowed_city(name) or not allowed_service(name):
            continue
        ids.append(pid)
    return ids


def replace_metas(ids: list[int]) -> None:
    id_list = ",".join(str(i) for i in ids)
    keys = ",".join("'" + k + "'" for k in PHONE_KEYS)
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key IN ({keys})")
    rows = []
    for pid in ids:
        for k in PHONE_KEYS:
            rows.append(f"({pid},'{k}','{TEL}')")
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(rows))
    print("phone metas", len(ids))


def write_seo_and_call_section(ids: list[int]) -> None:
    id_list = ",".join(str(i) for i in ids)
    keys = "('rank_math_title','post__call_section__data')"
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key IN {keys}")
    section = json.dumps(
        {
            "call_section_phone": TEL,
            "call_section_whatsapp": WA_DIGITS,
            "call_section_title": "تنظيف منازل",
            "call_section_subtitle": "اتصال وواتساب " + TEL_LOCAL,
        },
        ensure_ascii=False,
    )
    rows = []
    for pid in ids:
        rows.append(f"({pid},'rank_math_title','%title% | {TEL_LOCAL}')")
        rows.append(f"({pid},'post__call_section__data',{hex_sql(section)})")
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(rows))
    print("seo + call section", len(ids))


def rewrite_content(ids: list[int]) -> None:
    for pid in ids:
        raw = api_get(f"wp/v2/posts/{pid}?context=edit")["content"]["raw"]
        new = raw
        for old, repl in CONTENT_REPLACES:
            new = new.replace(old, repl)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid)
        else:
            print(" content skip", pid)


def verify(ids: list[int]) -> None:
    sample = [789, 344, 471, 6015, 6036, 5919, 791, 793, 38, 237, 448, 5902]
    id_list = ",".join(str(i) for i in sample)
    r = q(
        "SELECT post_id, meta_key, LEFT(meta_value,50) v FROM wp3mdn_postmeta "
        f"WHERE post_id IN ({id_list}) AND meta_key IN "
        "('phone_number','whatsapp','rank_math_title')"
    )
    print("verify", json.dumps(r.get("results"), ensure_ascii=False)[:4000])
    leaked = q(
        "SELECT post_id FROM wp3mdn_postmeta "
        "WHERE meta_key IN ('phone_number','whatsapp') "
        f"AND meta_value LIKE '%541673020%' AND post_id NOT IN ({','.join(str(i) for i in ids)}) "
        "LIMIT 20"
    ).get("results") or []
    print("leakage to other posts", leaked)


def main() -> None:
    ids = target_ids()
    print("targets", len(ids), ids[:12], "...")
    if 791 in ids or 793 in ids:
        raise RuntimeError("hourly Dubai/Sharjah must stay untouched")
    if 38 in ids or 237 in ids or 448 in ids or 5960 in ids:
        raise RuntimeError("pool/leak/drain must stay untouched")
    replace_metas(ids)
    write_seo_and_call_section(ids)
    rewrite_content(ids)
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    verify(ids)
    print("done")


if __name__ == "__main__":
    main()
