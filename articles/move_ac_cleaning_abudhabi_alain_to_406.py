#!/usr/bin/env python3
"""Move Abu Dhabi/Al Ain AC-cleaning posts from the cleaning number to +971556190406.

AC duct/unit cleaning is an AC service, not a general cleaning service.
Updates SEO title, SEO description, call button, and WhatsApp.
"""

from __future__ import annotations

import json
import time
from pathlib import Path

from rukn_rewrite_pipeline import api_get, cli
from set_ac_electrical_phone_0556190406 import (
    NEW_LOCAL,
    NEW_PHONE,
    SEO_TITLE,
    cli_set_meta,
    fetch_all_posts,
    get_meta,
    make_seo_description,
    strip_title,
    update_call_section,
    update_content,
    update_service_schema,
    update_simple_phone_metas,
)

CITIES = ("أبوظبي", "ابوظبي", "العين")
AC_WORDS = ("مكيف", "تكييف", "دكت")


def is_ac_cleaning(title: str) -> bool:
    if "تنظيف" not in title:
        return False
    if not any(c in title for c in CITIES):
        return False
    return any(w in title for w in AC_WORDS)


def main() -> None:
    posts = fetch_all_posts()
    targets = []
    for p in posts:
        title = strip_title(p)
        if is_ac_cleaning(title):
            targets.append(
                {
                    "id": int(p["id"]),
                    "title": title,
                    "link": p.get("link"),
                    "slug": p.get("slug") or "",
                }
            )
    print(f"AC-cleaning Abu Dhabi/Al Ain posts: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        print(f"[{i}/{len(targets)}] {item['title']} ({pid})", flush=True)
        try:
            seo_desc = make_seo_description(item["title"])
            cli_set_meta(pid, "rank_math_title", SEO_TITLE)
            cli_set_meta(pid, "rank_math_description", seo_desc)
            update_simple_phone_metas(pid)
            update_call_section(pid)
            schema = update_service_schema(pid)
            raw = ""
            try:
                full = api_get(f"wp/v2/posts/{pid}?context=edit&_fields=content")
                raw = (full.get("content") or {}).get("raw") or ""
            except Exception as e:
                print("  raw content warn:", e, flush=True)
            replaced, inserted = update_content(pid, raw)
            seo = get_meta(pid, "rank_math_title") or ""
            phone = get_meta(pid, "phone_number")
            wa = get_meta(pid, "whatsapp")
            ok = (
                NEW_LOCAL in str(seo)
                and phone == NEW_PHONE
                and wa == NEW_PHONE
            )
            row = {
                "id": pid,
                "title": item["title"],
                "link": item["link"],
                "ok": ok,
                "seo_title_after": seo,
                "phone_number": phone,
                "whatsapp": wa,
                "schema": schema,
                "content_replacements": replaced,
                "contact_block_inserted": inserted,
            }
            results.append(row)
            print("  OK" if ok else "  WARN", phone, wa, seo, flush=True)
        except Exception as e:
            print("  FAIL", e, flush=True)
            results.append({"id": pid, "title": item["title"], "error": str(e)})
        time.sleep(0.08)

    out = Path("/workspace/articles/ac-cleaning-abudhabi-alain-to-406.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Wrote {out} ok={sum(1 for r in results if r.get('ok'))}/{len(results)}")
    try:
        r = cli("litespeed-purge all", write=True)
        print("cache", r.get("exit_code"), (r.get("stdout") or "")[:120])
    except Exception as e:
        print("cache skip", e)


if __name__ == "__main__":
    main()
