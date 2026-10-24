#!/usr/bin/env python3
"""Set +971541673020 on all pest-related posts in selected emirates.

Cities: Dubai, Sharjah, Ajman, Fujairah, Ras Al Khaimah, Umm Al Quwain, Khorfakkan.
Includes insect/pest control, rodents, birds, pigeon deterrents, etc.
Sets call + WhatsApp button metas and related phone fields.
"""

from __future__ import annotations

import json
import re
import time
from copy import deepcopy
from pathlib import Path

from rukn_rewrite_pipeline import sql_set_meta
from replace_banned_phones_wa import get_meta

PHONE = "+971541673020"
PHONE_LOCAL = "0541673020"

CITIES = ["دبي", "الشارقة", "عجمان", "الفجيرة", "رأس الخيمة", "أم القيوين", "خورفكان"]
CITY_SLUGS = [
    "dubai",
    "sharjah",
    "ajman",
    "fujairah",
    "ras-al-khaimah",
    "umm-al-quwain",
    "khorfakkan",
    "khor-fakkan",
    "khawr",
]
PEST_KEYS = [
    "مكافحة",
    "وزغ",
    "برص",
    "فئران",
    "قوارض",
    "طيور",
    "أفاعي",
    "عقارب",
    "صراصير",
    "بق",
    "رمة",
    "نمل",
    "براغيث",
    "بعوض",
    "ذباب",
    "حشرات",
    "زواحف",
    "طارد",
    "حمام",
]
PEST_SLUGS = [
    "pest",
    "insect",
    "termite",
    "rodent",
    "snake",
    "bird",
    "lizard",
    "cockroach",
    "flea",
    "mosquito",
    "ant-control",
    "scorpion",
    "flying-pest",
    "crawling-pest",
    "pigeon",
    "dove",
    "repellent",
]
EXCLUDE = ["حريق", "عزل حمام", "تنظيف حمام", "ترميم حمام"]


def db_paged(sql_base: str, page_size: int = 100):
    from replace_banned_phones_bulk import db_query

    rows_all = []
    offset = 0
    while True:
        data = db_query(f"{sql_base} LIMIT {page_size} OFFSET {offset}")
        rows = data.get("results", data if isinstance(data, list) else [])
        if not isinstance(rows, list):
            rows = []
        rows_all.extend(rows)
        if len(rows) < page_size:
            break
        offset += page_size
    return rows_all


def is_target(title: str, slug: str) -> bool:
    if any(x in title for x in EXCLUDE):
        return False
    city_ok = any(c in title for c in CITIES) or any(c in slug for c in CITY_SLUGS)
    pest_ok = any(k in title for k in PEST_KEYS) or any(k in slug for k in PEST_SLUGS)
    return city_ok and pest_ok


def set_phones(post_id: int) -> dict:
    changed = []

    # Call section: BOTH call + whatsapp get this number
    data = get_meta(post_id, "post__call_section__data")
    if not isinstance(data, dict):
        data = {
            "call_section_title": "تواصل معنا الآن",
            "call_section_content": "تواصل معنا لطلب الخدمة.",
            "call_section_phone": PHONE,
            "call_section_whatsapp": PHONE,
        }
        sql_set_meta(post_id, "post__call_section__data", data)
        changed.append("call_section_created")
    else:
        data = deepcopy(data)
        if data.get("call_section_phone") != PHONE or data.get("call_section_whatsapp") != PHONE:
            data["call_section_phone"] = PHONE
            data["call_section_whatsapp"] = PHONE
            sql_set_meta(post_id, "post__call_section__data", data)
            changed.append("call_section")

    for key in (
        "memo-meta-phone",
        "phone",
        "phone_number",
        "whatsapp",
        "whatsapp_number",
        "contact_number",
    ):
        cur = get_meta(post_id, key)
        if cur != PHONE:
            sql_set_meta(post_id, key, PHONE)
            changed.append(key)

    svc = get_meta(post_id, "YourColor_Service")
    if isinstance(svc, dict):
        svc = deepcopy(svc)
        if svc.get("telephone") not in (PHONE, PHONE_LOCAL):
            svc["telephone"] = PHONE
            sql_set_meta(post_id, "YourColor_Service", svc)
            changed.append("YourColor_Service")

    return {"changed": changed}


def main() -> None:
    posts = db_paged(
        "SELECT ID, post_title, post_name FROM wp3mdn_posts "
        "WHERE post_status='publish' AND post_type='post' ORDER BY ID"
    )
    targets = []
    for p in posts:
        title = p.get("post_title") or ""
        slug = (p.get("post_name") or "").lower()
        if is_target(title, slug):
            targets.append({"id": int(p["ID"]), "title": title, "slug": slug})

    print(f"Targets: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        print(f"=== [{i}/{len(targets)}] {item['title'][:55]} ({pid}) ===", flush=True)
        try:
            info = set_phones(pid)
            # verify
            call = get_meta(pid, "post__call_section__data") or {}
            ok = (
                isinstance(call, dict)
                and call.get("call_section_phone") == PHONE
                and call.get("call_section_whatsapp") == PHONE
                and get_meta(pid, "whatsapp") == PHONE
            )
            row = {
                "id": pid,
                "title": item["title"],
                "ok": ok,
                "call": call.get("call_section_phone") if isinstance(call, dict) else None,
                "wa": call.get("call_section_whatsapp") if isinstance(call, dict) else None,
                "changed": info["changed"],
            }
            results.append(row)
            print("OK" if ok else "WARN", row["call"], row["wa"], flush=True)
        except Exception as e:
            print("FAIL", pid, e, flush=True)
            results.append({"id": pid, "title": item["title"], "error": str(e)})
        time.sleep(0.05)

    out = Path("/workspace/articles/pest-phone-541673020.json")
    out.write_text(
        json.dumps(
            {
                "phone": PHONE,
                "cities": CITIES,
                "results": results,
                "stats": {
                    "total": len(results),
                    "ok": sum(1 for r in results if r.get("ok")),
                    "fail": sum(1 for r in results if r.get("error")),
                },
            },
            ensure_ascii=False,
            indent=2,
        ),
        encoding="utf-8",
    )
    print("Wrote", out)
    print(out.read_text()[-300:] if False else "")
    print({"total": len(results), "ok": sum(1 for r in results if r.get("ok")), "fail": sum(1 for r in results if r.get("error"))})


if __name__ == "__main__":
    main()
